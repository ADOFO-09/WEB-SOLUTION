using System;
using System.Net;
using System.Net.WebSockets;
using System.Runtime.InteropServices;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading;
using System.Threading.Tasks;

/// <summary>
/// ZKFinger WebSocket Bridge v3 — direct P/Invoke into libzkfp.dll (no managed wrapper).
/// WebSocket: ws://localhost:15896/fingerprint
/// </summary>
class ZKFingerBridge
{
    // ── P/Invoke declarations (libzkfp.dll, __stdcall) ────────────────────────
    const string DLL = "libzkfp.dll";
    const CallingConvention CC = CallingConvention.StdCall;

    [DllImport(DLL, CallingConvention = CC, SetLastError = true)] static extern int    ZKFPM_Init();
    [DllImport(DLL, CallingConvention = CC)]                       static extern int    ZKFPM_Terminate();
    [DllImport(DLL, CallingConvention = CC)]                       static extern int    ZKFPM_GetDeviceCount();
    [DllImport(DLL, CallingConvention = CC, SetLastError = true)]  static extern IntPtr ZKFPM_OpenDevice(int index);
    [DllImport(DLL, CallingConvention = CC)]                       static extern int    ZKFPM_CloseDevice(IntPtr hDev);

    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_GetParameters(IntPtr hDev, int code, [Out] byte[] value, ref uint cbValue);

    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_AcquireFingerprint(
        IntPtr hDev,
        [Out] byte[] fpImage,  uint cbImage,
        [Out] byte[] fpTmpl,   ref uint cbTmpl);

    [DllImport(DLL, CallingConvention = CC, SetLastError = true)]  static extern IntPtr ZKFPM_DBInit();
    [DllImport(DLL, CallingConvention = CC)]                       static extern int    ZKFPM_DBFree(IntPtr hDB);
    [DllImport(DLL, CallingConvention = CC)]                       static extern int    ZKFPM_DBClear(IntPtr hDB);

    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_DBAdd(IntPtr hDB, uint fid, [In] byte[] tmpl, uint cbTmpl);

    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_DBIdentify(IntPtr hDB, [In] byte[] tmpl, uint cbTmpl, out uint fid, out uint score);

    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_DBMatch(IntPtr hDB, [In] byte[] t1, uint cb1, [In] byte[] t2, uint cb2);

    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_DBMerge(IntPtr hDB,
        [In]  byte[] t1, [In]  byte[] t2, [In]  byte[] t3,
        [Out] byte[] reg, ref uint cbReg);

    // Returns byte count written (>0 = ok, <=0 = fail)
    [DllImport(DLL, CallingConvention = CC, CharSet = CharSet.Ansi)]
    static extern int ZKFPM_Base64ToBlob(
        [MarshalAs(UnmanagedType.LPStr)] string src,
        [Out] byte[] blob, uint cbBlob);

    // Returns byte count written (>0 = ok, <=0 = fail)
    [DllImport(DLL, CallingConvention = CC)]
    static extern int ZKFPM_BlobToBase64(
        [In] byte[] src, uint cbSrc,
        [Out] byte[] b64, uint cbB64);

    // ── State ─────────────────────────────────────────────────────────────────
    const int WS_PORT   = 15896;
    const int TMPL_SIZE = 2048;

    static IntPtr devHandle = IntPtr.Zero;
    static IntPtr dbHandle  = IntPtr.Zero;
    static uint   fpW, fpH;
    static byte[] fpBuf;
    static volatile bool running = true;

    enum Mode { Idle, CaptureWait, Identify }
    static volatile Mode mode = Mode.Idle;

    static volatile TaskCompletionSource<(byte[] tmpl, uint size)> pendingTcs;
    static volatile WebSocket activeWs;
    static readonly SemaphoreSlim sendLock = new SemaphoreSlim(1, 1);

    static readonly object   cacheLock = new object();
    static byte[]   cachedTmpl;
    static uint     cachedSize;
    static DateTime cachedAt = DateTime.MinValue;

    // ── Entry point ───────────────────────────────────────────────────────────
    static void Main()
    {
        Console.WriteLine("=== ZKFinger Bridge v3.0 (direct P/Invoke) ===");
        Console.WriteLine($"WebSocket: ws://localhost:{WS_PORT}/fingerprint");
        Console.WriteLine();

        if (!InitDevice()) { Console.WriteLine("Press any key..."); Console.ReadKey(); return; }

        new Thread(CaptureLoop) { IsBackground = true, Name = "FPCapture" }.Start();

        var http = new HttpListener();
        http.Prefixes.Add($"http://localhost:{WS_PORT}/");
        try { http.Start(); }
        catch (Exception ex)
        {
            Console.Error.WriteLine($"[ERROR] Cannot bind port {WS_PORT}: {ex.Message}");
            Console.Error.WriteLine("Check that port is free and run as Administrator.");
            Shutdown(); Console.WriteLine("Press any key..."); Console.ReadKey(); return;
        }

        Console.WriteLine("[INFO] Ready. Waiting for connections...");
        Console.CancelKeyPress += (s, e) => { e.Cancel = true; running = false; http.Stop(); };

        while (running)
        {
            HttpListenerContext ctx;
            try { ctx = http.GetContext(); }
            catch { break; }

            if (ctx.Request.IsWebSocketRequest)
                Task.Run(() => HandleWebSocket(ctx));
            else
                HealthCheck(ctx);
        }

        Shutdown();
    }

    // ── Device init ───────────────────────────────────────────────────────────
    static bool InitDevice()
    {
        int r = ZKFPM_Init();
        if (r != 0)
        {
            int err = Marshal.GetLastWin32Error();
            Console.Error.WriteLine($"[ERROR] ZKFPM_Init failed: ret={r}, Win32={err}");
            return false;
        }

        int count = ZKFPM_GetDeviceCount();
        Console.WriteLine($"[INFO] Devices detected: {count}");
        if (count < 1)
        {
            Console.Error.WriteLine("[ERROR] No fingerprint device found.");
            ZKFPM_Terminate();
            return false;
        }

        devHandle = ZKFPM_OpenDevice(0);
        if (devHandle == IntPtr.Zero)
        {
            int err = Marshal.GetLastWin32Error();
            Console.Error.WriteLine($"[ERROR] ZKFPM_OpenDevice(0) failed. Win32 error={err} (0x{err:X8})");
            Console.Error.WriteLine("        Possible causes:");
            Console.Error.WriteLine("        - Another app has the device open");
            Console.Error.WriteLine("        - Windows Biometric Service still holds it (stop WbioSrvc)");
            Console.Error.WriteLine("        - SDK version mismatch with device firmware");
            ZKFPM_Terminate();
            return false;
        }

        dbHandle = ZKFPM_DBInit();
        if (dbHandle == IntPtr.Zero)
        {
            int err = Marshal.GetLastWin32Error();
            Console.Error.WriteLine($"[ERROR] ZKFPM_DBInit failed. Win32 error={err}");
            ZKFPM_CloseDevice(devHandle);
            ZKFPM_Terminate();
            return false;
        }

        // Read scanner dimensions
        byte[] pv = new byte[4]; uint sz = 4;
        int rW = ZKFPM_GetParameters(devHandle, 1, pv, ref sz); fpW = (uint)BitConverter.ToInt32(pv, 0);
        sz = 4;
        int rH = ZKFPM_GetParameters(devHandle, 2, pv, ref sz); fpH = (uint)BitConverter.ToInt32(pv, 0);

        if (fpW == 0 || fpH == 0)
        {
            Console.WriteLine($"[WARN] GetParameters returned W={fpW} H={fpH} (ret {rW}/{rH}) — using safe default 400x500");
            fpW = 400; fpH = 500;
        }
        fpBuf = new byte[fpW * fpH];
        Console.WriteLine($"[INFO] Scanner open: {fpW}x{fpH} px, image buffer {fpBuf.Length} bytes");
        return true;
    }

    static void Shutdown()
    {
        running = false;
        if (dbHandle  != IntPtr.Zero) { ZKFPM_DBFree(dbHandle);    dbHandle  = IntPtr.Zero; }
        if (devHandle != IntPtr.Zero) { ZKFPM_CloseDevice(devHandle); devHandle = IntPtr.Zero; }
        ZKFPM_Terminate();
        Console.WriteLine("[INFO] Shutdown complete.");
    }

    // ── Capture loop ──────────────────────────────────────────────────────────
    static void CaptureLoop()
    {
        byte[] tmp = new byte[TMPL_SIZE];
        int logTick = 0;
        while (running)
        {
            uint cbTmp = TMPL_SIZE;
            int ret = ZKFPM_AcquireFingerprint(devHandle, fpBuf, (uint)fpBuf.Length, tmp, ref cbTmp);
            if (ret != 0)
            {
                if (++logTick % 200 == 0) // log every ~10 s to confirm loop is alive
                    Console.WriteLine($"[CAP] Waiting for finger (AcquireFingerprint ret={ret}, mode={mode})");
                Thread.Sleep(50);
                continue;
            }
            Console.WriteLine($"[CAP] Fingerprint acquired! size={cbTmp}, mode={mode}");

            // Cache every successful scan
            byte[] copy = new byte[cbTmp];
            Array.Copy(tmp, copy, cbTmp);
            lock (cacheLock) { cachedTmpl = copy; cachedSize = cbTmp; cachedAt = DateTime.UtcNow; }

            var m = mode;

            if (m == Mode.CaptureWait)
            {
                var tcs = pendingTcs;
                if (tcs != null) { pendingTcs = null; mode = Mode.Idle; tcs.TrySetResult((copy, cbTmp)); }
            }
            else if (m == Mode.Identify)
            {
                var ws = activeWs;
                if (ws == null || ws.State != WebSocketState.Open) { Thread.Sleep(50); continue; }

                uint fid = 0, score = 0;
                int r2 = ZKFPM_DBIdentify(dbHandle, tmp, cbTmp, out fid, out score);
                if (fid >= 1_000_000) fid -= 1_000_000;  // resolve t2 offset

                string json = (r2 == 0)
                    ? $"{{\"type\":\"identify_result\",\"matched\":true,\"member_id\":{fid},\"score\":{score}}}"
                    : "{\"type\":\"identify_result\",\"matched\":false}";
                _ = SendAsync(ws, json);
            }

            Thread.Sleep(200);
        }
    }

    // ── WebSocket handler ─────────────────────────────────────────────────────
    static async Task HandleWebSocket(HttpListenerContext ctx)
    {
        WebSocket ws = null;
        try
        {
            ws = (await ctx.AcceptWebSocketAsync(null)).WebSocket;
            activeWs = ws;
            Console.WriteLine("[WS] Client connected");

            var buf = new byte[4 * 1024 * 1024]; // 4 MB — enough for large member lists

            while (ws.State == WebSocketState.Open && running)
            {
                var r = await ws.ReceiveAsync(new ArraySegment<byte>(buf), CancellationToken.None);
                if (r.MessageType == WebSocketMessageType.Close) break;

                string msg    = Encoding.UTF8.GetString(buf, 0, r.Count);
                string action = ExtractStr(msg, "action");
                Console.WriteLine($"[WS] action={action}");

                // ── capture ──────────────────────────────────────────────────
                if (action == "capture")
                {
                    mode = Mode.Idle;
                    pendingTcs = null;

                    // Use cached scan if recent (within 4 s)
                    byte[] recentTmpl = null; uint recentSz = 0;
                    lock (cacheLock)
                    {
                        if (cachedTmpl != null && (DateTime.UtcNow - cachedAt).TotalSeconds <= 4)
                        { recentTmpl = cachedTmpl; recentSz = cachedSize; cachedTmpl = null; }
                    }
                    if (recentTmpl != null)
                    {
                        string b64c = BlobToBase64(recentTmpl, recentSz);
                        await SendAsync(ws, MakeCaptureOk(b64c));
                        Console.WriteLine($"[WS] Capture from cache ({recentSz} bytes), b64 len={b64c.Length}");
                        continue;
                    }

                    await SendAsync(ws, "{\"type\":\"scanning\"}");
                    var tcs = new TaskCompletionSource<(byte[], uint)>(TaskCreationOptions.RunContinuationsAsynchronously);
                    pendingTcs = tcs;
                    mode = Mode.CaptureWait;

                    using var cts = new CancellationTokenSource(TimeSpan.FromSeconds(30));
                    cts.Token.Register(() => tcs.TrySetCanceled(), false);
                    try
                    {
                        var (tmpl, sz) = await tcs.Task;
                        string b64 = BlobToBase64(tmpl, sz);
                        await SendAsync(ws, MakeCaptureOk(b64));
                        Console.WriteLine($"[WS] Capture OK ({sz} bytes), b64 len={b64.Length}");
                    }
                    catch (TaskCanceledException)
                    {
                        mode = Mode.Idle; pendingTcs = null;
                        await SendAsync(ws, "{\"type\":\"capture_result\",\"success\":false,\"message\":\"Timeout — place your finger and try again\"}");
                    }
                }

                // ── start_identify ───────────────────────────────────────────
                else if (action == "start_identify")
                {
                    mode = Mode.Idle; pendingTcs = null;
                    ZKFPM_DBClear(dbHandle);
                    int n = LoadMembers(msg);
                    Console.WriteLine($"[WS] Identify mode: {n} templates loaded");
                    await SendAsync(ws, $"{{\"type\":\"ready\",\"count\":{n}}}");
                    mode = Mode.Identify;
                }

                // ── stop_identify ────────────────────────────────────────────
                else if (action == "stop_identify")
                {
                    mode = Mode.Idle;
                    ZKFPM_DBClear(dbHandle);
                    await SendAsync(ws, "{\"type\":\"stopped\"}");
                }
            }
        }
        catch (Exception ex) { Console.Error.WriteLine($"[WS ERROR] {ex.Message}"); }
        finally
        {
            mode = Mode.Idle; pendingTcs = null; activeWs = null;
            ZKFPM_DBClear(dbHandle);
            try { ws?.CloseAsync(WebSocketCloseStatus.NormalClosure, "", CancellationToken.None).Wait(2000); } catch { }
            Console.WriteLine("[WS] Client disconnected");
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    static string MakeCaptureOk(string b64)
    {
        // Convert.ToBase64String only produces [A-Za-z0-9+/=] — all safe in JSON strings
        return $"{{\"type\":\"capture_result\",\"success\":true,\"template\":\"{b64}\"}}";
    }

    static int LoadMembers(string json)
    {
        int count = 0;
        foreach (Match obj in Regex.Matches(json, @"\{[^{}]*\}"))
        {
            string o = obj.Value;
            var idM = Regex.Match(o, @"""id""\s*:\s*(\d+)");
            var t1M = Regex.Match(o, @"""t1""\s*:\s*""([A-Za-z0-9+/=]+)""");
            var t2M = Regex.Match(o, @"""t2""\s*:\s*""([A-Za-z0-9+/=]+)""");
            if (!idM.Success) continue;

            uint id = uint.Parse(idM.Groups[1].Value);
            if (t1M.Success)
            {
                (byte[] b, int s) = Base64ToBlob(t1M.Groups[1].Value);
                if (s > 0) { ZKFPM_DBAdd(dbHandle, id, b, (uint)s); count++; }
            }
            if (t2M.Success)
            {
                (byte[] b, int s) = Base64ToBlob(t2M.Groups[1].Value);
                if (s > 0) { ZKFPM_DBAdd(dbHandle, id + 1_000_000, b, (uint)s); count++; }
            }
        }
        return count;
    }

    static string BlobToBase64(byte[] blob, uint size)
    {
        // Use .NET's standard base64 — no newlines, no special chars, safe in JSON
        return Convert.ToBase64String(blob, 0, (int)size);
    }

    static (byte[] blob, int size) Base64ToBlob(string b64)
    {
        try
        {
            byte[] blob = Convert.FromBase64String(b64);
            return (blob, blob.Length);
        }
        catch { return (null, 0); }
    }

    static async Task SendAsync(WebSocket ws, string json)
    {
        if (ws == null || ws.State != WebSocketState.Open) return;
        byte[] bytes = Encoding.UTF8.GetBytes(json);
        await sendLock.WaitAsync();
        try { await ws.SendAsync(new ArraySegment<byte>(bytes), WebSocketMessageType.Text, true, CancellationToken.None); }
        catch { }
        finally { sendLock.Release(); }
    }

    static void HealthCheck(HttpListenerContext ctx)
    {
        ctx.Response.StatusCode = 200;
        byte[] b = Encoding.UTF8.GetBytes("{\"status\":\"ok\"}");
        ctx.Response.ContentLength64 = b.Length;
        ctx.Response.OutputStream.Write(b, 0, b.Length);
        ctx.Response.Close();
    }

    static string ExtractStr(string json, string key)
    {
        var m = Regex.Match(json, $"\"{Regex.Escape(key)}\"\\s*:\\s*\"([^\"]+)\"");
        return m.Success ? m.Groups[1].Value : string.Empty;
    }
}

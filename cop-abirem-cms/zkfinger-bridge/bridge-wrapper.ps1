# bridge-wrapper.ps1  — executed by the ZKFingerBridge scheduled task at boot.
# Stops WbioSrvc (releases scanner from Windows Hello), launches the bridge,
# then restores WbioSrvc when the bridge exits or crashes.

$BridgeDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$BridgeExe = "$BridgeDir\bin\Release\net48\ZKFingerBridge.exe"
$LogFile   = "$BridgeDir\bridge.log"

function Log($msg) {
    $ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$ts  $msg" | Out-File -FilePath $LogFile -Append -Encoding utf8
}

# Rotate log — keep last 300 lines when it grows large
if ((Test-Path $LogFile) -and ((Get-Item $LogFile).Length / 1KB) -gt 256) {
    $kept = Get-Content $LogFile | Select-Object -Last 300
    $kept | Set-Content $LogFile -Encoding utf8
}

Log "=============================="
Log "ZKFinger Bridge starting up"

if (-not (Test-Path $BridgeExe)) {
    Log "ERROR: $BridgeExe not found. Rebuild the project in Release mode."
    exit 1
}

# Give Windows time to finish loading device drivers after boot
Start-Sleep -Seconds 10

# Release the fingerprint scanner from Windows Biometric Service
Log "Stopping WbioSrvc..."
Stop-Service WbioSrvc -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 2

Log "Launching ZKFingerBridge.exe..."

$psi                        = New-Object System.Diagnostics.ProcessStartInfo
$psi.FileName               = $BridgeExe
$psi.WorkingDirectory       = $BridgeDir
$psi.UseShellExecute        = $false
$psi.RedirectStandardOutput = $true
$psi.RedirectStandardError  = $true
$psi.CreateNoWindow         = $true

$proc = New-Object System.Diagnostics.Process
$proc.StartInfo = $psi
$proc.add_OutputDataReceived({ param($s,$e); if ($e.Data) { Log "[BRIDGE] $($e.Data)" } })
$proc.add_ErrorDataReceived({  param($s,$e); if ($e.Data) { Log "[ERROR]  $($e.Data)" } })

$proc.Start()             | Out-Null
$proc.BeginOutputReadLine()
$proc.BeginErrorReadLine()
$proc.WaitForExit()

Log "Bridge exited (code $($proc.ExitCode)). Restoring WbioSrvc..."
Start-Service WbioSrvc -ErrorAction SilentlyContinue
Log "=============================="

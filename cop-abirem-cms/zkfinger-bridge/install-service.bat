@echo off
:: ============================================================
:: ZKFinger Bridge — One-Time Installer
:: Run this ONCE as Administrator on each computer that has
:: a fingerprint scanner. The bridge will then start
:: automatically at every Windows boot — no further action needed.
:: ============================================================

net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo  Administrator rights required.
    echo  Right-click this file and choose "Run as administrator".
    echo.
    pause
    exit /b 1
)

set TASK=ZKFingerBridge
set DIR=%~dp0
:: Remove trailing backslash
if "%DIR:~-1%"=="\" set DIR=%DIR:~0,-1%

echo.
echo  Installing ZKFinger Bridge auto-start...
echo  Location: %DIR%
echo.

:: Remove any old task silently
schtasks /delete /tn "%TASK%" /f >nul 2>&1

:: Create scheduled task via PowerShell
powershell -NonInteractive -ExecutionPolicy Bypass -Command ^
 "$a = New-ScheduledTaskAction -Execute 'powershell.exe' -Argument ('-NonInteractive -WindowStyle Hidden -ExecutionPolicy Bypass -File \"%DIR%\bridge-wrapper.ps1\"') -WorkingDirectory '%DIR%';" ^
 "$t = New-ScheduledTaskTrigger -AtStartup;" ^
 "$t.Delay = 'PT5S';" ^
 "$s = New-ScheduledTaskSettingsSet -ExecutionTimeLimit 0 -RestartCount 5 -RestartInterval (New-TimeSpan -Minutes 2) -StartWhenAvailable;" ^
 "$p = New-ScheduledTaskPrincipal -UserId 'SYSTEM' -LogonType ServiceAccount -RunLevel Highest;" ^
 "Register-ScheduledTask -TaskName 'ZKFingerBridge' -Action $a -Trigger $t -Settings $s -Principal $p -Description 'ZKFinger Fingerprint Bridge - COP Abirem CMS' -Force | Out-Null"

if %errorLevel% neq 0 (
    echo.
    echo  ERROR: Failed to create the scheduled task.
    echo  Make sure you are running as Administrator.
    echo.
    pause
    exit /b 1
)

echo  Task created. Starting bridge now...
echo.
schtasks /run /tn "%TASK%" >nul 2>&1

echo  ============================================================
echo   DONE! Setup complete.
echo.
echo   The ZKFinger Bridge will now start automatically every
echo   time this computer boots. No further action is needed.
echo.
echo   Log file: %DIR%\bridge.log
echo   To remove auto-start, run: uninstall-service.bat
echo  ============================================================
echo.
pause

@echo off
:: Re-launch as Administrator if not already elevated
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Requesting Administrator privileges...
    powershell -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"

echo Stopping Windows Biometric Service (releases device from Windows Hello)...
net stop WbioSrvc >nul 2>&1
echo Done.
echo.

echo Starting ZKFinger Bridge (Administrator)...
echo.
"bin\Release\net48\ZKFingerBridge.exe"
echo.

echo Restarting Windows Biometric Service...
net start WbioSrvc >nul 2>&1

echo === Bridge stopped. ===
pause

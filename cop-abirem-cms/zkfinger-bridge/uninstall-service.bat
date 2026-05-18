@echo off
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Administrator rights required. Right-click and choose "Run as administrator".
    pause & exit /b 1
)

echo Stopping ZKFingerBridge task...
schtasks /end /tn "ZKFingerBridge" >nul 2>&1

echo Removing scheduled task...
schtasks /delete /tn "ZKFingerBridge" /f
if %errorLevel% equ 0 (
    echo Task removed.
) else (
    echo Task was not found or already removed.
)

echo Restoring Windows Biometric Service...
net start WbioSrvc >nul 2>&1

echo.
echo Done. The bridge will no longer start automatically.
echo Use run-bridge.bat to start it manually when needed.
echo.
pause

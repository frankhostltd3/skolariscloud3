@echo off
REM Windows batch script to deploy to VPS using SSH
REM Usage: Run this from your local Windows machine

echo 🚀 Deploying SMATCAMPUS to VPS via SSH...
echo.

REM Set variables
set VPS_HOST=209.74.83.45
set VPS_PORT=22022
set VPS_USER=root
set VPS_PASSWORD=F!sh9T@ble
set PROJECT_DIR=/home/frankhost.us/public_html

echo 📡 Connecting to VPS and running deployment...

REM Upload the manual deploy script and run it
scp -P %VPS_PORT% -o StrictHostKeyChecking=no manual_deploy.sh %VPS_USER%@%VPS_HOST%:%PROJECT_DIR%/

REM Connect and run the deployment
ssh -p %VPS_PORT% -o StrictHostKeyChecking=no %VPS_USER%@%VPS_HOST% "cd %PROJECT_DIR% && chmod +x manual_deploy.sh && ./manual_deploy.sh"

echo.
echo ✅ Deployment completed!
echo 🌐 Check your site at: https://frankhost.us
pause
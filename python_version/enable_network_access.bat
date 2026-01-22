@echo off
echo ================================================
echo  Trust Mora Bank - Firewall Configuration
echo ================================================
echo.
echo This will allow other devices to access your
echo Trust Mora Bank app on port 5000.
echo.
echo Right-click this file and select "Run as administrator"
echo.
pause

netsh advfirewall firewall add rule name="Trust Mora Bank Flask Port 5000" dir=in action=allow protocol=TCP localport=5000

echo.
echo ================================================
echo  Firewall rule created successfully!
echo ================================================
echo.
echo Your Trust Mora Bank app can now be accessed from
echo other devices on your network at:
echo.
echo   http://YOUR_IP_ADDRESS:5000
echo.
echo To find your IP address, run: ipconfig
echo.
pause

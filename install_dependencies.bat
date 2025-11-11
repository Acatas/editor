@echo off
echo.
echo =======================================
echo   Installing Python Dependencies
echo =======================================
echo.
echo This will install the required packages
echo for the image processing scripts.
echo.
echo Make sure Python is installed first!
echo.
pause
echo.
echo Installing packages...
pip install -r requirements.txt
echo.
if %errorlevel% == 0 (
    echo ✅ Installation completed successfully!
    echo You can now run the Python scripts.
) else (
    echo ❌ Installation failed. 
    echo Make sure Python is installed and in your PATH.
)
echo.
pause
@echo off
REM Sync System Test Runner for Windows
REM This script helps test various sync scenarios and configurations

setlocal enabledelayedexpansion

REM Configuration
set PROJECT_DIR=%CD%
set SYNC_COMMAND=php artisan sync:auto
set LOG_FILE=storage\logs\sync-test.log

REM Create logs directory if it doesn't exist
if not exist "storage\logs" mkdir "storage\logs"

REM Create log file
echo === Sync Test Started: %date% %time% === > "%LOG_FILE%"

echo.
echo === Sync System Test Runner ===
echo Starting comprehensive sync system testing...
echo.

REM Environment Check
echo === Environment Check ===
if not exist "artisan" (
    echo ❌ Not in Laravel project directory. Please run this script from the project root.
    exit /b 1
)

if not exist "config\sync.php" (
    echo ⚠️  Sync configuration not found. Please publish the config first.
    echo Run: php artisan vendor:publish --tag=sync-config
) else (
    echo ✅ Sync configuration found
)

echo ✅ Environment check completed
echo.

REM Test basic sync functionality
echo === Testing Basic Sync Functionality ===
echo ℹ️  Testing full sync mode...
%SYNC_COMMAND% --mode=full --detailed >> "%LOG_FILE%" 2>&1
if %errorlevel% equ 0 (
    echo ✅ Full sync test completed
) else (
    echo ❌ Full sync test failed
)
echo.

REM Test individual sync modes
echo === Testing Individual Sync Modes ===
for %%m in (push-only pull-only queue-only) do (
    echo ℹ️  Testing %%m mode...
    %SYNC_COMMAND% --mode=%%m --detailed >> "%LOG_FILE%" 2>&1
    if !errorlevel! equ 0 (
        echo ✅ %%m mode test completed
    ) else (
        echo ⚠️  %%m mode test failed (this might be expected if service is unavailable)
    )
    echo.
)

REM Test health check functionality
echo === Testing Health Check Functionality ===
echo ℹ️  Running health check...
%SYNC_COMMAND% --health-check --mode=queue-only >> "%LOG_FILE%" 2>&1
if %errorlevel% equ 0 (
    echo ✅ Health check completed
) else (
    echo ⚠️  Health check failed (this might be expected if service is unavailable)
)
echo.

REM Test retry functionality
echo === Testing Retry Functionality ===
echo ℹ️  Testing retry failed items...
%SYNC_COMMAND% --retry-failed --mode=queue-only >> "%LOG_FILE%" 2>&1
if %errorlevel% equ 0 (
    echo ✅ Retry test completed
) else (
    echo ⚠️  Retry test failed (this might be expected if no failed items exist)
)
echo.

REM Test force sync
echo === Testing Force Sync ===
echo ℹ️  Testing force sync (ignoring offline status)...
%SYNC_COMMAND% --force --mode=queue-only >> "%LOG_FILE%" 2>&1
if %errorlevel% equ 0 (
    echo ✅ Force sync test completed
) else (
    echo ⚠️  Force sync test failed (this might be expected if service is unavailable)
)
echo.

REM Test with different batch sizes
echo === Testing Different Batch Sizes ===
for %%s in (25 50 100 200) do (
    echo ℹ️  Testing with batch size: %%s
    set SYNC_BATCH_SIZE=%%s
    %SYNC_COMMAND% --mode=queue-only --detailed >> "%LOG_FILE%" 2>&1
    if !errorlevel! equ 0 (
        echo ✅ Batch size %%s test completed
    ) else (
        echo ⚠️  Batch size %%s test failed
    )
    echo.
)

REM Test error handling
echo === Testing Error Handling ===
echo ℹ️  Testing with invalid mode...
%SYNC_COMMAND% --mode=invalid-mode >> "%LOG_FILE%" 2>&1
if %errorlevel% neq 0 (
    echo ✅ Error handling test passed (invalid mode rejected)
) else (
    echo ❌ Error handling test failed (invalid mode should have been rejected)
)
echo.

REM Run PHPUnit tests
echo === Running Unit Tests ===
if exist "vendor\bin\phpunit.bat" (
    echo ℹ️  Running AutoSync tests...
    vendor\bin\phpunit.bat --filter=AutoSyncTest >> "%LOG_FILE%" 2>&1
    if %errorlevel% equ 0 (
        echo ✅ Unit tests passed
    ) else (
        echo ❌ Unit tests failed
    )
) else (
    echo ⚠️  PHPUnit not found. Skipping unit tests.
    echo Install PHPUnit with: composer require --dev phpunit/phpunit
)
echo.

REM Performance testing
echo === Performance Testing ===
echo ℹ️  Testing sync performance with timing...
set start_time=%time%
%SYNC_COMMAND% --mode=queue-only --detailed >> "%LOG_FILE%" 2>&1
set exit_code=%errorlevel%
set end_time=%time%
echo ✅ Performance test completed
echo.

REM Generate test report
echo === Generating Test Report ===
set report_file=sync-test-report-%date:~-4,4%%date:~-10,2%%date:~-7,2%-%time:~0,2%%time:~3,2%%time:~6,2%.txt
set report_file=%report_file: =0%

(
echo Sync System Test Report
echo Generated: %date% %time%
echo ==================================
echo.
echo Test Log:
echo ---------
if exist "%LOG_FILE%" (
    powershell "Get-Content '%LOG_FILE%' | Select-Object -Last 50"
) else (
    echo No log file found
)
echo.
echo Configuration:
echo ---------------
if exist "config\sync.php" (
    echo Sync config exists
) else (
    echo Sync config missing
)
echo.
echo Environment Variables:
echo ----------------------
set | findstr /i sync
) > "%report_file%"

echo ✅ Test report generated: %report_file%
echo.

REM Test Summary
echo === Test Summary ===
echo ✅ All tests completed. Check the log file and report for details.
echo ℹ️  Log file: %LOG_FILE%
echo ℹ️  Report: %report_file%
echo.

pause

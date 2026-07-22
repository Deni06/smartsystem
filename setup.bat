@echo off
REM Script untuk quick start development environment (Windows)

echo ===================================
echo Smart Door AES - Quick Start Setup
echo ===================================
echo.

REM Check Docker installation
docker --version >nul 2>&1
if errorlevel 1 (
    echo Error: Docker tidak terinstall. Install dari https://docker.com
    pause
    exit /b 1
)

echo OK - Docker found

docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo Error: Docker Compose tidak terinstall
    pause
    exit /b 1
)

echo OK - Docker Compose found

REM Setup .env file
if not exist .env (
    echo.
    echo Creating .env file...
    copy .env.example .env
    echo OK - .env created. Edit sesuai kebutuhan Anda.
) else (
    echo OK - .env already exists
)

REM Create necessary directories
echo.
echo Creating directories...
if not exist database mkdir database
if not exist logs mkdir logs
if not exist logs\apache mkdir logs\apache
if not exist logs\php mkdir logs\php
if not exist logs\mysql mkdir logs\mysql
if not exist nginx\ssl mkdir nginx\ssl

echo.
echo Building Docker images...
docker-compose build --no-cache

echo.
echo Starting containers...
docker-compose up -d

echo.
echo Waiting for database to be ready...
timeout /t 10

REM Check status
echo.
echo Container status:
docker-compose ps

echo.
echo ===================================
echo OK - Setup selesai!
echo ===================================
echo.
echo Web Application:
echo   - Web App: http://localhost
echo   - PHPMyAdmin: http://localhost:8080
echo.
echo Database credentials (default):
echo   - Host: localhost:3306
echo   - User: smartdoor
echo   - Password: smartdoor123
echo   - Database: smartdoor_db
echo.
echo Useful commands:
echo   docker-compose logs -f        - View logs
echo   docker-compose ps             - Check status
echo   docker-compose stop           - Stop containers
echo   docker-compose down           - Remove containers
echo.
pause

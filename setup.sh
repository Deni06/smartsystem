#!/bin/bash

# Script untuk quick start development environment

set -e

echo "==================================="
echo "Smart Door AES - Quick Start Setup"
echo "==================================="
echo ""

# Check Docker installation
if ! command -v docker &> /dev/null; then
    echo "❌ Docker tidak terinstall. Install dari https://docker.com"
    exit 1
fi

echo "✅ Docker found: $(docker --version)"

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose tidak terinstall. Install dari https://docs.docker.com/compose/install/"
    exit 1
fi

echo "✅ Docker Compose found: $(docker-compose --version)"

# Setup .env file
if [ ! -f .env ]; then
    echo ""
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env created. Edit sesuai kebutuhan Anda."
else
    echo "✅ .env already exists."
fi

# Create necessary directories
echo ""
echo "📁 Creating directories..."
mkdir -p database logs/{apache,php,mysql} nginx/ssl

# Generate self-signed certificate (jika belum ada)
if [ ! -f nginx/ssl/cert.pem ]; then
    echo ""
    echo "🔐 Generating self-signed SSL certificate..."
    openssl genrsa -out nginx/ssl/key.pem 2048 2>/dev/null
    openssl req -new -x509 -key nginx/ssl/key.pem -out nginx/ssl/cert.pem -days 365 \
        -subj "/C=ID/ST=State/L=City/O=Organization/CN=smartdoor.local" 2>/dev/null
    echo "✅ Certificate generated"
else
    echo "✅ SSL certificate already exists"
fi

# Build images
echo ""
echo "🔨 Building Docker images..."
docker-compose build --no-cache

# Start containers
echo ""
echo "🚀 Starting containers..."
docker-compose up -d

# Wait for database
echo ""
echo "⏳ Waiting for database to be ready..."
sleep 10

# Check status
echo ""
echo "📊 Container status:"
docker-compose ps

echo ""
echo "==================================="
echo "✅ Setup selesai!"
echo "==================================="
echo ""
echo "🌐 Akses aplikasi:"
echo "   - Web App: http://localhost"
echo "   - PHPMyAdmin: http://localhost:8080"
echo ""
echo "📊 Database credentials (default):"
echo "   - Host: localhost:3306"
echo "   - User: smartdoor"
echo "   - Password: smartdoor123"
echo "   - Database: smartdoor_db"
echo ""
echo "📚 Useful commands:"
echo "   docker-compose logs -f        # View logs"
echo "   docker-compose ps             # Check status"
echo "   docker-compose stop           # Stop containers"
echo "   docker-compose down           # Remove containers"
echo ""
echo "📖 Dokumentasi: https://github.com/yourusername/smartdoor"
echo ""

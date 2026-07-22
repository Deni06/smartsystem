# FILE MANIFEST - Docker Deployment Scope

## Fokus

Daftar file penting untuk deployment berbasis Docker pada project ini.

## Docker Core

- Dockerfile
- docker-compose.yml
- docker-compose.prod.yml
- .dockerignore
- .env.example

## Konfigurasi

- config/koneksi.php
- config/php.ini
- config/my.cnf
- config/apache-overrides.conf

## Reverse Proxy dan SSL

- nginx/nginx.conf
- nginx/SSL_SETUP.md
- nginx/ssl/ (cert/key saat runtime)

## Script Setup

- setup.sh
- setup.bat

## Dokumentasi

- README.md
- QUICK_START.md
- DEPLOYMENT_GUIDE.md
- PRE_DEPLOYMENT_CHECKLIST.md

## Folder Runtime

- logs/
- database/

## Catatan

- Scope file manifest ini sengaja difokuskan ke kebutuhan Docker.
- Artefak deployment non-Docker sudah dikeluarkan dari alur utama.

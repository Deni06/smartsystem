# Smart Door AES - Quick Start Docker

## Ringkasan

File ini berisi panduan cepat untuk menjalankan project dengan Docker Compose.

## 1. Jalankan Cepat

### Linux/Mac
```bash
chmod +x setup.sh
./setup.sh
```

### Windows
```bat
setup.bat
```

## 2. Jalankan Manual

```bash
cp .env.example .env
docker compose up -d --build
docker compose ps
```

Akses aplikasi:

- Web: http://localhost
- PHPMyAdmin: http://localhost:8080

## 3. Struktur File Penting

```text
TA_fix/
|- Dockerfile
|- docker-compose.yml
|- docker-compose.prod.yml
|- .env.example
|- config/
|  |- koneksi.php
|  |- php.ini
|  `- my.cnf
|- nginx/
|  |- nginx.conf
|  `- SSL_SETUP.md
`- [source code aplikasi]
```

## 4. Command Harian

Start:
```bash
docker compose up -d
```

Stop:
```bash
docker compose down
```

Restart web:
```bash
docker compose restart web
```

Logs:
```bash
docker compose logs -f
docker compose logs -f web
docker compose logs -f db
```

## 5. Produksi

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

Pastikan SSL sudah siap di `nginx/ssl/`.

## 6. Troubleshooting Singkat

- Database gagal konek: cek `docker compose logs -f db`
- Port bentrok: ubah mapping di `docker-compose.yml`
- Permission: jalankan `chown/chmod` dari container web

## 7. Catatan

- Ubah credential default di `.env` sebelum production
- Jangan commit file `.env`
- Lakukan backup database rutin

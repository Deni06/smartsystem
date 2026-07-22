# Panduan Deployment Docker

Panduan ini fokus untuk deployment aplikasi Smart Door AES menggunakan Docker.

## 1. Prasyarat

- Docker sudah terpasang
- Docker Compose sudah terpasang
- Port 80 dan 3306 tidak dipakai service lain

Verifikasi:

```bash
docker --version
docker compose version
```

## 2. Persiapan Project

```bash
cd /path/to/TA_fix
cp .env.example .env
```

Edit `.env` sesuai kebutuhan, minimal:

```env
MYSQL_HOST=db
MYSQL_USER=smartdoor
MYSQL_PASSWORD=your_password
MYSQL_DATABASE=ta
MYSQL_ROOT_PASSWORD=your_root_password
```

## 3. Deploy Development (docker-compose.yml)

```bash
docker compose up -d --build
docker compose ps
```

Akses:

- Web: http://localhost
- PHPMyAdmin: http://localhost:8080

## 4. Deploy Production (docker-compose.prod.yml)

```bash
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml ps
```

Catatan:

- Pastikan sertifikat SSL tersedia di folder `nginx/ssl/`
- Sesuaikan `nginx/nginx.conf` dengan domain produksi

## 5. Operasi Harian

Start:

```bash
docker compose up -d
```

Stop:

```bash
docker compose down
```

Restart service web:

```bash
docker compose restart web
```

Lihat log:

```bash
docker compose logs -f
docker compose logs -f web
docker compose logs -f db
```

## 6. Health Check

```bash
curl -I http://localhost
docker compose exec db mysqladmin ping -h localhost
```

## 7. Backup dan Restore Database

Backup:

```bash
docker compose exec -T db mysqldump -u smartdoor -p ta > backup.sql
```

Restore:

```bash
docker compose exec -T db mysql -u smartdoor -p ta < backup.sql
```

## 8. Troubleshooting

Database belum ready:

```bash
docker compose logs -f db
docker compose restart db
```

Port bentrok:

- Ubah mapping port di `docker-compose.yml`
- Jalankan ulang container

Permission issue:

```bash
docker compose exec web chown -R www-data:www-data /var/www/html
docker compose exec web chmod -R 755 /var/www/html
```

## 9. Deploy dengan Docker Swarm (Opsional)

```bash
docker swarm init
docker stack deploy -c docker-compose.prod.yml smartdoor
docker stack services smartdoor
```

Gunakan opsi ini hanya jika butuh multi-host orchestration berbasis Docker.


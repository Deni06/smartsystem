# Database Setup untuk Docker

Panduan ini mengikuti contoh struktur terbaru pada db.sql.

## 1. Siapkan file SQL

Letakkan file db.sql ke folder database agar auto-import saat MySQL container pertama kali dibuat:

```bash
cp db.sql database/01_init_ta.sql
```

## 2. Pastikan nama database konsisten

Gunakan nilai berikut di .env:

```env
MYSQL_DATABASE=ta
MYSQL_USER=smartdoor
MYSQL_PASSWORD=smartdoor123
MYSQL_ROOT_PASSWORD=root123
```

## 3. Jalankan container

```bash
docker-compose up -d --build
```

## 4. Verifikasi import

```bash
docker-compose exec db mysql -u smartdoor -p ta -e "SHOW TABLES;"
```

Tabel yang diharapkan dari db.sql:
- board
- device
- device_logs
- device_type
- user
- user_access

## 5. Catatan penting

- Folder database dieksekusi otomatis hanya saat volume MySQL masih baru.
- Jika Anda mengubah db.sql setelah volume terbuat, lakukan re-init database.

## 6. Re-init database (jika perlu)

```bash
docker-compose down -v
docker-compose up -d --build
```

Perintah di atas akan menghapus volume database lama.

## 7. Backup dan restore

Backup:

```bash
docker-compose exec -T db mysqldump -u smartdoor -p ta > backup.sql
```

Restore:

```bash
docker-compose exec -T db mysql -u smartdoor -p ta < backup.sql
```

## 8. Troubleshooting singkat

Unknown database:
- Cek MYSQL_DATABASE di .env harus ta
- Cek koneksi aplikasi memakai env MYSQL_DATABASE

Tables tidak muncul:
- Pastikan db.sql ada di folder database sebelum container db pertama kali start
- Cek log: docker-compose logs -f db

Access denied:
- Sinkronkan MYSQL_USER, MYSQL_PASSWORD, MYSQL_ROOT_PASSWORD di .env

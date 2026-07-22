# Pre-Deployment Checklist (Docker)

## Application Files

- [ ] Source code sudah dibackup
- [ ] `.env` sudah dibuat dari `.env.example`
- [ ] `.env` tidak di-commit ke git
- [ ] Folder `logs/` tersedia
- [ ] Folder `database/` tersedia bila pakai init SQL

## Docker Configuration

- [ ] `Dockerfile` sudah direview
- [ ] `docker-compose.yml` sudah dites
- [ ] `docker-compose.prod.yml` sudah direview
- [ ] `.dockerignore` sudah benar
- [ ] Base image terbaru dan aman

## Database

- [ ] Credentials database di `.env` aman
- [ ] Password root bukan default
- [ ] Backup strategy sudah disiapkan
- [ ] Restore backup sudah diuji

## PHP & App Config

- [ ] `config/php.ini` sesuai kebutuhan
- [ ] Timezone sesuai lokasi
- [ ] Error logging aktif
- [ ] `config/koneksi.php` membaca environment variable

## Nginx / HTTPS

- [ ] `nginx/nginx.conf` sesuai domain
- [ ] Sertifikat SSL valid
- [ ] Redirect HTTP ke HTTPS aktif
- [ ] Security header aktif

## Security

- [ ] Tidak ada hardcoded secret di source code
- [ ] Input validation berjalan
- [ ] SQL query sensitif memakai prepared statement
- [ ] Mode debug dimatikan untuk production

## Monitoring & Logs

- [ ] `docker compose logs` tidak menunjukkan error kritis
- [ ] Health check endpoint bisa diakses
- [ ] Resource usage dimonitor (`docker stats`)

## Test Wajib

- [ ] Login/logout tested
- [ ] CRUD user/device tested
- [ ] Kontrol device via MQTT tested
- [ ] Import/export database tested

## Rollback Plan (Docker)

- [ ] Prosedur `docker compose down` dan re-deploy image lama siap
- [ ] Backup database siap restore
- [ ] Tag release sudah dibuat di git

## Success Criteria

- [ ] Semua container status `Up`
- [ ] Aplikasi dapat diakses normal
- [ ] Koneksi database stabil
- [ ] Tidak ada error kritis di log
- [ ] SSL berjalan normal di production

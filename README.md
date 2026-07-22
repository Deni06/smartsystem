# Smart Door AES - IoT Control Dashboard

## Deskripsi Aplikasi
**Smart System AES** adalah dasbor kendali berbasis web yang dirancang untuk memantau dan mengendalikan akses pintu pintar (*Smart Door*) secara nirkabel (*Internet of Things*)[cite: 5]. Sistem ini dibangun untuk menggantikan kunci konvensional, memungkinkan Administrator atau Pengguna dengan hak akses khusus untuk mengontrol aktuator *Solenoid Door Lock* dari jarak jauh melalui protokol MQTT[cite: 5]. 

Keunggulan utama aplikasi ini terletak pada penerapan keamanan kriptografi tingkat tinggi menggunakan arsitektur *End-to-End* (E2E)[cite: 5]. Seluruh *payload* data komunikasi diamankan menggunakan metode enkripsi **Advanced Encryption Standard (AES) 256-bit mode Galois/Counter Mode (GCM)** dan pra-autentikasi **HMAC-SHA256**[cite: 5]. Hal ini memastikan bahwa setiap perintah kontrol pintu kebal terhadap ancaman penyadapan (*eavesdropping*) maupun manipulasi data (*Man-in-the-Middle*) di jaringan nirkabel[cite: 5].

## Fitur Utama
*   **End-to-End Encryption:** Pengamanan ganda perintah kendali menggunakan dekripsi AES-256 GCM dan verifikasi integritas data via HMAC-SHA256 pada mikrokontroler[cite: 5].
*   **Dasbor Kendali Real-Time:** Sinkronisasi status pintu secara seketika menggunakan `paho-mqtt` *WebSocket*[cite: 5].
*   **Role-Based Access Control (RBAC):** Pembatasan akses pengguna berdasarkan matriks izin per-pintu yang diatur secara spesifik oleh Administrator[cite: 5].
*   **Dynamic Provisioning:** Administrator dapat mendaftarkan alat mikrokontroler (ESP32) baru atau mengubah alokasi pin GPIO secara dinamis dari dasbor tanpa perlu memprogram ulang (*re-flash*) perangkat keras[cite: 5].
*   **State Recovery:** Pemulihan otomatis posisi fisik aktuator pintu ke status terakhirnya pasca pemadaman listrik atau saat modul baru dihidupkan ulang[cite: 5].
*   **Closed-Loop Feedback:** Antarmuka web hanya akan mengubah status akhir pintu jika mendapatkan konfirmasi balikan (*callback*) dari perangkat keras fisik, sehingga memastikan akurasi data[cite: 5].

## Panduan Penggunaan Singkat

### 1. Mengakses Dasbor (Login)
*   Buka aplikasi melalui *browser* Anda[cite: 6].
*   Masukkan **Email** dan **Password** yang telah didaftarkan[cite: 6].
*   Klik **"Masuk"** untuk diarahkan langsung ke halaman *Dashboard*[cite: 6].

### 2. Mengontrol Pintu
*   Pada menu **Dashboard Kendali**, Anda akan melihat kartu-kartu perangkat (*Device Cards*) yang diizinkan untuk Anda kontrol[cite: 6].
*   Indikator **Merah** menandakan status **Mati/Terkunci**, sedangkan indikator **Hijau** menandakan status **Aktif/Terbuka**[cite: 6].
*   Klik tombol hijau **"BUKA"** untuk menarik tuas solenoid, atau tombol merah **"KUNCI"** untuk mengunci kembali pintu[cite: 6].

### 3. Manajemen User (Khusus Admin)
*   Pilih menu **"Manajemen User"** lalu klik tombol biru **"+ Tambah User"**[cite: 6].
*   Lengkapi form Nama Lengkap, Email, dan Password Akun[cite: 6].
*   Pada daftar centang di bagian bawah, **centang pintu/perangkat** mana saja yang boleh dikontrol oleh pengguna tersebut, lalu klik **"Simpan User"**[cite: 6].

### 4. Manajemen Perangkat / Dynamic Provisioning (Khusus Admin)
*   Pilih menu **"Manajemen Perangkat"** lalu klik **"+ Tambah Perangkat"**[cite: 6].
*   Pilih jenis perangkat, ketik nama dan lokasi, tentukan nomor PIN soket (GPIO) pada ESP32 yang digunakan, dan sesuaikan logika tegangan (*Active LOW/HIGH*)[cite: 6].
*   Setelah disimpan, **cabut dan pasang kembali daya ESP32** agar mikrokontroler mengunduh spesifikasi komponen yang baru disetel ini secara otomatis (*boot cycle*)[cite: 6].

### 5. Penanganan Darurat (SOP)
*   Jika aliran listrik atau koneksi WiFi terputus, instruksi nirkabel tidak akan berfungsi[cite: 6]. Sistem *Solenoid* akan kembali ke posisi bawaan[cite: 6]. Silakan gunakan kunci fisik konvensional untuk mengakses ruangan[cite: 6]. 
*   Saat daya atau internet kembali tersambung, perangkat otomatis menyesuaikan kembali posisinya sesuai memori dari *server* berkat fitur *State Recovery*[cite: 6].

---

## Deployment Guide

Panduan lengkap untuk deploy project Smart Door AES menggunakan Docker.

### Prasyarat

- Docker dan Docker Compose sudah terinstall
- Port 80, 443, 3306, dan 8080 tersedia

## Instalasi & Menjalankan Aplikasi

### 1. Clone/Download Project
```bash
cd /path/to/project
```

### 2. Setup Environment Variables
```bash
cp .env.example .env
```

Edit file `.env` sesuai kebutuhan Anda (database credentials, ports, dll)

### 3. Build dan Jalankan Container
```bash
docker-compose up -d --build
```

Tunggu hingga semua service berjalan (MySQL membutuhkan beberapa menit untuk startup pertama kali).

### 4. Verifikasi Service
```bash
docker-compose ps
```

Semua service harus dalam status "Up".

### 5. Import Database (jika ada)
Jika Anda memiliki backup database, letakkan file SQL di folder `database/` dan restart container:
```bash
docker-compose restart db
```

Atau gunakan PHPMyAdmin di http://localhost:8080

### 6. Akses Aplikasi
- **Web Application**: http://localhost
- **PHPMyAdmin**: http://localhost:8080
  - Username: `smartdoor` (default)
  - Password: `smartdoor123` (default)

---

## Struktur Folder

```
project/
├── Dockerfile              # Konfigurasi Docker
├── docker-compose.yml      # Konfigurasi services
├── .env.example            # Template environment variables
├── .dockerignore           # File yang diabaikan saat build
├── config/
│   ├── koneksi.php         # Koneksi DB (production)
│   └── php.ini             # Konfigurasi PHP
├── database/               # Folder untuk SQL files
└── [project files]
```

---

## Perintah Umum

### Start Container
```bash
docker-compose up -d
```

### Stop Container
```bash
docker-compose stop
```

### Restart Container
```bash
docker-compose restart
```

### View Logs
```bash
docker-compose logs -f web    # Logs web service
docker-compose logs -f db     # Logs database service
```

### Execute Command di Container
```bash
docker-compose exec web bash   # Akses shell web container
docker-compose exec db mysql -u smartdoor -p ta
```

### Remove Container & Volumes (HATI-HATI!)
```bash
docker-compose down -v
```

---

## Konfigurasi untuk Production

Untuk deploy di production server, lakukan:

### 1. Update koneksi.php
Ubah `config/koneksi.php` untuk menggunakan environment variables:

```php
$server   = getenv('MYSQL_HOST') ?: 'localhost';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';
$nama_db  = getenv('MYSQL_DATABASE') ?: 'ta';
```

### 2. Update .env
```bash
APP_ENV=production
APP_DEBUG=false
MYSQL_PASSWORD=your_secure_password
MYSQL_ROOT_PASSWORD=your_secure_root_password
```

### 3. Setup SSL/HTTPS (Nginx Reverse Proxy)
Buat file `nginx/Dockerfile`:

```dockerfile
FROM nginx:alpine
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/ssl/ /etc/nginx/ssl/
EXPOSE 80 443
CMD ["nginx", "-g", "daemon off;"]
```

### 4. Restart Services
```bash
docker-compose down
docker-compose up -d --build
```

---

## Troubleshooting

### Koneksi Database Gagal
1. Verifikasi MySQL sudah running: `docker-compose ps`
2. Check logs: `docker-compose logs db`
3. Pastikan credentials di `.env` benar

### Port Sudah Terpakai
Ubah port di `docker-compose.yml`:
```yaml
ports:
  - "8000:80"  # Port host:container
```

### File Upload Gagal
Tingkatkan limit di `config/php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
```

### Permissions Error
```bash
docker-compose exec web chown -R www-data:www-data /var/www/html
docker-compose exec web chmod -R 755 /var/www/html
```

---

## Support

Untuk pertanyaan atau issues, silakan hubungi developer atau check dokumentasi Docker di https://docs.docker.com


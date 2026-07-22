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

### Instalasi & Menjalankan Aplikasi

#### 1. Clone/Download Project
```bash
cd /path/to/project
# Database Schema

Dokumentasi ini merujuk ke contoh struktur pada db.sql.

## Database

- Nama database contoh: ta
- Engine: InnoDB
- Charset campuran (latin1 pada sebagian tabel, utf8mb4 pada tabel user) sesuai dump

## Daftar Tabel

### 1) user
Menyimpan akun pengguna aplikasi.

Kolom utama:
- id_user (PK, AI)
- email
- name
- password
- is_admin
- status
- created_at, updated_at
- created_by, updated_by

### 2) board
Menyimpan data board/gateway perangkat.

Kolom utama:
- id_board (PK, AI)
- board_uid (UNIQUE)
- board_name
- location
- status
- created_at

### 3) device
Menyimpan daftar perangkat yang dikontrol.

Kolom utama:
- id_device (PK, AI)
- device_name
- id_type
- id_board
- location
- pin_gpio
- active_state
- last_state
- status
- created_at, updated_at
- created_by, updated_by

### 4) device_type
Master jenis perangkat dan label aksi.

Kolom utama:
- id_type (PK, AI)
- type_name
- label_on
- label_off

### 5) device_logs
Menyimpan riwayat aksi kontrol perangkat.

Kolom utama:
- id_device_logs (PK, AI)
- id_user
- id_device
- action
- created_at

### 6) user_access
Hak akses user ke perangkat.

Kolom utama:
- id_user_access (PK, AI)
- id_user
- id_device

## Relasi Logis (tanpa FK eksplisit pada dump)

- user_access.id_user -> user.id_user
- user_access.id_device -> device.id_device
- device_logs.id_user -> user.id_user
- device_logs.id_device -> device.id_device
- device.id_type -> device_type.id_type
- device.id_board -> board.id_board

## Query Verifikasi Cepat

Lihat tabel:

```sql
SHOW TABLES;
```

Lihat struktur tabel device:

```sql
DESC device;
```

Cek data user aktif:

```sql
SELECT id_user, email, name, is_admin, status
FROM user
WHERE status = 1;
```

Cek perangkat + tipenya:

```sql
SELECT d.id_device, d.device_name, dt.type_name, d.location, d.status
FROM device d
LEFT JOIN device_type dt ON d.id_type = dt.id_type;
```

Cek akses user ke perangkat:

```sql
SELECT u.name, d.device_name
FROM user_access ua
JOIN user u ON ua.id_user = u.id_user
JOIN device d ON ua.id_device = d.id_device;
```

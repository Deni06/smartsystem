<?php
// Konfigurasi Database: pakai environment variable saat deploy Docker,
// fallback ke nilai dev lokal kalau env var tidak ada.
$server   = getenv('MYSQL_HOST') ?: 'localhost';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';
$nama_db  = getenv('MYSQL_DATABASE') ?: 'smartdoor_db';

$koneksi = mysqli_connect($server, $username, $password, $nama_db);

if (!$koneksi) {
	die("Koneksi Database Gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
?>
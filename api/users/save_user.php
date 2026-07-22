<?php
session_start();
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'message' => 'Terjadi kesalahan yang tidak diketahui.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $name     = $_POST['name'] ?? '';    
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $response['message'] = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Format email tidak valid.';
    } else {
        $check_sql = "SELECT id_user FROM user WHERE email = ?";
        $check_stmt = mysqli_prepare($koneksi, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $response['message'] = 'Email sudah terdaftar. Gunakan email lain.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO user (name, email, password, status, is_admin, created_at, created_by) 
                    VALUES (?, ?, ?, '1', '0', NOW(), ?)";
            $stmt = mysqli_prepare($koneksi, $sql);
            
            if ($stmt) {            
                $stmt->bind_param("sssi", $name, $email, $hashed_password, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $new_user_id = mysqli_insert_id($koneksi); 
                    
                    // RELASI KE TABEL PERANGKAT
                    if (isset($_POST['devices']) && is_array($_POST['devices'])) {
                        $sql_akses = "INSERT INTO user_access (id_user, id_device) VALUES (?, ?)";
                        $stmt_akses = mysqli_prepare($koneksi, $sql_akses);
                        foreach($_POST['devices'] as $device_id) {
                            mysqli_stmt_bind_param($stmt_akses, "ii", $new_user_id, $device_id);
                            mysqli_stmt_execute($stmt_akses);
                        }
                    }

                    $response['success'] = true;
                    $response['message'] = 'User dan hak akses perangkat berhasil ditambahkan.';
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    }
}
echo json_encode($response);
?>
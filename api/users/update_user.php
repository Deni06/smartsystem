<?php
session_start();
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'message' => 'Terjadi kesalahan yang tidak diketahui.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $id_user  = $_POST['id_user'] ?? '';
    $name     = $_POST['name'] ?? '';    
    $email    = $_POST['email'] ?? '';

    if (empty($id_user) || empty($name) || empty($email)) {
        $response['message'] = 'Semua field wajib diisi.';
    } else {
        $check_sql = "SELECT id_user FROM user WHERE email = ? AND id_user != ?";
        $check_stmt = mysqli_prepare($koneksi, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "si", $email, $id_user);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $response['message'] = 'Email sudah digunakan oleh user lain.';
        } else {
            $sql = "UPDATE user SET name = ?, email = ?, updated_at = NOW(), updated_by = ? WHERE id_user = ?";
            $stmt = mysqli_prepare($koneksi, $sql);
            
            if ($stmt) {            
                $stmt->bind_param("ssii", $name, $email, $_SESSION['user_id'], $id_user);
                
                if ($stmt->execute()) {
                    // Hapus hak akses perangkat lama, lalu insert yang baru
                    $sql_del = "DELETE FROM user_access WHERE id_user = ?";
                    $stmt_del = mysqli_prepare($koneksi, $sql_del);
                    mysqli_stmt_bind_param($stmt_del, "i", $id_user);
                    mysqli_stmt_execute($stmt_del);

                    if (isset($_POST['devices']) && is_array($_POST['devices'])) {
                        $sql_akses = "INSERT INTO user_access (id_user, id_device) VALUES (?, ?)";
                        $stmt_akses = mysqli_prepare($koneksi, $sql_akses);
                        foreach($_POST['devices'] as $device_id) {
                            mysqli_stmt_bind_param($stmt_akses, "ii", $id_user, $device_id);
                            mysqli_stmt_execute($stmt_akses);
                        }
                    }
                    $response['success'] = true;
                    $response['message'] = 'Data user dan hak akses perangkat berhasil diperbarui.';
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    }
}
echo json_encode($response);
?>
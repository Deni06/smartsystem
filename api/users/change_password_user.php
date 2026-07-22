<?php
session_start();

header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan yang tidak diketahui.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $id_user  = $_POST['id_user'] ?? '';    
    $password = $_POST['password'] ?? '';

    if (empty($id_user) || empty($password)) {
        $response['message'] = 'ID User dan Password wajib diisi.';
    } elseif (strlen($password) < 8) {
        // Tambahan keamanan: pastikan panjang password minimal 8 karakter
        $response['message'] = 'Password harus memiliki minimal 8 karakter.';
    } else {
        // Hash password baru menggunakan Bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Query Update Password
        $sql = "UPDATE user SET password = ?, updated_at = NOW(), updated_by = ? WHERE id_user = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {            
            $stmt->bind_param("sii", $hashed_password, $_SESSION['user_id'], $id_user);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Password berhasil diperbarui.';
            } else {
                $response['message'] = 'Gagal menyimpan ke database: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal menyiapkan query: ' . $koneksi->error;
        }
    }
} else {
    $response['message'] = 'Metode request tidak diizinkan.';
}

echo json_encode($response);
?>
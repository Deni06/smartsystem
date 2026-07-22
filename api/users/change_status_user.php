<?php
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'message' => 'Gagal memperbarui status.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menggunakan 'id_user' sesuai dengan JavaScript FormData yang kita buat sebelumnya
    $id = $_POST['id_user'] ?? $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id !== null && $status !== null) {
        $sql = "UPDATE user SET status = ? WHERE id_user = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {            
            $stmt->bind_param("si", $status, $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $statusText = ($status == '1') ? 'diaktifkan' : 'dinonaktifkan';
                $response['message'] = "Akun user berhasil $statusText.";
            } else {
                $response['message'] = 'Gagal update: ' . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $response['message'] = 'ID atau Status tidak ditemukan.';
    }
} else {
    $response['message'] = 'Gunakan metode POST.';
}

echo json_encode($response);
?>
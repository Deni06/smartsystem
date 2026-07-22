<?php
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'message' => 'Gagal memperbarui status.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id !== null && $status !== null) {
        $sql = "UPDATE device SET status = ? WHERE id_device = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        if ($stmt) {            
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                $response['success'] = true;
                $statusText = ($status == '1') ? 'diaktifkan' : 'dinonaktifkan';
                $response['message'] = "Perangkat berhasil $statusText.";
            }
            $stmt->close();
        }
    }
}
echo json_encode($response);
?>
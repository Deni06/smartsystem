<?php
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'message' => 'Gagal sinkronisasi data aktual.'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_device = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $state     = isset($_GET['state']) ? (int)$_GET['state'] : null;

    if ($id_device !== null && $state !== null) {
        // Menggunakan trigger dari hardware untuk memperbarui status terakhir di Cloud
        $sql = "UPDATE device SET last_state = ?, updated_at = NOW() WHERE id_device = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $state, $id_device);
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = 'Status fisik berhasil disinkronkan ke Database.';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $response['message'] = 'Parameter ID atau State tidak disertakan.';
    }
} else {
    $response['message'] = 'Gunakan metode GET.';
}

echo json_encode($response);
?>
<?php
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'data' => null, 'message' => 'Data tidak ditemukan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_user'] ?? $_POST['id'] ?? null;
    if ($id) {        
        $sql = "SELECT id_user, name, email, status FROM user WHERE id_user = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $userData = $result->fetch_assoc();

            if ($userData) {
                // AMBIL DAFTAR PERANGKAT YANG BISA DIAKSES
                $userData['devices'] = [];
                $sql_akses = "SELECT id_device FROM user_access WHERE id_user = ?";
                $stmt_akses = mysqli_prepare($koneksi, $sql_akses);
                mysqli_stmt_bind_param($stmt_akses, "i", $id);
                mysqli_stmt_execute($stmt_akses);
                $res_akses = mysqli_stmt_get_result($stmt_akses);
                while($row = mysqli_fetch_assoc($res_akses)) {
                    $userData['devices'][] = $row['id_device'];
                }

                $response['success'] = true;
                $response['data'] = $userData;
            }
            $stmt->close();
        }
    }
}
echo json_encode($response);
?>
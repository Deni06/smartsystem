<?php
session_start();
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $id_type      = $_POST['id_type'] ?? '';
    $id_board     = $_POST['id_board'] ?? '';
    $device_name  = $_POST['device_name'] ?? '';    
    $location     = $_POST['location'] ?? '';
    $pin_gpio     = $_POST['pin_gpio'] ?? '0';
    $active_state = $_POST['active_state'] ?? '0';

    if (empty($id_type) || empty($id_board) || empty($device_name) || empty($location)) {
        $response['message'] = 'Semua field wajib diisi.';
    } else {        
        $sql = "INSERT INTO device (id_type, id_board, device_name, location, pin_gpio, active_state, status, created_at, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, '1', NOW(), ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {            
            $stmt->bind_param("iissiii", $id_type, $id_board, $device_name, $location, $pin_gpio, $active_state, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Perangkat berhasil ditambahkan.';
            } else {
                $response['message'] = 'Gagal menyimpan: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}
echo json_encode($response);
?>
<?php
header('Content-Type: application/json');
require_once('../../config/koneksi.php');

$response = ['success' => false, 'data' => null, 'message' => 'Data tidak ditemukan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if ($id) {        
        $sql = "SELECT id_device, id_type, id_board, device_name, location, pin_gpio, active_state, status FROM device WHERE id_device = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $deviceData = $result->fetch_assoc();

            if ($deviceData) {
                $response['success'] = true;
                $response['data'] = $deviceData;
            }
            $stmt->close();
        }
    }
}
echo json_encode($response);
?>
<?php
header('Content-Type: application/json');

// Pastikan path ke file koneksi disesuaikan dengan struktur folder Anda
require_once('../../config/koneksi.php');

$response = ['success' => false, 'devices' => []];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Menangkap parameter board_uid yang dikirim oleh ESP32
    $board_uid = isset($_GET['board_uid']) ? mysqli_real_escape_string($koneksi, $_GET['board_uid']) : '';

    if (!empty($board_uid)) {
        // Melakukan JOIN tabel device dan board untuk mengambil konfigurasi
        // hanya untuk perangkat yang statusnya aktif ('1') pada board tersebut
        $sql = "SELECT d.id_device, d.pin_gpio, d.active_state, d.last_state 
                FROM device d
                JOIN board b ON d.id_board = b.id_board
                WHERE b.board_uid = ? AND d.status = '1'";
                
        $stmt = mysqli_prepare($koneksi, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $board_uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            // Memasukkan setiap baris data ke dalam array JSON
            while ($row = mysqli_fetch_assoc($result)) {
                $response['devices'][] = [
                    'id'         => (int)$row['id_device'],
                    'pin'        => (int)$row['pin_gpio'],
                    'state'      => (int)$row['active_state'],
                    'last_state' => (int)$row['last_state'] // Digunakan ESP32 untuk State Recovery
                ];
            }
            
            $response['success'] = true;
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Terjadi kesalahan saat menyiapkan query database.';
        }
    } else {
        $response['message'] = 'Parameter board_uid wajib disertakan.';
    }
} else {
    $response['message'] = 'Metode request tidak diizinkan. Gunakan metode GET.';
}

echo json_encode($response);
?>
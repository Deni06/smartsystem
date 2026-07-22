<?php
$startTime = microtime(true);

header('Content-Type: application/json');
session_start();

require_once('../../config/koneksi.php');
require_once('../../libs/phpMQTT.php');

$response = ['success' => false, 'message' => 'Terjadi kesalahan sistem.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_device = isset($_POST['id_device']) ? (int)$_POST['id_device'] : null;
    $action    = isset($_POST['action']) ? (int)$_POST['action'] : null; 
    $user_id   = $_SESSION['user_id'] ?? 0;

    if ($id_device && $action !== null && $user_id > 0) {
        
        $sql_label = "SELECT dt.label_on, dt.label_off FROM device d 
                      JOIN device_type dt ON d.id_type = dt.id_type 
                      WHERE d.id_device = ?";
        $stmt_label = mysqli_prepare($koneksi, $sql_label);
        mysqli_stmt_bind_param($stmt_label, "i", $id_device);
        mysqli_stmt_execute($stmt_label);
        $res_label = mysqli_stmt_get_result($stmt_label);
        $row_label = mysqli_fetch_assoc($res_label);
        
        $action_text = ($action === 1) ? ($row_label['label_on'] ?? 'AKTIFKAN') : ($row_label['label_off'] ?? 'MATIKAN');

        // =======================================================
        // PERUBAHAN: FORMAT ACTION MENJADI STRING
        // =======================================================
        $action_command = ($action === 1) ? "CHANGE_STATUS_1" : "CHANGE_STATUS_0";

        $payloadData = json_encode([
            'id_device' => $id_device,
            'action'    => $action_command, // Mengirim teks, bukan sekadar angka 1 atau 0
            'timestamp' => time() 
        ]);

        $encryption_key = "4fE9xR2wL7pM1nQ8vB5zY3tS6hG0kC4j"; 
        $signature_key  = "9zY3tS6hG0kC4j4fE9xR2wL7pM1nQ8vB"; 
        
        $nonce = openssl_random_pseudo_bytes(12); 
        $tag = ""; 

        $ciphertext = openssl_encrypt(
            $payloadData, 
            "aes-256-gcm", 
            $encryption_key, 
            OPENSSL_RAW_DATA, 
            $nonce, 
            $tag
        );

        $nonce_b64 = base64_encode($nonce);
        $ct_b64    = base64_encode($ciphertext);
        $tag_b64   = base64_encode($tag);

        $data_to_sign = $nonce_b64 . "." . $ct_b64 . "." . $tag_b64;
        $signature = hash_hmac('sha256', $data_to_sign, $signature_key);

        $mqttMessage = json_encode([
            'nonce'      => $nonce_b64,
            'ciphertext' => $ct_b64,
            'tag'        => $tag_b64,
            'signature'  => $signature
        ]);        

        $server     = getenv('MQTT_HOST') ?: 'localhost';
        $port       = (int)(getenv('MQTT_PORT') ?: 1883);
        $username   = getenv('MQTT_USERNAME') ?: 'admin_pintu';
        $password   = getenv('MQTT_PASSWORD') ?: '12345678';
        $client_id  = 'backend_ta_' . uniqid();

        $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);        
        
        // Coba koneksi ke MQTT dan Publish
        if ($mqtt->connect(true, NULL, $username, $password)) {
            $mqtt->publish("smartsystem/control", $mqttMessage, 0); 
            $mqtt->close();

            $sql_log = "INSERT INTO device_logs (id_device, id_user, action, created_at) VALUES (?, ?, ?, NOW())";
            $stmt_log = mysqli_prepare($koneksi, $sql_log);
            if ($stmt_log) {
                mysqli_stmt_bind_param($stmt_log, "iis", $id_device, $user_id, $action_text);
                mysqli_stmt_execute($stmt_log);
                mysqli_stmt_close($stmt_log);
            }
            
            // --- PENGUKURAN WAKTU PHP ---
            $endTime = microtime(true);
            $php_execution_time_ms = round(($endTime - $startTime) * 1000, 2); // Konversi ke milidetik

            $response['success'] = true;
            $response['message'] = "Perintah '$action_text' berhasil dikirim.";
            $response['latency_php_ms'] = $php_execution_time_ms; // Tampilkan di response Network Inspect
            
        } else {
            $response['message'] = "Gagal terhubung ke MQTT Broker.";
        }
    } else {
        $response['message'] = 'Parameter tidak lengkap atau sesi telah berakhir.';
    }
} else {
    $response['message'] = 'Metode request tidak diizinkan.';
}

echo json_encode($response);
?>
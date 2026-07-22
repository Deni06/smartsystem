<?php 
include('includes/header.php'); 
include('includes/sidebar.php'); 
require_once('config/koneksi.php'); 

// Konfigurasi MQTT WebSocket untuk dashboard (browser).
// Default: domain saat ini + path /mqtt.
$mqtt_ws_host    = getenv('MQTT_WS_HOST') ?: $_SERVER['HTTP_HOST'];
$mqtt_ws_port    = (int)(getenv('MQTT_WS_PORT') ?: 443);
$mqtt_ws_path    = getenv('MQTT_WS_PATH') ?: '/mqtt';
$mqtt_ws_use_ssl = getenv('MQTT_WS_USE_SSL') !== '0';
$mqtt_username   = getenv('MQTT_USERNAME') ?: 'admin_pintu';
$mqtt_password   = getenv('MQTT_PASSWORD') ?: '12345678';

$devices = [];

// Logika pengambilan data alat beserta tipe dan label dinamisnya
if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    $sql_devices = "SELECT d.*, dt.label_on, dt.label_off, dt.type_name FROM device d 
                    LEFT JOIN device_type dt ON d.id_type = dt.id_type 
                    WHERE d.status = '1'";
} else {
    $sql_devices = "SELECT d.*, dt.label_on, dt.label_off, dt.type_name FROM device d 
                    INNER JOIN user_access ua ON d.id_device = ua.id_device AND ua.id_user = ? 
                    LEFT JOIN device_type dt ON d.id_type = dt.id_type 
                    WHERE d.status = '1'";
}

$stmt_devices = mysqli_prepare($koneksi, $sql_devices);
if ($stmt_devices) {
    if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        mysqli_stmt_bind_param($stmt_devices, "i", $_SESSION['user_id']);
    }    

    mysqli_stmt_execute($stmt_devices);
    $res_devices = mysqli_stmt_get_result($stmt_devices);
    $devices = mysqli_fetch_all($res_devices, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_devices);
}
?>

<style>
    .device-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        border: 1px solid rgba(0,0,0,0.08) !important;
        border-radius: 15px;
    }
    .device-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .status-dot {
        width: 12px; height: 12px; border-radius: 50%; display: inline-block;
    }
</style>

<div class="main-content">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold m-0">Dashboard Kendali</h3>
            <p class="text-muted small mb-0">Pantau dan kendalikan perangkat cerdas Anda secara real-time.</p>
        </div>
        <div id="mqtt-connection-status" class="badge bg-secondary text-white py-2 px-3 shadow-sm rounded-pill">
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menghubungkan...
        </div>
    </div>

    <div class="row g-4">
        <?php if (count($devices) > 0): ?>
            <?php foreach ($devices as $device): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card device-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($device['device_name']) ?></h5>
                                <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($device['location']) ?></p>
                            </div>
                            <div class="p-2 bg-light rounded-circle text-primary">
                                <i class="bi bi-cpu fs-4"></i>
                            </div>
                        </div>

                        <div class="status-box rounded-3 p-3 mb-4 d-flex align-items-center gap-3" 
                             id="status-box-<?= $device['id_device'] ?>" 
                             style="background-color: rgba(108, 117, 125, 0.1); border: 1px solid rgba(108, 117, 125, 0.2);">
                            <div class="status-dot bg-secondary mt-1 flex-shrink-0" id="status-dot-<?= $device['id_device'] ?>"></div>
                            <div>
                                <p class="text-muted small mb-0 fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Status <?= htmlspecialchars($device['type_name'] ?? 'Perangkat') ?></p>
                                <h6 class="text-secondary fw-bold m-0 tracking-wide" id="status-text-<?= $device['id_device'] ?>">MEMUAT...</h6>
                            </div>
                        </div>

                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-outline-success w-50 fw-bold py-2 rounded-3" 
                                    onclick="sendDeviceCommand(<?= $device['id_device'] ?>, 1, '<?= $device['label_on'] ?? 'AKTIFKAN' ?>')">
                                <i class="bi bi-power me-1"></i> <?= htmlspecialchars($device['label_on'] ?? 'AKTIFKAN') ?>
                            </button>
                            <button class="btn btn-danger w-50 fw-bold py-2 rounded-3 shadow-sm" 
                                    onclick="sendDeviceCommand(<?= $device['id_device'] ?>, 0, '<?= $device['label_off'] ?? 'MATIKAN' ?>')">
                                <i class="bi bi-power me-1"></i> <?= htmlspecialchars($device['label_off'] ?? 'MATIKAN') ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h5 class="text-muted fw-bold">Tidak Ada Perangkat</h5>
                <p class="text-muted small">Anda belum memiliki akses ke perangkat apapun atau perangkat sedang dinonaktifkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.min.js" type="text/javascript"></script>
<script>
    const mqttServer = "<?= htmlspecialchars($mqtt_ws_host, ENT_QUOTES) ?>";
    const mqttPort = <?= (int)$mqtt_ws_port ?>;
    const mqttPath = "<?= htmlspecialchars($mqtt_ws_path, ENT_QUOTES) ?>";
    const mqttUseSSL = <?= $mqtt_ws_use_ssl ? 'true' : 'false' ?>;
    const mqttClientId = "dashboard_web_" + Math.random().toString(16).substr(2, 8);
    const connectionIndicator = document.getElementById('mqtt-connection-status');

    const mqttClient = new Paho.MQTT.Client(mqttServer, mqttPort, mqttPath, mqttClientId);

    mqttClient.onConnectionLost = function (responseObject) {
        if (responseObject.errorCode !== 0) {
            console.error("MQTT Terputus: " + responseObject.errorMessage);
            connectionIndicator.className = "badge bg-danger text-white py-2 px-3 shadow-sm rounded-pill";
            connectionIndicator.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Terputus. Menghubungkan ulang...';
            setTimeout(connectMQTT, 5000);
        }
    };

    mqttClient.onMessageArrived = function (message) {
        const topic = message.destinationName;
        const payload = message.payloadString;
        
        if (topic.startsWith("smartsystem/status/")) {
            const deviceId = topic.split("/")[2]; 
            const statusText = payload;

            const textElement = document.getElementById('status-text-' + deviceId);
            const boxElement = document.getElementById('status-box-' + deviceId);
            const dotElement = document.getElementById('status-dot-' + deviceId);

            if (!textElement || !boxElement || !dotElement) return;

            if (statusText === "1") {
                // Teks status diubah menjadi universal
                textElement.innerText = "AKTIF / ON";
                textElement.className = "text-success fw-bold m-0 tracking-wide";
                boxElement.style.backgroundColor = "rgba(25, 135, 84, 0.1)"; 
                boxElement.style.borderColor = "rgba(25, 135, 84, 0.2)";
                dotElement.className = "status-dot bg-success mt-1 flex-shrink-0";
                dotElement.style.boxShadow = "0 0 8px rgba(25,135,84,0.6)";
            } else if (statusText === "0") {
                // Teks status diubah menjadi universal
                textElement.innerText = "MATI / OFF";
                textElement.className = "text-danger fw-bold m-0 tracking-wide";
                boxElement.style.backgroundColor = "rgba(220, 53, 69, 0.1)"; 
                boxElement.style.borderColor = "rgba(220, 53, 69, 0.2)";
                dotElement.className = "status-dot bg-danger mt-1 flex-shrink-0";
                dotElement.style.boxShadow = "0 0 8px rgba(220,53,69,0.6)";
            }
        }
    };

    function connectMQTT() {
        mqttClient.connect({
            useSSL: mqttUseSSL,
            userName: "<?= htmlspecialchars($mqtt_username, ENT_QUOTES) ?>",
            password: "<?= htmlspecialchars($mqtt_password, ENT_QUOTES) ?>",
            onSuccess: function () {
                console.log("MQTT WebSocket Terhubung!");
                connectionIndicator.className = "badge bg-success text-white py-2 px-3 shadow-sm rounded-pill";
                connectionIndicator.innerHTML = '<i class="bi bi-wifi me-1"></i> Terhubung (Real-time)';
                
                // Subscribe ke status perangkat secara universal
                mqttClient.subscribe("smartsystem/status/+");
            },
            onFailure: function (e) {
                console.error("Gagal terhubung ke MQTT:", e);
                connectionIndicator.className = "badge bg-danger text-white py-2 px-3 shadow-sm rounded-pill";
                connectionIndicator.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Koneksi Gagal';
                setTimeout(connectMQTT, 5000);
            }
        });
    }

    connectMQTT();

    function sendDeviceCommand(deviceId, actionValue, labelAction) {
        const confirmColor = (actionValue === 1) ? '#198754' : '#dc3545'; 

        Swal.fire({
            title: 'Konfirmasi Tindakan',
            text: `Apakah Anda yakin ingin ${labelAction} perangkat ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${labelAction}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: `Sedang mengirim instruksi ${labelAction}...`,
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const formData = new FormData();
                formData.append('id_device', deviceId);
                formData.append('action', actionValue);

                // Fetch diarahkan ke endpoint control_device yang baru
                fetch('api/devices/control_device', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                });
            }
        });
    }
</script>

<?php include('includes/footer.php'); ?>
<?php
require_once('config/koneksi.php');
$res_type = mysqli_query($koneksi, "SELECT * FROM device_type");
$res_board = mysqli_query($koneksi, "SELECT id_board, board_name, board_uid FROM board WHERE status = '1'");
?>
<div class="modal-header border-0">
    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Perangkat Baru</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formDevice" onsubmit="return submitDeviceForm();">
    <div class="modal-body">
        <input type="hidden" name="id" id="id_device">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Jenis Perangkat</label>
                <select name="id_type" id="id_type" class="form-select" required>
                    <option value="">-- Pilih Jenis --</option>
                    <?php while($type = mysqli_fetch_assoc($res_type)): ?>
                        <option value="<?= $type['id_type'] ?>"><?= $type['type_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Modul Node ESP32</label>
                <select name="id_board" id="id_board" class="form-select" required>
                    <option value="">-- Pilih Gateway --</option>
                    <?php while($board = mysqli_fetch_assoc($res_board)): ?>
                        <option value="<?= $board['id_board'] ?>"><?= htmlspecialchars($board['board_name']) ?> (<?= $board['board_uid'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nama Perangkat</label>
            <input type="text" class="form-control" name="device_name" id="device_name" placeholder="Contoh: Lampu Utama Ruang Lab" required>
        </div>        
        <div class="mb-3">
            <label class="form-label fw-bold">Lokasi Komponen</label>
            <input type="text" class="form-control" name="location" id="location" placeholder="Contoh: Ruang Aula Lantai 1" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Alokasi PIN (GPIO)</label>
                <input type="number" class="form-control" name="pin_gpio" id="pin_gpio" placeholder="Misal: 14" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Logika Tegangan Relay</label>
                <select name="active_state" id="active_state" class="form-select" required>
                    <option value="0">Active LOW (GND Triggered)</option>
                    <option value="1">Active HIGH (VCC Triggered)</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button id="btnSubmitDevice" class="btn btn-primary px-4">
            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="submitText">Simpan Perangkat</span>
        </button>
    </div>
</form>

<script>
function submitDeviceForm() {
    const form = document.getElementById('formDevice');
    const btn = document.getElementById('btnSubmitDevice');
    const spinner = document.getElementById('submitSpinner');
    const btnText = document.getElementById('submitText');
    
    const deviceId = document.getElementById('id_device').value;
    const targetApi = deviceId ? 'api/devices/update_device' : 'api/devices/save_device';

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.innerText = ' Menyimpan...';
    
    fetch(targetApi, { method: 'POST', body: new FormData(form) })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('dynamicModal')).hide();
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
        } else {
            Swal.fire('Gagal!', data.message, 'error');
            btn.disabled = false;
            spinner.classList.add('d-none');
            btnText.innerText = deviceId ? 'Update Perangkat' : 'Simpan Perangkat';
        }
    }).catch(() => {
        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
    return false;
}
</script>
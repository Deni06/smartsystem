<?php
require_once('config/koneksi.php');
$res_device = mysqli_query($koneksi, "SELECT id_device, device_name, location FROM device WHERE status = '1'");
$devices = $res_device ? mysqli_fetch_all($res_device, MYSQLI_ASSOC) : [];
?>
<style>
    .door-access-card { cursor: pointer; transition: all 0.2s; border: 1px solid #dee2e6; background-color: #fff; }
    .door-access-card:hover { background-color: #f8f9fa; border-color: #babbbc; }
</style>
<div class="modal-header border-0">
    <h5 class="modal-title fw-bold" id="modalTitle">Tambah User Baru</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formUser" onsubmit="return submitUserForm();">
    <div class="modal-body">
        <input type="hidden" name="id_user" id="id_user">
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Lengkap</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>        
        <div class="mb-3">
            <label class="form-label fw-bold">Email Access</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="mb-3" id="passwordGroup">
            <label class="form-label fw-bold">Password Akun</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Minimal 8 karakter">
        </div>
        <div class="mb-3" id="confirmPasswordGroup">
            <label class="form-label fw-bold">Konfirmasi Ulang Password</label>
            <input type="password" class="form-control" id="confirm_password" placeholder="Ulangi password">
            <div class="invalid-feedback">Kombinasi input password tidak cocok!</div>
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold">Hak Akses Kontrol Perangkat</label>
            <div class="row g-2 max-h-200 overflow-y-auto">
                <?php foreach ($devices as $dev): ?>
                <div class="col-12 col-md-6">
                    <label class="door-access-card d-block p-2 rounded-3 h-100">
                        <div class="form-check m-0 d-flex align-items-center">
                            <input class="form-check-input me-2 mt-0" type="checkbox" name="devices[]" value="<?= $dev['id_device'] ?>" id="device_<?= $dev['id_device'] ?>">
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($dev['device_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($dev['location']) ?></div>
                            </div>
                        </div>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button id="btnSubmitUser" class="btn btn-primary px-4">
            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="submitText">Simpan User</span>
        </button>
    </div>
</form>

<script>
function submitUserForm() {
    const form = document.getElementById('formUser');
    const btn = document.getElementById('btnSubmitUser');
    const spinner = document.getElementById('submitSpinner');
    const btnText = document.getElementById('submitText');
    
    const userId = document.getElementById('id_user').value;
    const targetApi = userId ? 'api/users/update_user' : 'api/users/save_user';

    if (!userId) {
        if (document.getElementById('password').value !== document.getElementById('confirm_password').value) {
            document.getElementById('confirm_password').classList.add('is-invalid');
            return false;
        }
    }

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.innerText = ' Menyimpan...';
    
    fetch(targetApi, { method: 'POST', body: new FormData(form) })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('dynamicModal')).hide();
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
        } else {
            Swal.fire('Gagal!', data.message, 'error');
            btn.disabled = false;
            spinner.classList.add('d-none');
            btnText.innerText = userId ? 'Update User' : 'Simpan User';
        }
    });
    return false;
}
</script>
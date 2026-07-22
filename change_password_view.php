<?php session_start(); ?>
<div class="modal-header border-0">
    <h5 class="modal-title fw-bold">Ganti Password</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="formChangePassword" onsubmit="return submitPasswordForm();">
    <div class="modal-body">
        <p class="text-muted small">Mengganti password untuk akun: <strong id="displayUserName"></strong></p>
        <input type="hidden" name="id_user" id="id_user_password">

        <div class="mb-3">
            <label class="form-label fw-bold">Password Baru</label>
            <input type="password" class="form-control" name="password" id="new_password" placeholder="Minimal 8 karakter" required oninput="this.value = this.value.replace(/\s/g, '')">
        </div>        
        <div class="mb-3">
            <label class="form-label fw-bold">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="confirm_password" placeholder="Ulangi password baru" required oninput="this.value = this.value.replace(/\s/g, '')">
            <div class="invalid-feedback">Password tidak cocok!</div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button id="btnSubmitPassword" class="btn btn-warning px-4 fw-bold">
            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="submitText">Simpan Password</span>
        </button>
    </div>
</form>

<script>
function submitPasswordForm() {
    const form = document.getElementById('formChangePassword');
    const btn = document.getElementById('btnSubmitPassword');
    const spinner = document.getElementById('submitSpinner');
    
    if (document.getElementById('new_password').value !== document.getElementById('confirm_password').value) {
        document.getElementById('confirm_password').classList.add('is-invalid');
        return false;
    }

    btn.disabled = true;
    spinner.classList.remove('d-none');
    
    fetch('api/users/change_password_user', { method: 'POST', body: new FormData(form) })
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
        }
    });
    return false;
}
</script>
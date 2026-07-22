<?php 
require_once('config/koneksi.php'); 
include('includes/header.php'); 
include('includes/sidebar.php'); 

$sql = "SELECT * FROM user WHERE is_admin = 0 ORDER BY id_user DESC";
$result = mysqli_query($koneksi, $sql);
$users = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<div class="main-content">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold m-0">Manajemen User</h3>
            <p class="text-muted small mb-0">Kelola daftar pengguna dan konfigurasi hak akses kontrol perangkat.</p>
        </div>
        <button class="btn btn-primary px-4 shadow-sm text-nowrap" onclick="loadAddModal()">
            <i class="bi bi-person-plus-fill me-2"></i>Tambah User
        </button>
    </div>

    <div class="card card-custom overflow-hidden shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Status Akun</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($users) > 0): ?>
                        <?php foreach ($users as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#<?= $row['id_user'] ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <?php if($row['status'] == '1'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" onclick="editUser(<?= $row['id_user'] ?>)" title="Edit Akses">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning rounded-circle shadow-sm ms-1" onclick="changePassword(<?= $row['id_user'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')" title="Ganti Password">
                                    <i class="bi bi-key"></i>
                                </button>
                                <?php if($row['status'] == '1'): ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm ms-1" onclick="changeStatus(<?= $row['id_user'] ?>, 0, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')" title="Non-Aktifkan">
                                        <i class="bi bi-person-x"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success rounded-circle shadow-sm ms-1" onclick="changeStatus(<?= $row['id_user'] ?>, 1, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')" title="Aktifkan">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data user.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="dynamicModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow" id="modalContent"></div>
    </div>
</div>

<script>
    const modalContent = document.getElementById('modalContent');
    const dynamicModalEl = document.getElementById('dynamicModal');

    // Fungsi khusus pembaca script
    function injectHTML(container, html) {
        container.innerHTML = html;
        Array.from(container.querySelectorAll('script')).forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }

    function loadAddModal() {
        fetch('add_user_view')
            .then(response => response.text())
            .then(html => {
                injectHTML(modalContent, html);
                bootstrap.Modal.getOrCreateInstance(dynamicModalEl).show();
            });
    }

    function editUser(id) {
        fetch('add_user_view')
            .then(response => response.text())
            .then(html => {
                injectHTML(modalContent, html);
                document.getElementById('modalTitle').innerText = 'Edit User & Otorisasi Perangkat';
                document.getElementById('btnSubmitUser').innerHTML = '<span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span><span id="submitText">Update User</span>';
                
                document.getElementById('passwordGroup').style.display = 'none';
                document.getElementById('confirmPasswordGroup').style.display = 'none';
                document.getElementById('password').removeAttribute('required');
                document.getElementById('confirm_password').removeAttribute('required');
                
                const formData = new FormData();
                formData.append('id_user', id);
                
                fetch('api/users/get_user_detail', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('id_user').value = data.data.id_user;
                        document.getElementById('name').value = data.data.name;
                        document.getElementById('email').value = data.data.email;
                        
                        if(data.data.devices && data.data.devices.length > 0) {
                            data.data.devices.forEach(deviceId => {
                                const checkbox = document.getElementById('device_' + deviceId);
                                if(checkbox) checkbox.checked = true;
                            });
                        }
                        bootstrap.Modal.getOrCreateInstance(dynamicModalEl).show();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            });
    }

    function changePassword(id, name) {
        fetch('change_password_view')
            .then(response => response.text())
            .then(html => {
                injectHTML(modalContent, html);
                document.getElementById('id_user_password').value = id;
                document.getElementById('displayUserName').innerText = name;
                bootstrap.Modal.getOrCreateInstance(dynamicModalEl).show();
            });
    }

    function changeStatus(id, targetStatus, name) {
        Swal.fire({
            title: 'Konfirmasi Akun',
            text: `Apakah Anda yakin merubah status akun "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: targetStatus === 1 ? '#198754' : '#d33',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id_user', id);
                formData.append('status', targetStatus);

                fetch('api/users/change_status_user', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
<?php include('includes/footer.php'); ?>
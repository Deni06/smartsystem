<?php 
require_once('config/koneksi.php'); 
include('includes/header.php'); 
include('includes/sidebar.php'); 

$sql = "SELECT d.*, dt.type_name FROM device d 
        LEFT JOIN device_type dt ON d.id_type = dt.id_type 
        ORDER BY d.id_device DESC";
$result = mysqli_query($koneksi, $sql);
$devices = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<div class="main-content">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold m-0">Manajemen Perangkat</h3>
            <p class="text-muted small mb-0">Kelola daftar perangkat IoT dan alokasi pin hardware secara tersentralisasi.</p>
        </div>
        <button class="btn btn-primary px-4 shadow-sm text-nowrap" onclick="loadAddModal()">
            <i class="bi bi-plus-lg me-2"></i>Tambah Perangkat
        </button>
    </div>

    <div class="card card-custom overflow-hidden shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Perangkat</th>
                        <th>Tipe Alat</th>
                        <th>Lokasi</th>
                        <th>PIN (GPIO)</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($devices) > 0): ?>
                        <?php foreach ($devices as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#<?= $row['id_device'] ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['device_name']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['type_name'] ?? 'Universal') ?></span></td>
                            <td><i class="bi bi-geo-alt text-danger"></i> <?= htmlspecialchars($row['location']) ?></td>
                            <td class="fw-bold text-primary">GPIO <?= $row['pin_gpio'] ?> (<?= $row['active_state'] == 1 ? 'HIGH' : 'LOW' ?>)</td>
                            <td>
                                <?php if($row['status'] == '1'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" onclick="editDevice(<?= $row['id_device'] ?>)" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if($row['status'] == '1'): ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm ms-1" onclick="changeStatus(<?= $row['id_device'] ?>, 0, '<?= htmlspecialchars($row['device_name'], ENT_QUOTES) ?>')" title="Non-Aktifkan">
                                        <i class="bi bi-power"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success rounded-circle shadow-sm ms-1" onclick="changeStatus(<?= $row['id_device'] ?>, 1, '<?= htmlspecialchars($row['device_name'], ENT_QUOTES) ?>')" title="Aktifkan">
                                        <i class="bi bi-power"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data perangkat.</td></tr>
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

    // Fungsi khusus untuk mengeksekusi script yang ada di dalam HTML hasil Fetch
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
        fetch('add_device_view')
            .then(response => response.text())
            .then(html => {
                injectHTML(modalContent, html);
                bootstrap.Modal.getOrCreateInstance(dynamicModalEl).show();
            });
    }

    function editDevice(id) {
        fetch('add_device_view')
            .then(response => response.text())
            .then(html => {
                injectHTML(modalContent, html);
                document.getElementById('modalTitle').innerText = 'Edit Perangkat';
                document.getElementById('btnSubmitDevice').innerHTML = '<span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span><span id="submitText">Update Perangkat</span>';
                
                const formData = new FormData();
                formData.append('id', id);
                
                fetch('api/devices/get_device_detail', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('id_device').value = data.data.id_device;
                        document.getElementById('id_type').value = data.data.id_type;
                        document.getElementById('id_board').value = data.data.id_board;
                        document.getElementById('device_name').value = data.data.device_name;
                        document.getElementById('location').value = data.data.location;
                        document.getElementById('pin_gpio').value = data.data.pin_gpio;
                        document.getElementById('active_state').value = data.data.active_state;
                        
                        bootstrap.Modal.getOrCreateInstance(dynamicModalEl).show();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            });
    }

    function changeStatus(id, targetStatus, name) {
        const actionText = targetStatus === 1 ? 'MENGAKTIFKAN' : 'MENONAKTIFKAN';
        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${actionText} perangkat "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: targetStatus === 1 ? '#198754' : '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('status', targetStatus);

                fetch('api/devices/change_status_device', { method: 'POST', body: formData })
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
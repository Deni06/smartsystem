<?php $current_page = basename($_SERVER['PHP_SELF'], ".php"); ?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-cpu-fill"></i> Smart System AES
    </div>
    <nav class="nav flex-column mt-3">
        <a class="nav-link <?= ($current_page == 'dashboard') ? 'active' : ''; ?>" href="dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard Kendali
        </a>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { ?>
            <a class="nav-link <?= ($current_page == 'users') ? 'active' : ''; ?>" href="users">
                <i class="bi bi-people"></i> Manajemen User
            </a>
            <a class="nav-link <?= ($current_page == 'devices') ? 'active' : ''; ?>" href="devices">
                <i class="bi bi-hdd-network"></i> Manajemen Perangkat
            </a>
        <?php } ?>        
        <hr class="mx-3 my-2" style="border-color: rgba(255,255,255,0.1)">
        <a class="nav-link text-danger" href="logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
    
    <div class="user-profile-bottom">
        <div class="avatar-circle">
            <?= strtoupper(substr($_SESSION['name'], 0, 1)); ?>
        </div>
        <div class="d-flex flex-column text-white">
            <span class="fw-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($_SESSION['name']); ?></span>
            <span style="font-size: 0.75rem; opacity: 0.7;"><?= htmlspecialchars($_SESSION['email']); ?></span>
        </div>
    </div>
</div>
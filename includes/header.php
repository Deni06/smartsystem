<?php 
session_start();
if($_SESSION['is_login'] != 'Y' || ($_SESSION['is_login'] == 'Y' && 
in_array(basename($_SERVER['PHP_SELF'], ".php"), ["users", "doors"]) && 
(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1))) {
	header("location:index");	
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Door AES - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root { --sidebar-bg: #0f172a; --sidebar-active: #2b67f6; --bg-light: #f8fafc; }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; overflow-x: hidden; }
                
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            background-color: var(--sidebar-bg); 
            color: white; 
            display: flex; 
            flex-direction: column; 
            transition: all 0.3s ease;
            z-index: 1050;
            left: 0;
        }
        
        @media (max-width: 991.98px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; }
        }

        .sidebar-header { padding: 20px; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; }
        .nav-link { color: #94a3b8; padding: 12px 20px; display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: white; background-color: var(--sidebar-active); border-radius: 8px; margin: 0 10px; }
                
        .main-content { 
            margin-left: 260px; 
            padding: 20px; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            transition: all 0.3s ease;
        }
        
        .mobile-nav {
            display: none;
            background-color: var(--sidebar-bg);
            color: white;
            padding: 15px 20px;
        }

        @media (max-width: 991.98px) {
            .mobile-nav { display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1040; }
        }

        .user-profile-bottom { margin-top: auto; padding: 20px; border-top: 1px solid #1e293b; display: flex; align-items: center; gap: 10px; }
        .avatar-circle { width: 35px; height: 35px; background: #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--sidebar-bg); font-weight: bold; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1045;
        }
        .sidebar-overlay.active { display: block; }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="overlay"></div>

    <div class="mobile-nav">
        <span class="fw-bold"><i class="bi bi-lock-fill"></i> Smart Door</span>
        <div class="d-flex align-items-center gap-2">
            <a href="logout" class="btn btn-outline-danger border-0 p-1" title="Logout" onclick="return confirm('Yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right fs-4"></i>
            </a>
            <button class="btn btn-outline-light border-0 p-1" id="sidebarToggle">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>
    </div>
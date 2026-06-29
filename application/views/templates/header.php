<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'LABLOAN - Lab Mesin Poltesa' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* RESET */
        html, body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: #f4f6f9; 
            min-height: 100vh;
        }

        .wrapper { display: flex; flex: 1; min-height: 100vh; }
        
        /* SIDEBAR */
        .sidebar { 
            width: 250px; 
            background: #222d32; 
            color: #fff; 
            padding: 20px 10px; 
            box-sizing: border-box; 
            position: fixed; 
            top: 0; 
            left: 0; 
            height: 100vh; 
            overflow-y: auto; 
            z-index: 1000; 
        }
        
        .sidebar .profile {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #3c8dbc;
            margin-bottom: 20px;
        }
        .sidebar .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3c8dbc;
        }
        .sidebar .profile h5 {
            color: #fff;
            margin: 10px 0 5px;
            font-size: 16px;
        }
        .sidebar .profile .badge {
            background: #d9534f;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            color: #fff;
        }
        .sidebar a { 
            display: block; 
            color: #b8c7ce; 
            padding: 12px 15px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-bottom: 3px; 
            font-size: 14px; 
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active { 
            background: #1e282c; 
            color: #fff; 
            border-left: 3px solid #3c8dbc; 
        }
        .sidebar a i {
            width: 20px;
            margin-right: 10px;
        }
        .sidebar .logout {
            margin-top: 30px;
            border-top: 1px solid #3c8dbc;
            padding-top: 15px;
        }
        .sidebar .logout a {
            color: #d9534f;
        }
        .sidebar .logout a:hover {
            background: #d9534f;
            color: #fff;
            border-left-color: #fff;
        }
        
        /* MAIN PANEL */
        .main-panel { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            background: #f4f6f9; 
            margin-left: 250px; 
            min-height: 100vh; 
            box-sizing: border-box;
        }
        
        /* NAVBAR */
        .navbar { 
            background: #fff; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 1px 4px rgba(0,0,0,0.1); 
            position: fixed;
            top: 0;
            right: 0;
            left: 250px; 
            height: 60px; 
            box-sizing: border-box;
            z-index: 999; 
        }
        .navbar h4 {
            margin: 0;
            color: #333;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar .user-info img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        .navbar .user-info span {
            font-weight: 600;
            color: #333;
        }
        
        /* CONTENT */
        .content { 
            padding: 25px; 
            flex: 1; 
            margin-top: 60px; 
            box-sizing: border-box;
        }
        
        /* CARD STAT */
        .card-stat {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .display-6 {
            font-size: 2rem;
            font-weight: 700;
        }
        
        /* ALERT */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        
        /* TABLE */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
        table th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #dee2e6; }
        table td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; }
        table tr:hover { background: #f8f9fa; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #28a745; color: #fff; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-danger { background: #dc3545; color: #fff; }
        .badge-info { background: #17a2b8; color: #fff; }
        .badge-secondary { background: #6c757d; color: #fff; }
        
        /* FOOTER */
        footer { 
            background: #fff; 
            padding: 15px; 
            text-align: center; 
            font-size: 13px; 
            color: #666; 
            border-top: 1px solid #d2d6de; 
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar { width: 60px; padding: 10px 5px; }
            .sidebar .profile h5, .sidebar .profile .badge { display: none; }
            .sidebar a span { display: none; }
            .sidebar a i { font-size: 18px; margin-right: 0; }
            .main-panel { margin-left: 60px; }
            .navbar { left: 60px; padding: 10px 15px; }
            .navbar h4 { font-size: 14px; }
            .content { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="profile">
            <img src="<?= base_url('assets/uploads/profil/' . ($foto_profil ?? 'default.jpg')) ?>" alt="Profile">
            <h5><?= $nama_user ?? 'User' ?></h5>
            <span class="badge"><?= strtoupper(str_replace('_', ' ', $role ?? '')) ?></span>
        </div>
        
        <?php if($role == 'kepala_lab'): ?>
            <!-- ========================================= -->
            <!-- KEPALA LAB: LAPORAN + PROFIL -->
            <!-- ========================================= -->
            <a href="<?= base_url('kalab') ?>" class="<?= (uri_string() == 'kalab' || uri_string() == 'kalab/index' || uri_string() == 'kalab/laporan') ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> <span>Laporan Sirkulasi</span>
            </a>
            <a href="<?= base_url('kalab/profil') ?>" class="<?= uri_string() == 'kalab/profil' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i> <span>Profil Saya</span>
            </a>
            <div class="logout">
                <a href="<?= base_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
                </a>
            </div>
            
        <?php elseif($role == 'staff_admin'): ?>
            <a href="<?= base_url('admin') ?>" class="<?= (uri_string() == 'admin' || uri_string() == 'admin/index' || uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <a href="<?= base_url('admin/alat') ?>" class="<?= uri_string() == 'admin/alat' ? 'active' : '' ?>">
                <i class="fas fa-tools"></i> <span>Kelola Alat</span>
            </a>
            <a href="<?= base_url('admin/validasi') ?>" class="<?= uri_string() == 'admin/validasi' ? 'active' : '' ?>">
                <i class="fas fa-check-circle"></i> <span>Validasi</span>
            </a>
            <a href="<?= base_url('admin/pengembalian') ?>" class="<?= uri_string() == 'admin/pengembalian' ? 'active' : '' ?>">
                <i class="fas fa-undo"></i> <span>Pengembalian</span>
            </a>
            <a href="<?= base_url('admin/laporan') ?>" class="<?= uri_string() == 'admin/laporan' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> <span>Laporan</span>
            </a>
            <a href="<?= base_url('admin/petugas') ?>" class="<?= uri_string() == 'admin/petugas' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> <span>Petugas</span>
            </a>
            <a href="<?= base_url('admin/users') ?>" class="<?= uri_string() == 'admin/users' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> <span>User</span>
            </a>
            <div class="logout">
                <a href="<?= base_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
                </a>
            </div>
            
        <?php elseif($role == 'mahasiswa' || $role == 'dosen'): ?>
            <a href="<?= base_url('peminjaman') ?>" class="<?= (uri_string() == 'peminjaman' || uri_string() == 'peminjaman/index' || uri_string() == 'peminjaman/dashboard') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <a href="<?= base_url('peminjaman/katalog') ?>" class="<?= uri_string() == 'peminjaman/katalog' ? 'active' : '' ?>">
                <i class="fas fa-tools"></i> <span>Daftar Alat</span>
            </a>
            <a href="<?= base_url('peminjaman/pengembalian') ?>" class="<?= uri_string() == 'peminjaman/pengembalian' ? 'active' : '' ?>">
                <i class="fas fa-undo"></i> <span>Pengembalian</span>
            </a>
            <a href="<?= base_url('peminjaman/riwayat') ?>" class="<?= uri_string() == 'peminjaman/riwayat' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> <span>Riwayat</span>
            </a>
            <a href="<?= base_url('peminjaman/profil') ?>" class="<?= uri_string() == 'peminjaman/profil' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i> <span>Profil Saya</span>
            </a>
            <div class="logout">
                <a href="<?= base_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- MAIN PANEL -->
    <div class="main-panel">
        <!-- NAVBAR -->
        <div class="navbar">
            <h4><i class="fas fa-flask"></i> LABLOAN</h4>
            <div class="user-info">
                <span><?= $nama_user ?? 'User' ?></span>
                <img src="<?= base_url('assets/uploads/profil/' . ($foto_profil ?? 'default.jpg')) ?>" alt="Profile">
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
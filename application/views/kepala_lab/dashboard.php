<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Dashboard Kepala Lab' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background: #1a1a2e;
        }
        .sidebar .nav-link {
            color: #aaa;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: #dc3545;
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
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
        .badge-role {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        /* PERBAIKAN: tambahan untuk alert */
        .alert {
            border-radius: 10px;
        }
        .table th {
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <!-- Profil singkat di sidebar -->
                    <div class="text-center py-3 border-bottom border-secondary">
                        <img src="<?= base_url('assets/uploads/profil/' . ($foto_profil ?? 'default.jpg')) ?>" 
                             class="rounded-circle" width="60" height="60" style="object-fit:cover;">
                        <h6 class="text-white mt-2"><?= $nama_user ?? 'User' ?></h6>
                        <span class="badge bg-danger"><?= strtoupper(str_replace('_', ' ', $role ?? 'KEPALA LAB')) ?></span>
                    </div>
                    
                    <ul class="nav flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link <?= (uri_string() == 'kalab' || uri_string() == 'kalab/index' || uri_string() == 'kalab/dashboard') ? 'active' : '' ?>" 
                               href="<?= base_url('kalab') ?>">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'kalab/alat' ? 'active' : '' ?>" 
                               href="<?= base_url('kalab/alat') ?>">
                                <i class="fas fa-tools"></i> Pantau Alat & Stok
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'kalab/laporan' ? 'active' : '' ?>" 
                               href="<?= base_url('kalab/laporan') ?>">
                                <i class="fas fa-file-alt"></i> Laporan Sirkulasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'kalab/peminjaman' ? 'active' : '' ?>" 
                               href="<?= base_url('kalab/peminjaman') ?>">
                                <i class="fas fa-list"></i> Semua Peminjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'kalab/statistik' ? 'active' : '' ?>" 
                               href="<?= base_url('kalab/statistik') ?>">
                                <i class="fas fa-chart-bar"></i> Statistik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'kalab/profil' ? 'active' : '' ?>" 
                               href="<?= base_url('kalab/profil') ?>">
                                <i class="fas fa-user-circle"></i> Profil Saya
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="<?= base_url('auth/logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4 main-content">
                <!-- Flash Messages -->
                <?php if($this->session->flashdata('pesan')): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> <?= $this->session->flashdata('pesan') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">Halo, <?= $nama_user ?? 'User' ?>! 👋</h1>
                        <p class="text-muted">Selamat datang di Sistem Informasi Manajemen Alat Lab Teknik Mesin.</p>
                    </div>
                    <span class="badge bg-danger badge-role">KEPALA LAB</span>
                </div>

                <!-- Cards Statistik -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card card-stat text-white bg-primary mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-tools"></i> Total Inventaris</h6>
                                <p class="card-text display-6"><?= $total_alat ?? 0 ?></p>
                                <a href="<?= base_url('kalab/alat') ?>" class="text-white text-decoration-none">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat text-white bg-warning mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-clock"></i> Perlu Persetujuan</h6>
                                <p class="card-text display-6"><?= $pending_validasi ?? 0 ?></p>
                                <a href="<?= base_url('kalab/peminjaman') ?>" class="text-white text-decoration-none">
                                    Cek Pengajuan →
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat text-white bg-success mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-check-circle"></i> Peminjaman Aktif</h6>
                                <p class="card-text display-6"><?= $peminjaman_aktif ?? 0 ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat text-white bg-info mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-exchange-alt"></i> Total Transaksi</h6>
                                <p class="card-text display-6"><?= $total_peminjaman ?? 0 ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alat Terpopuler & Total User -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-fire text-danger"></i> Alat Paling Sering Dipinjam</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php if(isset($alat_terpopuler) && !empty($alat_terpopuler)): ?>
                                        <?php foreach($alat_terpopuler as $a): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?= $a->nama_alat ?>
                                            <span class="badge bg-primary rounded-pill"><?= $a->total_dipinjam ?> kali</span>
                                        </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item text-center text-muted">Belum ada data</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6><i class="fas fa-users text-info"></i> Total User</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <h1 class="display-1"><?= $total_user ?? 0 ?></h1>
                                    <p class="text-muted">Pengguna terdaftar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Peminjaman Terbaru -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6><i class="fas fa-list"></i> Peminjaman Terbaru</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($peminjaman_terbaru) && !empty($peminjaman_terbaru)): ?>
                                        <?php foreach($peminjaman_terbaru as $p): ?>
                                        <tr>
                                            <td><strong><?= $p->kode_peminjaman ?></strong></td>
                                            <td><?= $p->peminjam ?></td>
                                            <td><?= date('d/m/Y', strtotime($p->tanggal_pinjam)) ?></td>
                                            <td>
                                                <?php
                                                $status_class = 'secondary';
                                                $status_text = $p->status_peminjaman;
                                                if($p->status_peminjaman == 'pending') {
                                                    $status_class = 'warning';
                                                    $status_text = 'Menunggu';
                                                } elseif($p->status_peminjaman == 'disetujui') {
                                                    $status_class = 'success';
                                                    $status_text = 'Disetujui';
                                                } elseif($p->status_peminjaman == 'ditolak') {
                                                    $status_class = 'danger';
                                                    $status_text = 'Ditolak';
                                                } elseif($p->status_peminjaman == 'pending_kembali') {
                                                    $status_class = 'info';
                                                    $status_text = 'Menunggu Kembali';
                                                } elseif($p->status_peminjaman == 'selesai') {
                                                    $status_class = 'secondary';
                                                    $status_text = 'Selesai';
                                                }
                                                ?>
                                                <span class="badge bg-<?= $status_class ?>">
                                                    <?= $status_text ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('kalab/detail/' . $p->id_peminjaman) ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-inbox fa-2x text-muted d-block mb-2"></i>
                                                <span class="text-muted">Belum ada data peminjaman</span>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-muted py-3 mt-3 border-top">
                    <small>&copy; <?= date('Y') ?> LABLOAN - Sistem Peminjaman Alat Lab Mesin Poltesa</small>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto close alert
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
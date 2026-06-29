<div class="sidebar">
    <h3>Peminjaman Lab</h3>
    <div style="padding: 0 15px 15px 15px; border-bottom: 1px solid #4b545c; margin-bottom: 15px; font-size: 13px;">
        Status: <span style="color: #5cb85c; font-weight: bold;">● Online</span><br>
        Otoritas: <strong style="text-transform: uppercase; color: #3c8dbc;"><?php echo $this->session->userdata('role'); ?></strong>
    </div>

    <?php 
    $seg1 = $this->uri->segment(1); 
    $seg2 = $this->uri->segment(2); 
    ?>

    <?php if ($this->session->userdata('role') == 'mahasiswa' || $this->session->userdata('role') == 'dosen' ) : ?>
        <a href="<?php echo base_url('peminjaman'); ?>" class="<?php echo ($seg1 == 'peminjaman' && ($seg2 == '' || $seg2 == 'index')) ? 'active' : ''; ?>">Halaman Utama</a>
        <a href="<?php echo base_url('peminjaman/katalog'); ?>" class="<?php echo ($seg2 == 'katalog') ? 'active' : ''; ?>">Katalog & Pinjam</a>
        <a href="<?php echo base_url('peminjaman/pengembalian'); ?>" class="<?php echo ($seg2 == 'pengembalian') ? 'active' : ''; ?>">Pengembalian Alat</a>
        <a href="<?php echo base_url('peminjaman/riwayat'); ?>" class="<?php echo ($seg2 == 'riwayat') ? 'active' : ''; ?>">Riwayat Peminjaman</a>
        <a href="<?php echo base_url('peminjaman/profil'); ?>" class="<?php echo ($seg2 == 'profil') ? 'active' : ''; ?>">Profil Saya</a>
    <?php endif; ?>

    <?php if ($this->session->userdata('role') == 'staff_admin') : ?>
        <a href="<?php echo base_url('admin/dashboard'); ?>" class="<?php echo ($seg1 == 'admin' && $seg2 == 'dashboard') ? 'active' : ''; ?>">Dashboard Petugas</a>
        <a href="<?php echo base_url('admin/kelola_alat'); ?>" class="<?php echo ($seg1 == 'admin' && $seg2 == 'kelola_alat') ? 'active' : ''; ?>">Kelola Data Alat</a>
        <a href="<?php echo base_url('admin/sirkulasi'); ?>" class="<?php echo ($seg1 == 'admin' && $seg2 == 'sirkulasi') ? 'active' : ''; ?>">Log Sirkulasi Lab</a>
        
        <a href="<?php echo base_url('admin/daftar_kembali'); ?>" class="<?php echo ($seg1 == 'admin' && $seg2 == 'daftar_kembali') ? 'active' : ''; ?>" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Konfirmasi Pengembalian</span>
            <?php 
                $jumlah_pending = $this->db->get_where('peminjaman', ['status_peminjaman' => 'pending_kembali'])->num_rows();
                if ($jumlah_pending > 0): 
            ?>
                <small style="background: #e67e22; color: white; padding: 2px 7px; border-radius: 10px; font-weight: bold; font-size: 11px;">
                    <?php echo $jumlah_pending; ?>
                </small>
            <?php endif; ?>
        </a>

        <a href="<?php echo base_url('admin/laporan'); ?>" class="<?php echo ($seg1 == 'admin' && $seg2 == 'laporan') ? 'active' : ''; ?>">Laporan & Cetak BAST</a>
        <a href="<?php echo base_url('admin/petugas'); ?>" class="<?php echo ($seg1 == 'admin' && $seg2 == 'petugas') ? 'active' : ''; ?>">Input Petugas Baru</a>
    <?php endif; ?>

    <?php if ($this->session->userdata('role') == 'kepala_lab') : ?>
        <!-- ============================================= -->
        <!-- KEPALA LAB: LAPORAN + PROFIL -->
        <!-- ============================================= -->
        <a href="<?php echo base_url('kalab'); ?>" class="<?php echo ($seg1 == 'kalab' && ($seg2 == '' || $seg2 == 'index' || $seg2 == 'laporan')) ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i> Laporan Sirkulasi
        </a>
        <a href="<?php echo base_url('kalab/profil'); ?>" class="<?php echo ($seg1 == 'kalab' && $seg2 == 'profil') ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
    <?php endif; ?>

    <a href="<?php echo base_url('auth/logout'); ?>" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');" style="color: #ff6b6b; margin-top: 20px; border-top: 1px solid #444; padding-top: 15px;">
        Keluar (Logout)
    </a>
</div>

<div class="main-panel">
    
    <div class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background-color: #ffffff; border-bottom: 1px solid #dee2e6;">
        <div>
            <strong style="font-size: 18px;">Sistem Information Manajemen Aset Laboratorium</strong>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span style="color: #333; font-size: 14px;">
                Halo, <strong><?php echo $this->session->userdata('nama_lengkap'); ?></strong>
            </span>
            <a href="<?php echo base_url('auth/logout'); ?>" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');" class="btn-logout" style="background-color: #d9534f; color: white; padding: 6px 15px; border-radius: 4px; text-decoration: none; font-size: 14px; font-weight: bold;">
                Keluar
            </a>
        </div>
    </div>
    
    <div class="content" style="padding: 20px;">
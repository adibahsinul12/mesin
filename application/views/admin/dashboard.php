<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<div>
    <div>
        <h2><i class="fas fa-tachometer-alt"></i> Panel Utama Manajemen Logistik</h2>
        <p>Selamat datang kembali, <strong><?php echo $nama_user; ?></strong> [Otoritas Internal Staff / Kepala Lab]</p>
    </div>
    
    <hr>

    <div>
        <h3><i class="fas fa-chart-pie"></i> Ringkasan Aktivitas Laboratorium</h3>
        
        <p>
            <i class="fas fa-boxes"></i> Total Inventaris Alat Teknik Mesin: 
            <strong>0</strong> Instrumen
        </p>
        
        <p>
            <i class="fas fa-clock"></i> Permintaan Peminjaman Menunggu Persetujuan: 
            <strong>0</strong> Transaksi
        </p>
        
        <p>
            <i class="fas fa-hand-holding"></i> Alat Aktif Sedang Dipinjam Mahasiswa: 
            <strong>0</strong> Item
        </p>
        
        <p>
            <i class="fas fa-history"></i> Total Log Riwayat Pengembalian Berhasil: 
            <strong>0</strong> Catatan
        </p>
    </div>

    <hr>

    <div>
        <h3><i class="fas fa-folder-open"></i> Pilihan Modul Operasional</h3>
        <p>Silakan klik salah satu menu di bawah ini untuk mengelola logistik laboratorium:</p>
        
        <p>
            <i class="fas fa-user-shield"></i> 
            <a href="<?php echo base_url('admin/petugas'); ?>"><strong>Kelola Hak Akses Petugas</strong></a> 
            <br>Akses pendaftaran akun staff baru, pengiriman kode OTP internal, dan daftar list petugas aktif.
        </p>

        <p>
            <i class="fas fa-tools"></i> 
            <a href="<?php echo base_url('petugas/kelola_alat'); ?>"><strong>Manajemen Inventaris Alat Lab</strong></a> 
            <br>Fungsi CRUD internal untuk menambah instrumen baru, memperbarui spesifikasi fisik, dan menghapus data alat.
        </p>

        <p>
            <i class="fas fa-file-import"></i> 
            <a href="<?php echo base_url('petugas/transaksi_masuk'); ?>"><strong>Verifikasi Fisik Pengembalian Alat</strong></a> 
            <br>Konfirmasi pengembalian barang dari mahasiswa, pengecekan kondisi fisik (baik/rusak), dan pemulihan kuantitas stok otomatis.
        </p>

        <p>
            <i class="fas fa-print"></i> 
            <a href="<?php echo base_url('petugas/laporan'); ?>"><strong>Cetak Laporan Logistik</strong></a> 
            <br>Rekapitulasi riwayat transaksi bulanan untuk diserahkan sebagai laporan pertanggungjawaban ke Kepala Laboratorium.
        </p>
    </div>

    <hr>

    <p>
        <i class="fas fa-sign-out-alt"></i> 
        <a href="<?php echo base_url('auth/logout'); ?>">Keluar Sesi / Logout dari Sistem</a>
    </p>
</div>

<?php $this->load->view('templates/footer'); ?>
<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<div class="profile-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-left: 4px solid #3c8dbc;">
    <h3 style="margin-top: 0; margin-bottom: 5px;">Selamat Datang Kembali, <?php echo htmlspecialchars($nama_lengkap); ?>!</h3>
    <p style="margin: 0; color: #666; font-size: 14px;">
        Nomor Induk: <strong><?php echo htmlspecialchars($nomor_induk); ?></strong> 
        <?php if(!empty($program_studi)): ?> | Program Studi: <strong><?php echo htmlspecialchars($program_studi); ?></strong><?php endif; ?>
    </p>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 20px;">
    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px;">Ringkasan Aktivitas Peminjaman</h3>
    
    <div style="display: flex; gap: 20px; margin-top: 15px;">
        <div style="flex: 1; background: #eef7ee; padding: 20px; border-radius: 8px; border-left: 4px solid #5cb85c;">
            <span style="font-size: 12px; color: #777; font-weight: bold; text-transform: uppercase;">Sedang Dipinjam (Aktif)</span>
            <h2 style="margin: 5px 0 0 0; color: #5cb85c;">
                <?php echo $total_aktif; ?> <span style="font-size: 14px; color: #555; font-weight: normal;">Alat</span>
            </h2>
        </div>
        
        <div style="flex: 1; background: #f0f4f8; padding: 20px; border-radius: 8px; border-left: 4px solid #0275d8;">
            <span style="font-size: 12px; color: #777; font-weight: bold; text-transform: uppercase;">Sudah Dikembalikan</span>
            <h2 style="margin: 5px 0 0 0; color: #0275d8;">
                <?php echo $total_selesai; ?> <span style="font-size: 14px; color: #555; font-weight: normal;">Alat</span>
            </h2>
        </div>
        
        <div style="flex: 1; background: #fdf2f2; padding: 20px; border-radius: 8px; border-left: 4px solid #d9534f;">
            <span style="font-size: 12px; color: #777; font-weight: bold; text-transform: uppercase;">Terlambat Keluar (> 1 Minggu)</span>
            <h2 style="margin: 5px 0 0 0; color: #d9534f;">
                <?php echo $total_terlambat; ?> <span style="font-size: 14px; color: #555; font-weight: normal;">Alat</span>
            </h2>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>
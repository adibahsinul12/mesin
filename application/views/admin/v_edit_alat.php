<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2><i class="fas fa-edit"></i> Edit Instrumen Alat Lab</h2>
<p>Halaman untuk memperbarui data spesifikasi, kuantitas stok, kategori, maupun foto instrumen praktikum.</p>

<hr>

<div style="background: white; padding: 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; border-top: 4px solid #f0ad4e;">
    <h3><i class="fas fa-tools"></i> Form Perubahan Data: <?php echo $alat['nama_alat']; ?></h3>
    <p style="font-size: 13px; color: #666;">Catatan: Jika Anda mengubah jumlah Stok Total, sistem akan otomatis mengkalkulasi ulang jumlah Stok Tersedia saat ini.</p>
    
    <form action="<?php echo base_url('admin/proses_edit_alat'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_alat" value="<?php echo $alat['id_alat']; ?>">

        <p>
            <label>Kode Alat :</label><br>
            <input type="text" name="kode_alat" value="<?php echo htmlspecialchars($alat['kode_alat']); ?>" readonly required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; background-color: #eee; cursor: not-allowed; font-weight: bold; color: #2c5282;">
        </p>
        
        <p>
            <label>Nama Alat Lab :</label><br>
            <input type="text" name="nama_alat" value="<?php echo htmlspecialchars($alat['nama_alat']); ?>" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
        </p>

        <p>
            <label>Kategori Alat :</label><br>
            <select name="kategori_alat" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
                <option value="">-- Pilih Kategori --</option>
                <option value="Perawatan" <?php echo ($alat['kategori_alat'] == 'Perawatan') ? 'selected' : ''; ?>>Perawatan</option>
                <option value="Pengujian" <?php echo ($alat['kategori_alat'] == 'Pengujian') ? 'selected' : ''; ?>>Pengujian</option>
                <option value="Kelistrikan" <?php echo ($alat['kategori_alat'] == 'Kelistrikan') ? 'selected' : ''; ?>>Kelistrikan</option>
                <option value="Pneumatik" <?php echo ($alat['kategori_alat'] == 'Pneumatik') ? 'selected' : ''; ?>>Pneumatik</option>
            </select>
        </p>

        <p>
            <label>Spesifikasi Detail :</label><br>
            <textarea name="spesifikasi" rows="3" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;"><?php echo htmlspecialchars($alat['spesifikasi']); ?></textarea>
        </p>
        
        <p>
            <label>Jumlah Stok Total :</label><br>
            <input type="number" name="stok_total" min="0" value="<?php echo $alat['stok_total']; ?>" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
        </p>
        
        <p>
            <label>Kondisi Fisik Alat :</label><br>
            <select name="kondisi_alat" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
                <option value="baik" <?php echo ($alat['kondisi_alat'] == 'baik') ? 'selected' : ''; ?>>Kondisi Baik / Layak Pakai</option>
                <option value="rusak_ringan" <?php echo ($alat['kondisi_alat'] == 'rusak_ringan') ? 'selected' : ''; ?>>Rusak Ringan (Butuh Kalibrasi)</option>
            </select>
        </p>
        
        <p>
            <label>Foto Alat Saat Ini :</label><br>
            <?php 
            $path_foto = 'assets/uploads/alat/' . $alat['foto_alat'];
            if(!empty($alat['foto_alat']) && file_exists($path_foto)): ?>
                <div style="margin-top: 5px; margin-bottom: 5px;">
                    <img src="<?php echo base_url($path_foto); ?>" alt="Foto Alat Lama" style="width: 120px; height: 120px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                    <p style="font-size: 11px; color: #666; margin: 2px 0 0 0;">File: <?php echo $alat['foto_alat']; ?></p>
                </div>
            <?php else: ?>
                <div style="margin-top: 5px; margin-bottom: 5px;">
                    <img src="<?php echo base_url('assets/uploads/alat/default_alat.jpg'); ?>" alt="Tidak Ada Foto" style="width: 120px; height: 120px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; opacity: 0.6;">
                    <p style="font-size: 11px; color: #999; margin: 2px 0 0 0;">Belum ada foto yang diunggah untuk alat ini.</p>
                </div>
            <?php endif; ?>
        </p>

        <p>
            <label>Ganti Foto Alat Baru :</label><br>
            <input type="file" name="foto_alat" accept="image/*" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
            <small style="color: #666; font-size: 11px;">Format: JPG/PNG, Maksimal 2MB (Biarkan kosong jika tidak ingin mengubah foto saat ini)</small>
        </p>
        
        <p style="margin-top: 20px;">
            <button type="submit" style="background: #f0ad4e; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-right: 10px;">
                <i class="fas fa-check-circle"></i> Perbarui Data Alat
            </button>
            <a href="<?php echo base_url('admin/kelola_alat'); ?>" style="background: #aeaeae; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13.5px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </p>
    </form>
</div>

</div> 
</div> 

<?php $this->load->view('templates/footer'); ?>
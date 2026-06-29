<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2 style="margin-top: 0; color: #333;">Formulir Pengajuan Peminjaman Alat</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Silakan atur waktu dan pilih item alat laboratorium Teknik Mesin yang ingin Anda gunakan.</p>

<?php if($this->session->flashdata('pesan_sukses')): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; border-left: 5px solid #28a745; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
        <?php echo $this->session->flashdata('pesan_sukses'); ?>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('pesan_error')): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; border-left: 5px solid #dc3545; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
        <?php echo $this->session->flashdata('pesan_error'); ?>
    </div>
<?php endif; ?>

<form action="<?php echo base_url('peminjaman/proses_ajukan'); ?>" method="post">
    
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <h3 style="margin-top: 0; color: #3c8dbc;">1. Batas Waktu & Penggunaan</h3>
        <p style="color: #666; font-size: 13px; background: #fcf8e3; padding: 10px; border-radius: 4px; border-left: 4px solid #f0ad4e; margin-bottom: 15px;">
            <strong>Ketentuan Lab:</strong> Peminjaman alat langsung disetujui otomatis dengan batas waktu pengembalian maksimal <strong>1 minggu (7 hari)</strong> dari waktu pengambilan.
        </p>
        
        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Tanggal & Waktu Ambil:</label><br>
                <input type="datetime-local" id="tanggal_pinjam" name="tanggal_pinjam" required onchange="hitungTenggat()" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Batas Maksimal Pengembalian (7 Hari):</label><br>
                <input type="datetime-local" id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" readonly style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: #eee;">
            </div>
        </div>
        <div style="margin-top: 15px;">
            <label style="font-size: 14px; font-weight: bold; color: #555;">Tujuan / Keperluan Penggunaan Alat:</label><br>
            <textarea name="tujuan_keperluan" rows="2" placeholder="Contoh: Praktikum Kerja Plat kelompok 2" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; color: #3c8dbc;">2. Pilih Instrumen / Alat Mesin</h3>
        
        <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #dee2e6; margin-top: 10px;">
            <thead style="background: #f8f9fa; color: #333;">
                <tr>
                    <th width="5%">Pilih</th>
                    <th width="12%">Foto Alat</th> <th width="12%">Kode</th>
                    <th>Nama Alat / Deskripsi Spesifikasi</th>
                    <th width="15%">Tersedia</th>
                    <th width="15%">Jumlah Input</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($katalog_alat)): ?>
                    <?php foreach($katalog_alat as $alat): ?>
                    <tr>
                        <td align="center">
                            <?php if($alat['stok_tersedia'] > 0): ?>
                                <input type="checkbox" name="id_alat[]" value="<?php echo $alat['id_alat']; ?>" style="transform: scale(1.3);">
                            <?php else: ?>
                                <input type="checkbox" disabled style="transform: scale(1.3);">
                            <?php endif; ?>
                        </td>
                        
                        <td align="center">
                            <?php if(!empty($alat['foto_alat']) && file_exists('./assets/uploads/alat/' . $alat['foto_alat'])): ?>
                                <img src="<?php echo base_url('assets/uploads/alat/' . $alat['foto_alat']); ?>" alt="Foto Alat" style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            <?php else: ?>
                                <img src="<?php echo base_url('assets/uploads/alat/default_alat.jpg'); ?>" alt="Default Alat" style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            <?php endif; ?>
                        </td>

                        <td align="center"><strong><?php echo $alat['kode_alat']; ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($alat['nama_alat']); ?></strong><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($alat['spesifikasi']); ?></small>
                        </td>
                        <td align="center"><strong><?php echo $alat['stok_tersedia']; ?></strong> <span style="color: #999;">/ <?php echo $alat['stok_total']; ?></span></td>
                        <td align="center">
                            <?php if($alat['stok_tersedia'] > 0): ?>
                                <input type="number" name="jumlah_pinjam_[<?php echo $alat['id_alat']; ?>]" min="1" max="<?php echo $alat['stok_tersedia']; ?>" placeholder="0" style="width: 70px; padding: 5px; text-align: center; border: 1px solid #ccc; border-radius: 4px;">
                            <?php else: ?>
                                <span style="color: #d9534f; font-weight: bold; font-size: 12px; text-transform: uppercase;">Kosong</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" align="center" style="color: #999; padding: 20px;">Data inventaris alat lab kosong.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if(!empty($katalog_alat)): ?>
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" style="background: #5cb85c; color: white; padding: 12px 25px; border: none; border-radius: 4px; font-size: 15px; font-weight: bold; cursor: pointer;">
                    Konfirmasi & Ambil Alat Lab
                </button>
            </div>
        <?php endif; ?>
    </div>
</form>

<script>
    function hitungTenggat() {
        var tglPinjam = document.getElementById('tanggal_pinjam').value;
        if(tglPinjam) {
            var date = new Date(tglPinjam);
            date.setDate(date.getDate() + 7);
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            var hours = ('0' + date.getHours()).slice(-2);
            var minutes = ('0' + date.getMinutes()).slice(-2);
            document.getElementById('tanggal_kembali_rencana').value = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
        }
    }
    window.onload = function() {
        var now = new Date();
        var year = now.getFullYear();
        var month = ('0' + (now.getMonth() + 1)).slice(-2);
        var day = ('0' + now.getDate()).slice(-2);
        var hours = ('0' + now.getHours()).slice(-2);
        var minutes = ('0' + now.getMinutes()).slice(-2);
        document.getElementById('tanggal_pinjam').value = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
        hitungTenggat();
    }
</script>

<?php $this->load->view('templates/footer'); ?>
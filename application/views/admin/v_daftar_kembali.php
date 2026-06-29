<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2 style="margin-top: 0; color: #333;">Konfirmasi Verifikasi Pengembalian</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Daftar di bawah ini memuat instrumen lab yang telah dikembalikan oleh peminjam. Lakukan pengecekan kondisi fisik sebelum menyetujui.</p>

<?php if($this->session->flashdata('sukses_sirkulasi')): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; border-left: 5px solid #28a745; margin-bottom: 20px;">
        <?php echo $this->session->flashdata('sukses_sirkulasi'); ?>
    </div>
<?php endif; ?>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #dee2e6;">
        <thead style="background: #f8f9fa; color: #333;">
            <tr>
                <th width="5%">No</th>
                <th>Peminjam</th>
                <th>Kode Transaksi</th>
                <th width="25%">Kondisi Fisik Pengembalian (Wajib Diperiksa)</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($list_kembali)): ?>
                <?php $no = 1; foreach($list_kembali as $row): ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                        <small style="color: #777;"><?php echo htmlspecialchars($row['nomor_induk']); ?></small>
                    </td>
                    <td><strong><?php echo $row['kode_peminjaman']; ?></strong></td>
                    <td>
                        <form action="<?php echo base_url('admin/proses_verifikasi_kembali'); ?>" method="post">
                            <input type="hidden" name="id_peminjaman" value="<?php echo $row['id_peminjaman']; ?>">
                            
                            <select name="kondisi_kembali" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; width: 100%; margin-bottom: 8px;" required>
                                <option value="baik">✔ Baik / Utuh</option>
                                <option value="rusak_ringan">⚠️ Rusak Ringan</option>
                                <option value="rusak_berat">❌ Rusak Berat</option>
                            </select>
                            
                            <input type="text" name="keterangan_tambahan" placeholder="Catatan tambahan (opsional)..." style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; width: 100%; margin-bottom: 8px; box-sizing: border-box;">
                            
                            <button type="submit" style="background: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; font-size: 12px;">
                                Konfirmasi & Terima Alat
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" align="center" style="color: #666; padding: 30px; font-style: italic; background: #fafafa;">
                        📭 Tidak ada antrean permintaan pengembalian alat saat ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->load->view('templates/footer'); ?>
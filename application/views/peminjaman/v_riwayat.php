<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2 style="margin-top: 0; color: #333;">Log Riwayat Peminjaman Alat Anda</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Berikut adalah rekaman jejak digital peminjaman instrumen laboratorium Teknik Mesin Poltesa yang pernah Anda lakukan.</p>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #dee2e6;">
        <thead style="background: #f8f9fa; color: #333;">
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode Pinjam</th>
                <th width="16%">Nama Peminjam</th>
                <th width="14%">Program Studi</th>
                <th>Alat Lab Terpinjam</th>
                <th width="13%">Waktu Pengambilan</th>
                <th width="13%">Batas Pengembalian</th>
                <th width="8%">Status</th>
                <th width="10%">Aksi Dokumen</th> </tr>
        </thead>
        <tbody>
            <?php if(!empty($riwayat_pinjam)): ?>
                <?php $no = 1; foreach($riwayat_pinjam as $row): ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td align="center"><small><strong><?php echo $row['kode_peminjaman']; ?></strong></small></td>
                    <td><strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                    <td><small><?php echo htmlspecialchars($row['program_studi']); ?></small></td>
                    <td><span style="font-size: 13px; color: #333;"><?php echo htmlspecialchars($row['daftar_alat']); ?></span></td>
                    <td align="center">
                        <small><?php echo date('d M Y - H:i', strtotime($row['tanggal_pinjam'])); ?> WIB</small>
                    </td>
                    <td align="center">
                        <small style="color: #c9302c; font-weight: bold;">
                            <?php echo date('d M Y - H:i', strtotime($row['tanggal_kembali_rencana'])); ?> WIB
                        </small>
                    </td>
                    <td align="center">
                        <?php if($row['status_peminjaman'] == 'disetujui'): ?>
                            <span style="background: #5cb85c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Aktif</span>
                        <?php else: ?>
                            <span style="background: #0275d8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Selesai</span>
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <a href="<?php echo base_url('peminjaman/cetak/' . $row['id_peminjaman']); ?>" target="_blank" style="background: #f0ad4e; color: white; padding: 6px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-decoration: none; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            🖨️ Cetak Struk
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" align="center" style="color: #999; padding: 30px;">
                        Anda belum pernah melakukan aktivitas transaksi peminjaman alat lab.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->load->view('templates/footer'); ?>
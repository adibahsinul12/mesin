<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2 style="margin-top: 0; color: #333;">Formulir Pengembalian Alat Lab</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Daftar di bawah ini memuat seluruh instrumen mesin yang sedang Anda pinjam atau dalam proses peninjauan pengembalian.</p>

<?php if($this->session->flashdata('sukses_kembali')): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; border-left: 5px solid #28a745; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
        <?php echo $this->session->flashdata('sukses_kembali'); ?>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error_kembali')): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; border-left: 5px solid #dc3545; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
        <?php echo $this->session->flashdata('error_kembali'); ?>
    </div>
<?php endif; ?>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #dee2e6;">
        <thead style="background: #f8f9fa; color: #333;">
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Transaksi</th>
                <th>Alat yang Harus Dikembalikan</th>
                <th width="20%">Batas Rencana Kembali</th>
                <th width="20%">Aksi Pengembalian</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($pinjaman_aktif)): ?>
                <?php $no = 1; foreach($pinjaman_aktif as $row): ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td align="center"><strong><?php echo $row['kode_peminjaman']; ?></strong></td>
                    <td>
                        <span style="color: #2c3e50; font-weight: bold;"><?php echo htmlspecialchars($row['daftar_alat']); ?></span>
                        
                        <?php if ($row['status_peminjaman'] === 'pending_kembali'): ?>
                            <?php 
                            // Mengambil data log pengembalian sementara (jika ada catatan revisi dari admin)
                            $cek_catatan = $this->db->get_where('pengembalian', ['id_peminjaman' => $row['id_peminjaman']])->row_array();
                            if (!empty($cek_catatan) && !empty($cek_catatan['keterangan_tambahan'])): 
                            ?>
                                <div style="margin-top: 8px; background: #fff3cd; color: #856404; padding: 8px 12px; border-radius: 4px; font-size: 12px; border-left: 4px solid #ffc107; text-align: left;">
                                    <strong>💬 Pesan Admin Lab:</strong> <?php echo htmlspecialchars($cek_catatan['keterangan_tambahan']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <span style="color: #d9534f; font-weight: bold; font-size: 13px;">
                            <?php echo date('d M Y - H:i', strtotime($row['tanggal_kembali_rencana'])); ?> WIB
                        </span>
                    </td>
                    <td align="center">
                        <?php if ($row['status_peminjaman'] === 'pending_kembali'): ?>
                            <span style="color: #d9822b; font-weight: bold; background: #fff3cd; padding: 6px 12px; border: 1px solid #ffeba2; border-radius: 4px; font-size: 12px; display: inline-block;">
                                ⏳ Menunggu Cek Fisik Admin
                            </span>
                        <?php else: ?>
                            <a href="<?php echo base_url('peminjaman/ajukan_pengembalian/' . $row['id_peminjaman']); ?>" 
                               class="btn-kembali"
                               onclick="return confirm('Apakah Anda yakin ingin mengajukan pengembalian alat ini? Status akan ditinjau oleh laboran.');"
                               style="background: #d9534f; color: white; padding: 7px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-block;">
                                Kembalikan Alat
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" align="center" style="color: #28a745; padding: 30px; font-weight: bold; background: #f9fdf9;">
                        ✔ Luar biasa! Anda tidak memiliki tunggakan peminjaman alat laboratorium saat ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->load->view('templates/footer'); ?>
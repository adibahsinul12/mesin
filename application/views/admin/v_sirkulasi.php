<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<style>
.modal-profil {
    display: none; 
    position: fixed; 
    z-index: 9999; 
    left: 0; top: 0; 
    width: 100%; height: 100%; 
    background-color: rgba(0,0,0,0.5);
}
.modal-content-profil {
    background-color: white; 
    margin: 15% auto; 
    padding: 20px; 
    border-radius: 6px; 
    width: 400px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    position: relative;
}
.close-modal {
    position: absolute; right: 15px; top: 10px; 
    font-size: 20px; font-weight: bold; cursor: pointer; color: #aaa;
}
.close-modal:hover { color: black; }
.btn-detail-user {
    color: #3c8dbc; text-decoration: none; font-weight: bold; cursor: pointer;
}
.btn-detail-user:hover { text-decoration: underline; color: #2c5282; }
</style>

<h2><i class="fas fa-exchange-alt"></i> Daftar Peminjaman & Pengembalian Alat</h2>
<p>Halaman khusus petugas untuk memonitor sirkulasi instrumen lab serta melihat detail profil identitas peminjam.</p>

<hr>

<?php if($this->session->flashdata('sukses_sirkulasi')): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
        <?php echo $this->session->flashdata('sukses_sirkulasi'); ?>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error_sirkulasi')): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
        <?php echo $this->session->flashdata('error_sirkulasi'); ?>
    </div>
<?php endif; ?>

<div style="background: white; padding: 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
    <h3><i class="fas fa-history"></i> Log Sirkulasi Peminjaman</h3>
    
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left;">
        <thead style="background: #f4f6f9;">
            <tr>
                <th>No</th>
                <th>Peminjam (NIM)</th>
                <th>Kode Transaksi / Keperluan</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali Rencana</th>
                <th>Status Peminjaman</th>
                <th>Aksi Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($list_sirkulasi)): ?>
                <?php $no = 1; foreach($list_sirkulasi as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <span class="btn-detail-user" onclick="bukaProfil('<?php echo $row['nama_lengkap']; ?>', '<?php echo $row['nomor_induk']; ?>', '<?php echo $row['program_studi']; ?>', '<?php echo $row['kelas']; ?>', '<?php echo $row['role']; ?>')">
                                <i class="fas fa-user-circle"></i> <?php echo $row['nama_lengkap']; ?>
                            </span>
                            <br>
                            <small style="color: #666;"><?php echo $row['nomor_induk']; ?></small>
                        </td>
                        <td>
                            <strong style="color: #2c5282;"><?php echo $row['kode_peminjaman']; ?></strong><br>
                            <small style="color: #555;">Tujuan: <?php echo htmlspecialchars($row['tujuan_keperluan']); ?></small>
                        </td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_kembali_rencana'])); ?></td>
                        <td>
                            <?php 
                            if($row['status_peminjaman'] == 'pending') {
                                echo '<span style="background: #f0ad4e; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Pending</span>';
                            } elseif($row['status_peminjaman'] == 'disetujui') {
                                echo '<span style="background: #3c8dbc; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Disetujui</span>';
                            } elseif($row['status_peminjaman'] == 'pending_kembali') {
                                echo '<span style="background: #e67e22; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Pending Kembali</span>';
                            } elseif($row['status_peminjaman'] == 'ditolak') {
                                echo '<span style="background: #d9534f; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Ditolak</span>';
                            } else {
                                echo '<span style="background: #5cb85c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Selesai</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if($row['status_peminjaman'] == 'pending_kembali'): ?>
                                <a href="<?php echo base_url('admin/daftar_kembali'); ?>" style="background: #e67e22; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block;">
                                    <i class="fas fa-search"></i> Cek Fisik Alat
                                </a>
                            <?php elseif($row['status_peminjaman'] == 'disetujui'): ?>
                                <a href="<?php echo base_url('admin/proses_kembali/'.$row['id_peminjaman']); ?>" onclick="return confirm('Bypass langsung pengembalian manual untuk transaksi ini?')" style="background: #5cb85c; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block;">
                                    <i class="fas fa-undo"></i> Selesai
                                </a>
                            <?php else: ?>
                                <span style="color: #5cb85c; font-size: 12px; font-weight: bold;"><i class="fas fa-check-circle"></i> Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #999;">Belum ada riwayat data sirkulasi peminjaman alat saat ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalProfilPeminjam" class="modal-profil">
    <div class="modal-content-profil">
        <span class="close-modal" onclick="tutupProfil()">&times;</span>
        <h4 style="margin-top: 5px; border-bottom: 2px solid #3c8dbc; padding-bottom: 10px; color: #333;">
            <i class="fas fa-id-card"></i> Profil Identitas Peminjam
        </h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 10px;" cellpadding="6">
            <tr>
                <td style="width: 35%; font-weight: bold; color: #555;">Nama Lengkap</td>
                <td style="width: 5%;">:</td>
                <td id="mdNama" style="color: #000; font-weight: bold;">-</td>
            </tr>
            <tr>
                <td id="mdLabelNim" style="font-weight: bold; color: #555;">NIM / No Induk</td>
                <td>:</td>
                <td id="mdNim">-</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #555;">Program Studi</td>
                <td>:</td>
                <td id="mdProdi">-</td>
            </tr>
            <tr id="barisKelas">
                <td style="font-weight: bold; color: #555;">Kelas / Angkatan</td>
                <td>:</td>
                <td id="mdKelas">-</td>
            </tr>
        </table>
        <div style="text-align: right; margin-top: 20px;">
            <button onclick="tutupProfil()" style="padding: 6px 15px; background: #666; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px;">Tutup</button>
        </div>
    </div>
</div>

<script>
function bukaProfil(nama, nim, prodi, kelas, role) {
    document.getElementById("mdNama").innerText = nama || '-';
    document.getElementById("mdNim").innerText = nim || '-';
    document.getElementById("mdProdi").innerText = prodi || '-';
    document.getElementById("mdKelas").innerText = kelas || '-';
    
    let elemenBarisKelas = document.getElementById("barisKelas");
    let elemenLabelNim = document.getElementById("mdLabelNim"); // Tangkap komponen label tabel

    // PERBAIKAN UTAMA: Manipulasi teks label secara dinamis berdasarkan parameter role
    if (role === 'mahasiswa') {
        elemenBarisKelas.style.display = "table-row";
        elemenLabelNim.innerText = "Nomor Induk / NIM";
    } else {
        elemenBarisKelas.style.display = "none";
        elemenLabelNim.innerText = "NIDN / NIP";
    }
    
    document.getElementById("modalProfilPeminjam").style.display = "block";
}

function tutupProfil() {
    document.getElementById("modalProfilPeminjam").style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById("modalProfilPeminjam");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<?php $this->load->view('templates/footer'); ?>
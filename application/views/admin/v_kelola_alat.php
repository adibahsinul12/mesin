<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2><i class="fas fa-tools"></i> Manajemen Inventaris Alat Lab</h2>
<p>Halaman khusus petugas untuk mengelola ketersediaan instrumen praktikum Teknik Mesin.</p>

<hr>

<?php if($this->session->flashdata('sukses_alat')): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
        <?php echo $this->session->flashdata('sukses_alat'); ?>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error_alat')): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
        <?php echo $this->session->flashdata('error_alat'); ?>
    </div>
<?php endif; ?>

<div style="background: white; padding: 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; border-top: 4px solid #3c8dbc;">
    <h3><i class="fas fa-plus-circle"></i> Tambah Instrumen Alat Baru</h3>
    <p style="font-size: 13px; color: #666;">Catatan: Kode alat akan dibuat secara otomatis oleh sistem berdasarkan kategori pilihan Anda.</p>
    
    <form action="<?php echo base_url('admin/simpan_alat'); ?>" method="post" enctype="multipart/form-data">
        <p>
            <label>Nama Alat Lab :</label><br>
            <input type="text" name="nama_alat" placeholder="Contoh: Jangka Sorong Mitutoyo" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
        </p>
        <p>
            <label>Kategori Alat :</label><br>
            <select name="kategori_alat" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
                <option value="">-- Pilih Kategori --</option>
                <option value="Perawatan">Perawatan</option>
                <option value="Pengujian">Pengujian</option>
                <option value="Kelistrikan">Kelistrikan</option>
                <option value="Pneumatik">Pneumatik</option>
            </select>
        </p>
        <p>
            <label>Spesifikasi Detail :</label><br>
            <textarea name="spesifikasi" placeholder="Contoh: Ketelitian 0.02mm, Panjang 150mm" rows="3" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;"></textarea>
        </p>
        <p>
            <label>Jumlah Stok Total :</label><br>
            <input type="number" name="stok_total" min="1" placeholder="Masukkan Angka Kuantitas" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
        </p>
        <p>
            <label>Kondisi Awal Fisik :</label><br>
            <select name="kondisi_alat" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
                <option value="baik">Kondisi Baik / Layak Pakai</option>
                <option value="rusak_ringan">Rusak Ringan (Butuh Kalibrasi)</option>
            </select>
        </p>
        <p>
            <label>Foto Alat Lab :</label><br>
            <input type="file" name="foto_alat" accept="image/*" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;">
            <small style="color: #666; font-size: 11px;">Format: JPG/PNG, Maksimal 2MB (Boleh dikosongkan jika belum ada foto)</small>
        </p>
        <p>
            <button type="submit" style="background: #3c8dbc; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                <i class="fas fa-save"></i> Simpan ke Inventaris
            </button>
        </p>
    </form>
</div>

<hr>

<div style="background: white; padding: 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
    <h3><i class="fas fa-list"></i> Daftar Inventaris Alat Saat Ini</h3>
    
    <div style="display: flex; gap: 15px; margin-top: 15px; margin-bottom: 15px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <label style="font-size: 13px; font-weight: bold; color: #555;">Cari Nama atau Kode Alat:</label>
            <input type="text" id="inputCariAlat" onkeyup="jalankanFilterAlat()" placeholder="Ketik nama atau kode alat di sini..." style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="width: 200px;">
            <label style="font-size: 13px; font-weight: bold; color: #555;">Filter Kategori:</label>
            <select id="selectFilterKategori" onchange="jalankanFilterAlat()" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
                <option value="SEMUA">-- Tampilkan Semua --</option>
                <option value="Perawatan">Perawatan</option>
                <option value="Pengujian">Pengujian</option>
                <option value="Kelistrikan">Kelistrikan</option>
                <option value="Pneumatik">Pneumatik</option>
            </select>
        </div>
    </div>
    
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f4f6f9;">
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Kode</th>
                <th>Nama Alat</th>
                <th>Kategori</th>
                <th>Spesifikasi</th>
                <th>Stok Total</th>
                <th>Stok Tersedia</th>
                <th>Kondisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="bodyTabelAlat">
            <?php if(!empty($list_alat)): ?>
                <?php $no = 1; foreach($list_alat as $alat): ?>
                    <tr class="baris-alat" data-kategori="<?php echo !empty($alat['kategori_alat']) ? $alat['kategori_alat'] : '-'; ?>">
                        <td><?php echo $no++; ?></td>
                        <td style="text-align: center;">
                            <?php 
                            $path_foto = 'assets/uploads/alat/' . $alat['foto_alat']; 
                            if(!empty($alat['foto_alat']) && file_exists($path_foto)): ?>
                                <img src="<?php echo base_url($path_foto); ?>" alt="Foto Alat" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            <?php else: ?>
                                <img src="<?php echo base_url('assets/uploads/alat/default_alat.jpg'); ?>" alt="Tidak Ada Foto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; opacity: 0.7;">
                            <?php endif; ?>
                        </td>
                        <td class="target-kode"><strong style="color: #2c5282;"><?php echo $alat['kode_alat']; ?></strong></td>
                        <td class="target-nama"><?php echo $alat['nama_alat']; ?></td>
                        <td>
                            <span style="background: #e1ecf4; color: #2c5282; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                <?php echo !empty($alat['kategori_alat']) ? $alat['kategori_alat'] : '-'; ?>
                            </span>
                        </td>
                        <td><?php echo $alat['spesifikasi']; ?></td>
                        <td><?php echo $alat['stok_total']; ?></td>
                        <td style="color: #3c8dbc; font-weight: bold;"><?php echo $alat['stok_tersedia']; ?></td>
                        <td>
                            <span style="text-transform: uppercase; font-size: 12px; font-weight: bold; color: <?php echo ($alat['kondisi_alat'] == 'baik') ? '#5cb85c' : '#f0ad4e'; ?>;">
                                <?php echo str_replace('_', ' ', $alat['kondisi_alat']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo base_url('admin/edit_alat/'.$alat['id_alat']); ?>" style="color: #f0ad4e; text-decoration: none; font-weight: bold; margin-right: 15px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?php echo base_url('admin/hapus_alat/'.$alat['id_alat']); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus alat ini dari inventaris lab?');" style="color: #d9534f; text-decoration: none; font-weight: bold;">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; color: #999;">Belum ada data instrumen alat di dalam database.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div> 
</div> 

<script>
function jalankanFilterAlat() {
    let keyword = document.getElementById("inputCariAlat").value.toLowerCase();
    let kategoriPilihan = document.getElementById("selectFilterKategori").value;
    let daftarBaris = document.getElementsByClassName("baris-alat");
    
    for (let i = 0; i < daftarBaris.length; i++) {
        let baris = daftarBaris[i];
        
        let namaAlat = baris.querySelector(".target-nama").textContent.toLowerCase();
        let kodeAlat = baris.querySelector(".target-kode").textContent.toLowerCase();
        let kategoriAlat = baris.getAttribute("data-kategori");
        
        let cocokKeyword = namaAlat.includes(keyword) || kodeAlat.includes(keyword);
        let cocokKategori = (kategoriPilihan === "SEMUA") || (kategoriAlat === kategoriPilihan);
        
        if (cocokKeyword && cocokKategori) {
            baris.style.display = "";
        } else {
            baris.style.display = "none";
        }
    }
}
</script>

<?php $this->load->view('templates/footer'); ?>
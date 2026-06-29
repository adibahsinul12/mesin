<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/sidebar'); ?>

<h2 style="margin-top: 0; color: #333;">Profil Pengguna</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Kelola pengaturan kata sandi Anda sesuai Use Case sistem dan unggah foto identitas resmi sesuai arahan laboratorium Teknik Mesin.</p>

<?php if($this->session->flashdata('sukses_profil')): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; border-left: 5px solid #28a745; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
        <?php echo $this->session->flashdata('sukses_profil'); ?>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error_profil')): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; border-left: 5px solid #dc3545; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
        <?php echo $this->session->flashdata('error_profil'); ?>
    </div>
<?php endif; ?>
<div style="display: flex; gap: 30px; margin-top: 20px;">
    
    <div style="flex: 1; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center; border-top: 4px solid #3c8dbc; height: fit-content;">
        
        <?php if(!empty($user['foto_profil']) && $user['foto_profil'] != 'default.jpg' && file_exists('./assets/uploads/profil/' . $user['foto_profil'])): ?>
            <img src="<?php echo base_url('assets/uploads/profil/' . $user['foto_profil']); ?>" alt="Foto Profil" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #3c8dbc; margin-bottom: 15px;">
        <?php else: ?>
            <div style="width: 120px; height: 120px; background: #eee; border-radius: 50%; margin: 0 auto 15px auto; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #999; font-weight: bold; border: 3px solid #3c8dbc;">
                <?php echo strtoupper(substr($nama_lengkap, 0, 1)); ?>
            </div>
        <?php endif; ?>

        <h3 style="margin: 10px 0 5px 0; color: #333;"><?php echo htmlspecialchars($nama_lengkap); ?></h3>
        <p style="margin: 0; color: #777; font-size: 13px; text-transform: uppercase; font-weight: bold; background: #e8f0fe; display: inline-block; padding: 3px 10px; border-radius: 20px; color: #3c8dbc;">
            <?php echo htmlspecialchars($role); ?>
        </p>
        <p style="margin-top: 15px; font-size: 12px; color: #999;">Status Akun: <span style="color: #5cb85c; font-weight: bold;">AKTIF</span></p>
    </div>

    <div style="flex: 2; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px;">Perbarui Informasi Akun</h3>
        
        <form action="<?php echo base_url('peminjaman/update_profil'); ?>" method="post" enctype="multipart/form-data">
            
            <table cellpadding="8" cellspacing="0" style="width: 100%; font-size: 15px; color: #333; margin-bottom: 15px;">
                <tr>
                    <td width="35%" style="color: #777; font-weight: bold; padding-left: 0;">Nama Lengkap</td>
                    <td width="5%">:</td>
                    <td><strong><?php echo htmlspecialchars($nama_lengkap); ?></strong></td>
                </tr>
                <tr>
                    <td style="color: #777; font-weight: bold; padding-left: 0;">
                        <?php echo (strtolower($role) === 'dosen' || $this->session->userdata('role') === 'dosen') ? 'NIDN / NIP' : 'Nomor Induk / NIM'; ?>
                    </td>
                    <td>:</td>
                    <td><code><?php echo htmlspecialchars($nomor_induk); ?></code></td>
                </tr>
                <tr>
                    <td style="color: #777; font-weight: bold; padding-left: 0;">Program Studi</td>
                    <td>:</td>
                    <td><?php echo !empty($program_studi) ? htmlspecialchars($program_studi) : '<span style="color:#aaa; font-style:italic;">Tidak Ada (Rumpun Dosen)</span>'; ?></td>
                </tr>
                
                <?php if (strtolower($role) === 'mahasiswa' || $this->session->userdata('role') === 'mahasiswa'): ?>
                <tr>
                    <td style="color: #777; font-weight: bold; padding-left: 0;">Kelas / Angkatan</td>
                    <td>:</td>
                    <td><strong><?php echo !empty($user['kelas']) ? htmlspecialchars($user['kelas']) : htmlspecialchars($this->session->userdata('kelas')); ?></strong></td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td style="color: #777; font-weight: bold; padding-left: 0;">Otoritas Peminjaman</td>
                    <td>:</td>
                    <td><span style="color: #5cb85c; font-weight: bold;">✔ Terverifikasi Otomatis (Rumpun Mesin)</span></td>
                </tr>
            </table>

            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; color: #333; font-size: 14px;">Ganti Kata Sandi / Password Baru:</label>
                <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" style="width: 100%; padding: 9px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <small style="color: #888; font-style: italic;">*Biarkan kolom ini kosong jika Anda hanya ingin memperbarui berkas foto profil saja.</small>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; color: #333; font-size: 14px;">Unggah Foto Profil Baru:</label>
                <input type="file" name="foto_profil" accept="image/*" style="width: 100%; margin-top: 5px;">
                <br><small style="color: #888;">Format berkas yang didukung: JPG, JPEG, PNG (Maksimal ukuran file 2MB).</small>
            </div>

            <div style="text-align: right;">
                <button type="submit" style="background: #3c8dbc; color: white; padding: 11px 22px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    Simpan Perubahan Akun
                </button>
            </div>

        </form>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>
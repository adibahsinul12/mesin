<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Internal - Lab Mesin Poltesa</title>
</head>
<body>
    <h2>Pendaftaran Akun Khusus (Staff Admin / Kepala Lab)</h2>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_registrasi_internal'); ?>" method="post">
        <label>Nomor Induk (NIDN / NIP / ID Petugas):</label><br>
        <input type="text" name="nomor_induk" placeholder="Masukkan Nomor Induk" required><br><br>
        
        <label>Nama Lengkap & Gelar:</label><br>
        <input type="text" name="nama_lengkap" placeholder="Contoh: Nama, S.T., M.T." required><br><br>
        
        <label>Email Kerja:</label><br>
        <input type="email" name="email" placeholder="Contoh: staff@poltesa.ac.id" required><br><br>
        
        <label>Password Akun:</label><br>
        <input type="password" name="password" placeholder="Buat Password" required><br><br>
        
        <label>Otoritas Hak Akses (Role):</label><br>
        <select name="role" required>
            <option value="staff_admin">Staff Admin (Pengelola Lab)</option>
            <option value="kepala_lab">Kepala Laboratorium Teknik Mesin</option>
        </select><br><br>
        
        <div style="background-color: #fff3cd; padding: 15px; border: 1px solid #ffeeba; display: inline-block; margin-bottom: 15px;">
            <label style="color: #856404; font-weight: bold;">Token Otorisasi Internal Lab:</label><br>
            <input type="password" name="token_keamanan" placeholder="Masukkan Master Token Rahasia" required><br>
            <small style="color: #666;">*Minta token otorisasi ke Kepala Laboratorium untuk mendaftarkan akun internal baru.</small>
        </div>
        <br>
        
        <button type="submit">Daftarkan Akun Internal</button>
    </form>
    
    <br>
    <p><a href="<?php echo base_url('auth'); ?>">Kembali ke Login</a></p>
</body>
</html>
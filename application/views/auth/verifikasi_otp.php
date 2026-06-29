<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP - Lab Mesin Poltesa</title>
</head>
<body>
    <h2>Verifikasi Email Aktif Anda</h2>
    <p>Sistem telah mengirimkan 6 digit kode OTP ke alamat Gmail yang Anda daftarkan.</p>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan_otp'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_verifikasi_otp'); ?>" method="post">
        <p>
            <label>Masukkan Kode OTP :</label><br>
            <input type="text" name="otp_mahasiswa" maxlength="6" placeholder="Contoh: 123456" style="font-size:18px; letter-spacing: 3px; text-align:center;" required>
        </p>
        <button type="submit">Verifikasi & Daftarkan Akun</button>
    </form>
    
    <hr>
    <p>Salah memasukkan email? <a href="<?php echo base_url('auth/registrasi'); ?>">Kembali ke Form Registrasi</a></p>
</body>
</html>
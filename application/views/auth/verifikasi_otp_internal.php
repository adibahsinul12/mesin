<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP Internal - Lab Mesin Poltesa</title>
</head>
<body>

    <h2>Verifikasi Email Otoritas Internal</h2>
    <p>Sistem telah mengirimkan 6 digit kode OTP ke alamat Gmail internal yang Anda daftarkan.</p>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan_otp'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_verifikasi_otp_internal'); ?>" method="post">
        <p>
            <label>Masukkan Kode OTP Otoritas :</label><br>
            <input type="text" name="otp_internal" maxlength="6" placeholder="Contoh: 123456" style="font-size:18px; letter-spacing: 3px; text-align:center;" required>
        </p>
        <p>
            <button type="submit">Verifikasi & Aktifkan Otoritas Akun</button>
        </p>
    </form>
    
    <hr>
    
    <p>Salah memasukkan email petugas? <a href="<?php echo base_url('auth/registrasi_internal'); ?>">Kembali ke Form Registrasi Internal</a></p>

</body>
</html>
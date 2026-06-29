<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password - Lab Mesin Poltesa</title>
</head>
<body>
    <h2>Pemulihan Kata Sandi Akun</h2>
    <p>Masukkan alamat Gmail yang terdaftar pada akun Anda. Sistem akan mengirimkan kode OTP untuk mereset password.</p>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_forgot_password'); ?>" method="post">
        <p>
            <label>Masukkan Email Gmail Anda:</label><br>
            <input type="email" name="email" placeholder="xxx@gmail.com" required style="width: 250px; padding: 5px;">
        </p>
        <button type="submit">Kirim Kode Reset</button>
    </form>
    
    <hr>
    <p><a href="<?php echo base_url('auth'); ?>">Kembali ke Login</a></p>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Lab Mesin Poltesa</title>
</head>
<body>
    <h2>Buat Kata Sandi Baru</h2>
    <p>Silakan periksa kotak masuk Gmail Anda, lalu masukkan kode OTP beserta password baru yang ingin Anda gunakan.</p>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan_reset'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_reset_password'); ?>" method="post">
        <p>
            <label>Masukkan Kode OTP Reset:</label><br>
            <input type="text" name="otp_reset" maxlength="6" placeholder="6 Digit Angka" required style="font-size:16px; letter-spacing: 2px; text-align:center;">
        </p>
        <p>
            <label>Kata Sandi Baru:</label><br>
            <input type="password" name="password_baru" placeholder="Minimal 6 karakter" required>
        </p>
        <p>
            <button type="submit">Perbarui Kata Sandi</button>
        </p>
    </form>
    
    <hr>
    
    <p>
        <?php 
        $data_reset = $this->session->userdata('temp_reset'); 
        if (isset($data_reset['role_reset']) && ($data_reset['role_reset'] == 'staff_admin' || $data_reset['role_reset'] == 'kepala_lab')) :
        ?>
            <a href="<?php echo base_url('auth/login_internal'); ?>">Batal dan Kembali ke Login Internal</a>
        <?php else : ?>
            <a href="<?php echo base_url('auth'); ?>">Batal dan Kembali ke Login Umum</a>
        <?php endif; ?>
    </p>
</body>
</html>
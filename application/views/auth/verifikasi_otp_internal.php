<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP Internal - Lab Mesin Poltesa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        h2 { color: #333; text-align: center; }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin-bottom: 15px;
        }
        .info-email {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 24px;
            letter-spacing: 5px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover { background: #0056b3; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #007bff; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .resend-link { text-align: center; margin-top: 10px; }
        .resend-link a { color: #28a745; text-decoration: none; }
        .resend-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>🔐 Verifikasi Email Otoritas Internal</h2>
    
    <div class="info-email">
        <strong>📧 Email:</strong> <?= isset($email) ? $email : 'tidak tersedia' ?>
    </div>
    
    <p>Sistem telah mengirimkan 6 digit kode OTP ke alamat Gmail internal yang Anda daftarkan.</p>
    
    <!-- TAMPILKAN PESAN ERROR ATAU SUCCESS -->
    <?php if($this->session->flashdata('pesan_otp')): ?>
        <div class="alert-danger">
            ❌ <?= $this->session->flashdata('pesan_otp') ?>
        </div>
    <?php endif; ?>
    
    <?php if($this->session->flashdata('pesan_sukses_otp')): ?>
        <div class="alert-success">
            ✅ <?= $this->session->flashdata('pesan_sukses_otp') ?>
        </div>
    <?php endif; ?>
    
    <form action="<?= base_url('auth/proses_verifikasi_otp_internal') ?>" method="post">
        <p>
            <label>Masukkan Kode OTP Otoritas :</label>
            <!-- PERBAIKAN: name="otp" (bukan otp_internal) -->
            <input type="text" name="otp" maxlength="6" placeholder="Contoh: 123456" required>
            <small style="display:block; margin-top:5px; color:#666;">Masukkan 6 digit kode OTP yang dikirim ke email Anda</small>
        </p>
        <p>
            <button type="submit">✅ Verifikasi & Aktifkan Otoritas Akun</button>
        </p>
    </form>
    
    <div class="resend-link">
        <a href="<?= base_url('auth/resend_otp_internal') ?>">🔄 Kirim Ulang Kode OTP</a>
    </div>
    
    <hr>
    
    <div class="back-link">
        <p>Salah memasukkan email petugas? <a href="<?= base_url('auth/registrasi_internal') ?>">Kembali ke Form Registrasi Internal</a></p>
    </div>
</div>

</body>
</html>
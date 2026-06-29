<!DOCTYPE html>
<html>
<head>
    <title>Login - Lab Mesin Poltesa</title>
    <style>
        .btn-switch { padding: 8px 16px; cursor: pointer; border: 1px solid #ccc; background: #f0f0f0; }
        .active { background: #333; color: #fff; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Login Sistem Peminjaman Alat Lab Mesin</h2>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan'); ?></p>
    
    <div style="margin-bottom: 20px;">
        <button type="button" id="btn-mhs" class="btn-switch active" onclick="switchRole('mahasiswa')">Mahasiswa</button>
        <button type="button" id="btn-internal" class="btn-switch" onclick="switchRole('internal')">Dosen</button>
    </div>
    
    <form action="<?php echo base_url('auth/proses_login'); ?>" method="post">
        <p>
            <label id="label-induk">Nomor Induk Mahasiswa (NIM):</label><br>
            <input type="text" name="nomor_induk" id="input-induk" placeholder="Masukkan NIM Anda (Awalan 320/420...)" required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" placeholder="Masukkan Password" required>
        </p>
        <p>
            <a href="<?php echo base_url('auth/forgot_password'); ?>">Lupa Kata Sandi?</a>
        </p>
        <button type="submit">Masuk ke Sistem</button>
    </form>
    
    <hr>
    
    <p>Pengguna baru? <a href="<?php echo base_url('auth/registrasi'); ?>">Daftar Akun Di Sini</a></p>
    
    <div style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
        <p style="font-size: 12px; color: #777;">
            Akses khusus Staff Admin atau Kepala Laboratorium? 
            <a href="<?php echo base_url('auth/login_internal'); ?>" style="font-weight: bold; text-decoration: none;">
                Klik di sini untuk Login Internal
            </a>
        </p>
    </div>

    <script>
        function switchRole(role) {
            var label = document.getElementById('label-induk');
            var input = document.getElementById('input-induk');
            var btnMhs = document.getElementById('btn-mhs');
            var btnInternal = document.getElementById('btn-internal');
            
            if (role === 'mahasiswa') {
                label.innerText = "Nomor Induk Mahasiswa (NIM):";
                input.placeholder = "Masukkan NIM Anda (Awalan 320/420...)";
                btnMhs.classList.add('active');
                btnInternal.classList.remove('active');
            } else {
                label.innerText = "Nomor Induk Dosen / Pegawai (NIDN / NIP):";
                input.placeholder = "Masukkan NIDN atau NIP Anda";
                btnInternal.classList.add('active');
                btnMhs.classList.remove('active');
            }
        }
    </script>
</body>
</html>
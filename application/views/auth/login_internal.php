<div class="login-box"> 
    <h2>Login Otoritas Internal</h2>
    <p>Staff Admin & Kepala Laboratorium</p>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_login'); ?>" method="post">
        <p>
            <label>NIP / Nomor Induk Pegawai:</label><br>
            <input type="text" name="nomor_induk" placeholder="Masukkan NIP" required>
        </p>
        <p>
            <label>Kata Sandi:</label><br>
            <input type="password" name="password" placeholder="Masukkan Kata Sandi" required>
        </p>
        
        <p>
            <a href="<?php echo base_url('auth/forgot_password'); ?>">Lupa Kata Sandi?</a>
        </p>
        
        <p>
            <button type="submit" class="btn">Login Internal</button>
        </p>
    </form>

    <hr>

    <p style="font-size: 13px; color: #666;">
        Belum memiliki akun otoritas petugas? 
        <br>
        Registrasi khusus internal: <a href="<?php echo base_url('auth/registrasi_internal'); ?>">Klik di sini</a>
    </p>
    
    <p style="font-size: 12px; margin-top: 20px;">
        <a href="<?php echo base_url('auth'); ?>">&larr; Kembali ke Login Umum</a>
    </p>
</div>
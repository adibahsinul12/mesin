<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Akun - Lab Mesin Poltesa</title>
</head>
<body>
    <h2>Pendaftaran Akun Lab Teknik Mesin</h2>
    
    <p style="color: red; font-weight: bold;"><?php echo $this->session->flashdata('pesan'); ?></p>
    
    <form action="<?php echo base_url('auth/proses_registrasi'); ?>" method="post">
        <label>Mendaftar Sebagai:</label><br>
        <select name="role" id="regRole" onchange="pilihRoleRegistrasi()" required>
            <option value="mahasiswa" <?php echo ($this->session->flashdata('role_terpilih') == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
            <option value="dosen" <?php echo ($this->session->flashdata('role_terpilih') == 'dosen') ? 'selected' : ''; ?>>Dosen</option>
        </select><br><br>

        <label id="labelNomorInduk">Nomor Induk Mahasiswa (NIM):</label><br>
        <input type="text" name="nomor_induk" id="inputNomorInduk" placeholder="Masukkan nomor induk..." required><br><br>
        
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap Anda" required><br><br>
        
        <label>Email Resmi:</label><br>
        <input type="email" name="email" placeholder="Contoh: user@gmail.com" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" placeholder="Buat Password Baru" required><br><br>
        
        <label>Program Studi:</label><br>
        <select name="program_studi" required>
            <option value="Teknik Mesin">Teknik Mesin</option>
            <option value="Teknik Mesin Pertanian">Teknik Mesin Pertanian</option>
        </select><br><br>

        <div id="groupKelas">
            <label>Kelas / Angkatan:</label><br>
            <input type="text" name="kelas" placeholder="Contoh: 3A / 2024"><br><br>
        </div>
        
        <button type="submit">Daftar Sekarang</button>
    </form>
    
    <br>
    <p>Sudah punya akun? <a href="<?php echo base_url('auth'); ?>">Kembali ke Halaman Login</a></p>

    <script>
    function pilihRoleRegistrasi() {
        let role = document.getElementById("regRole").value;
        let labelNomorInduk = document.getElementById("labelNomorInduk");
        let inputNomorInduk = document.getElementById("inputNomorInduk");
        let groupKelas = document.getElementById("groupKelas");
        let inputKelas = groupKelas.querySelector('input');

        if (role === 'dosen') {
            // Jika dosen yang dipilih
            labelNomorInduk.innerText = "NIP / NIDN (Nomor Induk Dosen):";
            inputNomorInduk.placeholder = "Masukkan NIP / NIDN Anda...";
            groupKelas.style.display = "none"; // Sembunyikan inputan kelas
            inputKelas.value = "";             // Kosongkan nilainya agar masuk DB sebagai NULL
        } else {
            // Jika mahasiswa yang dipilih
            labelNomorInduk.innerText = "Nomor Induk Mahasiswa (NIM):";
            inputNomorInduk.placeholder = "Masukkan nomor induk...";
            groupKelas.style.display = "block"; // Tampilkan kembali inputan kelas
        }
    }

    // Jalankan fungsi sekali saat halaman dimuat pertama kali untuk memastikan posisi awal
    document.addEventListener("DOMContentLoaded", function() {
        pilihRoleRegistrasi();
    });
    </script>
</body>
</html>
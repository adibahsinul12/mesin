<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peminjaman extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cek Session Login
        if (!$this->session->userdata('role')) {
            $this->session->set_flashdata('pesan', 'Silakan login terlebih dahulu.');
            redirect('auth');
        }
<<<<<<< HEAD
        
        // Hanya untuk Mahasiswa dan Dosen
        $role = $this->session->userdata('role');
        if (!in_array($role, ['mahasiswa', 'dosen'])) {
            show_error('Akses ditolak! Halaman ini hanya untuk Mahasiswa dan Dosen.', 403);
        }
        
        $this->load->model('M_Peminjaman');
        $this->load->model('M_Alat');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();
    }

    // =========================================================
    // 1. MENU: HALAMAN UTAMA (Dashboard Ringkasan)
    // =========================================================
    public function index() {
        $data['title']         = 'Dashboard Peminjaman';
=======
        $this->load->model('M_Peminjaman');
    }

    // 1. MENU: HALAMAN UTAMA (Dashboard Ringkasan)
    public function index() {
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');
<<<<<<< HEAD
        $data['kelas']         = $this->session->userdata('kelas');
        $data['foto_profil']   = $this->session->userdata('foto_profil');

        // Ambil ID User dari session login saat ini
        $id_user_login = $this->session->userdata('id_user');

        // Ambil data statistik dari model
        $statistik = $this->M_Peminjaman->get_statistik_peminjam($id_user_login);
        
        $data['total_aktif']     = $statistik['aktif'];
        $data['total_selesai']   = $statistik['selesai'];
        $data['total_terlambat'] = $statistik['terlambat'];
        $data['total_pending']   = $statistik['pending'];
        
        // Ambil peminjaman terbaru
        $data['peminjaman_terbaru'] = $this->M_Peminjaman->get_peminjaman_terbaru($id_user_login, 5);
        
        // Ambil total alat tersedia
        $data['total_alat'] = $this->M_Alat->count_tersedia();

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/dashboard', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 2. MENU: KATALOG & PINJAM
    // =========================================================
    public function katalog() {
        $data['title']         = 'Katalog Alat Lab';
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['role']          = $this->session->userdata('role');
        $data['foto_profil']   = $this->session->userdata('foto_profil');

        // Ambil data katalog dari model
        $data['katalog_alat']  = $this->M_Alat->get_all_with_stok();

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/v_katalog_pinjam', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 3. MENU: FORM PINJAM (Detail Alat)
    // =========================================================
    public function pinjam($id_alat = NULL) {
        if (empty($id_alat)) {
            redirect('peminjaman/katalog');
            return;
        }

        $data['title'] = 'Form Peminjaman Alat';
        $data['nama_lengkap'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        // Ambil data alat
        $data['alat'] = $this->M_Alat->get_by_id($id_alat);
        
        if (empty($data['alat'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/v_form_pinjam', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 4. ACTION: PROSES PINJAM
    // =========================================================
    public function proses_pinjam() {
        // Validasi form
        $this->form_validation->set_rules('id_alat', 'ID Alat', 'required|numeric');
        $this->form_validation->set_rules('jumlah_pinjam', 'Jumlah Pinjam', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('tanggal_pinjam', 'Tanggal Pinjam', 'required');
        $this->form_validation->set_rules('tanggal_kembali_rencana', 'Tanggal Kembali', 'required');
        $this->form_validation->set_rules('tujuan_keperluan', 'Tujuan Keperluan', 'required|min_length[5]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan_error', validation_errors());
            redirect('peminjaman/katalog');
            return;
        }

        $id_alat = $this->input->post('id_alat');
        $jumlah = $this->input->post('jumlah_pinjam');
        $tanggal_pinjam = $this->input->post('tanggal_pinjam');
        $tanggal_kembali = $this->input->post('tanggal_kembali_rencana');
        $tujuan = $this->input->post('tujuan_keperluan');
        $id_user_login = $this->session->userdata('id_user');

        // Cek user
        $user = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
        if (!$user) {
            $this->session->set_flashdata('pesan_error', 'Data pengguna tidak ditemukan.');
=======

        // Ambil ID User dari session login saat ini untuk hitung statistik secara real-time
        $id_user_login = $this->session->userdata('id_user');

        // Ambil data hitungan statistik dari model M_Peminjaman
        $statistik = $this->M_Peminjaman->get_statistik_peminjam($id_user_login);
        
        // Masukkan ke array data untuk dikirim ke view dashboard
        $data['total_aktif']     = $statistik['aktif'];
        $data['total_selesai']   = $statistik['selesai'];
        $data['total_terlambat'] = $statistik['terlambat'];

        $this->load->view('peminjaman/dashboard', $data);
    }

    // 2. MENU: KATALOG & PINJAM (Formulir Transaksi terpisah)
    public function katalog() {
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');

        // Ambil data katalog dari model
        $data['katalog_alat']  = $this->M_Peminjaman->get_katalog_alat();

        $this->load->view('peminjaman/v_katalog_pinjam', $data);
    }

    // =========================================================================
    // ACTION: PROSES TRANSAKSI - SISTEM MEMUTUSKAN OTOMATIS (REVISI DOSEN)
    // =========================================================================
    public function proses_ajukan() {
        $id_alat_dipilih = $this->input->post('id_alat');
        $tanggal_pinjam  = $this->input->post('tanggal_pinjam');
        $tanggal_kembali = $this->input->post('tanggal_kembali_rencana');
        $tujuan          = $this->input->post('tujuan_keperluan');
        
        $id_user_login   = $this->session->userdata('id_user');

        // Batasan Awal: Pastikan ada item yang dicentang
        if (empty($id_alat_dipilih)) {
            $this->session->set_flashdata('pesan_error', 'Gagal! Anda belum memilih atau mencentang alat yang ingin dipinjam.');
            redirect('peminjaman/katalog');
            return;
        }

        // 1. SYARAT DOSEN: Verifikasi keberadaan akun di database
        $user = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
        if (!$user) {
            $this->session->set_flashdata('pesan_error', 'Sistem Menolak: Data pengguna tidak ditemukan.');
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
            redirect('auth');
            return;
        }

<<<<<<< HEAD
        // Cek stok alat
        $alat = $this->M_Alat->get_by_id($id_alat);
        if (!$alat) {
            $this->session->set_flashdata('pesan_error', 'Alat tidak ditemukan.');
=======
        // 2. SYARAT DOSEN: Harus murni dari rumpun Teknik Mesin
        if (strpos(strtolower($user['program_studi']), 'mesin') === false) {
            $this->session->set_flashdata('pesan_error', 'Sistem Menolak: Fitur ini hanya dikhususkan untuk rumpun mahasiswa Teknik Mesin.');
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
            redirect('peminjaman/katalog');
            return;
        }

<<<<<<< HEAD
        if ($jumlah > $alat['stok_tersedia']) {
            $this->session->set_flashdata('pesan_error', 'Stok tidak mencukupi! Tersedia: ' . $alat['stok_tersedia']);
            redirect('peminjaman/katalog');
            return;
        }

        // Generate kode transaksi
=======
        // 3. SYARAT DOSEN: Validasi ketersediaan stok barang sebelum sistem memutuskan
        foreach ($id_alat_dipilih as $id_alat) {
            $jumlah_diminta = intval($this->input->post("jumlah_pinjam_")[$id_alat]);
            $alat = $this->db->get_where('alat', ['id_alat' => $id_alat])->row_array();

            if ($jumlah_diminta <= 0) {
                $this->session->set_flashdata('pesan_error', 'Gagal! Kuantitas alat yang dipinjam minimal harus 1 unit.');
                redirect('peminjaman/katalog');
                return;
            }

            if ($jumlah_diminta > $alat['stok_tersedia']) {
                // Jika stok di lab mendadak habis atau kurang, SISTEM OTOMATIS MENOLAK
                $this->session->set_flashdata('pesan_error', 'Sistem Menolak: Stok untuk alat "' . $alat['nama_alat'] . '" tidak mencukupi atau sedang kosong.');
                redirect('peminjaman/katalog');
                return;
            }
        }

        // 4. EKSEKUSI DATA: Jika semua syarat di atas lolos, SISTEM LANGSUNG MENYETUJUI
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $kode_transaksi = 'PMJ-' . date('YmdHis') . '-' . rand(10, 99);

        // Insert ke tabel peminjaman
        $data_peminjaman = [
            'kode_peminjaman'         => $kode_transaksi,
            'id_user'                 => $id_user_login,
            'tanggal_pinjam'          => $tanggal_pinjam,
            'tanggal_kembali_rencana' => $tanggal_kembali,
            'tujuan_keperluan'        => htmlspecialchars($tujuan),
<<<<<<< HEAD
            'status_peminjaman'       => 'pending' // Menunggu persetujuan admin
=======
            'status_peminjaman'       => 'disetujui' // Langsung otomatis disetujui sistem
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        ];
        $this->db->insert('peminjaman', $data_peminjaman);
        $id_peminjaman_baru = $this->db->insert_id();

<<<<<<< HEAD
        // Insert ke detail peminjaman
        $data_detail = [
            'id_peminjaman' => $id_peminjaman_baru,
            'id_alat'       => $id_alat,
            'jumlah_pinjam' => $jumlah
        ];
        $this->db->insert('detail_peminjaman', $data_detail);

        // Kurangi stok sementara (akan dikurangi permanen saat disetujui)
        $this->M_Alat->kurangi_stok_sementara($id_alat, $jumlah);

        $this->session->set_flashdata('pesan_sukses', 'Pengajuan peminjaman berhasil dikirim! Menunggu persetujuan admin.');
        redirect('peminjaman/riwayat');
    }

    // =========================================================
    // 5. MENU: PENGEMBALIAN
    // =========================================================
    public function pengembalian() {
        $data['title'] = 'Pengembalian Alat';
        $data['nama_lengkap'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');

        $id_user_login = $this->session->userdata('id_user');
        
        // Mengambil data peminjaman yang masih aktif
        $data['pinjaman_aktif'] = $this->M_Peminjaman->get_peminjaman_aktif($id_user_login);

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/v_pengembalian', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 6. ACTION: AJUKAN PENGEMBALIAN
    // =========================================================
    public function ajukan_pengembalian($id_peminjaman = NULL) {
        if (empty($id_peminjaman)) {
            $this->session->set_flashdata('error_kembali', 'Transaksi pengembalian tidak valid.');
=======
        // Insert ke detail peminjaman & update (potong) stok_tersedia di tabel alat
        foreach ($id_alat_dipilih as $id_alat) {
            $jumlah_diminta = intval($this->input->post("jumlah_pinjam_")[$id_alat]);

            // Hitung sisa stok baru
            $alat_lama = $this->db->get_where('alat', ['id_alat' => $id_alat])->row_array();
            $stok_baru = $alat_lama['stok_tersedia'] - $jumlah_diminta;

            // Update stok fisik di tabel alat
            $this->db->where('id_alat', $id_alat);
            $this->db->update('alat', ['stok_tersedia' => $stok_baru]);

            // Simpan detail rincian barang yang dibawa
            $data_detail = [
                'id_peminjaman' => $id_peminjaman_baru,
                'id_alat'       => $id_alat,
                'jumlah_pinjam' => $jumlah_diminta
            ];
            $this->db->insert('detail_peminjaman', $data_detail);
        }

        // Notifikasi keberhasilan keputusan sistem
        $this->session->set_flashdata('pesan_sukses', 'Sistem Berhasil Menyetujui! Alokasi alat aman. Silakan menuju ke laboran untuk serah terima fisik alat.');
        redirect('peminjaman/katalog');
    }

    // =========================================================================
    // MENU & ACTION: PENGEMBALIAN ALAT (DIUBAH MENJADI ALUR PENDING KE ADMIN)
    // =========================================================================
    public function pengembalian() {
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');

        $id_user_login = $this->session->userdata('id_user');
        
        // Mengambil data peminjaman yang masih aktif (belum dikembalikan / berstatus pending_kembali)
        $data['pinjaman_aktif'] = $this->M_Peminjaman->get_peminjaman_aktif($id_user_login);

        $this->load->view('peminjaman/v_pengembalian', $data);
    }

    // DIUBAH: Fungsi ini sekarang hanya mengajukan perubahan status ke 'pending_kembali'
    // Pemotongan/pemulihan stok & log tabel pengembalian sepenuhnya dipindah ke hak akses Admin
    public function ajukan_pengembalian($id_peminjaman = NULL) {
        if (empty($id_peminjaman)) {
            $this->session->set_flashdata('error_kembali', 'Gagal! Transaksi pengembalian tidak valid.');
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
            redirect('peminjaman/pengembalian');
            return;
        }

<<<<<<< HEAD
        $id_user_login = $this->session->userdata('id_user');
        
        // Cek kepemilikan
        $peminjaman = $this->db->get_where('peminjaman', [
            'id_peminjaman' => $id_peminjaman,
            'id_user' => $id_user_login
        ])->row_array();
        
        if (!$peminjaman) {
            $this->session->set_flashdata('error_kembali', 'Data transaksi tidak ditemukan.');
=======
        // Ambil data peminjaman untuk memastikan data itu ada
        $peminjaman = $this->db->get_where('peminjaman', ['id_peminjaman' => $id_peminjaman])->row_array();
        if (!$peminjaman) {
            $this->session->set_flashdata('error_kembali', 'Gagal! Data transaksi tidak ditemukan.');
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
            redirect('peminjaman/pengembalian');
            return;
        }

<<<<<<< HEAD
        // Update status menjadi pending_kembali
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->update('peminjaman', ['status_peminjaman' => 'pending_kembali']);

        $this->session->set_flashdata('sukses_kembali', 'Permintaan pengembalian berhasil dikirim! Silakan serahkan alat ke Laboran.');
        redirect('peminjaman/pengembalian');
    }

    // =========================================================
    // 7. MENU: RIWAYAT
    // =========================================================
    public function riwayat() {
        $data['title'] = 'Riwayat Peminjaman';
        $data['nama_lengkap'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');

        $id_user_login = $this->session->userdata('id_user');

        $data['riwayat_pinjam'] = $this->M_Peminjaman->get_riwayat_peminjam($id_user_login);

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/v_riwayat', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 8. MENU: PROFIL
    // =========================================================
    public function profil() {
        $data['title'] = 'Profil Saya';
        $data['nama_lengkap'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');

        $id_user_login = $this->session->userdata('id_user');
        
        $data['user'] = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/v_profil', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 9. ACTION: UPDATE PROFIL
    // =========================================================
=======
        // Update status_peminjaman di tabel peminjaman menjadi 'pending_kembali'
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->update('peminjaman', ['status_peminjaman' => 'pending_kembali']);

        $this->session->set_flashdata('sukses_kembali', 'Permintaan pengembalian alat berhasil dikirim! Silakan serahkan alat ke Laboran/Admin untuk dilakukan pengecekan kondisi fisik.');
        redirect('peminjaman/pengembalian');
    }

    // 3. MENU: RIWAYAT (Menampilkan Log Transaksi Lengkap Nama, Prodi & Waktu)
    public function riwayat() {
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');

        // Ambil ID User login untuk memfilter data riwayat miliknya sendiri
        $id_user_login = $this->session->userdata('id_user');

        // Mengambil query JOIN dari model M_Peminjaman
        $data['riwayat_pinjam'] = $this->M_Peminjaman->get_riwayat_peminjam($id_user_login);

        $this->load->view('peminjaman/v_riwayat', $data);
    }

    // 4. MENU: PROFIL (Menampilkan Detail Akun Mahasiswa/Dosen)
    public function profil() {
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');

        $id_user_login = $this->session->userdata('id_user');
        
        // Ambil data user secara utuh dari database tabel users
        $data['user'] = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();

        $this->load->view('peminjaman/v_profil', $data);
    }

    // =========================================================================
    // ACTION: UPDATE PROFIL - UPLOAD FOTO & UBAH SANDI (PERMINTAAN DOSEN & USE CASE)
    // =========================================================================
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
    public function update_profil() {
        $id_user_login = $this->session->userdata('id_user');
        $password_baru = $this->input->post('password_baru');
        
        $data_update = [];

<<<<<<< HEAD
        // 1. Update nama jika diubah
        $nama_baru = $this->input->post('nama_lengkap');
        if (!empty($nama_baru)) {
            $data_update['nama_lengkap'] = htmlspecialchars($nama_baru);
        }

        // 2. Update password jika diisi
=======
        // 1. PROSES UBAH SANDI (Jika input password diisi oleh user)
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        if (!empty($password_baru)) {
            $data_update['password'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

<<<<<<< HEAD
        // 3. Upload foto profil
        if (!empty($_FILES['foto_profil']['name'])) {
            $config['upload_path']   = './assets/uploads/profil/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 2048;
            $config['file_name']     = 'user_' . $id_user_login . '_' . time();

            // Buat folder jika belum ada
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

=======
        // 2. PROSES UPLOAD FOTO PROFIL (Menggunakan Library Upload CodeIgniter)
        if (!empty($_FILES['foto_profil']['name'])) {
            $config['upload_path']   = './assets/uploads/profil/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 2048; // Batas maksimal 2MB
            $config['file_name']     = 'user_' . $id_user_login . '_' . time();

>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_profil')) {
                $upload_data = $this->upload->data();
                $data_update['foto_profil'] = $upload_data['file_name'];
                
<<<<<<< HEAD
                // Hapus foto lama
                $user_lama = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
                if ($user_lama['foto_profil'] != 'default.jpg' && 
                    file_exists('./assets/uploads/profil/' . $user_lama['foto_profil'])) {
=======
                // Hapus foto lama dari server jika bukan default.jpg agar tidak memenuhi memori
                $user_lama = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
                if ($user_lama['foto_profil'] != 'default.jpg' && file_exists('./assets/uploads/profil/' . $user_lama['foto_profil'])) {
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
                    unlink('./assets/uploads/profil/' . $user_lama['foto_profil']);
                }
            } else {
                $this->session->set_flashdata('error_profil', 'Gagal upload foto: ' . $this->upload->display_errors('', ''));
                redirect('peminjaman/profil');
                return;
            }
        }

<<<<<<< HEAD
        // 4. Eksekusi update
        if (!empty($data_update)) {
            $this->db->where('id_user', $id_user_login);
            $this->db->update('users', $data_update);
            
            // Update session
            foreach ($data_update as $key => $value) {
                $this->session->set_userdata($key, $value);
            }
            
            $this->session->set_flashdata('sukses_profil', 'Profil berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error_profil', 'Tidak ada perubahan yang disimpan.');
=======
        // 3. EKSEKUSI UPDATE KE DATABASE
        if (!empty($data_update)) {
            $this->db->where('id_user', $id_user_login);
            $this->db->update('users', $data_update);
            $this->session->set_flashdata('sukses_profil', 'Profil Anda berhasil diperbarui oleh sistem!');
        } else {
            $this->session->set_flashdata('error_profil', 'Tidak ada data atau perubahan yang disimpan.');
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        }

        redirect('peminjaman/profil');
    }

<<<<<<< HEAD
    // =========================================================
    // 10. ACTION: CETAK BUKTI
    // =========================================================
    public function cetak($id_peminjaman) {
        $id_user_login = $this->session->userdata('id_user');

        // Ambil data peminjaman
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi, users.kelas');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.id_peminjaman', $id_peminjaman);
        $this->db->where('peminjaman.id_user', $id_user_login);
=======
    // =========================================================================
    // ACTION: CETAK BUKTI PEMINJAMAN & PENGEMBALIAN (MEMENUHI ALUR USE CASE DIAGRAM)
    // =========================================================================
    public function cetak($id_peminjaman) {
        $id_user_login = $this->session->userdata('id_user');

        // 1. Ambil data induk peminjaman & data mahasiswa (JOIN)
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.id_peminjaman', $id_peminjaman);
        $this->db->where('peminjaman.id_user', $id_user_login); // Proteksi keamanan data
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $data['transaksi'] = $this->db->get()->row_array();

        if (empty($data['transaksi'])) {
            show_404();
            return;
        }

<<<<<<< HEAD
        // Ambil detail alat
=======
        // 2. Ambil rincian detail item alat yang ada dalam satu id peminjaman tersebut
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $this->db->select('detail_peminjaman.*, alat.nama_alat, alat.kode_alat, alat.spesifikasi');
        $this->db->from('detail_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('detail_peminjaman.id_peminjaman', $id_peminjaman);
        $data['daftar_alat'] = $this->db->get()->result_array();

<<<<<<< HEAD
        $this->load->view('peminjaman/v_cetak_bukti', $data);
    }

    // =========================================================
    // 11. DETAIL PEMINJAMAN
    // =========================================================
    public function detail($id_peminjaman) {
        $id_user_login = $this->session->userdata('id_user');

        $data['title'] = 'Detail Peminjaman';
        $data['nama_lengkap'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');

        $data['peminjaman'] = $this->M_Peminjaman->get_detail_peminjaman($id_peminjaman, $id_user_login);
        
        if (empty($data['peminjaman'])) {
            show_404();
        }

        $data['detail_alat'] = $this->M_Peminjaman->get_detail_alat($id_peminjaman);

        $this->load->view('templates/header', $data);
        $this->load->view('peminjaman/v_detail', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // 12. BATAL PINJAMAN (jika masih pending)
    // =========================================================
    public function batal($id_peminjaman) {
        $id_user_login = $this->session->userdata('id_user');

        // Cek peminjaman milik user dan status pending
        $peminjaman = $this->db->get_where('peminjaman', [
            'id_peminjaman' => $id_peminjaman,
            'id_user' => $id_user_login,
            'status_peminjaman' => 'pending'
        ])->row_array();

        if (!$peminjaman) {
            $this->session->set_flashdata('pesan_error', 'Tidak dapat membatalkan peminjaman ini.');
            redirect('peminjaman/riwayat');
            return;
        }

        // Ambil detail untuk mengembalikan stok
        $detail = $this->db->get_where('detail_peminjaman', ['id_peminjaman' => $id_peminjaman])->result_array();
        
        foreach ($detail as $item) {
            $this->M_Alat->tambah_stok_sementara($item['id_alat'], $item['jumlah_pinjam']);
        }

        // Hapus data
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->delete('peminjaman');
        
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->delete('detail_peminjaman');

        $this->session->set_flashdata('pesan_sukses', 'Peminjaman berhasil dibatalkan.');
        redirect('peminjaman/riwayat');
    }
=======
        // 3. Lempar ke file view khusus cetak struk nota
        $this->load->view('peminjaman/v_cetak_bukti', $data);
    }
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
}
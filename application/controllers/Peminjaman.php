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
        $this->load->model('M_Peminjaman');
    }

    // 1. MENU: HALAMAN UTAMA (Dashboard Ringkasan)
    public function index() {
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');

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
            redirect('auth');
            return;
        }

        // 2. SYARAT DOSEN: Harus murni dari rumpun Teknik Mesin
        if (strpos(strtolower($user['program_studi']), 'mesin') === false) {
            $this->session->set_flashdata('pesan_error', 'Sistem Menolak: Fitur ini hanya dikhususkan untuk rumpun mahasiswa Teknik Mesin.');
            redirect('peminjaman/katalog');
            return;
        }

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
        $kode_transaksi = 'PMJ-' . date('YmdHis') . '-' . rand(10, 99);

        // Insert ke tabel peminjaman
        $data_peminjaman = [
            'kode_peminjaman'         => $kode_transaksi,
            'id_user'                 => $id_user_login,
            'tanggal_pinjam'          => $tanggal_pinjam,
            'tanggal_kembali_rencana' => $tanggal_kembali,
            'tujuan_keperluan'        => htmlspecialchars($tujuan),
            'status_peminjaman'       => 'disetujui' // Langsung otomatis disetujui sistem
        ];
        $this->db->insert('peminjaman', $data_peminjaman);
        $id_peminjaman_baru = $this->db->insert_id();

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
            redirect('peminjaman/pengembalian');
            return;
        }

        // Ambil data peminjaman untuk memastikan data itu ada
        $peminjaman = $this->db->get_where('peminjaman', ['id_peminjaman' => $id_peminjaman])->row_array();
        if (!$peminjaman) {
            $this->session->set_flashdata('error_kembali', 'Gagal! Data transaksi tidak ditemukan.');
            redirect('peminjaman/pengembalian');
            return;
        }

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
    public function update_profil() {
        $id_user_login = $this->session->userdata('id_user');
        $password_baru = $this->input->post('password_baru');
        
        $data_update = [];

        // 1. PROSES UBAH SANDI (Jika input password diisi oleh user)
        if (!empty($password_baru)) {
            $data_update['password'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

        // 2. PROSES UPLOAD FOTO PROFIL (Menggunakan Library Upload CodeIgniter)
        if (!empty($_FILES['foto_profil']['name'])) {
            $config['upload_path']   = './assets/uploads/profil/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 2048; // Batas maksimal 2MB
            $config['file_name']     = 'user_' . $id_user_login . '_' . time();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_profil')) {
                $upload_data = $this->upload->data();
                $data_update['foto_profil'] = $upload_data['file_name'];
                
                // Hapus foto lama dari server jika bukan default.jpg agar tidak memenuhi memori
                $user_lama = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
                if ($user_lama['foto_profil'] != 'default.jpg' && file_exists('./assets/uploads/profil/' . $user_lama['foto_profil'])) {
                    unlink('./assets/uploads/profil/' . $user_lama['foto_profil']);
                }
            } else {
                $this->session->set_flashdata('error_profil', 'Gagal upload foto: ' . $this->upload->display_errors('', ''));
                redirect('peminjaman/profil');
                return;
            }
        }

        // 3. EKSEKUSI UPDATE KE DATABASE
        if (!empty($data_update)) {
            $this->db->where('id_user', $id_user_login);
            $this->db->update('users', $data_update);
            $this->session->set_flashdata('sukses_profil', 'Profil Anda berhasil diperbarui oleh sistem!');
        } else {
            $this->session->set_flashdata('error_profil', 'Tidak ada data atau perubahan yang disimpan.');
        }

        redirect('peminjaman/profil');
    }

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
        $data['transaksi'] = $this->db->get()->row_array();

        if (empty($data['transaksi'])) {
            show_404();
            return;
        }

        // 2. Ambil rincian detail item alat yang ada dalam satu id peminjaman tersebut
        $this->db->select('detail_peminjaman.*, alat.nama_alat, alat.kode_alat, alat.spesifikasi');
        $this->db->from('detail_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('detail_peminjaman.id_peminjaman', $id_peminjaman);
        $data['daftar_alat'] = $this->db->get()->result_array();

        // 3. Lempar ke file view khusus cetak struk nota
        $this->load->view('peminjaman/v_cetak_bukti', $data);
    }
}
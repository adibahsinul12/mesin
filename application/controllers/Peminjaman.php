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
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['nomor_induk']   = $this->session->userdata('nomor_induk');
        $data['program_studi'] = $this->session->userdata('program_studi');
        $data['role']          = $this->session->userdata('role');
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
            redirect('auth');
            return;
        }

        // Cek stok alat
        $alat = $this->M_Alat->get_by_id($id_alat);
        if (!$alat) {
            $this->session->set_flashdata('pesan_error', 'Alat tidak ditemukan.');
            redirect('peminjaman/katalog');
            return;
        }

        if ($jumlah > $alat['stok_tersedia']) {
            $this->session->set_flashdata('pesan_error', 'Stok tidak mencukupi! Tersedia: ' . $alat['stok_tersedia']);
            redirect('peminjaman/katalog');
            return;
        }

        // Generate kode transaksi
        $kode_transaksi = 'PMJ-' . date('YmdHis') . '-' . rand(10, 99);

        // Insert ke tabel peminjaman
        $data_peminjaman = [
            'kode_peminjaman'         => $kode_transaksi,
            'id_user'                 => $id_user_login,
            'tanggal_pinjam'          => $tanggal_pinjam,
            'tanggal_kembali_rencana' => $tanggal_kembali,
            'tujuan_keperluan'        => htmlspecialchars($tujuan),
            'status_peminjaman'       => 'pending' // Menunggu persetujuan admin
        ];
        $this->db->insert('peminjaman', $data_peminjaman);
        $id_peminjaman_baru = $this->db->insert_id();

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
            redirect('peminjaman/pengembalian');
            return;
        }

        $id_user_login = $this->session->userdata('id_user');
        
        // Cek kepemilikan
        $peminjaman = $this->db->get_where('peminjaman', [
            'id_peminjaman' => $id_peminjaman,
            'id_user' => $id_user_login
        ])->row_array();
        
        if (!$peminjaman) {
            $this->session->set_flashdata('error_kembali', 'Data transaksi tidak ditemukan.');
            redirect('peminjaman/pengembalian');
            return;
        }

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
    public function update_profil() {
        $id_user_login = $this->session->userdata('id_user');
        $password_baru = $this->input->post('password_baru');
        
        $data_update = [];

        // 1. Update nama jika diubah
        $nama_baru = $this->input->post('nama_lengkap');
        if (!empty($nama_baru)) {
            $data_update['nama_lengkap'] = htmlspecialchars($nama_baru);
        }

        // 2. Update password jika diisi
        if (!empty($password_baru)) {
            $data_update['password'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

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

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_profil')) {
                $upload_data = $this->upload->data();
                $data_update['foto_profil'] = $upload_data['file_name'];
                
                // Hapus foto lama
                $user_lama = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
                if ($user_lama['foto_profil'] != 'default.jpg' && 
                    file_exists('./assets/uploads/profil/' . $user_lama['foto_profil'])) {
                    unlink('./assets/uploads/profil/' . $user_lama['foto_profil']);
                }
            } else {
                $this->session->set_flashdata('error_profil', 'Gagal upload foto: ' . $this->upload->display_errors('', ''));
                redirect('peminjaman/profil');
                return;
            }
        }

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
        }

        redirect('peminjaman/profil');
    }

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
        $data['transaksi'] = $this->db->get()->row_array();

        if (empty($data['transaksi'])) {
            show_404();
            return;
        }

        // Ambil detail alat
        $this->db->select('detail_peminjaman.*, alat.nama_alat, alat.kode_alat, alat.spesifikasi');
        $this->db->from('detail_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('detail_peminjaman.id_peminjaman', $id_peminjaman);
        $data['daftar_alat'] = $this->db->get()->result_array();

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
}
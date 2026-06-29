<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('pesan', 'Silakan login terlebih dahulu.');
            redirect('auth/login_internal');
            return;
        }
        
        // Hanya staff_admin yang bisa akses
        $role_sekarang = $this->session->userdata('role');
        if ($role_sekarang !== 'staff_admin') {
            $this->session->set_flashdata('pesan', 'Akses ditolak! Halaman ini khusus untuk Staff Admin.');
            redirect('auth/login_internal');
            return;
        }
        
        $this->load->model('M_Alat');
        $this->load->model('M_Peminjaman');
        $this->load->model('M_Auth');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();
    }

    // =========================================================
    // DASHBOARD ADMIN
    // =========================================================
    public function index() {
        $data['title'] = 'Dashboard Staff Admin';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        // Statistik
        $data['total_alat'] = $this->M_Alat->count_all();
        $data['total_peminjaman'] = $this->M_Peminjaman->count_all();
        $data['pending_validasi'] = $this->M_Peminjaman->count_by_status('pending');
        $data['pending_kembali'] = $this->M_Peminjaman->count_by_status('pending_kembali');
        $data['total_user'] = $this->M_Auth->count_all();
        
        // Data terbaru
        $data['peminjaman_terbaru'] = $this->M_Peminjaman->get_all_recent(5);
        
        $this->load->view('templates/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/footer');
    }

    public function dashboard() {
        $this->index();
    }

    // =========================================================
    // MODUL 1: MANAJEMEN AKUN PETUGAS INTERNAL
    // =========================================================
    public function petugas() {
        $data['title'] = 'Kelola Petugas';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $data['list_petugas'] = $this->db->get_where('users', ['role' => 'staff_admin'])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_data_petugas', $data);
        $this->load->view('templates/footer');
    }

    public function simpan_petugas() {
        $nip = str_replace(' ', '', $this->input->post('nomor_induk'));
        $nama = $this->input->post('nama_lengkap');
        $email = $this->input->post('email');
        $prodi = $this->input->post('program_studi');
        $pass = $this->input->post('password');

        if (empty($nip) || empty($nama) || empty($email) || empty($pass)) {
            $this->session->set_flashdata('error_petugas', 'Semua kolom isian wajib dilengkapi.');
            redirect('admin/petugas');
            return;
        }

        // Cek duplicate
        if ($this->M_Auth->cek_nomor_induk($nip)) {
            $this->session->set_flashdata('error_petugas', 'NIP/NIDN sudah terdaftar.');
            redirect('admin/petugas');
            return;
        }
        
        if ($this->M_Auth->cek_email($email)) {
            $this->session->set_flashdata('error_petugas', 'Email sudah terdaftar.');
            redirect('admin/petugas');
            return;
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Konfigurasi Email
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'vloraflor9@gmail.com', 
            'smtp_pass' => 'nqkq kmuo lpmn yfsz', 
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('vloraflor9@gmail.com', 'Sistem Aset Lab Poltesa');
        $this->email->to($email);
        $this->email->subject('Kode OTP Verifikasi Akun Petugas Lab');
        $this->email->message('
            <h3>Halo, ' . htmlspecialchars($nama) . '</h3>
            <p>Admin baru saja mendaftarkan email Anda sebagai Petugas Laboratorium Teknik Mesin.</p>
            <p>Berikut adalah kode OTP untuk memverifikasi pendaftaran Anda:</p>
            <h2 style="background:#eee; padding:10px; display:inline-block; letter-spacing:3px;">' . $otp . '</h2>
            <p>Kode ini berlaku selama 15 menit.</p>
        ');

        if ($this->email->send()) {
            $data_temp = [
                'nomor_induk'   => $nip,
                'nama_lengkap'  => htmlspecialchars($nama),
                'email'         => htmlspecialchars($email),
                'password'      => password_hash($pass, PASSWORD_DEFAULT),
                'program_studi' => $prodi,
                'role'          => 'staff_admin',
                'kode_otp'      => $otp,
                'otp_expiry'    => time() + 900
            ];
            
            $this->session->set_userdata('pendaftaran_petugas_temp', $data_temp);
            $this->session->set_flashdata('pesan_sukses_otp', 'Kode OTP telah berhasil dikirim ke email: <strong>' . $email . '</strong>');
            redirect('admin/verifikasi_otp');
        } else {
            log_message('error', $this->email->print_debugger());
            $this->session->set_flashdata('error_petugas', 'Gagal mengirim email OTP. Periksa koneksi internet.');
            redirect('admin/petugas');
        }
    }

    public function verifikasi_otp() {
        if (!$this->session->userdata('pendaftaran_petugas_temp')) {
            redirect('admin/petugas');
        }
        
        $data['title'] = 'Verifikasi OTP';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        $data['email'] = $this->session->userdata('pendaftaran_petugas_temp')['email'];
        
        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_verifikasi_otp', $data);
        $this->load->view('templates/footer');
    }

    public function proses_otp() {
        $input_otp = $this->input->post('kode_otp');
        $data_temp = $this->session->userdata('pendaftaran_petugas_temp');

        if (!$data_temp) {
            $this->session->set_flashdata('error_petugas', 'Sesi registrasi berakhir.');
            redirect('admin/petugas');
            return;
        }

        if (time() > $data_temp['otp_expiry']) {
            $this->session->unset_userdata('pendaftaran_petugas_temp');
            $this->session->set_flashdata('error_petugas', 'Kode OTP telah kadaluarsa. Silakan daftar ulang.');
            redirect('admin/petugas');
            return;
        }

        if ($input_otp == $data_temp['kode_otp']) {
            unset($data_temp['kode_otp']);
            unset($data_temp['otp_expiry']);
            
            $this->db->insert('users', $data_temp);
            $this->session->unset_userdata('pendaftaran_petugas_temp');
            
            $this->session->set_flashdata('sukses_petugas', 'Verifikasi OTP berhasil! Akun petugas baru resmi terdaftar.');
            redirect('admin/petugas');
        } else {
            $this->session->set_flashdata('error_otp', 'Kode OTP yang Anda masukkan salah.');
            redirect('admin/verifikasi_otp');
        }
    }

    public function hapus_petugas($id) {
        $user = $this->M_Auth->get_user_by_id($id);
        if ($user && $user['role'] == 'staff_admin') {
            $this->M_Auth->delete_user($id);
            $this->session->set_flashdata('sukses_petugas', 'Petugas berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error_petugas', 'Data petugas tidak ditemukan.');
        }
        redirect('admin/petugas');
    }

    // =========================================================
    // MODUL 2: MANAJEMEN INVENTARIS ALAT LAB (CRUD)
    // =========================================================
    public function alat() {
        $data['title'] = 'Kelola Data Alat';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $data['list_alat'] = $this->M_Alat->get_all_with_kategori();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_kelola_alat', $data);
        $this->load->view('templates/footer');
    }

    public function kelola_alat() {
        $this->alat();
    }

    public function simpan_alat() {
        $this->form_validation->set_rules('nama_alat', 'Nama Alat', 'required');
        $this->form_validation->set_rules('kategori_alat', 'Kategori', 'required');
        $this->form_validation->set_rules('stok_total', 'Stok Total', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('spesifikasi', 'Spesifikasi', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_alat', validation_errors());
            redirect('admin/alat');
            return;
        }

        $nama_alat = $this->input->post('nama_alat');
        $kategori = $this->input->post('kategori_alat');
        $spesifikasi = $this->input->post('spesifikasi');
        $stok_total = intval($this->input->post('stok_total'));
        $kondisi = $this->input->post('kondisi_alat');

        // Generate kode alat otomatis
        $prefix = $this->get_kode_prefix($kategori);
        $kode_alat = $this->generate_kode_alat($prefix);

        // Upload foto
        $nama_foto = 'default_alat.jpg';
        if (!empty($_FILES['foto_alat']['name'])) {
            $config['upload_path'] = './assets/uploads/alat/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048;
            $config['file_name'] = 'alat-' . time();

            // Buat folder jika belum ada
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_alat')) {
                $upload_data = $this->upload->data();
                $nama_foto = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_alat', 'Gagal Upload Gambar: ' . $this->upload->display_errors('', ''));
                redirect('admin/alat');
                return;
            }
        }

        $data_alat = [
            'kode_alat' => $kode_alat,
            'nama_alat' => htmlspecialchars($nama_alat),
            'kategori_alat' => $kategori,
            'spesifikasi' => htmlspecialchars($spesifikasi),
            'stok_total' => $stok_total,
            'stok_tersedia' => $stok_total,
            'kondisi_alat' => $kondisi,
            'foto_alat' => $nama_foto
        ];

        if ($this->M_Alat->insert($data_alat)) {
            $this->session->set_flashdata('sukses_alat', 'Sukses! Alat baru berhasil disimpan dengan Kode: <strong>' . $kode_alat . '</strong>');
        } else {
            $this->session->set_flashdata('error_alat', 'Gagal menyimpan data alat.');
        }
        redirect('admin/alat');
    }

    public function edit_alat($id_alat) {
        $data['title'] = 'Edit Data Alat';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $data['alat'] = $this->M_Alat->get_by_id($id_alat);
        
        if (!$data['alat']) {
            $this->session->set_flashdata('error_alat', 'Data alat tidak ditemukan.');
            redirect('admin/alat');
            return;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_edit_alat', $data);
        $this->load->view('templates/footer');
    }

    public function proses_edit_alat() {
        $id_alat = $this->input->post('id_alat');
        $kode_alat = $this->input->post('kode_alat');
        $nama_alat = $this->input->post('nama_alat');
        $kategori = $this->input->post('kategori_alat');
        $spesifikasi = $this->input->post('spesifikasi');
        $stok_total = intval($this->input->post('stok_total'));
        $kondisi = $this->input->post('kondisi_alat');

        $alat_lama = $this->M_Alat->get_by_id($id_alat);
        if (!$alat_lama) {
            $this->session->set_flashdata('error_alat', 'Data alat tidak ditemukan.');
            redirect('admin/alat');
            return;
        }

        $nama_foto = $alat_lama['foto_alat'];

        // Upload foto baru
        if (!empty($_FILES['foto_alat']['name'])) {
            $config['upload_path'] = './assets/uploads/alat/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048;
            $config['file_name'] = 'alat-' . time();

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_alat')) {
                // Hapus foto lama
                if ($alat_lama['foto_alat'] != 'default_alat.jpg') {
                    $path_lama = './assets/uploads/alat/' . $alat_lama['foto_alat'];
                    if (file_exists($path_lama)) {
                        unlink($path_lama);
                    }
                }
                $upload_data = $this->upload->data();
                $nama_foto = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_alat', 'Gagal Upload Gambar: ' . $this->upload->display_errors('', ''));
                redirect('admin/alat');
                return;
            }
        }

        // Hitung stok tersedia baru
        $selisih_stok = $stok_total - $alat_lama['stok_total'];
        $stok_tersedia_baru = $alat_lama['stok_tersedia'] + $selisih_stok;
        if ($stok_tersedia_baru < 0) {
            $stok_tersedia_baru = 0;
        }

        $update_data = [
            'kode_alat' => htmlspecialchars($kode_alat),
            'nama_alat' => htmlspecialchars($nama_alat),
            'kategori_alat' => $kategori,
            'spesifikasi' => htmlspecialchars($spesifikasi),
            'stok_total' => $stok_total,
            'stok_tersedia' => $stok_tersedia_baru,
            'kondisi_alat' => $kondisi,
            'foto_alat' => $nama_foto
        ];

        if ($this->M_Alat->update($id_alat, $update_data)) {
            $this->session->set_flashdata('sukses_alat', 'Data alat berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error_alat', 'Gagal memperbarui data.');
        }
        redirect('admin/alat');
    }

    public function hapus_alat($id_alat) {
        $alat = $this->M_Alat->get_by_id($id_alat);
        if ($alat) {
            // Hapus foto
            if ($alat['foto_alat'] != 'default_alat.jpg') {
                $path_gambar = './assets/uploads/alat/' . $alat['foto_alat'];
                if (file_exists($path_gambar)) {
                    unlink($path_gambar);
                }
            }
            
            if ($this->M_Alat->delete($id_alat)) {
                $this->session->set_flashdata('sukses_alat', 'Data alat berhasil dihapus.');
            } else {
                $this->session->set_flashdata('error_alat', 'Gagal menghapus data alat.');
            }
        } else {
            $this->session->set_flashdata('error_alat', 'Data alat tidak ditemukan.');
        }
        redirect('admin/alat');
    }

    // =========================================================
    // MODUL 3: VALIDASI PEMINJAMAN
    // =========================================================
    public function validasi() {
        $data['title'] = 'Validasi Peminjaman';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $data['list_peminjaman'] = $this->M_Peminjaman->get_by_status('pending');

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_validasi', $data);
        $this->load->view('templates/footer');
    }

    public function setujui($id_peminjaman) {
        $peminjaman = $this->M_Peminjaman->get_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error_validasi', 'Data peminjaman tidak ditemukan.');
            redirect('admin/validasi');
            return;
        }

        // Kurangi stok alat
        $details = $this->M_Peminjaman->get_detail_by_id($id_peminjaman);
        foreach ($details as $d) {
            $this->M_Alat->kurangi_stok($d['id_alat'], $d['jumlah_pinjam']);
        }

        // Update status
        if ($this->M_Peminjaman->update_status($id_peminjaman, 'disetujui')) {
            $this->session->set_flashdata('sukses_validasi', 'Peminjaman berhasil disetujui.');
        } else {
            $this->session->set_flashdata('error_validasi', 'Gagal menyetujui peminjaman.');
        }
        redirect('admin/validasi');
    }

    public function tolak($id_peminjaman) {
        if ($this->M_Peminjaman->update_status($id_peminjaman, 'ditolak')) {
            $this->session->set_flashdata('sukses_validasi', 'Peminjaman berhasil ditolak.');
        } else {
            $this->session->set_flashdata('error_validasi', 'Gagal menolak peminjaman.');
        }
        redirect('admin/validasi');
    }

    // =========================================================
    // MODUL 4: PENGEMBALIAN
    // =========================================================
    public function pengembalian() {
        $data['title'] = 'Data Pengembalian';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $data['list_kembali'] = $this->M_Peminjaman->get_by_status('pending_kembali');

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_pengembalian', $data);
        $this->load->view('templates/footer');
    }

    public function proses_kembali($id_peminjaman) {
        $peminjaman = $this->M_Peminjaman->get_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error_pengembalian', 'Data peminjaman tidak ditemukan.');
            redirect('admin/pengembalian');
            return;
        }

        $id_petugas = $this->session->userdata('id_user');
        $kondisi_kembali = $this->input->post('kondisi_kembali') ?: 'baik';
        $keterangan = $this->input->post('keterangan_tambahan') ?: '';

        // Kembalikan stok
        $details = $this->M_Peminjaman->get_detail_by_id($id_peminjaman);
        foreach ($details as $d) {
            $this->M_Alat->tambah_stok($d['id_alat'], $d['jumlah_pinjam']);
        }

        // Simpan data pengembalian
        $data_pengembalian = [
            'id_peminjaman' => $id_peminjaman,
            'tanggal_kembali_asli' => date('Y-m-d H:i:s'),
            'id_petugas' => $id_petugas,
            'kondisi_kembali' => $kondisi_kembali,
            'keterangan_tambahan' => htmlspecialchars($keterangan)
        ];
        $this->db->insert('pengembalian', $data_pengembalian);

        // Update status
        if ($this->M_Peminjaman->update_status($id_peminjaman, 'selesai')) {
            $this->session->set_flashdata('sukses_pengembalian', 'Pengembalian berhasil diproses.');
        } else {
            $this->session->set_flashdata('error_pengembalian', 'Gagal memproses pengembalian.');
        }
        redirect('admin/pengembalian');
    }

    // =========================================================
    // MODUL 5: LAPORAN
    // =========================================================
    public function laporan() {
        $data['title'] = 'Laporan Sirkulasi';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['laporan'] = $this->M_Peminjaman->get_laporan_bulanan($bulan, $tahun);
        $data['statistik'] = $this->M_Peminjaman->get_statistik_bulanan($bulan, $tahun);

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_laporan', $data);
        $this->load->view('templates/footer');
    }

    public function cetak_laporan() {
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        
        $data['laporan'] = $this->M_Peminjaman->get_laporan_bulanan($bulan, $tahun);
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        
        $this->load->view('admin/v_cetak_laporan', $data);
    }

    // =========================================================
    // MODUL 6: MANAJEMEN USER
    // =========================================================
    public function users() {
        $data['title'] = 'Manajemen User';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $data['users'] = $this->M_Auth->get_all_users();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/v_users', $data);
        $this->load->view('templates/footer');
    }

    public function hapus_user($id) {
        $user = $this->M_Auth->get_user_by_id($id);
        if ($user && $user['role'] != 'staff_admin') {
            // Hapus foto profil
            if ($user['foto_profil'] != 'default.jpg') {
                $path = './assets/uploads/profil/' . $user['foto_profil'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $this->M_Auth->delete_user($id);
            $this->session->set_flashdata('sukses', 'User berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus user ini.');
        }
        redirect('admin/users');
    }

    // =========================================================
    // HELPER FUNCTIONS
    // =========================================================
    private function get_kode_prefix($kategori) {
        $map = [
            'Perawatan' => 'PRW',
            'Pengujian' => 'PGJ',
            'Kelistrikan' => 'KLS',
            'Pneumatik' => 'PNM'
        ];
        return isset($map[$kategori]) ? $map[$kategori] : 'ALT';
    }

    private function generate_kode_alat($prefix) {
        $this->db->like('kode_alat', $prefix, 'after');
        $this->db->order_by('kode_alat', 'DESC');
        $query = $this->db->get('alat', 1)->row_array();

        if ($query) {
            $angka_terakhir = intval(substr($query['kode_alat'], -3));
            $angka_baru = $angka_terakhir + 1;
        } else {
            $angka_baru = 1;
        }

        return $prefix . sprintf("%03d", $angka_baru);
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $role_sekarang = $this->session->userdata('role');
        if ($role_sekarang !== 'staff_admin' && $role_sekarang !== 'kepala_lab') {
            $this->session->set_flashdata('pesan', 'Akses ditolak! Halaman ini khusus untuk manajemen petugas.');
            // PERBAIKAN 1: Diarahkan kembali ke gerbang internal agar selaras
            redirect('auth/login_internal');
        }
    }

    // =========================================================
    // FUNGSI UTAMA UNTUK MENAMPILKAN DASHBOARD ADMIN
    // =========================================================
    public function dashboard() {
        // Mengambil data kredensial dari session yang sedang aktif
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role']      = $this->session->userdata('role');
        
        // Memanggil file view dashboard.php di folder views/admin/
        $this->load->view('admin/dashboard', $data);
    }

    // =========================================================
    // MODUL 1: MANAJEMEN AKUN PETUGAS INTERNAL (WITH OTP)
    // =========================================================

    // 1. TAMPILAN: Form Input & Daftar Petugas
    public function petugas() {
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $data['role']          = $this->session->userdata('role');
        $data['list_petugas']  = $this->db->get_where('users', ['role' => 'staff_admin'])->result_array();

        $this->load->view('admin/v_data_petugas', $data);
    }

    // 2. ACTION: Kirim OTP & Simpan Data ke Session Sementara
    public function simpan_petugas() {
        $nip     = $this->input->post('nomor_induk');
        // PERBAIKAN 2: Bersihkan spasi otomatis dari input form agar klop ke tipe numerik BIGINT database
        $nip     = str_replace(' ', '', $nip);
        
        $nama    = $this->input->post('nama_lengkap');
        $email   = $this->input->post('email');
        $prodi   = $this->input->post('program_studi'); 
        $pass    = $this->input->post('password');

        if (empty($nip) || empty($nama) || empty($email) || empty($pass)) {
            $this->session->set_flashdata('error_petugas', 'Semua kolom isian wajib dilengkapi.');
            redirect('admin/petugas'); return;
        }

        $cek_nip = $this->db->get_where('users', ['nomor_induk' => $nip])->row_array();
        $cek_email = $this->db->get_where('users', ['email' => $email])->row_array();
        
        if ($cek_nip || $cek_email) {
            $this->session->set_flashdata('error_petugas', 'NIP/NIDN atau Email tersebut sudah terdaftar di sistem.');
            redirect('admin/petugas'); return;
        }

        // GENERATE KODE OTP 6 DIGIT
        $otp = rand(100000, 999999);

        // KONFIGURASI EMAIL KODE OTP (PERBAIKAN 3: Disamakan dengan SMTP Auth pengirim yang valid)
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
            <p>Jangan berikan kode ini kepada siapa pun.</p>
        ');

        if ($this->email->send()) {
            // Simpan data calon petugas dan kode OTP ke Session
            $data_temp = [
                'nomor_induk'   => $nip,
                'nama_lengkap'  => htmlspecialchars($nama),
                'email'         => htmlspecialchars($email),
                'password'      => password_hash($pass, PASSWORD_DEFAULT),
                'program_studi' => $prodi,
                'role'          => 'staff_admin',
                'kode_otp'      => $otp 
            ];
            
            $this->session->set_userdata('pendaftaran_petugas_temp', $data_temp);
            $this->session->set_flashdata('pesan_sukses_otp', 'Kode OTP telah berhasil dikirim ke email: <strong>' . $email . '</strong>');
            redirect('admin/verifikasi_otp');
        } else {
            log_message('error', $this->email->print_debugger());
            $this->session->set_flashdata('error_petugas', 'Gagal mengirim email OTP. Periksa koneksi internet atau pengaturan SMTP Anda.');
            redirect('admin/petugas');
        }
    }

    // 3. TAMPILAN: Halaman Verifikasi OTP
    public function verifikasi_otp() {
        if (!$this->session->userdata('pendaftaran_petugas_temp')) {
            redirect('admin/petugas');
        }
        
        $data['nama_lengkap']  = $this->session->userdata('nama_lengkap');
        $this->load->view('admin/v_verifikasi_otp', $data);
    }

    // 4. ACTION: Cocokkan OTP dan Insert ke Database
    public function proses_otp() {
        $input_otp = $this->input->post('kode_otp');
        $data_temp = $this->session->userdata('pendaftaran_petugas_temp');

        if ($data_temp && $input_otp == $data_temp['kode_otp']) {
            unset($data_temp['kode_otp']); 
            
            $this->db->insert('users', $data_temp);
            $this->session->unset_userdata('pendaftaran_petugas_temp');
            
            $this->session->set_flashdata('sukses_petugas', 'Verifikasi OTP berhasil! Akun petugas baru resmi terdaftar.');
            redirect('admin/petugas');
        } else {
            $this->session->set_flashdata('error_otp', 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali email Anda.');
            redirect('admin/verifikasi_otp');
        }
    }

    // =========================================================
    // MODUL 2: MANAJEMEN INVENTARIS ALAT LAB (CRUD)
    // =========================================================
    
    // 1. TAMPILAN: Daftar Inventaris Alat
    public function kelola_alat() {
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role']      = $this->session->userdata('role');
        
        // Mengambil seluruh data alat dari database tabel 'alat'
        $data['list_alat'] = $this->db->get('alat')->result_array();
        
        // Memanggil view yang diletakkan di folder views/admin/v_kelola_alat.php
        $this->load->view('admin/v_kelola_alat', $data);
    }

    // 2. ACTION: Simpan Data Alat Baru (FORMAT RAPAT TANPA STRIP + UPLOAD FOTO + KATEGORI)
    public function simpan_alat() {
        $nama_alat     = $this->input->post('nama_alat');
        $kategori_alat = $this->input->post('kategori_alat');
        $spesifikasi   = $this->input->post('spesifikasi');
        $stok_total    = intval($this->input->post('stok_total'));
        $kondisi       = $this->input->post('kondisi_alat');

        if (empty($nama_alat) || empty($kategori_alat) || $stok_total <= 0) {
            $this->session->set_flashdata('error_alat', 'Gagal! Kolom nama, kategori, dan kuantitas total wajib diisi.');
            redirect('admin/kelola_alat'); return;
        }

        // =========================================================
        // PERBAIKAN FORMAT KODE: RAPAT TANPA TANDA STRIP (-)
        // =========================================================
        switch ($kategori_alat) {
            case 'Perawatan':   $prefix = 'PRW'; break; 
            case 'Pengujian':   $prefix = 'PGJ'; break;
            case 'Kelistrikan': $prefix = 'KLS'; break;
            case 'Pneumatik':   $prefix = 'PNM'; break;
            default:            $prefix = 'ALT'; break;
        }

        // Ambil data instrumen terakhir dari database yang memiliki prefix serupa
        $this->db->like('kode_alat', $prefix, 'after');
        $this->db->order_by('kode_alat', 'DESC');
        $query = $this->db->get('alat', 1)->row_array();

        if ($query) {
            // Karena format rapat (Contoh: PNM001), potong 3 digit angka terakhir langsung
            $angka_terakhir = intval(substr($query['kode_alat'], -3));
            $angka_baru     = $angka_terakhir + 1;
        } else {
            // Jika kategori tersebut baru pertama kali diisi
            $angka_baru     = 1;
        }

        // Format angka digabung langsung tanpa separator strip (Contoh: PNM001)
        $kode_alat_otomatis = $prefix . sprintf("%03d", $angka_baru);
        // =========================================================

        // LOGIKA PROSES UPLOAD FOTO ALAT
        $nama_foto = ''; 
        if (!empty($_FILES['foto_alat']['name'])) {
            $config['upload_path']   = './assets/uploads/alat/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = 'alat-' . time();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_alat')) {
                $upload_data = $this->upload->data();
                $nama_foto   = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_alat', 'Gagal Upload Gambar: ' . $this->upload->display_errors('', ''));
                redirect('admin/kelola_alat'); return;
            }
        }

        $data_alat = [
            'kode_alat'     => $kode_alat_otomatis, 
            'nama_alat'     => htmlspecialchars($nama_alat),
            'kategori_alat' => htmlspecialchars($kategori_alat),
            'spesifikasi'   => htmlspecialchars($spesifikasi),
            'stok_total'    => $stok_total,
            'stok_tersedia' => $stok_total, 
            'kondisi_alat'  => $kondisi,
            'foto_alat'     => $nama_foto
        ];

        if ($this->db->insert('alat', $data_alat)) {
            $this->session->set_flashdata('sukses_alat', 'Sukses! Alat baru berhasil disimpan dengan Kode Otomatis: <strong>'.$kode_alat_otomatis.'</strong>');
        } else {
            $this->session->set_flashdata('error_alat', 'Gagal! Terjadi kesalahan sistem saat menyimpan data.');
        }
        redirect('admin/kelola_alat');
    }

    // 3. ACTION: Hapus Data Alat dari Sistem
    public function hapus_alat($id_alat) {
        $alat = $this->db->get_where('alat', ['id_alat' => $id_alat])->row_array();
        if ($alat && !empty($alat['foto_alat'])) {
            $path_gambar = './assets/uploads/alat/' . $alat['foto_alat'];
            if (file_exists($path_gambar)) {
                unlink($path_gambar);
            }
        }

        $this->db->where('id_alat', $id_alat);
        if ($this->db->delete('alat')) {
            $this->session->set_flashdata('sukses_alat', 'Sukses! Data instrumen alat telah dihapus dari inventaris.');
        } else {
            $this->session->set_flashdata('error_alat', 'Gagal! Data alat gagal dihapus.');
        }
        redirect('admin/kelola_alat');
    }

    // 4. TAMPILAN: Halaman Edit Alat Lab
    public function edit_alat($id_alat) {
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role']      = $this->session->userdata('role');
        
        $data['alat'] = $this->db->get_where('alat', ['id_alat' => $id_alat])->row_array();
        
        if (!$data['alat']) {
            $this->session->set_flashdata('error_alat', 'Data alat tidak ditemukan.');
            redirect('admin/kelola_alat'); return;
        }

        $this->load->view('admin/v_edit_alat', $data);
    }

    // 5. ACTION: Proses Pembaruan Data Alat & Foto Baru + KATEGORI DOSEN
    public function proses_edit_alat() {
        $id_alat       = $this->input->post('id_alat');
        $kode_alat     = $this->input->post('kode_alat'); 
        $nama_alat     = $this->input->post('nama_alat');
        $kategori_alat = $this->input->post('kategori_alat');
        $spesifikasi   = $this->input->post('spesifikasi');
        $stok_total    = intval($this->input->post('stok_total'));
        $kondisi       = $this->input->post('kondisi_alat');

        $alat_lama = $this->db->get_where('alat', ['id_alat' => $id_alat])->row_array();
        if (!$alat_lama) {
            redirect('admin/kelola_alat'); return;
        }

        $nama_foto = $alat_lama['foto_alat'];

        if (!empty($_FILES['foto_alat']['name'])) {
            $config['upload_path']   = './assets/uploads/alat/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = 'alat-' . time();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_alat')) {
                if (!empty($alat_lama['foto_alat'])) {
                    $path_lama = './assets/uploads/alat/' . $alat_lama['foto_alat'];
                    if (file_exists($path_lama)) {
                        unlink($path_lama);
                    }
                }
                $upload_data = $this->upload->data();
                $nama_foto   = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_alat', 'Gagal Edit Gambar: ' . $this->upload->display_errors('', ''));
                redirect('admin/kelola_alat'); return;
            }
        }

        $selisih_stok = $stok_total - $alat_lama['stok_total'];
        $stok_tersedia_baru = $alat_lama['stok_tersedia'] + $selisih_stok;

        if ($stok_tersedia_baru < 0) { $stok_tersedia_baru = 0; }

        $update_data = [
            'kode_alat'     => htmlspecialchars($kode_alat), 
            'nama_alat'     => htmlspecialchars($nama_alat),
            'kategori_alat' => htmlspecialchars($kategori_alat),
            'spesifikasi'   => htmlspecialchars($spesifikasi),
            'stok_total'    => $stok_total,
            'stok_tersedia' => $stok_tersedia_baru,
            'kondisi_alat'  => $kondisi,
            'foto_alat'     => $nama_foto
        ];

        $this->db->where('id_alat', $id_alat);
        if ($this->db->update('alat', $update_data)) {
            $this->session->set_flashdata('sukses_alat', 'Sukses! Data instrumen alat berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error_alat', 'Gagal memperbarui data.');
        }
        redirect('admin/kelola_alat');
    }

    // =========================================================
    // MODUL 3: MONITORING SIRKULASI (SESUAI USE CASE & DB ASLI)
    // =========================================================

    // 1. TAMPILAN: Melihat Daftar Peminjaman & Pengembalian
    public function sirkulasi() {
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role']      = $this->session->userdata('role');

        // PERBAIKAN: Menarik field role agar logic hide baris kelas untuk dosen bisa terbaca di view
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi, users.kelas, users.role');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC');
        $data['list_sirkulasi'] = $this->db->get()->result_array();

        $this->load->view('admin/v_sirkulasi', $data);
    }

    // 2. TAMPILAN: List Antrean Pengembalian Alat yang Berstatus 'pending_kembali'
    public function daftar_kembali() {
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role']      = $this->session->userdata('role');

        // Mengambil peminjaman dengan status 'pending_kembali' untuk diverifikasi fisik
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.status_peminjaman', 'pending_kembali');
        $this->db->order_by('peminjaman.id_peminjaman', 'ASC');
        $data['list_kembali'] = $this->db->get()->result_array();

        $this->load->view('admin/v_daftar_kembali', $data);
    }

    // 3. ACTION: Proses Verifikasi Cek Fisik oleh Admin & Input ke Tabel Pengembalian
    public function proses_verifikasi_kembali() {
        $id_peminjaman   = $this->input->post('id_peminjaman');
        $kondisi_kembali = $this->input->post('kondisi_kembali'); // baik / rusak_ringan / rusak_berat
        $keterangan      = $this->input->post('keterangan_tambahan');
        $id_petugas      = $this->session->userdata('id_user'); // ID Admin yang memeriksa

        if (empty($id_peminjaman) || empty($kondisi_kembali)) {
            $this->session->set_flashdata('error_sirkulasi', 'Gagal! Data verifikasi pengembalian tidak valid.');
            redirect('admin/daftar_kembali');
            return;
        }

        // 1. Ambil detail peminjaman barang untuk memulihkan stok logistik lab
        $details = $this->db->get_where('detail_peminjaman', ['id_peminjaman' => $id_peminjaman])->result_array();
        foreach ($details as $d) {
            $alat = $this->db->get_where('alat', ['id_alat' => $d['id_alat']])->row_array();
            $stok_pulang = $alat['stok_tersedia'] + $d['jumlah_pinjam'];

            // Kembalikan sisa kapasitas unit di tabel alat
            $this->db->where('id_alat', $d['id_alat']);
            $this->db->update('alat', ['stok_tersedia' => $stok_pulang]);
        }

        // 2. Insert log rekam data ke dalam tabel 'pengembalian' sesuai struktur DB asli
        $data_pengembalian = [
            'id_peminjaman'         => $id_peminjaman,
            'tanggal_kembali_asli'  => date('Y-m-d H:i:s'),
            'id_petugas'            => $id_petugas,
            'kondisi_kembali'       => $kondisi_kembali,
            'keterangan_tambahan'   => htmlspecialchars($keterangan)
        ];
        $this->db->insert('pengembalian', $data_pengembalian);

        // 3. Update status peminjaman di tabel utama menjadi 'selesai'
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->update('peminjaman', ['status_peminjaman' => 'selesai']);

        $this->session->set_flashdata('sukses_sirkulasi', 'Sukses memverifikasi pengembalian alat! Kondisi fisik tercatat dan stok logistik lab kembali utuh.');
        redirect('admin/sirkulasi');
    }

    // Fungsi lama opsional (dibiarkan tetap ada untuk fleksibilitas bypass manual)
    public function proses_kembali($id_peminjaman) {
        $pinjam = $this->db->get_where('peminjaman', ['id_peminjaman' => $id_peminjaman])->row_array();
        
        if (!$pinjam) {
            $this->session->set_flashdata('error_sirkulasi', 'Transaksi tidak ditemukan.');
            redirect('admin/sirkulasi'); return;
        }

        // Jika dibypass langsung lewat tombol sirkulasi, kembalikan juga unit stoknya
        $details = $this->db->get_where('detail_peminjaman', ['id_peminjaman' => $id_peminjaman])->result_array();
        foreach ($details as $d) {
            $alat = $this->db->get_where('alat', ['id_alat' => $d['id_alat']])->row_array();
            $stok_pulang = $alat['stok_tersedia'] + $d['jumlah_pinjam'];
            $this->db->where('id_alat', $d['id_alat']);
            $this->db->update('alat', ['stok_tersedia' => $stok_pulang]);
        }

        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->update('peminjaman', ['status_peminjaman' => 'selesai']);
        
        $this->session->set_flashdata('sukses_sirkulasi', 'Sukses! Transaksi peminjaman resmi diselesaikan manual.');
        redirect('admin/sirkulasi');
    }
}
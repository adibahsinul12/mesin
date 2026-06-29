<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Auth');
    }

    public function index() {
        if ($this->session->userdata('role')) {
            $role = $this->session->userdata('role');
            if ($role == 'staff_admin') { redirect('admin/dashboard'); } 
            elseif ($role == 'kepala_lab') { redirect('kalab/dashboard'); } 
            else { redirect('peminjaman'); }
        }
        $this->load->view('auth/login');
    }

    public function registrasi() {
        $this->load->view('auth/registrasi');
    }

    // =========================================================
    // TAHAP 1: PROSES FORM & KIRIM OTP KE GMAIL USER
    // =========================================================
    public function proses_registrasi() {
        $nomor_induk   = $this->input->post('nomor_induk');
        $nomor_induk   = str_replace(' ', '', $nomor_induk); // Bersihkan spasi bawaan
        
        $nama_lengkap  = $this->input->post('nama_lengkap');
        $email         = $this->input->post('email');
        $password      = $this->input->post('password');
        $program_studi = $this->input->post('program_studi');
        $role          = $this->input->post('role'); // AMBIL DINAMIS DARI VIEW
        $kelas         = $this->input->post('kelas'); // AMBIL DINAMIS DARI VIEW

        // 1. Validasi NIM hanya jika Role Mahasiswa (Dosen dilewati)
        if ($role === 'mahasiswa') {
            $nim_string = (string)$nomor_induk;
            $awalan_valid = ['320', '420'];
            if (!in_array(substr($nim_string, 0, 3), $awalan_valid)) {
                // Amankan state agar pilihan dropdown dosen/mahasiswa tidak reset ke default
                $this->session->set_flashdata('role_terpilih', $role);
                $this->session->set_flashdata('pesan', 'Gagal! Pendaftaran ditolak. Hanya untuk NIM Jurusan Teknik Mesin Poltesa.');
                redirect('auth/registrasi');
                return;
            }
        }

        // 2. Cek duplicate pendaftaran nomor induk
        if ($this->M_Auth->cek_nomor_induk($nomor_induk)) {
            $this->session->set_flashdata('role_terpilih', $role);
            $this->session->set_flashdata('pesan', 'Gagal! Nomor Induk sudah terdaftar.');
            redirect('auth/registrasi');
            return;
        }

        // 3. GENERATE KODE OTP (6 Digit Angka Acak)
        $kode_otp = rand(100000, 999999);

        // 4. KONFIGURASI SMTP GMAIL
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'vloraflor9@gmail.com', 
            'smtp_pass' => 'nqkq kmuo lpmn yfsz', 
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('vloraflor9@gmail.com', 'Lab Mesin Poltesa');
        $this->email->to($email);
        $this->email->subject('Kode Verifikasi OTP Pendaftaran Akun Lab Mesin');
        
        $html_pesan = "<h3>Halo, " . htmlspecialchars($nama_lengkap) . "!</h3>
                       <p>Terima kasih telah melakukan registrasi pada Sistem Peminjaman Alat Lab Mesin Poltesa.</p>
                       <p>Berikut adalah Kode OTP Verifikasi Anda:</p>
                       <h2 style='color:#333; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp . "</strong></h2>
                       <p>Jangan sebarkan kode ini kepada siapa pun. Kode ini digunakan untuk memastikan Gmail Anda aktif.</p>";
        
        $this->email->message($html_pesan);

        // 5. Kirim Email & Simpan Data Sementara ke Session
        if ($this->email->send()) {
            $temp_user = [
                'nomor_induk'   => $nomor_induk, 
                'nama_lengkap'  => htmlspecialchars($nama_lengkap), 
                'email'         => htmlspecialchars($email), 
                'password'      => password_hash($password, PASSWORD_DEFAULT), 
                'program_studi' => $program_studi,
                'role'          => $role, // Masukkan data role yang dipilih (mahasiswa/dosen)
                'kelas'         => ($role === 'dosen') ? NULL : $kelas, // Set NULL jika dosen
                'otp_rahasia'   => $kode_otp 
            ];
            $this->session->set_userdata('temp_pendaftar', $temp_user);
            redirect('auth/verifikasi_otp');
        } else {
            log_message('error', $this->email->print_debugger());
            $this->session->set_flashdata('role_terpilih', $role);
            $this->session->set_flashdata('pesan', 'Gagal mengirimkan kode OTP ke Gmail Anda. Periksa koneksi internet atau setelan SMTP aplikasi.');
            redirect('auth/registrasi');
        }
    }

    public function verifikasi_otp() {
        if (!$this->session->userdata('temp_pendaftar')) { redirect('auth/registrasi'); }
        $this->load->view('auth/verifikasi_otp');
    }

    public function proses_verifikasi_otp() {
        $otp_input = $this->input->post('otp_mahasiswa');
        $data_temp = $this->session->userdata('temp_pendaftar');

        if ($data_temp && $otp_input == $data_temp['otp_rahasia']) {
            unset($data_temp['otp_rahasia']);

            // Insert langsung ke model dengan data dinamis ($data_temp sudah berisi role & kelas yang benar)
            $this->M_Auth->simpan_pendaftaran($data_temp);
            $this->session->unset_userdata('temp_pendaftar');

            $this->session->set_flashdata('pesan', 'Registrasi Berhasil & Terverifikasi Gmail Aktif! Silakan masuk.');
            redirect('auth');
        } else {
            $this->session->set_flashdata('pesan_otp', 'Kode OTP Salah / Tidak Cocok! Silakan periksa kotak masuk Gmail Anda lagi.');
            redirect('auth/verifikasi_otp');
        }
    }

    // =========================================================
    // FITUR: FORGOT PASSWORD (DAPAT MENDETEKSI ROLE TUJUAN)
    // =========================================================
    public function forgot_password() {
        $this->load->view('auth/forgot_password');
    }

    public function proses_forgot_password() {
        $email = $this->input->post('email');
        $user = $this->db->get_where('users', ['email' => $email])->row_array();

        if (!$user) {
            $this->session->set_flashdata('pesan', 'Gagal! Alamat Gmail tidak terdaftar di dalam sistem.');
            redirect('auth/forgot_password');
            return;
        }

        $kode_otp_reset = rand(100000, 999999);

        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'vloraflor9@gmail.com', 
            'smtp_pass' => 'nqkq kmuo lpmn yfsz', 
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('vloraflor9@gmail.com', 'Lab Mesin Poltesa');
        $this->email->to($email);
        $this->email->subject('Kode OTP Pemulihan Password Akun Lab Mesin');
        
        $html_pesan = "<h3>Halo, " . htmlspecialchars($user['nama_lengkap']) . "!</h3>
                       <p>Kami menerima permintaan untuk mereset kata sandi akun Anda.</p>
                       <p>Berikut adalah Kode OTP Pemulihan Password Anda:</p>
                       <h2 style='color:#d9534f; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp_reset . "</strong></h2>
                       <p>Jangan berikan kode ini ke siapa pun. Jika Anda tidak meminta ini, abaikan saja email ini.</p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $session_reset = [
                'email_reset' => $email,
                'otp_reset'   => $kode_otp_reset,
                'role_reset'  => $user['role']
            ];
            $this->session->set_userdata('temp_reset', $session_reset);
            redirect('auth/reset_password');
        } else {
            $this->session->set_flashdata('pesan', 'Gagal mengirimkan kode verifikasi. Coba lagi nanti.');
            redirect('auth/forgot_password');
        }
    }

    public function reset_password() {
        if (!$this->session->userdata('temp_reset')) { redirect('auth/forgot_password'); }
        $this->load->view('auth/reset_password');
    }

    public function proses_reset_password() {
        $otp_input     = $this->input->post('otp_reset');
        $password_baru = $this->input->post('password_baru');
        $data_reset    = $this->session->userdata('temp_reset');

        if ($data_reset && $otp_input == $data_reset['otp_reset']) {
            $email_target  = $data_reset['email_reset'];
            $role_target   = $data_reset['role_reset'];
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

            $this->db->where('email', $email_target);
            $this->db->update('users', ['password' => $password_hash]);

            $this->session->unset_userdata('temp_reset');
            $this->session->set_flashdata('pesan', 'Sukses! Kata sandi baru berhasil diperbarui. Silakan login.');
            
            if ($role_target == 'staff_admin' || $role_target == 'kepala_lab') {
                redirect('auth/login_internal');
            } else {
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('pesan_reset', 'Gagal! Kode OTP yang Anda masukkan salah atau kedaluwarsa.');
            redirect('auth/reset_password');
        }
    }

    // =========================================================
    // LOGIC LOGIN (WITH AUTO-EXPIRY SECURITY), LOGOUT
    // =========================================================
    public function proses_login() {
        $nomor_induk = $this->input->post('nomor_induk');
        $nomor_induk = str_replace(' ', '', $nomor_induk);
        $password    = $this->input->post('password');
        
        $user = $this->M_Auth->cek_nomor_induk($nomor_induk);
        
        if ($user) {
            if ($user['role'] == 'mahasiswa') {
                $nim_string = (string)$nomor_induk;
                $awalan_prodi = substr($nim_string, 0, 3);
                $dua_digit_angkatan = substr($nim_string, 3, 2); 
                $tahun_angkatan = 2000 + intval($dua_digit_angkatan); 
                
                if ($awalan_prodi == '320') {
                    $batas_tahun_aktif = $tahun_angkatan + 5;
                } elseif ($awalan_prodi == '420') {
                    $batas_tahun_aktif = $tahun_angkatan + 6;
                } else {
                    $batas_tahun_aktif = $tahun_angkatan + 5; 
                }

                $tahun_sekarang = intval(date('Y')); 

                if ($tahun_sekarang > $batas_tahun_aktif) {
                    $this->session->set_flashdata('pesan', 'Gagal! Akun Anda telah dinonaktifkan secara otomatis karena masa tenggang kelulusan (+2 tahun alumni) telah habis.');
                    redirect('auth');
                    return;
                }
            }

            if (password_verify($password, $user['password'])) {
                // DIUBAH: Memasukkan kolom 'kelas' ke dalam array session pendaftaran
                $session_data = [
                    'id_user'       => $user['id_user'], 
                    'nomor_induk'   => $user['nomor_induk'], 
                    'nama_lengkap'  => $user['nama_lengkap'], 
                    'program_studi' => $user['program_studi'], 
                    'role'          => $user['role'],
                    'kelas'         => isset($user['kelas']) ? $user['kelas'] : NULL 
                ];
                $this->session->set_userdata($session_data);
                if ($user['role'] == 'staff_admin') { redirect('admin/dashboard'); } 
                elseif ($user['role'] == 'kepala_lab') { redirect('kalab/dashboard'); } 
                else { redirect('peminjaman'); }
            } else { 
                $this->session->set_flashdata('pesan', 'Gagal! Kata sandi salah.'); 
                if ($user['role'] !== 'mahasiswa') {
                    redirect('auth/login_internal');
                } else {
                    redirect('auth');
                }
            }
        } else { 
            $this->session->set_flashdata('pesan', 'Gagal! Nomor Induk tidak ditemukan.'); 
            $cek_string = (string)$nomor_induk;
            if (!in_array(substr($cek_string, 0, 3), ['320', '420'])) {
                redirect('auth/login_internal');
            } else {
                redirect('auth');
            }
        }
    }

    public function logout() { $this->session->sess_destroy(); redirect('auth'); }

    // =========================================================
    // REGISTRASI INTERNAL (STAFF ADMIN & KEPALA LAB) - WITH OTP
    // =========================================================
    public function registrasi_internal() { 
        $this->load->view('auth/registrasi_internal'); 
    }

    public function proses_registrasi_internal() {
        $nomor_induk  = $this->input->post('nomor_induk'); 
        $nomor_induk  = str_replace(' ', '', $nomor_induk);
        
        $nama_lengkap = $this->input->post('nama_lengkap');
        $email        = $this->input->post('email'); 
        $password     = $this->input->post('password'); 
        $role         = $this->input->post('role');

        if ($this->input->post('token_keamanan') !== 'MESIN-POLTESA-2026') {
            $this->session->set_flashdata('pesan', 'Gagal! Token Otorisasi Keamanan Salah.'); 
            redirect('auth/registrasi_internal'); 
            return;
        }

        if ($this->M_Auth->cek_nomor_induk($nomor_induk)) {
            $this->session->set_flashdata('pesan', 'Gagal! Nomor Induk internal sudah terdaftar.'); 
            redirect('auth/registrasi_internal'); 
            return;
        }

        $kode_otp = rand(100000, 999999);

        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'vloraflor9@gmail.com', 
            'smtp_pass' => 'nqkq kmuo lpmn yfsz', 
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('vloraflor9@gmail.com', 'Admin Lab Mesin Poltesa');
        $this->email->to($email);
        $this->email->subject('Kode Verifikasi OTP Akun Internal Lab');
        
        $html_pesan = "<h3>Halo, " . htmlspecialchars($nama_lengkap) . "!</h3>
                       <p>Pendaftaran akun internal untuk hak akses <strong>" . strtoupper($role) . "</strong> sedang diproses.</p>
                       <p>Berikut adalah Kode OTP Verifikasi Anda:</p>
                       <h2 style='color:#3c8dbc; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp . "</strong></h2>
                       <p>Jangan sebarkan kode ini kepada siapa pun. Gunakan kode ini untuk mengaktifkan akun otoritas lab Anda.</p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $temp_internal = [
                'nomor_induk'  => $nomor_induk, 
                'nama_lengkap' => htmlspecialchars($nama_lengkap), 
                'email'        => htmlspecialchars($email), 
                'password'     => password_hash($password, PASSWORD_DEFAULT), 
                'role'         => $role,
                'otp_rahasia'  => $kode_otp
            ];
            $this->session->set_userdata('temp_internal', $temp_internal);
            redirect('auth/verifikasi_otp_internal');
        } else {
            $this->session->set_flashdata('pesan', 'Gagal mengirimkan kode OTP ke Gmail Anda. Periksa koneksi internet atau setelan SMTP aplikasi.');
            redirect('auth/registrasi_internal');
        }
    }

    public function verifikasi_otp_internal() {
        if (!$this->session->userdata('temp_internal')) { redirect('auth/registrasi_internal'); }
        $this->load->view('auth/verifikasi_otp_internal');
    }

    public function proses_verifikasi_otp_internal() {
        $otp_input = $this->input->post('otp_internal');
        $data_temp = $this->session->userdata('temp_internal');

        if ($data_temp && $otp_input == $data_temp['otp_rahasia']) {
            unset($data_temp['otp_rahasia']);

            if ($this->M_Auth->simpan_pendaftaran($data_temp)) {
                $this->session->unset_userdata('temp_internal');
                $this->session->set_flashdata('pesan', 'Registrasi Akun Internal Berhasil & Terverifikasi! Silakan masuk.');
                redirect('auth/login_internal');
            } else {
                $this->session->set_flashdata('pesan', 'Terjadi kesalahan sistem saat menyimpan data.');
                redirect('auth/registrasi_internal');
            }
        } else {
            $this->session->set_flashdata('pesan_otp', 'Kode OTP Salah / Tidak Cocok! Silakan periksa kembali email Anda.');
            redirect('auth/verifikasi_otp_internal');
        }
    }

    public function login_internal() {
        $this->load->view('auth/login_internal');
    }
}
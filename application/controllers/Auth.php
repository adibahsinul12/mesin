<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Auth');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->database();
    }

    // =========================================================
    // RESEND OTP INTERNAL
    // =========================================================
    public function resend_otp_internal() {
        $data_temp = $this->session->userdata('temp_internal');
        
        if (!$data_temp) {
            $this->session->set_flashdata('pesan', 'Sesi registrasi berakhir. Silakan daftar ulang.');
            redirect('auth/registrasi_internal');
            return;
        }

        $kode_otp = rand(100000, 999999);
        
        $data_temp['otp_rahasia'] = $kode_otp;
        $data_temp['otp_expiry'] = time() + 900;
        $this->session->set_userdata('temp_internal', $data_temp);

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
        $this->email->to($data_temp['email']);
        $this->email->subject('Kode OTP Baru - Registrasi Internal Lab Mesin');
        
        $html_pesan = "<h3>Halo, " . $data_temp['nama_lengkap'] . "!</h3>
                    <p>Berikut adalah Kode OTP baru Anda:</p>
                    <h2 style='color:#3c8dbc; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp . "</strong></h2>
                    <p>Kode ini berlaku selama 15 menit.</p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $this->session->set_flashdata('pesan_sukses_otp', 'Kode OTP baru telah dikirim ke email Anda.');
        } else {
            $this->session->set_flashdata('pesan_otp', 'Gagal mengirim ulang kode OTP. Silakan coba lagi.');
        }
        
        redirect('auth/verifikasi_otp_internal');
    }

    // =========================================================
    // HALAMAN LOGIN UTAMA (Mahasiswa & Dosen)
    // =========================================================
    public function index() {
        if ($this->session->userdata('role')) {
            $role = $this->session->userdata('role');
            redirect($this->get_dashboard_url($role));
        }
        
        $data['title'] = 'Login Sistem Peminjaman Alat Lab Mesin';
        $this->load->view('auth/login', $data);
    }

    // =========================================================
    // HALAMAN LOGIN INTERNAL (Staff Admin & Kepala Lab)
    // =========================================================
    public function login_internal() {
        if ($this->session->userdata('role')) {
            $role = $this->session->userdata('role');
            redirect($this->get_dashboard_url($role));
        }
        
        $data['title'] = 'Login Internal - Staff Admin / Kepala Lab';
        $this->load->view('auth/login_internal', $data);
    }

    // =========================================================
    // PROSES LOGIN (SEMUA ROLE)
    // =========================================================
    public function proses_login() {
        $nomor_induk = $this->input->post('nomor_induk');
        $nomor_induk = str_replace(' ', '', $nomor_induk);
        $password    = $this->input->post('password');
        $role_input  = $this->input->post('role');
        
        $user = $this->M_Auth->cek_nomor_induk($nomor_induk);
        
        if ($user) {
            // Validasi role (khusus untuk login mahasiswa/dosen)
            if (!empty($role_input) && $user['role'] != $role_input) {
                $this->session->set_flashdata('pesan', 'Gagal! Role yang dipilih tidak sesuai dengan akun Anda.');
                redirect('auth');
                return;
            }

            if (password_verify($password, $user['password'])) {
                // Cek masa aktif untuk mahasiswa
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
                        $this->session->set_flashdata('pesan', 'Gagal! Akun Anda telah dinonaktifkan secara otomatis karena masa tenggang kelulusan telah habis.');
                        redirect('auth');
                        return;
                    }
                }

                // Set session
                $session_data = [
                    'id_user'       => $user['id_user'], 
                    'nomor_induk'   => $user['nomor_induk'], 
                    'nama_lengkap'  => $user['nama_lengkap'], 
                    'email'         => $user['email'],
                    'program_studi' => $user['program_studi'], 
                    'role'          => $user['role'],
                    'kelas'         => isset($user['kelas']) ? $user['kelas'] : NULL,
                    'foto_profil'   => isset($user['foto_profil']) ? $user['foto_profil'] : 'default.jpg',
                    'logged_in'     => TRUE
                ];
                $this->session->set_userdata($session_data);
                
                // =====================================================
                // REDIRECT - TANPA '/dashboard'
                // =====================================================
                redirect($this->get_dashboard_url($user['role']));
                
            } else { 
                $this->session->set_flashdata('pesan', 'Gagal! Kata sandi salah.');
                if ($user['role'] == 'mahasiswa' || $user['role'] == 'dosen') {
                    redirect('auth');
                } else {
                    redirect('auth/login_internal');
                }
            }
        } else { 
            $this->session->set_flashdata('pesan', 'Gagal! Nomor Induk tidak ditemukan.');
            
            $cek_string = (string)$nomor_induk;
            if (!in_array(substr($cek_string, 0, 3), ['320', '420']) && strlen($cek_string) < 10) {
                redirect('auth/login_internal');
            } else {
                redirect('auth');
            }
        }
    }

    // =========================================================
    // HALAMAN REGISTRASI (Mahasiswa & Dosen)
    // =========================================================
    public function registrasi() {
        if ($this->session->userdata('role')) {
            redirect($this->get_dashboard_url($this->session->userdata('role')));
        }
        
        $data['title'] = 'Registrasi Akun Baru';
        $this->load->view('auth/registrasi', $data);
    }

    // =========================================================
    // PROSES REGISTRASI (Mahasiswa & Dosen) + OTP
    // =========================================================
    public function proses_registrasi() {
        $nomor_induk   = $this->input->post('nomor_induk');
        $nomor_induk   = str_replace(' ', '', $nomor_induk);
        
        $nama_lengkap  = $this->input->post('nama_lengkap');
        $email         = $this->input->post('email');
        $password      = $this->input->post('password');
        $program_studi = $this->input->post('program_studi');
        $role          = $this->input->post('role');
        $kelas         = $this->input->post('kelas');

        if ($role === 'mahasiswa') {
            $nim_string = (string)$nomor_induk;
            $awalan_valid = ['320', '420'];
            if (!in_array(substr($nim_string, 0, 3), $awalan_valid)) {
                $this->session->set_flashdata('role_terpilih', $role);
                $this->session->set_flashdata('pesan', 'Gagal! Pendaftaran ditolak. Hanya untuk NIM Jurusan Teknik Mesin Poltesa.');
                redirect('auth/registrasi');
                return;
            }
        }

        if ($this->M_Auth->cek_nomor_induk($nomor_induk)) {
            $this->session->set_flashdata('role_terpilih', $role);
            $this->session->set_flashdata('pesan', 'Gagal! Nomor Induk sudah terdaftar.');
            redirect('auth/registrasi');
            return;
        }

        if ($this->M_Auth->cek_email($email)) {
            $this->session->set_flashdata('role_terpilih', $role);
            $this->session->set_flashdata('pesan', 'Gagal! Email sudah terdaftar.');
            redirect('auth/registrasi');
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
        $this->email->from('vloraflor9@gmail.com', 'Lab Mesin Poltesa');
        $this->email->to($email);
        $this->email->subject('Kode Verifikasi OTP Pendaftaran Akun Lab Mesin');
        
        $html_pesan = "<h3>Halo, " . htmlspecialchars($nama_lengkap) . "!</h3>
                       <p>Terima kasih telah melakukan registrasi pada Sistem Peminjaman Alat Lab Mesin Poltesa.</p>
                       <p>Berikut adalah Kode OTP Verifikasi Anda:</p>
                       <h2 style='color:#333; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp . "</strong></h2>
                       <p>Jangan sebarkan kode ini kepada siapa pun. Kode ini berlaku selama 15 menit.</p>
                       <hr>
                       <p><small>Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.</small></p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $temp_user = [
                'nomor_induk'   => $nomor_induk, 
                'nama_lengkap'  => htmlspecialchars($nama_lengkap), 
                'email'         => htmlspecialchars($email), 
                'password'      => password_hash($password, PASSWORD_DEFAULT), 
                'program_studi' => $program_studi,
                'role'          => $role,
                'kelas'         => ($role === 'dosen') ? NULL : $kelas,
                'otp_rahasia'   => $kode_otp,
                'otp_expiry'    => time() + 900
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

    // =========================================================
    // VERIFIKASI OTP
    // =========================================================
    public function verifikasi_otp() {
        if (!$this->session->userdata('temp_pendaftar')) { 
            redirect('auth/registrasi'); 
        }
        
        $data['title'] = 'Verifikasi OTP';
        $data['email'] = $this->session->userdata('temp_pendaftar')['email'];
        $this->load->view('auth/verifikasi_otp', $data);
    }

    public function proses_verifikasi_otp() {
        $otp_input = $this->input->post('otp');
        $data_temp = $this->session->userdata('temp_pendaftar');

        if (!$data_temp) {
            $this->session->set_flashdata('pesan', 'Sesi registrasi berakhir. Silakan daftar ulang.');
            redirect('auth/registrasi');
            return;
        }

        if (time() > $data_temp['otp_expiry']) {
            $this->session->unset_userdata('temp_pendaftar');
            $this->session->set_flashdata('pesan', 'Kode OTP telah kadaluarsa. Silakan daftar ulang.');
            redirect('auth/registrasi');
            return;
        }

        if ($otp_input == $data_temp['otp_rahasia']) {
            unset($data_temp['otp_rahasia']);
            unset($data_temp['otp_expiry']);

            if ($this->M_Auth->simpan_pendaftaran($data_temp)) {
                $this->session->unset_userdata('temp_pendaftar');
                $this->session->set_flashdata('pesan', 'Registrasi Berhasil & Terverifikasi! Silakan login.');
                redirect('auth');
            } else {
                $this->session->set_flashdata('pesan', 'Gagal menyimpan data. Silakan coba lagi.');
                redirect('auth/registrasi');
            }
        } else {
            $this->session->set_flashdata('pesan_otp', 'Kode OTP Salah! Silakan periksa kembali email Anda.');
            redirect('auth/verifikasi_otp');
        }
    }

    // =========================================================
    // REGISTRASI INTERNAL (Staff Admin & Kepala Lab)
    // =========================================================
    public function registrasi_internal() {
        if ($this->session->userdata('role')) {
            redirect($this->get_dashboard_url($this->session->userdata('role')));
        }
        
        $data['title'] = 'Registrasi Akun Internal';
        $this->load->view('auth/registrasi_internal', $data);
    }

    public function proses_registrasi_internal() {
        $nomor_induk  = $this->input->post('nomor_induk'); 
        $nomor_induk  = str_replace(' ', '', $nomor_induk);
        
        $nama_lengkap = $this->input->post('nama_lengkap');
        $email        = $this->input->post('email'); 
        $password     = $this->input->post('password'); 
        $role         = $this->input->post('role');
        $token        = $this->input->post('token_keamanan');

        if ($token !== 'MESIN-POLTESA-2026') {
            $this->session->set_flashdata('pesan', 'Gagal! Token Otorisasi Keamanan Salah.'); 
            redirect('auth/registrasi_internal'); 
            return;
        }

        if ($this->M_Auth->cek_nomor_induk($nomor_induk)) {
            $this->session->set_flashdata('pesan', 'Gagal! Nomor Induk internal sudah terdaftar.'); 
            redirect('auth/registrasi_internal'); 
            return;
        }

        if ($this->M_Auth->cek_email($email)) {
            $this->session->set_flashdata('pesan', 'Gagal! Email sudah terdaftar.');
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
                       <p>Jangan sebarkan kode ini kepada siapa pun. Kode ini berlaku selama 15 menit.</p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $temp_internal = [
                'nomor_induk'  => $nomor_induk, 
                'nama_lengkap' => htmlspecialchars($nama_lengkap), 
                'email'        => htmlspecialchars($email), 
                'password'     => password_hash($password, PASSWORD_DEFAULT), 
                'role'         => $role,
                'program_studi'=> 'Teknik Mesin',
                'kelas'        => NULL,
                'otp_rahasia'  => $kode_otp,
                'otp_expiry'   => time() + 900
            ];
            $this->session->set_userdata('temp_internal', $temp_internal);
            redirect('auth/verifikasi_otp_internal');
        } else {
            $this->session->set_flashdata('pesan', 'Gagal mengirimkan kode OTP. Periksa koneksi internet.');
            redirect('auth/registrasi_internal');
        }
    }

    public function verifikasi_otp_internal() {
        if (!$this->session->userdata('temp_internal')) { 
            redirect('auth/registrasi_internal'); 
        }
        
        $data['title'] = 'Verifikasi OTP Internal';
        $data['email'] = $this->session->userdata('temp_internal')['email'];
        $this->load->view('auth/verifikasi_otp_internal', $data);
    }

    public function proses_verifikasi_otp_internal() {
        $otp_input = $this->input->post('otp');
        $data_temp = $this->session->userdata('temp_internal');

        if (!$data_temp) {
            $this->session->set_flashdata('pesan', 'Sesi registrasi berakhir. Silakan daftar ulang.');
            redirect('auth/registrasi_internal');
            return;
        }

        if (time() > $data_temp['otp_expiry']) {
            $this->session->unset_userdata('temp_internal');
            $this->session->set_flashdata('pesan', 'Kode OTP telah kadaluarsa.');
            redirect('auth/registrasi_internal');
            return;
        }

        if ($otp_input == $data_temp['otp_rahasia']) {
            unset($data_temp['otp_rahasia']);
            unset($data_temp['otp_expiry']);

            if ($this->M_Auth->simpan_pendaftaran($data_temp)) {
                $this->session->unset_userdata('temp_internal');
                $this->session->set_flashdata('pesan', 'Registrasi Akun Internal Berhasil! Silakan login.');
                redirect('auth/login_internal');
            } else {
                $this->session->set_flashdata('pesan', 'Terjadi kesalahan sistem saat menyimpan data.');
                redirect('auth/registrasi_internal');
            }
        } else {
            $this->session->set_flashdata('pesan_otp', 'Kode OTP Salah! Silakan periksa kembali.');
            redirect('auth/verifikasi_otp_internal');
        }
    }

    // =========================================================
    // FORGOT PASSWORD
    // =========================================================
    public function forgot_password() {
        $data['title'] = 'Lupa Kata Sandi';
        $this->load->view('auth/forgot_password', $data);
    }

    public function proses_forgot_password() {
        $email = $this->input->post('email');
        $user = $this->db->get_where('users', ['email' => $email])->row_array();

        if (!$user) {
            $this->session->set_flashdata('pesan', 'Gagal! Email tidak terdaftar di dalam sistem.');
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
                       <p>Berikut adalah Kode OTP Pemulihan Password:</p>
                       <h2 style='color:#d9534f; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp_reset . "</strong></h2>
                       <p>Jangan berikan kode ini ke siapa pun. Kode ini berlaku selama 15 menit.</p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $session_reset = [
                'email_reset' => $email,
                'otp_reset'   => $kode_otp_reset,
                'role_reset'  => $user['role'],
                'otp_expiry'  => time() + 900
            ];
            $this->session->set_userdata('temp_reset', $session_reset);
            redirect('auth/reset_password');
        } else {
            $this->session->set_flashdata('pesan', 'Gagal mengirimkan kode verifikasi.');
            redirect('auth/forgot_password');
        }
    }

    public function reset_password() {
        if (!$this->session->userdata('temp_reset')) { 
            redirect('auth/forgot_password'); 
        }
        
        $data['title'] = 'Reset Kata Sandi';
        $data['email'] = $this->session->userdata('temp_reset')['email_reset'];
        $this->load->view('auth/reset_password', $data);
    }

    public function proses_reset_password() {
        $otp_input     = $this->input->post('otp');
        $password_baru = $this->input->post('password_baru');
        $konfirmasi    = $this->input->post('konfirmasi_password');
        $data_reset    = $this->session->userdata('temp_reset');

        if (!$data_reset) {
            $this->session->set_flashdata('pesan', 'Sesi reset password berakhir.');
            redirect('auth/forgot_password');
            return;
        }

        if (time() > $data_reset['otp_expiry']) {
            $this->session->unset_userdata('temp_reset');
            $this->session->set_flashdata('pesan', 'Kode OTP telah kadaluarsa.');
            redirect('auth/forgot_password');
            return;
        }

        if ($password_baru !== $konfirmasi) {
            $this->session->set_flashdata('pesan_reset', 'Password dan Konfirmasi Password tidak sama.');
            redirect('auth/reset_password');
            return;
        }

        if ($otp_input == $data_reset['otp_reset']) {
            $email_target  = $data_reset['email_reset'];
            $role_target   = $data_reset['role_reset'];
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

            $this->db->where('email', $email_target);
            $this->db->update('users', ['password' => $password_hash]);

            $this->session->unset_userdata('temp_reset');
            $this->session->set_flashdata('pesan', 'Sukses! Kata sandi baru berhasil diperbarui.');
            
            if ($role_target == 'staff_admin' || $role_target == 'kepala_lab') {
                redirect('auth/login_internal');
            } else {
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('pesan_reset', 'Gagal! Kode OTP yang Anda masukkan salah.');
            redirect('auth/reset_password');
        }
    }

    // =========================================================
    // LOGOUT
    // =========================================================
    public function logout() { 
        $this->session->sess_destroy(); 
        redirect('auth'); 
    }

    // =========================================================
    // HELPER: Redirect berdasarkan role (PERBAIKAN)
    // =========================================================
    private function get_dashboard_url($role) {
        switch($role) {
            case 'staff_admin':
                return 'admin';      // ← TANPA /dashboard
            case 'kepala_lab':
                return 'kalab';      // ← TANPA /dashboard
            case 'dosen':
                return 'dosen';      // ← TANPA /dashboard
            case 'mahasiswa':
            default:
                return 'peminjaman';
        }
    }

    // =========================================================
    // RESEND OTP
    // =========================================================
    public function resend_otp() {
        $data_temp = $this->session->userdata('temp_pendaftar');
        
        if (!$data_temp) {
            $this->session->set_flashdata('pesan', 'Sesi registrasi berakhir. Silakan daftar ulang.');
            redirect('auth/registrasi');
            return;
        }

        $kode_otp = rand(100000, 999999);
        
        $data_temp['otp_rahasia'] = $kode_otp;
        $data_temp['otp_expiry'] = time() + 900;
        $this->session->set_userdata('temp_pendaftar', $data_temp);

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
        $this->email->to($data_temp['email']);
        $this->email->subject('Kode OTP Baru - Pendaftaran Akun Lab Mesin');
        
        $html_pesan = "<h3>Halo, " . $data_temp['nama_lengkap'] . "!</h3>
                       <p>Berikut adalah Kode OTP baru Anda:</p>
                       <h2 style='color:#333; background:#f4f4f4; padding:10px; display:inline-block; letter-spacing:5px;'><strong>" . $kode_otp . "</strong></h2>
                       <p>Kode ini berlaku selama 15 menit.</p>";
        
        $this->email->message($html_pesan);

        if ($this->email->send()) {
            $this->session->set_flashdata('pesan_otp', 'Kode OTP baru telah dikirim ke email Anda.');
        } else {
            $this->session->set_flashdata('pesan_otp', 'Gagal mengirim ulang kode OTP. Silakan coba lagi.');
        }
        
        redirect('auth/verifikasi_otp');
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KepalaLab extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->model('M_Peminjaman');
        $this->load->model('M_Alat');
        $this->load->model('M_Auth');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->database();
        
        if(!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('pesan', 'Silakan login terlebih dahulu.');
            redirect('auth/login_internal');
        }
        
        if($this->session->userdata('role') != 'kepala_lab') {
            show_error('Akses ditolak! Anda bukan Kepala Laboratorium.', 403);
        }
    }

    // =========================================================
    // LAPORAN SIRKULASI (USE CASE: MEMINTA LAPORAN)
    // =========================================================
    public function index() {
        $data['title'] = 'Laporan Sirkulasi';
        $data['nama_user'] = $this->session->userdata('nama_lengkap');
        $data['role'] = $this->session->userdata('role');
        $data['foto_profil'] = $this->session->userdata('foto_profil');
        
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['laporan'] = $this->M_Peminjaman->get_laporan_bulanan($bulan, $tahun);
        $data['total_peminjaman'] = count($data['laporan']);
        
        $this->load->view('templates/header', $data);
        $this->load->view('kepala_lab/laporan', $data);
        $this->load->view('templates/footer');
    }

    // =========================================================
    // LAPORAN (alias index)
    // =========================================================
    public function laporan() {
        $this->index();
    }

    // =========================================================
    // PROFIL SAYA (TAMBAHAN)
    // =========================================================
  public function profil() {
    $data['title'] = 'Profil Saya';
    $data['nama_user'] = $this->session->userdata('nama_lengkap');
    $data['role'] = $this->session->userdata('role');
    $data['foto_profil'] = $this->session->userdata('foto_profil');

    $id_user_login = $this->session->userdata('id_user');
    $data['user'] = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();

    $this->load->view('templates/header', $data);
    $this->load->view('kepala_lab/profil', $data);
    $this->load->view('templates/footer');
}

    // =========================================================
    // UPDATE PROFIL
    // =========================================================
    public function update_profil() {
        $id_user_login = $this->session->userdata('id_user');
        $password_baru = $this->input->post('password_baru');
        
        $data_update = [];

        $nama_baru = $this->input->post('nama_lengkap');
        if (!empty($nama_baru)) {
            $data_update['nama_lengkap'] = htmlspecialchars($nama_baru);
        }

        if (!empty($password_baru)) {
            $data_update['password'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

        if (!empty($_FILES['foto_profil']['name'])) {
            $config['upload_path'] = './assets/uploads/profil/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 2048;
            $config['file_name'] = 'user_' . $id_user_login . '_' . time();

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_profil')) {
                $upload_data = $this->upload->data();
                $data_update['foto_profil'] = $upload_data['file_name'];
                
                $user_lama = $this->db->get_where('users', ['id_user' => $id_user_login])->row_array();
                if ($user_lama['foto_profil'] != 'default.jpg' && 
                    file_exists('./assets/uploads/profil/' . $user_lama['foto_profil'])) {
                    unlink('./assets/uploads/profil/' . $user_lama['foto_profil']);
                }
            } else {
                $this->session->set_flashdata('error_profil', 'Gagal upload foto: ' . $this->upload->display_errors('', ''));
                redirect('kalab/profil');
                return;
            }
        }

        if (!empty($data_update)) {
            $this->db->where('id_user', $id_user_login);
            $this->db->update('users', $data_update);
            
            foreach ($data_update as $key => $value) {
                $this->session->set_userdata($key, $value);
            }
            
            $this->session->set_flashdata('sukses_profil', 'Profil berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error_profil', 'Tidak ada perubahan yang disimpan.');
        }

        redirect('kalab/profil');
    }
}
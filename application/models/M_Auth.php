<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Auth extends CI_Model {

<<<<<<< HEAD
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================
    // 1. CEK USER BERDASARKAN NOMOR INDUK (NIM/NIDN)
    // =========================================================
    public function cek_nomor_induk($nomor_induk) {
        $this->db->where('nomor_induk', $nomor_induk);
        $query = $this->db->get('users');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    // =========================================================
    // 2. CEK EMAIL (UNTUK CEK DUPLICATE)
    // =========================================================
    public function cek_email($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    // =========================================================
    // 3. MENYIMPAN DATA REGISTRASI
    // =========================================================
    public function simpan_pendaftaran($data) {
        // Pastikan kolom yang diperlukan ada
        $insert_data = [
            'nomor_induk'   => $data['nomor_induk'],
            'nama_lengkap'  => $data['nama_lengkap'],
            'email'         => $data['email'],
            'password'      => $data['password'],
            'program_studi' => isset($data['program_studi']) ? $data['program_studi'] : 'Teknik Mesin',
            'kelas'         => isset($data['kelas']) ? $data['kelas'] : NULL,
            'role'          => $data['role'],
            'foto_profil'   => isset($data['foto_profil']) ? $data['foto_profil'] : 'default.jpg'
        ];
        
        return $this->db->insert('users', $insert_data);
    }

    // =========================================================
    // 4. GET USER BY ID
    // =========================================================
    public function get_user_by_id($id) {
        $this->db->where('id_user', $id);
        $query = $this->db->get('users');
        return $query->row_array();
    }

    // =========================================================
    // 5. UPDATE USER
    // =========================================================
    public function update_user($id, $data) {
        $this->db->where('id_user', $id);
        return $this->db->update('users', $data);
    }

    // =========================================================
    // 6. GET ALL USERS
    // =========================================================
    public function get_all_users() {
        $this->db->order_by('nama_lengkap', 'ASC');
        $query = $this->db->get('users');
        return $query->result_array();
    }

    // =========================================================
    // 7. GET USERS BY ROLE
    // =========================================================
    public function get_users_by_role($role) {
        $this->db->where('role', $role);
        $this->db->order_by('nama_lengkap', 'ASC');
        $query = $this->db->get('users');
        return $query->result_array();
    }

    // =========================================================
    // 8. DELETE USER
    // =========================================================
    public function delete_user($id) {
        $this->db->where('id_user', $id);
        return $this->db->delete('users');
    }

    // =========================================================
    // 9. COUNT USERS BY ROLE
    // =========================================================
    public function count_by_role($role) {
        $this->db->where('role', $role);
        return $this->db->count_all_results('users');
    }

    // =========================================================
    // 10. CEK USERNAME ATAU EMAIL UNTUK LOGIN INTERNAL
    // =========================================================
    public function cek_login_internal($username) {
        $this->db->where('nomor_induk', $username);
        $this->db->or_where('email', $username);
        $this->db->where_in('role', ['staff_admin', 'kepala_lab']);
        $query = $this->db->get('users');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    // =========================================================
    // 11. CEK TOTAL USER
    // =========================================================
    public function count_all() {
        return $this->db->count_all('users');
    }
=======
    // KELOMPOK MODEL: Murni interaksi query database saja!

    // 1. Mengambil data user berdasarkan nomor induk (NIM/NIDN)
    public function cek_nomor_induk($nomor_induk) {
        return $this->db->get_where('users', ['nomor_induk' => $nomor_induk])->row_array();
    }

    // 2. Menyimpan data registrasi (Baik mahasiswa maupun internal admin/kalab) ke dalam database
    public function simpan_pendaftaran($data) {
        return $this->db->insert('users', $data);
    }

>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Auth extends CI_Model {

    // KELOMPOK MODEL: Murni interaksi query database saja!

    // 1. Mengambil data user berdasarkan nomor induk (NIM/NIDN)
    public function cek_nomor_induk($nomor_induk) {
        return $this->db->get_where('users', ['nomor_induk' => $nomor_induk])->row_array();
    }

    // 2. Menyimpan data registrasi (Baik mahasiswa maupun internal admin/kalab) ke dalam database
    public function simpan_pendaftaran($data) {
        return $this->db->insert('users', $data);
    }

}
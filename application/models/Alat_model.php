<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alat_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Hitung total alat
    public function count_all() {
        return $this->db->count_all('alat');
    }

    // Ambil semua alat dengan kategori
    public function get_all_with_kategori() {
        $this->db->select('alat.*, 
                           (alat.stok_total - alat.stok_tersedia) as dipinjam');
        $this->db->from('alat');
        $this->db->order_by('alat.kategori_alat', 'ASC');
        return $this->db->get()->result();
    }

    // Alat paling sering dipinjam
    public function get_most_borrowed($limit = 5) {
        $this->db->select('alat.id_alat, alat.nama_alat, alat.kode_alat, 
                           COUNT(detail_peminjaman.id_detail) as total_dipinjam');
        $this->db->from('alat');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_alat = alat.id_alat');
        $this->db->join('peminjaman', 'peminjaman.id_peminjaman = detail_peminjaman.id_peminjaman');
        $this->db->where('peminjaman.status_peminjaman', 'selesai');
        $this->db->group_by('alat.id_alat');
        $this->db->order_by('total_dipinjam', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}
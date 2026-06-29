<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peminjaman_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Hitung total peminjaman
    public function count_all() {
        return $this->db->count_all('peminjaman');
    }

    // Hitung berdasarkan status
    public function count_by_status($status) {
        $this->db->where('status_peminjaman', $status);
        return $this->db->count_all_results('peminjaman');
    }

    // Ambil data peminjaman terbaru
    public function get_recent($limit = 5) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->order_by('tanggal_pinjam', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // Ambil detail peminjaman berdasarkan ID
    public function get_by_id($id) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam, users.nomor_induk');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.id_peminjaman', $id);
        return $this->db->get()->row();
    }

    // Ambil detail barang yang dipinjam
    public function get_detail_by_id($id_peminjaman) {
        $this->db->select('detail_peminjaman.*, alat.nama_alat, alat.kode_alat');
        $this->db->from('detail_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('detail_peminjaman.id_peminjaman', $id_peminjaman);
        return $this->db->get()->result();
    }

    // Laporan bulanan
    public function get_laporan_bulanan($bulan, $tahun) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam, 
                           pengembalian.tanggal_kembali_asli, pengembalian.kondisi_kembali');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->join('pengembalian', 'pengembalian.id_peminjaman = peminjaman.id_peminjaman', 'left');
        $this->db->where('MONTH(tanggal_pinjam)', $bulan);
        $this->db->where('YEAR(tanggal_pinjam)', $tahun);
        $this->db->order_by('tanggal_pinjam', 'DESC');
        return $this->db->get()->result();
    }

    // Statistik bulanan
    public function get_statistik_bulanan($bulan, $tahun) {
        $this->db->select('status_peminjaman, COUNT(*) as jumlah');
        $this->db->from('peminjaman');
        $this->db->where('MONTH(tanggal_pinjam)', $bulan);
        $this->db->where('YEAR(tanggal_pinjam)', $tahun);
        $this->db->group_by('status_peminjaman');
        return $this->db->get()->result();
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Alat extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================
    // 1. GET ALL ALAT
    // =========================================================
    public function get_all() {
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }

    // =========================================================
    // 2. GET ALAT BY ID
    // =========================================================
    public function get_by_id($id) {
        $this->db->where('id_alat', $id);
        return $this->db->get('alat')->row_array();
    }

    // =========================================================
    // 3. GET ALAT BY KODE
    // =========================================================
    public function get_by_kode($kode) {
        $this->db->where('kode_alat', $kode);
        return $this->db->get('alat')->row_array();
    }

    // =========================================================
    // 4. GET ALL ALAT WITH KATEGORI (untuk Kepala Lab)
    // =========================================================
    public function get_all_with_kategori() {
        $this->db->select('alat.*, 
                           (alat.stok_total - alat.stok_tersedia) as dipinjam,
                           CASE 
                               WHEN alat.stok_tersedia = 0 THEN "Habis"
                               WHEN alat.stok_tersedia <= 2 THEN "Sedikit"
                               ELSE "Tersedia"
                           END as status_stok');
        $this->db->from('alat');
        $this->db->order_by('alat.kategori_alat', 'ASC');
        $this->db->order_by('alat.nama_alat', 'ASC');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 5. GET ALAT WITH STOK (untuk Mahasiswa/Dosen)
    // =========================================================
    public function get_all_with_stok() {
        $this->db->select('alat.*, 
                           CASE 
                               WHEN alat.stok_tersedia = 0 THEN "Habis"
                               WHEN alat.stok_tersedia <= 2 THEN "Sedikit"
                               ELSE "Tersedia"
                           END as status_stok');
        $this->db->from('alat');
        $this->db->where('alat.stok_tersedia >', 0);
        $this->db->order_by('alat.kategori_alat', 'ASC');
        $this->db->order_by('alat.nama_alat', 'ASC');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 6. GET ALAT BY KATEGORI
    // =========================================================
    public function get_by_kategori($kategori) {
        $this->db->where('kategori_alat', $kategori);
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }

    // =========================================================
    // 7. GET ALAT BY KONDISI
    // =========================================================
    public function get_by_kondisi($kondisi) {
        $this->db->where('kondisi_alat', $kondisi);
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }

    // =========================================================
    // 8. COUNT FUNCTIONS
    // =========================================================
    public function count_all() {
        return $this->db->count_all('alat');
    }

    public function count_tersedia() {
        $this->db->where('stok_tersedia >', 0);
        return $this->db->count_all_results('alat');
    }

    public function count_rusak() {
        $this->db->where('kondisi_alat !=', 'baik');
        return $this->db->count_all_results('alat');
    }

    public function count_by_kategori($kategori) {
        $this->db->where('kategori_alat', $kategori);
        return $this->db->count_all_results('alat');
    }

    public function count_by_kondisi($kondisi) {
        $this->db->where('kondisi_alat', $kondisi);
        return $this->db->count_all_results('alat');
    }

    // =========================================================
    // 9. INSERT ALAT
    // =========================================================
    public function insert($data) {
        return $this->db->insert('alat', $data);
    }

    // =========================================================
    // 10. UPDATE ALAT
    // =========================================================
    public function update($id, $data) {
        $this->db->where('id_alat', $id);
        return $this->db->update('alat', $data);
    }

    // =========================================================
    // 11. DELETE ALAT
    // =========================================================
    public function delete($id) {
        $this->db->where('id_alat', $id);
        return $this->db->delete('alat');
    }

    // =========================================================
    // 12. STOK OPERATIONS
    // =========================================================
    public function kurangi_stok($id_alat, $jumlah) {
        $alat = $this->get_by_id($id_alat);
        if ($alat) {
            $stok_baru = $alat['stok_tersedia'] - $jumlah;
            if ($stok_baru < 0) $stok_baru = 0;
            
            $this->db->where('id_alat', $id_alat);
            return $this->db->update('alat', ['stok_tersedia' => $stok_baru]);
        }
        return false;
    }

    public function tambah_stok($id_alat, $jumlah) {
        $alat = $this->get_by_id($id_alat);
        if ($alat) {
            $stok_baru = $alat['stok_tersedia'] + $jumlah;
            if ($stok_baru > $alat['stok_total']) {
                $stok_baru = $alat['stok_total'];
            }
            
            $this->db->where('id_alat', $id_alat);
            return $this->db->update('alat', ['stok_tersedia' => $stok_baru]);
        }
        return false;
    }

    public function kurangi_stok_sementara($id_alat, $jumlah) {
        // Sama dengan kurangi_stok, untuk digunakan saat pengajuan
        return $this->kurangi_stok($id_alat, $jumlah);
    }

    public function tambah_stok_sementara($id_alat, $jumlah) {
        // Sama dengan tambah_stok, untuk digunakan saat pembatalan
        return $this->tambah_stok($id_alat, $jumlah);
    }

    // =========================================================
    // 13. UPDATE KONDISI ALAT
    // =========================================================
    public function update_kondisi($id_alat, $kondisi) {
        $this->db->where('id_alat', $id_alat);
        return $this->db->update('alat', ['kondisi_alat' => $kondisi]);
    }

    // =========================================================
    // 14. GET ALAT TERPOPULER (Paling Sering Dipinjam)
    // =========================================================
    public function get_most_borrowed($limit = 5) {
        $this->db->select('alat.id_alat, alat.nama_alat, alat.kode_alat, alat.kategori_alat, alat.foto_alat,
                           COUNT(detail_peminjaman.id_detail) as total_dipinjam');
        $this->db->from('alat');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_alat = alat.id_alat');
        $this->db->join('peminjaman', 'peminjaman.id_peminjaman = detail_peminjaman.id_peminjaman');
        $this->db->where_in('peminjaman.status_peminjaman', ['selesai', 'disetujui', 'pending_kembali']);
        $this->db->group_by('alat.id_alat');
        $this->db->order_by('total_dipinjam', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // =========================================================
    // 15. GET ALAT TERPOPULER PER KATEGORI
    // =========================================================
    public function get_most_borrowed_by_kategori() {
        $this->db->select('alat.kategori_alat, COUNT(detail_peminjaman.id_detail) as total_dipinjam');
        $this->db->from('alat');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_alat = alat.id_alat');
        $this->db->join('peminjaman', 'peminjaman.id_peminjaman = detail_peminjaman.id_peminjaman');
        $this->db->where_in('peminjaman.status_peminjaman', ['selesai', 'disetujui', 'pending_kembali']);
        $this->db->group_by('alat.kategori_alat');
        $this->db->order_by('total_dipinjam', 'DESC');
        return $this->db->get()->result();
    }

    // =========================================================
    // 16. SEARCH ALAT
    // =========================================================
    public function search($keyword) {
        $this->db->like('nama_alat', $keyword);
        $this->db->or_like('kode_alat', $keyword);
        $this->db->or_like('spesifikasi', $keyword);
        $this->db->or_like('kategori_alat', $keyword);
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }

    // =========================================================
    // 17. GET ALAT WITH PAGING
    // =========================================================
    public function get_paging($limit, $offset, $kategori = null) {
        $this->db->select('alat.*');
        $this->db->from('alat');
        
        if ($kategori) {
            $this->db->where('kategori_alat', $kategori);
        }
        
        $this->db->order_by('nama_alat', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 18. GET TOTAL STOK TERSEDIA
    // =========================================================
    public function get_total_stok_tersedia() {
        $this->db->select_sum('stok_tersedia');
        return $this->db->get('alat')->row_array()['stok_tersedia'];
    }

    // =========================================================
    // 19. GET TOTAL STOK SELURUHNYA
    // =========================================================
    public function get_total_stok() {
        $this->db->select_sum('stok_total');
        return $this->db->get('alat')->row_array()['stok_total'];
    }

    // =========================================================
    // 20. CEK KETERSEDIAAN STOK
    // =========================================================
    public function cek_ketersediaan($id_alat, $jumlah) {
        $alat = $this->get_by_id($id_alat);
        if ($alat) {
            return $alat['stok_tersedia'] >= $jumlah;
        }
        return false;
    }

    // =========================================================
    // 21. GENERATE KODE ALAT OTOMATIS
    // =========================================================
    public function generate_kode($kategori) {
        $prefix = '';
        switch ($kategori) {
            case 'Perawatan':   $prefix = 'PRW'; break;
            case 'Pengujian':   $prefix = 'PGJ'; break;
            case 'Kelistrikan': $prefix = 'KLS'; break;
            case 'Pneumatik':   $prefix = 'PNM'; break;
            default:            $prefix = 'ALT'; break;
        }

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

    // =========================================================
    // 22. GET KATEGORI LIST (untuk dropdown)
    // =========================================================
    public function get_kategori_list() {
        $this->db->distinct();
        $this->db->select('kategori_alat');
        $this->db->order_by('kategori_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }

    // =========================================================
    // 23. GET ALAT YANG SEDANG DIPINJAM (tidak tersedia)
    // =========================================================
    public function get_alat_dipinjam() {
        $this->db->where('stok_tersedia <', 'stok_total', false);
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }

    // =========================================================
    // 24. GET ALAT RUSAK
    // =========================================================
    public function get_alat_rusak() {
        $this->db->where_in('kondisi_alat', ['rusak_ringan', 'rusak_berat']);
        $this->db->order_by('kondisi_alat', 'ASC');
        $this->db->order_by('nama_alat', 'ASC');
        return $this->db->get('alat')->result_array();
    }
}
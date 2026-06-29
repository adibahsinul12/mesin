<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Peminjaman extends CI_Model {

<<<<<<< HEAD
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================
    // 1. GET ALL PEMINJAMAN
    // =========================================================
    public function get_all() {
        $this->db->order_by('id_peminjaman', 'DESC');
        return $this->db->get('peminjaman')->result_array();
    }

    // =========================================================
    // 2. GET PEMINJAMAN WITH USER
    // =========================================================
    public function get_all_with_user() {
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi, users.kelas');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 3. GET PEMINJAMAN BY ID
    // =========================================================
    public function get_by_id($id) {
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi, users.kelas, users.email');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.id_peminjaman', $id);
        return $this->db->get()->row_array();
    }

    // =========================================================
    // 4. GET DETAIL PEMINJAMAN BY ID
    // =========================================================
    public function get_detail_by_id($id_peminjaman) {
        $this->db->select('detail_peminjaman.*, alat.nama_alat, alat.kode_alat, alat.spesifikasi, alat.foto_alat');
        $this->db->from('detail_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('detail_peminjaman.id_peminjaman', $id_peminjaman);
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 5. GET PEMINJAMAN BY STATUS
    // =========================================================
    public function get_by_status($status) {
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi, users.kelas');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.status_peminjaman', $status);
        $this->db->order_by('peminjaman.tanggal_pinjam', 'ASC');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 6. GET PEMINJAMAN BY USER ID
    // =========================================================
    public function get_by_user($id_user) {
        $this->db->select('peminjaman.*, 
                           GROUP_CONCAT(CONCAT(alat.nama_alat, " (", detail_peminjaman.jumlah_pinjam, " unit)") SEPARATOR ", ") as daftar_alat');
        $this->db->from('peminjaman');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_peminjaman = peminjaman.id_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('peminjaman.id_user', $id_user);
        $this->db->group_by('peminjaman.id_peminjaman');
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 7. GET PEMINJAMAN AKTIF (disetujui & pending_kembali)
    // =========================================================
    public function get_peminjaman_aktif($id_user) {
        $this->db->select('peminjaman.*, 
                           GROUP_CONCAT(CONCAT(alat.nama_alat, " (", detail_peminjaman.jumlah_pinjam, " unit)") SEPARATOR ", ") as daftar_alat');
        $this->db->from('peminjaman');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_peminjaman = peminjaman.id_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('peminjaman.id_user', $id_user);
        $this->db->group_start();
            $this->db->where('peminjaman.status_peminjaman', 'disetujui');
            $this->db->or_where('peminjaman.status_peminjaman', 'pending_kembali');
        $this->db->group_end();
        $this->db->group_by('peminjaman.id_peminjaman');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 8. GET PEMINJAMAN TERBARU
    // =========================================================
    public function get_recent($limit = 5) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->order_by('peminjaman.tanggal_pinjam', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_peminjaman_terbaru($id_user, $limit = 5) {
        $this->db->select('peminjaman.*, 
                           GROUP_CONCAT(CONCAT(alat.nama_alat, " (", detail_peminjaman.jumlah_pinjam, " unit)") SEPARATOR ", ") as daftar_alat');
        $this->db->from('peminjaman');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_peminjaman = peminjaman.id_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('peminjaman.id_user', $id_user);
        $this->db->group_by('peminjaman.id_peminjaman');
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_all_recent($limit = 5) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->order_by('peminjaman.tanggal_pinjam', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // =========================================================
    // 9. GET RIWAYAT PEMINJAM (Dengan daftar alat)
    // =========================================================
    public function get_riwayat_peminjam($id_user) {
        $this->db->select('
            peminjaman.*, 
            users.nama_lengkap, 
            users.program_studi,
            users.kelas,
            GROUP_CONCAT(CONCAT(alat.nama_alat, " (", detail_peminjaman.jumlah_pinjam, " unit)") SEPARATOR ", ") as daftar_alat
        ');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_peminjaman = peminjaman.id_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('peminjaman.id_user', $id_user);
        $this->db->group_by('peminjaman.id_peminjaman');
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC');
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 10. GET KATALOG ALAT
    // =========================================================
=======
    // 1. Mengambil semua data dari tabel alat untuk katalog
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
    public function get_katalog_alat() {
        return $this->db->get('alat')->result_array();
    }

<<<<<<< HEAD
    // =========================================================
    // 11. GET STATISTIK PEMINJAM
    // =========================================================
    public function get_statistik_peminjam($id_user) {
        $now = date('Y-m-d H:i:s');
        
        // A. Aktif (disetujui & pending_kembali)
=======
    // 2. Menghitung statistik peminjaman milik mahasiswa/dosen yang sedang login (Real-time)
    public function get_statistik_peminjam($id_user) {
        $now = date('Y-m-d H:i:s');
        
        // A. Sedang Dipinjam (Aktif) -> Termasuk yang sedang diajukan pending_kembali
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $this->db->select_sum('d.jumlah_pinjam');
        $this->db->from('detail_peminjaman d');
        $this->db->join('peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->where('p.id_user', $id_user);
        $this->db->group_start();
            $this->db->where('p.status_peminjaman', 'disetujui');
            $this->db->or_where('p.status_peminjaman', 'pending_kembali');
        $this->db->group_end();
<<<<<<< HEAD
        $aktif = $this->db->get()->row_array()['jumlah_pinjam'];

        // B. Selesai
=======
        $this->db->where('p.tanggal_kembali_rencana >=', $now);
        $aktif = $this->db->get()->row_array()['jumlah_pinjam'];

        // B. Sudah Dikembalikan -> Status diubah menjadi selesai oleh petugas lab
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $this->db->select_sum('d.jumlah_pinjam');
        $this->db->from('detail_peminjaman d');
        $this->db->join('peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->where('p.id_user', $id_user);
        $this->db->where('p.status_peminjaman', 'selesai');
        $selesai = $this->db->get()->row_array()['jumlah_pinjam'];

<<<<<<< HEAD
        // C. Terlambat
=======
        // C. Terlambat Keluar -> Lewat batas waktu, walaupun statusnya sudah pending_kembali (belum disetujui admin)
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
        $this->db->select_sum('d.jumlah_pinjam');
        $this->db->from('detail_peminjaman d');
        $this->db->join('peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->where('p.id_user', $id_user);
        $this->db->group_start();
            $this->db->where('p.status_peminjaman', 'disetujui');
            $this->db->or_where('p.status_peminjaman', 'pending_kembali');
        $this->db->group_end();
        $this->db->where('p.tanggal_kembali_rencana <', $now);
        $terlambat = $this->db->get()->row_array()['jumlah_pinjam'];

<<<<<<< HEAD
        // D. Pending (menunggu persetujuan)
        $this->db->select_sum('d.jumlah_pinjam');
        $this->db->from('detail_peminjaman d');
        $this->db->join('peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->where('p.id_user', $id_user);
        $this->db->where('p.status_peminjaman', 'pending');
        $pending = $this->db->get()->row_array()['jumlah_pinjam'];

        return [
            'aktif'     => $aktif ? $aktif : 0,
            'selesai'   => $selesai ? $selesai : 0,
            'terlambat' => $terlambat ? $terlambat : 0,
            'pending'   => $pending ? $pending : 0
        ];
    }

    // =========================================================
    // 12. COUNT FUNCTIONS
    // =========================================================
    public function count_all() {
        return $this->db->count_all('peminjaman');
    }

    public function count_by_status($status) {
        $this->db->where('status_peminjaman', $status);
        return $this->db->count_all_results('peminjaman');
    }

    public function count_by_user($id_user) {
        $this->db->where('id_user', $id_user);
        return $this->db->count_all_results('peminjaman');
    }

    public function count_by_user_status($id_user, $status) {
        $this->db->where('id_user', $id_user);
        $this->db->where('status_peminjaman', $status);
        return $this->db->count_all_results('peminjaman');
    }

    // =========================================================
    // 13. UPDATE STATUS
    // =========================================================
    public function update_status($id, $status) {
        $this->db->where('id_peminjaman', $id);
        return $this->db->update('peminjaman', ['status_peminjaman' => $status]);
    }

    // =========================================================
    // 14. INSERT PEMINJAMAN
    // =========================================================
    public function insert($data) {
        $this->db->insert('peminjaman', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data) {
        return $this->db->insert('detail_peminjaman', $data);
    }

    // =========================================================
    // 15. LAPORAN BULANAN
    // =========================================================
    public function get_laporan_bulanan($bulan, $tahun) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam, users.nomor_induk, users.program_studi,
                           pengembalian.tanggal_kembali_asli, pengembalian.kondisi_kembali');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->join('pengembalian', 'pengembalian.id_peminjaman = peminjaman.id_peminjaman', 'left');
        $this->db->where('MONTH(peminjaman.tanggal_pinjam)', $bulan);
        $this->db->where('YEAR(peminjaman.tanggal_pinjam)', $tahun);
        $this->db->order_by('peminjaman.tanggal_pinjam', 'DESC');
        return $this->db->get()->result();
    }

    public function get_statistik_bulanan($bulan, $tahun) {
        $this->db->select('status_peminjaman, COUNT(*) as jumlah');
        $this->db->from('peminjaman');
        $this->db->where('MONTH(tanggal_pinjam)', $bulan);
        $this->db->where('YEAR(tanggal_pinjam)', $tahun);
        $this->db->group_by('status_peminjaman');
        return $this->db->get()->result();
    }

    public function get_statistik_tahunan($tahun) {
        $this->db->select('MONTH(tanggal_pinjam) as bulan, COUNT(*) as jumlah');
        $this->db->from('peminjaman');
        $this->db->where('YEAR(tanggal_pinjam)', $tahun);
        $this->db->group_by('MONTH(tanggal_pinjam)');
        $this->db->order_by('bulan', 'ASC');
        return $this->db->get()->result();
    }

    // =========================================================
    // 16. GET DETAIL PEMINJAMAN UNTUK ADMIN/KALAB
    // =========================================================
    public function get_detail_peminjaman($id_peminjaman, $id_user = null) {
        $this->db->select('peminjaman.*, users.nama_lengkap, users.nomor_induk, users.program_studi, users.kelas');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->where('peminjaman.id_peminjaman', $id_peminjaman);
        
        if ($id_user) {
            $this->db->where('peminjaman.id_user', $id_user);
        }
        
        return $this->db->get()->row_array();
    }

    public function get_detail_alat($id_peminjaman) {
        $this->db->select('detail_peminjaman.*, alat.nama_alat, alat.kode_alat, alat.spesifikasi, alat.foto_alat');
        $this->db->from('detail_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('detail_peminjaman.id_peminjaman', $id_peminjaman);
        return $this->db->get()->result_array();
    }

    // =========================================================
    // 17. DELETE PEMINJAMAN
    // =========================================================
    public function delete($id) {
        // Hapus detail terlebih dahulu
        $this->db->where('id_peminjaman', $id);
        $this->db->delete('detail_peminjaman');
        
        // Hapus peminjaman
        $this->db->where('id_peminjaman', $id);
        return $this->db->delete('peminjaman');
    }

    // =========================================================
    // 18. CEK TANGGAL KEMBALI RENCANA
    // =========================================================
    public function is_terlambat($id_peminjaman) {
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->where('tanggal_kembali_rencana <', date('Y-m-d H:i:s'));
        $this->db->where_in('status_peminjaman', ['disetujui', 'pending_kembali']);
        return $this->db->count_all_results('peminjaman') > 0;
    }

    // =========================================================
    // 19. GET PEMINJAMAN DENGAN PAGING
    // =========================================================
    public function get_paging($limit, $offset, $status = null) {
        $this->db->select('peminjaman.*, users.nama_lengkap as peminjam');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        
        if ($status) {
            $this->db->where('peminjaman.status_peminjaman', $status);
        }
        
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
=======
        // Jika hasilnya NULL (belum pernah melakukan transaksi), otomatis ubah menjadi angka 0
        return [
            'aktif'     => $aktif ? $aktif : 0,
            'selesai'   => $selesai ? $selesai : 0,
            'terlambat' => $terlambat ? $terlambat : 0
        ];
    }

    // 3. Mengambil data riwayat peminjaman lengkap dengan identitas nama & prodi peminjam (Permintaan Dosen)
    public function get_riwayat_peminjam($id_user) {
        // Menggabungkan tabel peminjaman, users, detail, dan alat
        $this->db->select('
            peminjaman.*, 
            users.nama_lengkap, 
            users.program_studi,
            GROUP_CONCAT(CONCAT(alat.nama_alat, " (", detail_peminjaman.jumlah_pinjam, " unit)") SEPARATOR ", ") as daftar_alat
        ');
        $this->db->from('peminjaman');
        $this->db->join('users', 'users.id_user = peminjaman.id_user');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_peminjaman = peminjaman.id_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('peminjaman.id_user', $id_user);
        $this->db->group_by('peminjaman.id_peminjaman');
        $this->db->order_by('peminjaman.id_peminjaman', 'DESC'); // Urutkan dari transaksi terbaru
        
        return $this->db->get()->result_array();
    }

    // 4. DIUPDATE: Mengambil data instrumen yang berstatus 'disetujui' DAN 'pending_kembali'
    public function get_peminjaman_aktif($id_user) {
        $this->db->select('
            peminjaman.*, 
            GROUP_CONCAT(CONCAT(alat.nama_alat, " (", detail_peminjaman.jumlah_pinjam, " unit)") SEPARATOR ", ") as daftar_alat
        ');
        $this->db->from('peminjaman');
        $this->db->join('detail_peminjaman', 'detail_peminjaman.id_peminjaman = peminjaman.id_peminjaman');
        $this->db->join('alat', 'alat.id_alat = detail_peminjaman.id_alat');
        $this->db->where('peminjaman.id_user', $id_user);
        
        // PERBAIKAN UTAMA: Menggunakan group_start & group_end agar menampung kedua status di view user
        $this->db->group_start();
            $this->db->where('peminjaman.status_peminjaman', 'disetujui');
            $this->db->or_where('peminjaman.status_peminjaman', 'pending_kembali');
        $this->db->group_end();
        
        $this->db->group_by('peminjaman.id_peminjaman');
        
        return $this->db->get()->result_array();
>>>>>>> 4efcef41079c5f43d6756666ee25cf08716694c0
    }
}
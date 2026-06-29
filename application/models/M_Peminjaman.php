<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Peminjaman extends CI_Model {

    // 1. Mengambil semua data dari tabel alat untuk katalog
    public function get_katalog_alat() {
        return $this->db->get('alat')->result_array();
    }

    // 2. Menghitung statistik peminjaman milik mahasiswa/dosen yang sedang login (Real-time)
    public function get_statistik_peminjam($id_user) {
        $now = date('Y-m-d H:i:s');
        
        // A. Sedang Dipinjam (Aktif) -> Termasuk yang sedang diajukan pending_kembali
        $this->db->select_sum('d.jumlah_pinjam');
        $this->db->from('detail_peminjaman d');
        $this->db->join('peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->where('p.id_user', $id_user);
        $this->db->group_start();
            $this->db->where('p.status_peminjaman', 'disetujui');
            $this->db->or_where('p.status_peminjaman', 'pending_kembali');
        $this->db->group_end();
        $this->db->where('p.tanggal_kembali_rencana >=', $now);
        $aktif = $this->db->get()->row_array()['jumlah_pinjam'];

        // B. Sudah Dikembalikan -> Status diubah menjadi selesai oleh petugas lab
        $this->db->select_sum('d.jumlah_pinjam');
        $this->db->from('detail_peminjaman d');
        $this->db->join('peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->where('p.id_user', $id_user);
        $this->db->where('p.status_peminjaman', 'selesai');
        $selesai = $this->db->get()->row_array()['jumlah_pinjam'];

        // C. Terlambat Keluar -> Lewat batas waktu, walaupun statusnya sudah pending_kembali (belum disetujui admin)
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
    }
}
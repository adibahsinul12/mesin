<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
*/

// ============================================================
// RESERVED ROUTES
// ============================================================
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ============================================================
// ROUTING AUTH
// ============================================================
$route['auth'] = 'Auth/index';
$route['auth/index'] = 'Auth/index';
$route['auth/login'] = 'Auth/index';
$route['auth/proses_login'] = 'Auth/proses_login';
$route['auth/login_internal'] = 'Auth/login_internal';
$route['auth/registrasi'] = 'Auth/registrasi';
$route['auth/proses_registrasi'] = 'Auth/proses_registrasi';
$route['auth/registrasi_internal'] = 'Auth/registrasi_internal';
$route['auth/proses_registrasi_internal'] = 'Auth/proses_registrasi_internal';
$route['auth/verifikasi_otp'] = 'Auth/verifikasi_otp';
$route['auth/proses_verifikasi_otp'] = 'Auth/proses_verifikasi_otp';
$route['auth/verifikasi_otp_internal'] = 'Auth/verifikasi_otp_internal';
$route['auth/proses_verifikasi_otp_internal'] = 'Auth/proses_verifikasi_otp_internal';
$route['auth/forgot_password'] = 'Auth/forgot_password';
$route['auth/proses_forgot_password'] = 'Auth/proses_forgot_password';
$route['auth/reset_password'] = 'Auth/reset_password';
$route['auth/proses_reset_password'] = 'Auth/proses_reset_password';
$route['auth/resend_otp'] = 'Auth/resend_otp';
$route['auth/resend_otp_internal'] = 'Auth/resend_otp_internal';
$route['auth/logout'] = 'Auth/logout';

// ============================================================
// ROUTING PEMINJAMAN (Mahasiswa & Dosen)
// ============================================================
$route['peminjaman'] = 'Peminjaman/index';
$route['peminjaman/index'] = 'Peminjaman/index';
$route['peminjaman/dashboard'] = 'Peminjaman/index';
$route['peminjaman/katalog'] = 'Peminjaman/katalog';
$route['peminjaman/pinjam/(:num)'] = 'Peminjaman/pinjam/$1';
$route['peminjaman/riwayat'] = 'Peminjaman/riwayat';
$route['peminjaman/detail/(:num)'] = 'Peminjaman/detail/$1';
$route['peminjaman/proses_pinjam'] = 'Peminjaman/proses_pinjam';
$route['peminjaman/pengembalian'] = 'Peminjaman/pengembalian';
$route['peminjaman/ajukan_pengembalian/(:num)'] = 'Peminjaman/ajukan_pengembalian/$1';
$route['peminjaman/profil'] = 'Peminjaman/profil';
$route['peminjaman/update_profil'] = 'Peminjaman/update_profil';
$route['peminjaman/cetak/(:num)'] = 'Peminjaman/cetak/$1';
$route['peminjaman/batal/(:num)'] = 'Peminjaman/batal/$1';

// ============================================================
// ROUTING ADMIN (Staff Admin)
// ============================================================
$route['admin'] = 'Admin/index';
$route['admin/index'] = 'Admin/index';
$route['admin/dashboard'] = 'Admin/index';
$route['admin/alat'] = 'Admin/alat';
$route['admin/kelola_alat'] = 'Admin/kelola_alat';
$route['admin/simpan_alat'] = 'Admin/simpan_alat';
$route['admin/edit_alat/(:num)'] = 'Admin/edit_alat/$1';
$route['admin/proses_edit_alat'] = 'Admin/proses_edit_alat';
$route['admin/hapus_alat/(:num)'] = 'Admin/hapus_alat/$1';
$route['admin/petugas'] = 'Admin/petugas';
$route['admin/simpan_petugas'] = 'Admin/simpan_petugas';
$route['admin/verifikasi_otp'] = 'Admin/verifikasi_otp';
$route['admin/proses_otp'] = 'Admin/proses_otp';
$route['admin/hapus_petugas/(:num)'] = 'Admin/hapus_petugas/$1';
$route['admin/validasi'] = 'Admin/validasi';
$route['admin/setujui/(:num)'] = 'Admin/setujui/$1';
$route['admin/tolak/(:num)'] = 'Admin/tolak/$1';
$route['admin/pengembalian'] = 'Admin/pengembalian';
$route['admin/proses_kembali/(:num)'] = 'Admin/proses_kembali/$1';
$route['admin/laporan'] = 'Admin/laporan';
$route['admin/cetak_laporan'] = 'Admin/cetak_laporan';
$route['admin/users'] = 'Admin/users';
$route['admin/hapus_user/(:num)'] = 'Admin/hapus_user/$1';

// ============================================================
// ROUTING KALAB (Kepala Lab)
// ============================================================
$route['kalab'] = 'KepalaLab/index';
$route['kalab/index'] = 'KepalaLab/index';
$route['kalab/laporan'] = 'KepalaLab/laporan';
$route['kalab/profil'] = 'KepalaLab/profil';
$route['kalab/update_profil'] = 'KepalaLab/update_profil';


// ============================================================
// ROUTING DOSEN
// ============================================================
$route['dosen'] = 'Dosen/index';
$route['dosen/index'] = 'Dosen/index';
$route['dosen/dashboard'] = 'Dosen/index';
$route['dosen/alat'] = 'Dosen/alat';
$route['dosen/pinjam/(:num)'] = 'Dosen/pinjam/$1';
$route['dosen/riwayat'] = 'Dosen/riwayat';
$route['dosen/detail/(:num)'] = 'Dosen/detail/$1';
$route['dosen/proses_pinjam'] = 'Dosen/proses_pinjam';

// ============================================================
// ROUTING MAHASISWA
// ============================================================
$route['mahasiswa'] = 'Mahasiswa/index';
$route['mahasiswa/index'] = 'Mahasiswa/index';
$route['mahasiswa/dashboard'] = 'Mahasiswa/index';
$route['mahasiswa/alat'] = 'Mahasiswa/alat';
$route['mahasiswa/pinjam/(:num)'] = 'Mahasiswa/pinjam/$1';
$route['mahasiswa/riwayat'] = 'Mahasiswa/riwayat';
$route['mahasiswa/detail/(:num)'] = 'Mahasiswa/detail/$1';
$route['mahasiswa/proses_pinjam'] = 'Mahasiswa/proses_pinjam';

// ============================================================
// ROUTING API
// ============================================================
$route['api/alat'] = 'Api/alat';
$route['api/alat/(:num)'] = 'Api/alat/$1';
$route['api/peminjaman'] = 'Api/peminjaman';
$route['api/peminjaman/(:num)'] = 'Api/peminjaman/$1';
$route['api/users'] = 'Api/users';
$route['api/users/(:num)'] = 'Api/users/$1';
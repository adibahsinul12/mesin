<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3><i class="fas fa-info-circle"></i> Detail Peminjaman</h3>
                    <a href="<?= base_url('kalab/peminjaman') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="<?= base_url('kalab/cetak/' . ($peminjaman['id_peminjaman'] ?? '')) ?>" 
                       class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($peminjaman) && !empty($peminjaman)): ?>
    <div class="card mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Kode Peminjaman</th>
                            <td>: <?= $peminjaman['kode_peminjaman'] ?></td>
                        </tr>
                        <tr>
                            <th>Peminjam</th>
                            <td>: <?= $peminjaman['nama_lengkap'] ?></td>
                        </tr>
                        <tr>
                            <th>NIM/NIDN</th>
                            <td>: <?= $peminjaman['nomor_induk'] ?></td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>: <?= $peminjaman['program_studi'] ?></td>
                        </tr>
                        <?php if(isset($peminjaman['kelas']) && !empty($peminjaman['kelas'])): ?>
                        <tr>
                            <th>Kelas</th>
                            <td>: <?= $peminjaman['kelas'] ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Tanggal Pinjam</th>
                            <td>: <?= date('d/m/Y H:i', strtotime($peminjaman['tanggal_pinjam'])) ?></td>
                        </tr>
                        <tr>
                            <th>Rencana Kembali</th>
                            <td>: <?= date('d/m/Y H:i', strtotime($peminjaman['tanggal_kembali_rencana'])) ?></td>
                        </tr>
                        <tr>
                            <th>Tujuan</th>
                            <td>: <?= $peminjaman['tujuan_keperluan'] ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: 
                                <span class="badge bg-<?= $peminjaman['status_peminjaman'] == 'pending' ? 'warning' : ($peminjaman['status_peminjaman'] == 'selesai' ? 'success' : 'info') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $peminjaman['status_peminjaman'])) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Alat -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Daftar Alat yang Dipinjam</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Alat</th>
                            <th>Nama Alat</th>
                            <th>Jumlah</th>
                            <th>Spesifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($detail_alat) && !empty($detail_alat)): ?>
                            <?php $no=1; foreach($detail_alat as $d): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $d['kode_alat'] ?></td>
                                <td><?= $d['nama_alat'] ?></td>
                                <td><?= $d['jumlah_pinjam'] ?></td>
                                <td><?= $d['spesifikasi'] ?? '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger mt-4">Data peminjaman tidak ditemukan.</div>
    <?php endif; ?>
</div>
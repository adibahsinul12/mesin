<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3><i class="fas fa-list"></i> Semua Peminjaman</h3>
                    <p class="text-muted">Monitoring seluruh transaksi peminjaman alat</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>Daftar Peminjaman</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>NIM/NIDN</th>
                            <th>Tanggal Pinjam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($peminjaman) && !empty($peminjaman)): ?>
                            <?php $no=1; foreach($peminjaman as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $p['kode_peminjaman'] ?></td>
                                <td><?= $p['nama_lengkap'] ?></td>
                                <td><?= $p['nomor_induk'] ?></td>
                                <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $p['status_peminjaman'] == 'pending' ? 'warning' : ($p['status_peminjaman'] == 'selesai' ? 'success' : 'info') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $p['status_peminjaman'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('kalab/detail/' . $p['id_peminjaman']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data peminjaman</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
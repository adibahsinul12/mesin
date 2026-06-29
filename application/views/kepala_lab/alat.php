<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3><i class="fas fa-tools"></i> Pantau Alat & Stok</h3>
                    <p class="text-muted">Monitoring ketersediaan alat laboratorium</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Alat</h5>
                    <h2><?= $total_alat ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Tersedia</h5>
                    <h2><?= $alat_tersedia ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5>Rusak</h5>
                    <h2><?= $alat_rusak ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Alat -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Daftar Inventaris Alat</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Kode</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($alat) && !empty($alat)): ?>
                            <?php $no=1; foreach($alat as $a): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <img src="<?= base_url('assets/uploads/alat/' . ($a['foto_alat'] ?? 'default_alat.jpg')) ?>" 
                                         width="50" height="50" style="object-fit:cover; border-radius:5px;">
                                </td>
                                <td><?= $a['kode_alat'] ?></td>
                                <td><?= $a['nama_alat'] ?></td>
                                <td><?= $a['kategori_alat'] ?></td>
                                <td><?= $a['stok_tersedia'] ?>/<?= $a['stok_total'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $a['stok_tersedia'] > 0 ? 'success' : 'danger' ?>">
                                        <?= $a['stok_tersedia'] > 0 ? 'Tersedia' : 'Habis' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $a['kondisi_alat'] == 'baik' ? 'success' : ($a['kondisi_alat'] == 'rusak_ringan' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $a['kondisi_alat'])) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data alat</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
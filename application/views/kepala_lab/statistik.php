<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3><i class="fas fa-chart-bar"></i> Statistik Peminjaman</h3>
                    <p class="text-muted">Analisis data peminjaman alat laboratorium</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card mt-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-control">
                        <?php for($i=date('Y'); $i>=2020; $i--): ?>
                            <option value="<?= $i ?>" <?= ($tahun ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Bulanan -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Statistik Peminjaman Per Bulan (Tahun <?= $tahun ?? date('Y') ?>)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Jumlah Peminjaman</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $max = 0;
                        if(isset($statistik_bulanan) && !empty($statistik_bulanan)) {
                            foreach($statistik_bulanan as $s) {
                                if($s->jumlah > $max) $max = $s->jumlah;
                            }
                        }
                        ?>
                        <?php if(isset($statistik_bulanan) && !empty($statistik_bulanan)): ?>
                            <?php foreach($statistik_bulanan as $s): ?>
                            <tr>
                                <td><?= date('F', mktime(0,0,0,$s->bulan,1)) ?></td>
                                <td><?= $s->jumlah ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" 
                                             style="width: <?= $max > 0 ? ($s->jumlah/$max)*100 : 0 ?>%">
                                            <?= $max > 0 ? round(($s->jumlah/$max)*100) : 0 ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alat Terpopuler -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Alat Paling Sering Dipinjam</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Alat</th>
                                    <th>Total Dipinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($alat_terpopuler) && !empty($alat_terpopuler)): ?>
                                    <?php $no=1; foreach($alat_terpopuler as $a): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $a->nama_alat ?></td>
                                        <td><?= $a->total_dipinjam ?> kali</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Statistik Per Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kategori</th>
                                    <th>Total Dipinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($kategori_populer) && !empty($kategori_populer)): ?>
                                    <?php $no=1; foreach($kategori_populer as $k): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $k->kategori_alat ?></td>
                                        <td><?= $k->total_dipinjam ?> kali</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
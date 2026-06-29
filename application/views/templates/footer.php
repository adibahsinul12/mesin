<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Flash Messages -->
<?php if($this->session->flashdata('pesan')): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <?= $this->session->flashdata('pesan') ?>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-file-alt"></i> Laporan Sirkulasi</h2>
        <p class="text-muted">Laporan peminjaman alat laboratorium</p>
    </div>
    <span class="badge bg-danger" style="padding: 8px 20px; font-size: 14px;">KEPALA LAB</span>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Bulan</label>
                <select name="bulan" class="form-control">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= sprintf('%02d', $i) ?>" <?= ($bulan ?? '') == sprintf('%02d', $i) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0,0,0,$i,1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Tahun</label>
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

<!-- Tabel Laporan -->
<div class="card">
    <div class="card-header">
        <h6><i class="fas fa-list"></i> Data Laporan (Total: <?= $total_peminjaman ?? 0 ?> peminjaman)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Peminjam</th>
                        <th>NIM/NIDN</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($laporan) && !empty($laporan)): ?>
                        <?php $no=1; foreach($laporan as $l): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= $l->kode_peminjaman ?></strong></td>
                            <td><?= $l->peminjam ?></td>
                            <td><?= $l->nomor_induk ?? '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($l->tanggal_pinjam)) ?></td>
                            <td>
                                <?php
                                $status_class = 'secondary';
                                $status_text = $l->status_peminjaman;
                                if($l->status_peminjaman == 'pending') {
                                    $status_class = 'warning';
                                    $status_text = 'Menunggu';
                                } elseif($l->status_peminjaman == 'disetujui') {
                                    $status_class = 'success';
                                    $status_text = 'Disetujui';
                                } elseif($l->status_peminjaman == 'ditolak') {
                                    $status_class = 'danger';
                                    $status_text = 'Ditolak';
                                } elseif($l->status_peminjaman == 'pending_kembali') {
                                    $status_class = 'info';
                                    $status_text = 'Menunggu Kembali';
                                } elseif($l->status_peminjaman == 'selesai') {
                                    $status_class = 'secondary';
                                    $status_text = 'Selesai';
                                }
                                ?>
                                <span class="badge bg-<?= $status_class ?>">
                                    <?= $status_text ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                Tidak ada data untuk periode ini
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
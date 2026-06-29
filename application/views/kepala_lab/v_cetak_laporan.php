<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman Alat Lab Mesin</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #666; }
        .text-center { text-align: center; }
        .badge-success { color: green; }
        .badge-warning { color: orange; }
        .badge-danger { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN ALAT LABORATORIUM MESIN</h1>
        <p>Politeknik Negeri Sambas</p>
        <p>Periode: <?= date('F Y', mktime(0,0,0,$bulan,1,$tahun)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Peminjaman</th>
                <th>Peminjam</th>
                <th>NIM/NIDN</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($laporan) && !empty($laporan)): ?>
                <?php $no=1; foreach($laporan as $l): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $l->kode_peminjaman ?></td>
                    <td><?= $l->peminjam ?></td>
                    <td><?= $l->nomor_induk ?? '-' ?></td>
                    <td><?= date('d/m/Y', strtotime($l->tanggal_pinjam)) ?></td>
                    <td><?= date('d/m/Y', strtotime($l->tanggal_kembali_rencana)) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $l->status_peminjaman)) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data untuk periode ini</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <p>&copy; <?= date('Y') ?> - Sistem Informasi Peminjaman Alat Lab Mesin Poltesa</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
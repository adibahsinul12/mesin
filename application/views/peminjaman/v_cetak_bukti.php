<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Transaksi Lab - <?php echo $transaksi['kode_peminjaman']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.4; padding: 20px; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .kop-surat p { margin: 5px 0 0 0; font-size: 12px; color: #555; }
        .judul-struk { text-align: center; text-transform: uppercase; font-weight: bold; font-size: 16px; margin-bottom: 20px; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 20px; font-size: 14px; }
        .info-table td { padding: 4px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; }
        .ttd-container { width: 100%; margin-top: 5px; font-size: 14px; display: flex; justify-content: space-between; }
        .ttd-box { width: 40%; text-align: center; margin-top: 40px; }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <div style="text-align: right; margin-bottom: 10px;">
        <button class="btn-print" onclick="window.print()" style="padding: 8px 15px; background: #3c8dbc; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">🖨️ Cetak Dokumen</button>
    </div>

    <div class="kop-surat">
        <h2>Sistem Informasi Peminjaman ALat Laboratorium</h2>
        <h2>Jurusan Teknik Mesin - Politeknik Negeri Sambas</h2>
        <p>Jl. Raya Sejangkung, Sambas, Kalimantan Barat</p>
    </div>

    <div class="judul-struk">
        <?php echo ($transaksi['status_peminjaman'] == 'disetujui') ? 'Bukti Peminjaman Alat' : 'Surat Bukti Pengembalian Alat'; ?>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%"><strong>Kode Transaksi</strong></td>
            <td width="3%">:</td>
            <td><code><strong><?php echo $transaksi['kode_peminjaman']; ?></strong></code></td>
            <td width="20%"><strong>Nama Peminjam</strong></td>
            <td width="3%">:</td>
            <td><?php echo htmlspecialchars($transaksi['nama_lengkap']); ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal Ambil</strong></td>
            <td>:</td>
            <td><?php echo date('d M Y - H:i', strtotime($transaksi['tanggal_pinjam'])); ?> WIB</td>
            <td><strong>NIM / No. Induk</strong></td>
            <td>:</td>
            <td><code><?php echo htmlspecialchars($transaksi['nomor_induk']); ?></code></td>
        </tr>
        <tr>
            <td><strong>Batas Kembali</strong></td>
            <td>:</td>
            <td><?php echo date('d M Y - H:i', strtotime($transaksi['tanggal_kembali_rencana'])); ?> WIB</td>
            <td><strong>Program Studi</strong></td>
            <td>:</td>
            <td><?php echo htmlspecialchars($transaksi['program_studi']); ?></td>
        </tr>
        <tr>
            <td><strong>Keperluan / Tujuan</strong></td>
            <td>:</td>
            <td colspan="4"><em><?php echo htmlspecialchars($transaksi['tujuan_keperluan']); ?></em></td>
        </tr>
    </table>

    <p style="font-size: 14px; margin-bottom: 5px;"><strong>Daftar Item / Instrumen Laboratorium:</strong></p>
    
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Alat</th>
                <th>Nama Alat / Spesifikasi Teknis</th>
                <th width="15%">Jumlah Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($daftar_alat as $alat): ?>
            <tr>
                <td align="center"><?php echo $no++; ?></td>
                <td align="center"><code><?php echo $alat['kode_alat']; ?></code></td>
                <td>
                    <strong><?php echo htmlspecialchars($alat['nama_alat']); ?></strong><br>
                    <small style="color: #555;"><?php echo htmlspecialchars($alat['spesifikasi']); ?></small>
                </td>
                <td align="center"><strong><?php echo $alat['jumlah_pinjam']; ?></strong> Unit</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="font-size: 12px; margin-top: 15px; color: #555;">
        * Catatan: Dokumen ini diterbitkan sah oleh sistem secara otomatis sebagai bukti transaksi yang valid di lingkungan Laboratorium Bengkel Teknik Mesin Poltesa.
    </p>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Peminjam / Mahasiswa,</p>
            <br><br><br>
            <p><strong>( <u><?php echo htmlspecialchars($transaksi['nama_lengkap']); ?></u> )</strong></p>
        </div>
        <div class="ttd-box">
            <p>Sambas, <?php echo date('d M Y'); ?><br>Petugas Laboran Bengkel,</p>
            <br><br><br>
            <p><strong>( ............................................ )</strong></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
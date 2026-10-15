<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Hadir - <?= esc($ujian['nama']) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12pt; color: #000; padding: 20mm; }
    .kop { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 16px; }
    .kop h2 { font-size: 14pt; text-transform: uppercase; }
    .kop p { font-size: 10pt; }
    h3 { text-align: center; font-size: 13pt; text-transform: uppercase; margin: 12px 0; text-decoration: underline; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 3px 6px; vertical-align: top; }
    .info-table td:first-child { width: 35%; font-weight: bold; }
    table.hadir { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.hadir th, table.hadir td { border: 1px solid #000; padding: 5px 8px; }
    table.hadir th { background: #f0f0f0; text-align: center; }
    .ttd { display: flex; justify-content: flex-end; margin-top: 40px; }
    .ttd-item { text-align: center; width: 200px; }
    .ttd-item .garis { border-bottom: 1px solid #000; margin: 60px 0 4px; }
    @media print { body { padding: 10mm; } }
  </style>
</head>
<body>
  <div class="kop">
    <h2><?= esc($sekolah['nama_sekolah'] ?? 'NAMA SEKOLAH') ?></h2>
    <p><?= esc($sekolah['alamat'] ?? '') ?></p>
  </div>

  <h3>Daftar Hadir Peserta Ujian</h3>

  <table class="info-table">
    <tr><td>Nama Ujian</td><td>: <?= esc($ujian['nama']) ?></td></tr>
    <tr><td>Mata Pelajaran</td><td>: <?= esc($ujian['nama_mapel'] ?? '-') ?></td></tr>
    <tr><td>Ruang</td><td>: <?= esc($ujian['nama_ruang'] ?? '-') ?></td></tr>
    <tr><td>Hari / Tanggal</td><td>: <?= $ujian['mulai_at'] ? date('l, d F Y', strtotime($ujian['mulai_at'])) : '-' ?></td></tr>
    <tr><td>Waktu</td><td>: <?= $ujian['mulai_at'] ? date('H:i', strtotime($ujian['mulai_at'])) : '-' ?> s/d <?= $ujian['selesai_at'] ? date('H:i', strtotime($ujian['selesai_at'])) : '-' ?></td></tr>
  </table>

  <table class="hadir">
    <thead>
      <tr>
        <th style="width:40px;">No</th>
        <th>NIS</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th style="width:80px;">Hadir (✔)</th>
        <th>Tanda Tangan</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($peserta as $i => $p): ?>
      <tr>
        <td style="text-align:center;"><?= $i + 1 ?></td>
        <td><?= esc($p['nis']) ?></td>
        <td><?= esc($p['nama_siswa']) ?></td>
        <td style="text-align:center;"><?= esc($p['nama_kelas'] ?? '-') ?></td>
        <td style="text-align:center;">☐</td>
        <td style="height:28px;"></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="ttd">
    <div class="ttd-item">
      <p>Pengawas Ruang</p>
      <div class="garis"></div>
      <p>(________________________)</p>
    </div>
  </div>

  <script>window.onload = function(){ window.print(); }</script>
</body>
</html>

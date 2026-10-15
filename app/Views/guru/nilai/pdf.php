<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Nilai — <?= esc($ujian['nama']) ?></title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11pt;
      color: #10222B;
      line-height: 1.4;
      margin: 0;
      padding: 10px;
    }
    .kop {
      text-align: center;
      border-bottom: 2px solid #10222B;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .kop h2 {
      margin: 0 0 4px 0;
      font-size: 16pt;
      text-transform: uppercase;
    }
    .kop p {
      margin: 0;
      font-size: 9pt;
      color: #52666F;
    }
    .title {
      text-align: center;
      margin-bottom: 15px;
    }
    .title h3 {
      margin: 0 0 5px 0;
      font-size: 13pt;
      text-decoration: underline;
    }
    .meta-table {
      width: 100%;
      margin-bottom: 15px;
      font-size: 10pt;
    }
    .meta-table td {
      padding: 2px 4px;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
    }
    .data-table th, .data-table td {
      border: 1px solid #CBD5E1;
      padding: 6px 8px;
    }
    .data-table th {
      background-color: #F1F5F9;
      font-weight: bold;
      text-align: left;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .ttd-box {
      margin-top: 30px;
      width: 100%;
    }
    .ttd-box td {
      width: 50%;
      text-align: center;
      font-size: 10pt;
    }
  </style>
</head>
<body>

  <!-- Kop Surat Lembaga -->
  <div class="kop">
    <h2><?= esc($settings['nama_sekolah'] ?? 'SMK / SMA NEGERI CONTOH') ?></h2>
    <p><?= esc($settings['alamat_sekolah'] ?? 'Jl. Pendidikan No. 1, Jakarta') ?> &nbsp;|&nbsp; Telp: <?= esc($settings['telepon_sekolah'] ?? '021-12345678') ?> &nbsp;|&nbsp; Email: <?= esc($settings['email_sekolah'] ?? 'admin@sekolah.sch.id') ?></p>
  </div>

  <div class="title">
    <h3>REKAPITULASI HASIL EVALUASI / UJIAN SISWA</h3>
    <div style="font-size: 10pt; color: #52666F;">Sistem Computer Based Test (CBT)</div>
  </div>

  <table class="meta-table">
    <tr>
      <td style="width: 15%;"><strong>Nama Ujian</strong></td>
      <td style="width: 35%;">: <?= esc($ujian['nama']) ?></td>
      <td style="width: 15%;"><strong>Waktu Ujian</strong></td>
      <td style="width: 35%;">: <?= date('d/m/Y H:i', strtotime($ujian['mulai_at'])) ?> WIB</td>
    </tr>
    <tr>
      <td><strong>Durasi Waktu</strong></td>
      <td>: <?= esc($ujian['durasi_menit']) ?> Menit</td>
      <td><strong>Nilai Minimal (KKM)</strong></td>
      <td>: <?= esc($ujian['nilai_minimal']) ?></td>
    </tr>
  </table>

  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 30px;">No</th>
        <th style="width: 80px;">NIS</th>
        <th>Nama Lengkap Siswa</th>
        <th style="width: 90px;">Kelas</th>
        <th class="text-center" style="width: 65px;">Skor PG</th>
        <th class="text-center" style="width: 65px;">Skor Esai</th>
        <th class="text-center" style="width: 65px;">Nilai Akhir</th>
        <th class="text-center" style="width: 90px;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <?php $kkm = (float)$ujian['nilai_minimal']; ?>
      <?php foreach ($rekap as $idx => $r): ?>
        <?php 
          $na = (float)($r['nilai_akhir'] ?? 0);
          $isLulus = ($na >= $kkm);
        ?>
        <tr>
          <td class="text-center"><?= $idx + 1 ?></td>
          <td><?= esc($r['nis'] ?: '-') ?></td>
          <td><strong><?= esc($r['nama']) ?></strong></td>
          <td><?= esc($r['nama_kelas'] ?? '-') ?></td>
          <td class="text-center"><?= $r['skor_objektif'] ?? '0.00' ?></td>
          <td class="text-center"><?= $r['skor_uraian'] ?? '0.00' ?></td>
          <td class="text-center" style="font-weight: bold;"><?= round($na, 1) ?></td>
          <td class="text-center"><?= $isLulus ? 'TUNTAS' : 'BELUM TUNTAS' ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Tanda Tangan -->
  <table class="ttd-box">
    <tr>
      <td>
        Mengetahui,<br>
        Kepala Sekolah<br><br><br><br>
        <strong><?= esc($settings['kepala_sekolah'] ?? 'Drs. H. Ahmad Sudirman, M.Pd') ?></strong><br>
        NIP: <?= esc($settings['nip_kepala_sekolah'] ?? '197501012000031001') ?>
      </td>
      <td>
        Jakarta, <?= date('d F Y') ?><br>
        Guru Pengampu / Proktor<br><br><br><br>
        <strong><?= esc(session()->get('nama') ?? 'Guru Pengampu') ?></strong><br>
        NIP / ID: <?= esc(session()->get('username') ?? '-') ?>
      </td>
    </tr>
  </table>

</body>
</html>

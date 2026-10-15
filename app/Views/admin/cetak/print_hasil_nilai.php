<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Nilai - <?= esc($ujian['nama']) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; padding: 20mm; }
    .kop { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 16px; }
    .kop h2 { font-size: 14pt; text-transform: uppercase; }
    .kop p { font-size: 10pt; }
    h3 { text-align: center; font-size: 13pt; text-transform: uppercase; margin: 12px 0; text-decoration: underline; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 3px 6px; vertical-align: top; }
    .info-table td:first-child { width: 35%; font-weight: bold; }
    table.nilai { width: 100%; border-collapse: collapse; }
    table.nilai th, table.nilai td { border: 1px solid #000; padding: 5px 7px; }
    table.nilai th { background: #f0f0f0; text-align: center; }
    table.nilai td.num { text-align: center; }
    .lulus { color: green; font-weight: bold; }
    .tidak-lulus { color: red; }
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

  <h3>Daftar Hasil Nilai Ujian</h3>

  <table class="info-table">
    <tr><td>Nama Ujian</td><td>: <?= esc($ujian['nama']) ?></td></tr>
    <tr><td>Mata Pelajaran</td><td>: <?= esc($ujian['nama_mapel'] ?? '-') ?></td></tr>
    <tr><td>Hari / Tanggal</td><td>: <?= $ujian['mulai_at'] ? date('l, d F Y', strtotime($ujian['mulai_at'])) : '-' ?></td></tr>
    <tr><td>Jumlah Peserta</td><td>: <?= count($hasil) ?> siswa</td></tr>
  </table>

  <table class="nilai">
    <thead>
      <tr>
        <th style="width:40px;">No</th>
        <th>NIS</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th class="num">Benar</th>
        <th class="num">Salah</th>
        <th class="num">Kosong</th>
        <th class="num">Nilai</th>
        <th class="num">Ket.</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $kkm = $ujian['kkm'] ?? 70;
      foreach ($hasil as $i => $h):
        $nilai = round($h['nilai_akhir'] ?? 0, 1);
        $lulus = $nilai >= $kkm;
      ?>
      <tr>
        <td class="num"><?= $i + 1 ?></td>
        <td><?= esc($h['nis']) ?></td>
        <td><?= esc($h['nama_siswa']) ?></td>
        <td class="num"><?= esc($h['nama_kelas'] ?? '-') ?></td>
        <td class="num"><?= $h['benar'] ?? '-' ?></td>
        <td class="num"><?= $h['salah'] ?? '-' ?></td>
        <td class="num"><?= $h['kosong'] ?? '-' ?></td>
        <td class="num <?= $lulus ? 'lulus' : 'tidak-lulus' ?>"><?= $nilai ?: '-' ?></td>
        <td class="num <?= $lulus ? 'lulus' : 'tidak-lulus' ?>"><?= $h['nilai_akhir'] !== null ? ($lulus ? 'Lulus' : 'TL') : '-' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p style="margin-top:10px; font-size:10pt;">KKM: <?= $kkm ?></p>

  <div class="ttd">
    <div class="ttd-item">
      <p><?= date('d F Y') ?></p>
      <p>Guru Mapel</p>
      <div class="garis"></div>
      <p>(________________________)</p>
    </div>
  </div>

  <script>window.onload = function(){ window.print(); }</script>
</body>
</html>

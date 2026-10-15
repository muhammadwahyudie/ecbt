<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<div class="d-flex gap-2">
  <a href="<?= base_url('guru/nilai') ?>" class="btn btn-outline-secondary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>
  <a href="<?= base_url('guru/nilai/ekspor-excel/' . $ujian['id']) ?>" class="btn btn-success btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
    </svg>
    Ekspor Excel
  </a>
  <a href="<?= base_url('guru/nilai/ekspor-pdf/' . $ujian['id']) ?>" class="btn btn-danger btn-sm" target="_blank">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24-1.076-.673-2.684-.77-3.245-.08-.477-.1-.955-.04-1.432.06-.477.25-.918.55-1.282.3-.364.72-.61 1.18-.707.46-.098.94-.038 1.36.173.42.21.75.55.95.97.2.42.27.9.2 1.37-.07.47-.28.9-.6 1.23-.32.33-.74.55-1.2.64l-1.68.273z"/>
    </svg>
    Cetak PDF
  </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- ─── 4 Kartu Statistik Hasil ───────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-info-light text-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= $stats['rata'] ?></div>
        <div class="card-metric-label">Rata-rata Nilai</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-success-light text-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= $stats['tertinggi'] ?></div>
        <div class="card-metric-label">Nilai Tertinggi</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-danger-light text-danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= $stats['terendah'] ?></div>
        <div class="card-metric-label">Nilai Terendah</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-metric">
      <div class="card-metric-icon bg-accent-light text-accent">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="24" height="24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
        </svg>
      </div>
      <div>
        <div class="card-metric-value"><?= $stats['persen'] ?>%</div>
        <div class="card-metric-label">Kelulusan KKM (<?= $stats['lulus'] ?> / <?= $stats['total'] ?>)</div>
      </div>
    </div>
  </div>
</div>

<!-- Tabs Rekap Nilai vs Analisis Butir Soal -->
<ul class="nav nav-pills mb-4" id="nilaiTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active px-4 py-2 fw-semibold" id="tab-rekap-btn" data-bs-toggle="pill" data-bs-target="#tab-rekap" type="button" role="tab">
      Rekapitulasi Nilai Siswa
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link px-4 py-2 fw-semibold" id="tab-analisis-btn" data-bs-toggle="pill" data-bs-target="#tab-analisis" type="button" role="tab">
      Analisis Butir Soal
    </button>
  </li>
</ul>

<div class="tab-content" id="nilaiTabContent">
  <!-- Tab 1: Nilai Siswa -->
  <div class="tab-pane fade show active" id="tab-rekap" role="tabpanel">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Daftar Nilai Peserta (KKM: <?= $ujian['nilai_minimal'] ?>)</h5>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle w-100" id="tableSiswaNilai">
            <thead class="table-light">
              <tr>
                <th style="width: 50px;">No</th>
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>Skor Objektif</th>
                <th>Skor Uraian</th>
                <th>Nilai Akhir</th>
                <th>Keterangan</th>
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
                  <td class="tabular-nums text-secondary"><?= $idx + 1 ?></td>
                  <td class="tabular-nums"><?= esc($r['nis'] ?: '-') ?></td>
                  <td class="fw-bold text-dark"><?= esc($r['nama']) ?></td>
                  <td><span class="badge bg-light text-dark border"><?= esc($r['nama_kelas'] ?? '-') ?></span></td>
                  <td class="tabular-nums"><?= $r['skor_objektif'] ?? '0.00' ?></td>
                  <td class="tabular-nums"><?= $r['skor_uraian'] ?? '0.00' ?></td>
                  <td class="tabular-nums">
                    <span class="badge bg-dark fs-6 px-3 py-1"><?= round($na, 1) ?></span>
                  </td>
                  <td>
                    <?php if ($isLulus): ?>
                      <span class="badge bg-success-light text-success fw-bold">✓ Tuntas</span>
                    <?php else: ?>
                      <span class="badge bg-danger-light text-danger fw-bold">Belum Tuntas</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 2: Analisis Butir Soal -->
  <div class="tab-pane fade" id="tab-analisis" role="tabpanel">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Tingkat Kesukaran & Ketepatan Jawaban Soal</h5>
        <small class="text-secondary">Persentase siswa yang menjawab dengan benar per nomor butir soal</small>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 80px;">Nomor</th>
                <th>Bentuk Soal</th>
                <th>Bobot</th>
                <th>Menjawab</th>
                <th>Benar</th>
                <th style="min-width: 200px;">Ketepatan Menjawab (%)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($analisisSoal as $as): ?>
                <?php 
                  $p = $as['persen_benar'];
                  $barCls = $p >= 75 ? 'bg-success' : ($p >= 50 ? 'bg-primary' : ($p >= 25 ? 'bg-warning' : 'bg-danger'));
                ?>
                <tr>
                  <td class="fw-bold">#<?= $as['urutan'] ?></td>
                  <td>
                    <span class="badge bg-light text-dark border text-capitalize"><?= str_replace('_', ' ', $as['tipe']) ?></span>
                  </td>
                  <td class="tabular-nums"><?= $as['bobot'] ?></td>
                  <td class="tabular-nums"><?= $as['total_jawab'] ?> Siswa</td>
                  <td class="tabular-nums"><?= $as['total_benar'] ?> Siswa</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="height: 8px; background-color: #E8EEF0;">
                        <div class="progress-bar <?= $barCls ?>" style="width: <?= $p ?>%;"></div>
                      </div>
                      <span class="tabular-nums fw-bold small" style="width: 40px;"><?= $p ?>%</span>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('#tableSiswaNilai').DataTable({
    language: {
      search: "Cari siswa:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
      paginate: { next: "›", previous: "‹" }
    }
  });
});
</script>
<?= $this->endSection() ?>

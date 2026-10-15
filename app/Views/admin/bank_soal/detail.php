<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-3">
  <a href="<?= base_url('admin/bank-soal') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Kembali ke Bank Soal
  </a>
</div>

<div class="card mb-4">
  <div class="card-header bg-white py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h4 class="card-title fw-bold mb-1"><?= esc($paket['nama']) ?></h4>
        <div class="text-secondary small d-flex flex-wrap gap-3 align-items-center">
          <span><i class="bi bi-person"></i> Guru: <strong><?= esc($paket['nama_pemilik'] ?? '-') ?></strong></span>
          <span><i class="bi bi-book"></i> Mapel: <strong><?= esc($paket['nama_mapel'] ?? '-') ?></strong></span>
          <?php if (!empty($paket['tingkat'])): ?>
            <span><i class="bi bi-layers"></i> Tingkat: <strong><?= esc($paket['tingkat']) ?></strong></span>
          <?php endif; ?>
          <span><i class="bi bi-question-circle"></i> Total: <strong><?= count($soal) ?> Butir Soal</strong></span>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body p-4">
    <?php if (empty($soal)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        Belum ada butir soal dalam paket ini.
      </div>
    <?php else: ?>
      <?php foreach ($soal as $i => $s): ?>
      <div class="card border shadow-none mb-3">
        <div class="card-body p-3">
          <div class="d-flex gap-3 align-items-start">
            <span class="badge bg-primary fs-6 px-3 py-2 rounded"><?= $i + 1 ?></span>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <span class="badge bg-light text-dark border text-capitalize">
                  <?= str_replace('_', ' ', $s['tipe']) ?>
                </span>
                <span class="badge bg-secondary-subtle text-secondary border">
                  Bobot: <?= $s['bobot'] ?>
                </span>
              </div>

              <div class="fs-6 mb-3 text-break">
                <?= $s['pertanyaan'] ?>
              </div>

              <?php if (!empty($s['opsi'])): ?>
                <div class="d-flex flex-column gap-2 mt-2">
                  <?php foreach ($s['opsi'] as $o): ?>
                    <div class="p-2 px-3 rounded border d-flex gap-2 align-items-center <?= $o['benar'] ? 'bg-success-subtle border-success' : 'bg-light' ?>">
                      <span class="badge <?= $o['benar'] ? 'bg-success' : 'bg-secondary' ?>"><?= esc($o['label']) ?></span>
                      <div class="flex-grow-1 text-break"><?= $o['isi'] ?></div>
                      <?php if ($o['benar']): ?>
                        <span class="badge bg-success"><i class="bi bi-check2"></i> Kunci Jawaban</span>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($s['pedoman'])): ?>
                <div class="alert alert-info py-2 px-3 small mt-3 mb-0">
                  <strong>Pedoman Penskoran:</strong><br><?= nl2br(esc($s['pedoman'])) ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>

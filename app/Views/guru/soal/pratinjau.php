<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<style>
  .preview-question-card {
    background: #FFFFFF;
    border: 1px solid #E1E8EB;
    border-radius: 14px;
    padding: 2rem;
  }
  .preview-opsi-item {
    border: 1.5px solid #CBD5E1;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .preview-opsi-item:hover {
    border-color: #0E7C86;
    background: #F8FAFC;
  }
  .preview-opsi-letter {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #F1F5F9;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('header_action') ?>
<a href="<?= base_url('guru/paket-soal/' . $paket['id']) ?>" class="btn btn-outline-secondary btn-sm">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
  </svg>
  Kembali ke Paket Soal
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-lg-9">
    <div class="alert alert-info py-2 d-flex align-items-center gap-2 mb-3">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      <div>
        <strong>Mode Pratinjau Tampilan Siswa:</strong> Butir soal ditampilkan persis sebagaimana siswa melihatnya saat ujian berlangsung.
      </div>
    </div>

    <div class="preview-question-card shadow-sm mb-4">
      <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <span class="badge bg-dark fs-6 px-3 py-1">Soal Nomor #<?= $soal['urutan'] ?></span>
        <span class="badge bg-light text-secondary border">Bobot: <?= $soal['bobot'] ?></span>
      </div>

      <!-- Pertanyaan -->
      <div class="fs-5 text-dark mb-4" id="previewPertanyaan">
        <?= $soal['pertanyaan'] ?>
      </div>

      <!-- Opsi Siswa -->
      <div class="mt-4">
        <?php if ($soal['tipe'] === 'pilihan_ganda'): ?>
          <?php foreach ($soal['opsi'] as $op): ?>
            <label class="preview-opsi-item">
              <input type="radio" name="preview_ans" class="form-check-input mt-0 me-2">
              <span class="preview-opsi-letter"><?= esc($op['label']) ?></span>
              <div class="fs-6"><?= esc($op['isi']) ?></div>
            </label>
          <?php endforeach; ?>
        <?php elseif ($soal['tipe'] === 'pilihan_ganda_kompleks'): ?>
          <?php foreach ($soal['opsi'] as $op): ?>
            <label class="preview-opsi-item">
              <input type="checkbox" name="preview_ans[]" class="form-check-input mt-0 me-2">
              <span class="preview-opsi-letter"><?= esc($op['label']) ?></span>
              <div class="fs-6"><?= esc($op['isi']) ?></div>
            </label>
          <?php endforeach; ?>
        <?php elseif ($soal['tipe'] === 'benar_salah'): ?>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Pernyataan</th>
                  <th class="text-center" style="width: 100px;">Benar</th>
                  <th class="text-center" style="width: 100px;">Salah</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($soal['opsi'] as $idx => $op): ?>
                  <tr>
                    <td class="text-center fw-bold"><?= $idx + 1 ?></td>
                    <td><?= esc($op['isi']) ?></td>
                    <td class="text-center">
                      <input type="radio" name="bs_row_<?= $idx ?>" value="1" class="form-check-input">
                    </td>
                    <td class="text-center">
                      <input type="radio" name="bs_row_<?= $idx ?>" value="0" class="form-check-input">
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php elseif ($soal['tipe'] === 'isian_singkat'): ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Jawaban Anda:</label>
            <input type="text" class="form-control form-control-lg" placeholder="Ketikkan jawaban isian singkat di sini...">
          </div>
        <?php elseif ($soal['tipe'] === 'uraian'): ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Uraian / Esai Anda:</label>
            <textarea class="form-control" rows="6" placeholder="Ketikkan jawaban uraian lengkap di sini..."></textarea>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  if (typeof renderMathInElement !== 'undefined') {
    renderMathInElement(document.getElementById("previewPertanyaan"), {
      delimiters: [
        {left: '$$', right: '$$', display: true},
        {left: '$', right: '$', display: false}
      ]
    });
  }
});
</script>
<?= $this->endSection() ?>

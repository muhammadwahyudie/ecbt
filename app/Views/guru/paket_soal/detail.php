<?= $this->extend('layout/main') ?>

<?= $this->section('header_action') ?>
<div class="d-flex gap-2">
  <a href="<?= base_url('guru/paket-soal') ?>" class="btn btn-outline-secondary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>
  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImporSoal">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
    </svg>
    Impor Excel
  </button>
  <a href="<?= base_url('guru/soal/' . $paket['id'] . '/tambah') ?>" class="btn btn-primary btn-sm">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="18" height="18" class="me-1">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Tambah Butir Soal
  </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Banner Ringkasan Paket Soal -->
<div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
  <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-accent-light text-accent"><?= esc($paket['nama_mapel']) ?></span>
        <?php if ($paket['tingkat']): ?>
          <span class="badge bg-info-light text-info">Kelas <?= esc($paket['tingkat']) ?></span>
        <?php endif; ?>
        <span class="badge" style="background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.75); font-size: 10px; font-weight: 500;">Paket dikelola Admin</span>
      </div>
      <h3 class="fw-bold mb-1" style="font-family: 'Sora', sans-serif;"><?= esc($paket['nama']) ?></h3>
      <p class="mb-0 text-white-50">Dibuat oleh: <strong><?= esc($paket['nama_pemilik'] ?? 'Admin') ?></strong> &nbsp;&middot;&nbsp; Total: <strong><?= count($soalList) ?> Butir Soal</strong></p>
    </div>
    <div class="text-end">
      <small class="text-white-50 d-block" style="font-size: 11px;">Identitas paket (nama, mapel, tingkat)</small>
      <small class="text-white-50" style="font-size: 11px;">hanya dapat diubah oleh Admin.</small>
    </div>
  </div>
</div>

<!-- Daftar Butir Soal -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Daftar Butir Soal</h5>
    <span class="badge bg-primary fs-6"><?= count($soalList) ?> Butir Soal</span>
  </div>
  <div class="card-body p-0">
    <?php if (empty($soalList)): ?>
      <div class="text-center py-5 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="48" height="48" class="mb-2 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        <p class="mb-2">Paket soal ini masih kosong. Belum ada butir soal yang ditambahkan.</p>
        <a href="<?= base_url('guru/soal/' . $paket['id'] . '/tambah') ?>" class="btn btn-primary btn-sm">
          Tambah Soal Pertama Sekarang
        </a>
      </div>
    <?php else: ?>
      <div class="list-group list-group-flush">
        <?php foreach ($soalList as $idx => $s): ?>
          <div class="list-group-item p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark fs-6 px-2 py-1">#<?= $idx + 1 ?></span>
                <?php
                  $tipeBadge = match($s['tipe']) {
                    'pilihan_ganda'          => ['Pilihan Ganda', 'bg-info-light text-info'],
                    'pilihan_ganda_kompleks'  => ['PG Kompleks', 'bg-accent-light text-accent'],
                    'benar_salah'            => ['Benar / Salah', 'bg-warning-light text-warning'],
                    'isian_singkat'          => ['Isian Singkat', 'bg-secondary-light text-secondary'],
                    'uraian'                 => ['Uraian / Essay', 'bg-success-light text-success'],
                    default                  => ['Soal', 'bg-light text-dark']
                  };
                ?>
                <span class="badge <?= $tipeBadge[1] ?>"><?= $tipeBadge[0] ?></span>
                <span class="badge bg-light text-secondary border">Bobot: <?= $s['bobot'] ?></span>
              </div>

              <!-- Aksi per Butir Soal: Icon ringkas, rapi & clean -->
              <div class="btn-action-group">
                <a href="<?= base_url('guru/soal/pratinjau/' . $s['id']) ?>" class="btn-action btn-action-view" title="Pratinjau Soal">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </a>
                <a href="<?= base_url('guru/soal/' . $paket['id'] . '/edit/' . $s['id']) ?>" class="btn-action btn-action-edit" title="Ubah Soal">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                  </svg>
                </a>
                <button type="button" class="btn-action btn-action-duplicate btn-duplikat-soal" data-id="<?= $s['id'] ?>" title="Duplikasi Soal">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
                  </svg>
                </button>
                <button type="button" class="btn-action btn-action-delete btn-delete-soal" data-id="<?= $s['id'] ?>" data-nomor="<?= $idx + 1 ?>" title="Hapus Soal">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Isi Pertanyaan -->
            <div class="soal-pertanyaan-content my-2 text-dark fs-6">
              <?= $s['pertanyaan'] ?>
            </div>

            <!-- Opsi Jawaban (jika ada) -->
            <?php if (!empty($s['opsi'])): ?>
              <div class="bg-light p-3 rounded-3 mt-3 border">
                <div class="small fw-bold text-secondary mb-2">Pilihan / Opsi Jawaban:</div>
                <div class="row g-2">
                  <?php foreach ($s['opsi'] as $op): ?>
                    <div class="col-12 col-md-6">
                      <div class="d-flex align-items-center gap-2 p-2 rounded <?= $op['benar'] ? 'bg-success-light text-success fw-semibold border border-success' : 'bg-white border' ?>">
                        <span class="badge <?= $op['benar'] ? 'bg-success' : 'bg-secondary' ?>"><?= esc($op['label']) ?></span>
                        <div class="text-truncate flex-grow-1"><?= esc($op['isi']) ?></div>
                        <?php if ($op['benar']): ?>
                          <span class="badge bg-success-light text-success ms-auto">✓ Kunci</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php elseif ($s['tipe'] === 'uraian' && !empty($s['pedoman'])): ?>
              <div class="bg-light p-3 rounded-3 mt-3 border">
                <div class="small fw-bold text-secondary mb-1">Rubrik / Pedoman Penilaian:</div>
                <div class="small text-muted"><?= nl2br(esc($s['pedoman'])) ?></div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>

<!-- Modal Impor Soal dari Excel -->
<div class="modal fade" id="modalImporSoal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Impor Soal dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/soal/impor/' . $paket['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body py-3">
          <div class="mb-3">
            <p class="text-secondary small mb-2">Unduh templat format Excel agar format kolom sesuai sistem:</p>
            <a href="<?= base_url('guru/soal/template/' . $paket['id']) ?>" class="btn btn-outline-primary btn-sm">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16" class="me-1">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
              </svg>
              Unduh Templat Soal (.xlsx)
            </a>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih File Excel <span class="text-danger">*</span></label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
            <small class="text-muted">Maksimal 5 MB. Format: .xlsx, .xls</small>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Unggah &amp; Tambahkan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
  $('.btn-duplikat-soal').on('click', function() {
    const id = $(this).data('id');
    const form = $('<form>', {
      method: 'POST',
      action: `<?= base_url('guru/soal/duplikat') ?>/${id}`
    }).append($('<input>', {
      type: 'hidden',
      name: '<?= csrf_token() ?>',
      value: '<?= csrf_hash() ?>'
    }));
    $('body').append(form);
    form.submit();
  });

  $('.btn-delete-soal').on('click', function() {
    const id = $(this).data('id');
    const nomor = $(this).data('nomor');
    confirmDelete(`<?= base_url('guru/soal/hapus') ?>/${id}`, `Butir soal nomor #${nomor} akan dihapus.`);
  });
});
</script>
<?= $this->endSection() ?>

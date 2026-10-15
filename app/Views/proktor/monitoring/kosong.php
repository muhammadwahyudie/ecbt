<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6 text-center py-5">
    <div class="card p-5">
      <div class="mx-auto mb-3 text-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="56" height="56">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h4 class="fw-bold mb-2">Tidak Ada Sesi Ujian Aktif</h4>
      <p class="text-secondary small mb-4">Saat ini tidak ada sesi ujian yang terjadwal atau sedang berlangsung untuk dipantau.</p>
      <div>
        <a href="<?= base_url('proktor/dashboard') ?>" class="btn btn-outline-secondary btn-sm px-3">
          Kembali ke Dashboard Proktor
        </a>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

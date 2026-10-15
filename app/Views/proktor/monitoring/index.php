<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- ─── Banner Ujian & Token Pengawasan ──────────────────────────── -->
<div class="card mb-4 border-0" style="background-color: #0F2229; color: #FFFFFF; border-radius: 14px;">
  <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="flex-grow-1">
      <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-success">Pengawasan Langsung</span>
        <span class="badge bg-info text-capitalize"><?= esc($selectedUjian['status'] ?? 'Aktif') ?></span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <label class="small text-white-50 text-nowrap">Pilih Sesi Ujian:</label>
        <select id="selectUjian" class="form-select form-select-sm bg-dark text-white border-secondary" style="max-width: 320px;">
          <?php foreach ($ujianList as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($selectedUjian && $selectedUjian['id'] == $u['id']) ? 'selected' : '' ?>>
              <?= esc($u['nama']) ?> (<?= esc($u['status']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Token Card di Header -->
    <div class="d-flex align-items-center gap-3 bg-dark px-3 py-2 rounded-3 border border-secondary">
      <div>
        <div class="small text-white-50 text-uppercase" style="letter-spacing: 1px; font-size: 10px;">Token Ujian</div>
        <div class="fs-4 fw-bold text-warning font-monospace" style="letter-spacing: 3px;" id="tokenDisplay">
          <?= esc($selectedUjian['token'] ?? '------') ?>
        </div>
      </div>
      <button type="button" class="btn btn-outline-light btn-sm" id="btnSalinTokenMonitoring" data-token="<?= esc($selectedUjian['token'] ?? '') ?>">
        Salin
      </button>
    </div>
  </div>
</div>

<!-- ─── Tabel Peserta Monitoring ──────────────────────────────────── -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h5 class="card-title mb-0">Status Peserta Ujian</h5>
      <small class="text-secondary">Pantau aktivitas, progres pengerjaan, dan indikasi kecurangan siswa secara real-time</small>
    </div>

    <div class="d-flex align-items-center flex-wrap gap-2">
      <!-- Polling Status & Manual Refresh -->
      <span class="badge bg-light text-secondary border d-flex align-items-center gap-1 py-1 px-2">
        <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
        <span id="pollCountdown" class="fw-bold tabular-nums">10</span>s
      </span>
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btnManualRefresh" title="Segarkan Data Sekarang">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="15" height="15" class="me-1">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
        </svg>
        Segarkan
      </button>

      <!-- Filter Ruang -->
      <select id="filterRuang" class="form-select form-select-sm" style="width: 140px;">
        <option value="">Semua Ruangan</option>
        <?php foreach ($ruangList as $r): ?>
          <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
        <?php endforeach; ?>
      </select>

      <!-- Filter Status -->
      <select id="filterStatus" class="form-select form-select-sm" style="width: 140px;">
        <option value="">Semua Status</option>
        <option value="belum_login">Belum Masuk</option>
        <option value="mengerjakan">Mengerjakan</option>
        <option value="selesai">Selesai</option>
        <option value="terkunci">Terkunci</option>
      </select>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle w-100" id="tableMonitoring">
        <thead class="table-light">
          <tr>
            <th>Peserta</th>
            <th>Rombel / Kelas</th>
            <th>Status Pengerjaan</th>
            <th style="min-width: 160px;">Progres Soal</th>
            <th>Pelanggaran</th>
            <th>Terakhir Aktif</th>
            <th class="text-end" style="width: 120px;">Aksi Proktor</th>
          </tr>
        </thead>
        <tbody>
          <!-- Diisi via DataTables AJAX Polling -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ─── Modal Tambah Waktu Peserta ─────────────────────────────────── -->
<div class="modal fade" id="modalTambahWaktu" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tambah Waktu Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTambahWaktu">
        <input type="hidden" id="twPesertaId" value="">
        <div class="modal-body py-3">
          <p class="text-secondary small mb-3">Tambah alokasi waktu pengerjaan khusus untuk peserta <strong id="twNamaPeserta"></strong>:</p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Tambahan Durasi</label>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-primary flex-grow-1 btn-quick-menit" data-menit="5">+5 Mnt</button>
              <button type="button" class="btn btn-outline-primary flex-grow-1 btn-quick-menit" data-menit="10">+10 Mnt</button>
              <button type="button" class="btn btn-outline-primary flex-grow-1 btn-quick-menit" data-menit="15">+15 Mnt</button>
              <button type="button" class="btn btn-outline-primary flex-grow-1 btn-quick-menit" data-menit="30">+30 Mnt</button>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Atau Masukkan Menit Kustom</label>
            <div class="input-group">
              <input type="number" id="twMenitCustom" class="form-control" value="10" min="1" max="180">
              <span class="input-group-text">Menit</span>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Terapkan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let curUjianId = <?= $selectedUjian['id'] ?? 0 ?>;
let pollTime = 10;

$(document).ready(function() {
  const table = $('#tableMonitoring').DataTable({
    ajax: {
      url: '<?= base_url('proktor/monitoring/data') ?>',
      data: function(d) {
        d.ujian_id = curUjianId;
        d.ruang_id = $('#filterRuang').val();
        d.status   = $('#filterStatus').val();
      }
    },
    columns: [
      {
        data: 'nama',
        render: function(data, type, row) {
          const onlineDot = row.is_online 
            ? '<span class="spinner-grow spinner-grow-sm text-success me-1" style="width: 8px; height: 8px;"></span>' 
            : '<span class="badge bg-secondary rounded-circle me-1" style="width: 8px; height: 8px; display: inline-block;"></span>';
          return `
            <div class="d-flex align-items-center">
              ${onlineDot}
              <div>
                <div class="fw-bold text-dark">${data}</div>
                <small class="text-muted">${row.username} ${row.nis ? '· NIS ' + row.nis : ''}</small>
              </div>
            </div>
          `;
        }
      },
      {
        data: 'nama_kelas',
        render: function(data) {
          return data ? `<span class="badge bg-light text-dark border">${data}</span>` : '<span class="text-muted">-</span>';
        }
      },
      {
        data: 'status',
        render: function(data, type, row) {
          if (row.terkunci == 1 || data === 'terkunci') {
            return '<span class="badge bg-danger-light text-danger fw-bold">Terkunci</span>';
          }
          const maps = {
            belum_login: '<span class="badge bg-secondary">Belum Masuk</span>',
            mengerjakan: '<span class="badge bg-warning-light text-warning fw-bold">Mengerjakan</span>',
            selesai: '<span class="badge bg-success-light text-success fw-bold">Selesai</span>'
          };
          return maps[data] || data;
        }
      },
      {
        data: null,
        render: function(data, type, row) {
          const total = row.total_soal || 0;
          const jawab = row.dijawab || 0;
          const persen = row.persen || 0;
          return `
            <div>
              <div class="d-flex justify-content-between align-items-center small mb-1">
                <span class="tabular-nums fw-bold">${jawab} / ${total} Soal</span>
                <span class="tabular-nums fw-bold">${persen}%</span>
              </div>
              <div class="progress" style="height: 6px; background-color: #E8EEF0;">
                <div class="progress-bar ${persen === 100 ? 'bg-success' : 'bg-primary'}" style="width: ${persen}%;"></div>
              </div>
            </div>
          `;
        }
      },
      {
        data: 'pelanggaran',
        render: function(data) {
          return data > 0 
            ? `<span class="badge bg-danger px-2">${data} Kali</span>` 
            : '<span class="badge bg-light text-muted border">0</span>';
        }
      },
      {
        data: 'last_seen_at',
        render: function(data) {
          return data ? `<small class="tabular-nums text-muted">${data.split(' ')[1]}</small>` : '<small class="text-muted">-</small>';
        }
      },
      {
        data: null,
        orderable: false,
        className: 'text-end',
        render: function(data, type, row) {
          return `
            <div class="dropdown">
              <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Aksi
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><button class="dropdown-item btn-act-reset" data-id="${row.id}" data-nama="${row.nama}">Reset Sesi Login</button></li>
                ${row.terkunci == 1 ? `<li><button class="dropdown-item text-success fw-bold btn-act-unlock" data-id="${row.id}" data-nama="${row.nama}">Buka Kunci</button></li>` : ''}
                <li><button class="dropdown-item btn-act-waktu" data-id="${row.id}" data-nama="${row.nama}">Tambah Waktu</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item text-danger btn-act-finish" data-id="${row.id}" data-nama="${row.nama}">Paksa Selesai</button></li>
              </ul>
            </div>
          `;
        }
      }
    ],
    language: {
      search: "Cari peserta:",
      lengthMenu: "Tampilkan _MENU_",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ peserta",
      paginate: { next: "›", previous: "‹" }
    }
  });

  // Switch Ujian
  $('#selectUjian').on('change', function() {
    window.location.href = `<?= base_url('proktor/monitoring') ?>/${this.value}`;
  });

  // Filters
  $('#filterRuang, #filterStatus').on('change', function() {
    table.ajax.reload(null, false);
  });

  // 10-Second Polling
  setInterval(() => {
    pollTime--;
    if (pollTime <= 0) {
      pollTime = 10;
      table.ajax.reload(null, false);
    }
    $('#pollCountdown').text(pollTime);
  }, 1000);

  $('#btnManualRefresh').on('click', () => {
    pollTime = 10;
    table.ajax.reload(null, false);
    showToast('info', 'Data pengawasan diperbarui');
  });

  // Salin Token
  $('#btnSalinTokenMonitoring').on('click', function() {
    const token = $(this).data('token');
    navigator.clipboard.writeText(token).then(() => {
      showToast('success', 'Token disalin ke clipboard!');
    });
  });

  // ─── Aksi Proktor ─────────────────────────────────────────────────
  // 1. Reset Login
  $(document).on('click', '.btn-act-reset', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmAction({
      title: 'Reset Login?',
      text: `Sesi login peserta "${nama}" akan direset sehingga siswa dapat masuk kembali jika terjadi kendala perangkat.`,
      icon: 'question',
      confirmButtonText: 'Ya, Reset Login'
    }).then((res) => {
      if (res.isConfirmed) {
        $.post(`<?= base_url('proktor/monitoring/reset-login') ?>/${id}`, function(resp) {
          showToast('success', resp.message);
          table.ajax.reload(null, false);
        });
      }
    });
  });

  // 2. Buka Kunci
  $(document).on('click', '.btn-act-unlock', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmAction({
      title: 'Buka Kunci Ujian?',
      text: `Kunci ujian peserta "${nama}" akan dibuka dan pelanggaran direset.`,
      icon: 'warning',
      confirmButtonColor: '#0E7C86',
      confirmButtonText: 'Buka Kunci'
    }).then((res) => {
      if (res.isConfirmed) {
        $.post(`<?= base_url('proktor/monitoring/buka-kunci') ?>/${id}`, function(resp) {
          showToast('success', resp.message);
          table.ajax.reload(null, false);
        });
      }
    });
  });

  // 3. Tambah Waktu
  $(document).on('click', '.btn-act-waktu', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    $('#twPesertaId').val(id);
    $('#twNamaPeserta').text(nama);
    new bootstrap.Modal(document.getElementById('modalTambahWaktu')).show();
  });

  $('.btn-quick-menit').on('click', function() {
    $('#twMenitCustom').val($(this).data('menit'));
  });

  $('#formTambahWaktu').on('submit', function(e) {
    e.preventDefault();
    const id = $('#twPesertaId').val();
    const menit = $('#twMenitCustom').val();
    $.post(`<?= base_url('proktor/monitoring/tambah-waktu') ?>/${id}`, { menit: menit }, function(resp) {
      bootstrap.Modal.getInstance(document.getElementById('modalTambahWaktu')).hide();
      showToast('success', resp.message);
      table.ajax.reload(null, false);
    });
  });

  // 4. Paksa Selesai
  $(document).on('click', '.btn-act-finish', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    confirmAction({
      title: 'Paksa Selesaikan Ujian?',
      text: `Ujian peserta "${nama}" akan dihentikan seketika dan langsung dinilai oleh sistem.`,
      icon: 'warning',
      confirmButtonColor: '#DC2626',
      confirmButtonText: 'Ya, Paksa Selesai'
    }).then((res) => {
      if (res.isConfirmed) {
        $.post(`<?= base_url('proktor/monitoring/paksa-selesai') ?>/${id}`, function(resp) {
          showToast('success', resp.message);
          table.ajax.reload(null, false);
        });
      }
    });
  });
});
</script>
<?= $this->endSection() ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Cetak Kartu Ujian') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ─── RESET & SPESIFIKASI HALAMAN A4 SESUAI KARTU.MD ─── */
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    @page {
      size: A4 portrait;
      margin: 10mm; /* Simetris 10mm semua sisi */
    }

    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Open Sans', Arial, Helvetica, sans-serif;
      color: #000;
      background: #eef2f6;
    }

    /* ─── TOOLBAR KONTROL (LAYAR SAJA) ─── */
    .toolbar {
      position: sticky;
      top: 0;
      z-index: 9999;
      background: #1e293b;
      color: #f8fafc;
      padding: 10px 18px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      font-size: 13px;
    }

    .toolbar-left, .toolbar-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .toolbar-label {
      display: flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      user-select: none;
      font-weight: 500;
    }

    .toolbar-label input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: #2563eb;
      cursor: pointer;
    }

    .btn-print {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 7px 16px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.2s;
    }
    .btn-print:hover { background: #1d4ed8; }

    .btn-close {
      background: #475569;
      color: #fff;
      border: none;
      padding: 7px 14px;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .btn-close:hover { background: #334155; }

    .toolbar-info {
      color: #94a3b8;
      font-size: 12px;
    }

    /* ─── WRAPPER DOKUMEN CETAK ─── */
    .document-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
    }

    /* Tiap lembar Halaman A4: 190mm × 277mm area efektif di dalam margin 10mm */
    .halaman {
      width: 190mm;
      height: 277mm;
      background: #fff;
      display: grid;
      grid-template-rows: repeat(5, 53mm);
      row-gap: 3mm;
      box-sizing: border-box;
      margin-bottom: 25px;
      padding: 0;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      page-break-after: always;
      break-after: page;
      overflow: hidden;
    }

    .halaman:last-child {
      margin-bottom: 0;
      page-break-after: auto;
      break-after: auto;
    }

    /* ─── SATU BARIS (SEPASANG KARTU) ─── */
    .baris {
      display: grid;
      grid-template-columns: 93mm 93mm;
      column-gap: 4mm;
      height: 53mm;
      box-sizing: border-box;
    }

    /* ─── KARTU DASAR (93mm × 53mm) ─── */
    .kartu {
      width: 93mm;
      height: 53mm;
      border: 0.3mm solid #000;
      padding: 2.2mm 2.8mm;
      position: relative;
      overflow: hidden;
      break-inside: avoid;
      page-break-inside: avoid;
      box-sizing: border-box;
      background: #fff;
      display: flex;
      flex-direction: column;
    }

    .kartu-kosong {
      border: 0.3mm dashed #d1d5db !important;
      background: #fdfdfd;
    }

    /* ─── HEADER UMUM KARTU (KOP) ─── */
    .kartu-header {
      display: flex;
      align-items: center;
      gap: 2mm;
      height: 9.6mm;
      position: relative;
      padding-right: 20mm; /* Ruang untuk badge pojok kanan atas */
      padding-bottom: 1mm;
      border-bottom: 1.2px solid #000; /* Garis pembatas di bawah kop kartu */
      margin-bottom: 0.8mm;
    }

    .header-logo {
      width: 8.8mm;
      height: 8.8mm;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .header-logo img, .header-logo svg {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .header-titles {
      flex-grow: 1;
      text-align: center;
      line-height: 1.15;
    }

    .header-titles .t1 {
      font-size: 8.2pt;
      font-weight: 800;
      letter-spacing: 0.3px;
    }
    .header-titles .t2 {
      font-size: 6.8pt;
      font-weight: 700;
      letter-spacing: 0.2px;
    }
    .header-titles .t3 {
      font-size: 6.8pt;
      font-weight: 700;
    }

    /* ─── BADGE SUDUT KANAN ATAS ─── */
    /* Kartu Meja: kotak hitam solid teks putih */
    .badge-meja {
      position: absolute;
      top: 0;
      right: 0;
      background: #000;
      color: #fff;
      font-size: 6.5pt;
      font-weight: 800;
      padding: 1.5px 5px;
      line-height: 1.1;
      letter-spacing: 0.4px;
    }

    /* Kartu Peserta: kotak border garis hitam teks hitam */
    .badge-peserta {
      position: absolute;
      top: 0;
      right: 0;
      border: 0.3mm solid #000;
      background: #fff;
      color: #000;
      font-size: 6.5pt;
      font-weight: 800;
      padding: 1px 4.5px;
      line-height: 1.1;
      letter-spacing: 0.3px;
    }

    /* ─── SPESIFIK: KARTU MEJA (KIRI) ─── */
    .kartu-meja-body {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding-top: 0.3mm;
    }

    .meja-foto {
      width: 17.5mm;
      height: 21mm;
      border: 0.25mm solid #777;
      background: #fafafa;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      flex-shrink: 0;
    }
    .meja-foto img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .meja-nomor {
      font-size: 10.5pt;
      font-weight: 800;
      line-height: 1.1;
      text-align: center;
      letter-spacing: 0.3px;
      margin-top: 1mm;
      color: #000;
    }

    .meja-divider {
      width: 100%;
      border: none;
      border-top: 1.4px solid #000;
      margin: 0.8mm 0 1mm 0;
    }

    .meja-nama {
      font-size: 8pt;
      font-weight: 800;
      text-transform: uppercase;
      text-align: center;
      line-height: 1.15;
      width: 100%;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .meja-ruang-badge {
      display: inline-block;
      border: 0.3mm dashed #444;
      background: #eef1f5;
      border-radius: 3px;
      padding: 1.2px 8px;
      font-size: 6.8pt;
      font-weight: 800;
      line-height: 1.15;
      margin-top: 1mm;
      text-align: center;
    }

    /* ─── SPESIFIK: KARTU PESERTA (KANAN) ─── */
    .kartu-peserta-body {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding-top: 0.5mm;
    }

    .tabel-info {
      width: 100%;
      border-collapse: collapse;
      font-size: 6.8pt;
      line-height: 1.35;
      margin-top: 0.5mm;
    }
    .tabel-info td {
      padding: 0.2mm 0;
      vertical-align: top;
    }
    .tabel-info .col-lbl {
      width: 21mm;
      font-weight: 500;
    }
    .tabel-info .col-sep {
      width: 2.2mm;
      text-align: center;
    }
    .tabel-info .col-val {
      font-weight: 600;
      color: #000;
    }
    .tabel-info .val-nomor {
      font-weight: 800;
    }

    .peserta-bottom-row {
      display: grid;
      grid-template-columns: 16mm 1fr 14.5mm;
      column-gap: 2mm;
      align-items: flex-end;
      height: 22.5mm;
      margin-top: auto;
    }

    .peserta-foto {
      width: 16mm;
      height: 21mm;
      border: 0.25mm solid #777;
      background: #fafafa;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      flex-shrink: 0;
    }
    .peserta-foto img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .peserta-ttd {
      font-size: 6.2pt;
      line-height: 1.2;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding-bottom: 0.5mm;
    }
    .ttd-kota-tgl {
      font-weight: 500;
    }
    .ttd-jabatan {
      font-weight: 500;
      margin-bottom: 7mm; /* Ruang tanda tangan */
    }
    .ttd-nama {
      font-weight: 800;
      text-decoration: underline;
    }
    .ttd-nip {
      font-weight: 500;
    }

    .peserta-qr {
      width: 14.5mm;
      height: 14.5mm;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.5mm;
    }
    .peserta-qr canvas, .peserta-qr img {
      width: 100% !important;
      height: 100% !important;
    }

    /* ─── KONTROL FOTO DARI TOOLBAR ─── */
    body.tanpa-foto .foto-peserta-img {
      visibility: hidden !important;
    }
    body.tanpa-foto.tanpa-bingkai .meja-foto,
    body.tanpa-foto.tanpa-bingkai .peserta-foto {
      border-color: transparent !important;
      background: transparent !important;
    }

    /* ─── MEDIA PRINT ─── */
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background: #fff;
      }
      .document-container {
        padding: 0;
      }
      .halaman {
        box-shadow: none;
        margin-bottom: 0;
      }
    }
  </style>
</head>
<body>

  <!-- ─── TOOLBAR KONTROL ATAS (TIDAK IKUT TERCETAK) ─── -->
  <header class="toolbar no-print">
    <div class="toolbar-left">
      <label class="toolbar-label" title="Tampilkan atau sembunyikan foto peserta">
        <input type="checkbox" id="toggleFoto" checked>
        Cetak dengan foto
      </label>
      <label class="toolbar-label" title="Sembunyikan garis bingkai bila tidak ingin menempel foto">
        <input type="checkbox" id="toggleBingkai">
        Sembunyikan bingkai juga
      </label>
      <span class="toolbar-info">
        Total: <?= esc($totalPeserta) ?> Peserta &bull; <?= count($pages) ?> Lembar A4
      </span>
    </div>

    <div class="toolbar-right">
      <button type="button" class="btn-print" id="btnCetak">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/>
        </svg>
        Cetak Langsung
      </button>
      <a href="<?= base_url('admin/cetak/kartu') ?>" class="btn-close">
        &times; Kembali
      </a>
    </div>
  </header>

  <!-- ─── AREA DOKUMEN CETAK ─── -->
  <main class="document-container">
    <?php foreach ($pages as $pIndex => $chunk): ?>
    <section class="halaman">
      <?php for ($r = 0; $r < 5; $r++): ?>
        <?php if (isset($chunk[$r])): $p = $chunk[$r]; ?>
        <div class="baris">
          <!-- ─── KARTU MEJA (KIRI) ─── -->
          <article class="kartu kartu-meja">
            <header class="kartu-header">
              <div class="header-logo">
                <?php if (!empty($logo)): ?>
                  <img src="<?= base_url($logo) ?>" alt="Logo">
                <?php else: ?>
                  <!-- Logo Emblem Standar Pendidikan -->
                  <svg viewBox="0 0 100 100" fill="currentColor">
                    <polygon points="50,5 92,28 92,72 50,95 8,72 8,28" fill="#1e3a8a"/>
                    <polygon points="50,15 82,33 82,67 50,85 18,67 18,33" fill="#ffffff"/>
                    <path d="M50 25 L65 42 L35 42 Z" fill="#d97706"/>
                    <path d="M50 42 L50 75 M32 58 L50 50 L68 58" stroke="#1e3a8a" stroke-width="5" stroke-linecap="round" fill="none"/>
                  </svg>
                <?php endif; ?>
              </div>
              <div class="header-titles">
                <div class="t1"><?= esc($judulBaris1) ?></div>
                <div class="t2"><?= esc($judulBaris2) ?></div>
                <div class="t3"><?= esc($judulBaris3) ?></div>
              </div>
              <div class="badge-meja">KARTU MEJA</div>
            </header>

            <div class="kartu-meja-body">
              <div class="meja-foto">
                <?php if (!empty($p['foto'])): ?>
                  <img src="<?= base_url($p['foto']) ?>" class="foto-peserta-img" alt="Foto">
                <?php else: ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" width="22" height="22" class="foto-peserta-img">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                <?php endif; ?>
              </div>

              <div class="meja-nomor"><?= esc($p['nomor_peserta']) ?></div>
              <hr class="meja-divider">
              <div class="meja-nama" title="<?= esc($p['nama']) ?>"><?= esc($p['nama']) ?></div>
              <div>
                <span class="meja-ruang-badge">RUANG: <?= esc($p['ruang']) ?></span>
              </div>
            </div>
          </article>

          <!-- ─── KARTU PESERTA (KANAN) ─── -->
          <article class="kartu kartu-peserta">
            <header class="kartu-header">
              <div class="header-logo">
                <?php if (!empty($logo)): ?>
                  <img src="<?= base_url($logo) ?>" alt="Logo">
                <?php else: ?>
                  <svg viewBox="0 0 100 100" fill="currentColor">
                    <polygon points="50,5 92,28 92,72 50,95 8,72 8,28" fill="#1e3a8a"/>
                    <polygon points="50,15 82,33 82,67 50,85 18,67 18,33" fill="#ffffff"/>
                    <path d="M50 25 L65 42 L35 42 Z" fill="#d97706"/>
                    <path d="M50 42 L50 75 M32 58 L50 50 L68 58" stroke="#1e3a8a" stroke-width="5" stroke-linecap="round" fill="none"/>
                  </svg>
                <?php endif; ?>
              </div>
              <div class="header-titles">
                <div class="t1"><?= esc($judulBaris1) ?></div>
                <div class="t2"><?= esc($judulBaris2) ?></div>
                <div class="t3"><?= esc($judulBaris3) ?></div>
              </div>
              <div class="badge-peserta">KARTU PESERTA</div>
            </header>

            <div class="kartu-peserta-body">
              <table class="tabel-info">
                <tr>
                  <td class="col-lbl">Nomor Peserta</td>
                  <td class="col-sep">:</td>
                  <td class="col-val val-nomor"><?= esc($p['nomor_peserta']) ?></td>
                </tr>
                <tr>
                  <td class="col-lbl">Nama Peserta</td>
                  <td class="col-sep">:</td>
                  <td class="col-val"><?= esc($p['nama']) ?></td>
                </tr>
                <tr>
                  <td class="col-lbl">Kelas</td>
                  <td class="col-sep">:</td>
                  <td class="col-val"><?= esc($p['kelas']) ?></td>
                </tr>
                <tr>
                  <td class="col-lbl">Ruang</td>
                  <td class="col-sep">:</td>
                  <td class="col-val"><?= esc($p['ruang']) ?></td>
                </tr>
              </table>

              <div class="peserta-bottom-row">
                <!-- Foto Kiri -->
                <div class="peserta-foto">
                  <?php if (!empty($p['foto'])): ?>
                    <img src="<?= base_url($p['foto']) ?>" class="foto-peserta-img" alt="Foto">
                  <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" width="20" height="20" class="foto-peserta-img">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  <?php endif; ?>
                </div>

                <!-- Tanda Tangan Tengah -->
                <div class="peserta-ttd">
                  <div class="ttd-kota-tgl"><?= esc($kota) ?>, <?= esc($tanggal) ?></div>
                  <div class="ttd-jabatan">Kepala Satuan Pendidikan,</div>
                  <div class="ttd-nama"><?= esc($kepalaSekolah) ?></div>
                  <div class="ttd-nip">NIP. <?= esc($nip ?: '-') ?></div>
                </div>

                <!-- QR Code Kanan -->
                <div class="peserta-qr qr-box" data-code="<?= esc($p['qr_data']) ?>" title="<?= esc($p['qr_data']) ?>"></div>
              </div>
            </div>
          </article>
        </div>
        <?php else: ?>
        <!-- Baris sisa kosong bila peserta tidak genap kelipatan 5 (menjaga margin & presisi A4) -->
        <div class="baris">
          <div class="kartu kartu-kosong"></div>
          <div class="kartu kartu-kosong"></div>
        </div>
        <?php endif; ?>
      <?php endfor; ?>
    </section>
    <?php endforeach; ?>
  </main>

  <!-- ─── LIBRARY QR CODE STANDAR (OFFLINE LOCAL + FALLBACK) ─── -->
  <script src="<?= base_url('assets/js/qrcode.min.js') ?>"></script>
  <script>
    // Inisialisasi QR Code
    function initQRCodes() {
      if (typeof QRCode === 'undefined') {
        console.warn('QRCode library not loaded yet');
        return;
      }
      document.querySelectorAll('.qr-box').forEach(el => {
        if (el.childNodes.length === 0) {
          const code = el.getAttribute('data-code');
          if (code) {
            new QRCode(el, {
              text: code,
              width: 54,
              height: 54,
              colorDark: "#000000",
              colorLight: "#ffffff",
              correctLevel: QRCode.CorrectLevel.M
            });
          }
        }
      });
    }

    // Toggle Cetak Foto
    const chkFoto = document.getElementById('toggleFoto');
    const chkBingkai = document.getElementById('toggleBingkai');

    function applyFotoPreference() {
      document.body.classList.toggle('tanpa-foto', !chkFoto.checked);
      document.body.classList.toggle('tanpa-bingkai', chkBingkai.checked);
      try {
        localStorage.setItem('cetakFoto', chkFoto.checked ? '1' : '0');
        localStorage.setItem('cetakBingkai', chkBingkai.checked ? '1' : '0');
      } catch (e) {}
    }

    // Baca preferensi dari localStorage
    try {
      if (localStorage.getItem('cetakFoto') !== null) {
        chkFoto.checked = localStorage.getItem('cetakFoto') === '1';
      }
      if (localStorage.getItem('cetakBingkai') !== null) {
        chkBingkai.checked = localStorage.getItem('cetakBingkai') === '1';
      }
    } catch (e) {}

    chkFoto.addEventListener('change', applyFotoPreference);
    chkBingkai.addEventListener('change', applyFotoPreference);
    applyFotoPreference();

    // Tombol Cetak Langsung (Opsi A kartu.md)
    document.getElementById('btnCetak').addEventListener('click', () => {
      applyFotoPreference();
      window.print();
    });

    // Inisialisasi saat DOM siap
    document.addEventListener('DOMContentLoaded', () => {
      initQRCodes();
    });
    window.addEventListener('load', () => {
      initQRCodes();
    });
  </script>
</body>
</html>

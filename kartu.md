# Rencana Desain & Cetak Kartu Ujian (Kartu Peserta + Kartu Meja)

Dokumen ini adalah rencana implementasi untuk mereplikasi desain kartu pada file `Aplikasi_Cetak_Kartu_Ujian.pdf`, dengan margin yang rapi, fitur **aktif/nonaktif cetak foto**, ukuran **A4**, dan **cetak langsung tanpa preview**.

---

## 1. Analisis Desain Lampiran

Setiap baris halaman berisi **sepasang kartu** (Kartu Peserta di kiri, Kartu Meja di kanan):

| Kolom | Jenis Kartu | Isi |
|---|---|---|
| Kiri | **KARTU PESERTA** | Logo, judul, label "KARTU PESERTA" (pojok kanan atas), data (Nomor Peserta, Nama Peserta, Kelas, Ruang), foto di kiri bawah, tempat/tanggal + jabatan + nama & NIP Kepala Satuan Pendidikan, QR Code di kanan bawah |
| Kanan | **KARTU MEJA** | Logo, judul, label "KARTU MEJA" (pojok kanan atas), foto di tengah, nomor peserta (tebal, besar), garis pemisah, nama peserta, kotak putus-putus "RUANG: ..." |

- Judul (tengah, tebal, 3 baris): `KARTU PESERTA` / `TES KEMAMPUAN AKADEMIK SMP/MTS` / `TAHUN 2026`
- **5 baris per halaman**, 10 kartu per halaman (5 kartu peserta di kiri + 5 kartu meja di kanan).
- Contoh data: 15 peserta → 3 halaman (kelas IX-A, IX-B, IX-C; Ruang 01 dan Ruang 02).

### Masalah pada lampiran yang diperbaiki
1. Logo tampil sebagai gambar rusak (broken image) → sediakan logo sebenarnya (atau placeholder yang rapi jika kosong).
2. Foto berupa kotak merah placeholder → ganti foto asli; bila tidak ada, tampilkan bingkai kosong netral (bukan merah).
3. Kartu menempel ke tepi kiri/kanan kertas dan ada ruang kosong tidak seimbang di bawah → atur margin simetris.
4. Tinggi/lebar kartu tidak presisi → gunakan satuan `mm` agar hasil cetak konsisten.
5. Nama Kepala Satuan Pendidikan dan "NIP. -" perlu ditata ulang (nama + garis bawah, NIP di bawahnya).

---

## 2. Spesifikasi Ukuran Kertas & Margin (A4)

- Kertas: **A4 = 210 mm × 297 mm**, orientasi **portrait**
- Margin halaman (simetris):
  - Atas & bawah: **10 mm**
  - Kiri & kanan: **10 mm**
- Area cetak efektif: **190 mm × 277 mm**
- Jarak antar kartu: **4 mm** (horizontal) dan **3 mm** (vertikal)

**Perhitungan ukuran kartu (5 baris × 2 kolom):**

- Lebar kartu = (190 − 4) / 2 = **93 mm**
- Tinggi kartu = (277 − 4 × 3) / 5 = **53 mm**

> Catatan: sebagian besar printer tidak bisa mencetak sampai tepi kertas (tepi tak tercetak ± 4–5 mm). Margin 10 mm aman untuk hampir semua printer.

### Padding dalam kartu
- Border kartu: 0,3 mm solid hitam
- Padding dalam: 3 mm
- Font: sans-serif (mis. Open Sans / Arial). Judul 9–10 pt tebal; nomor peserta (kartu meja) 13–14 pt tebal; data 8–9 pt.

---

## 3. Struktur Halaman (HTML + CSS)

### Kerangka HTML

```html
<body class="">
  <div class="toolbar no-print">
    <label>
      <input type="checkbox" id="toggleFoto" checked>
      Cetak dengan foto
    </label>
    <button id="btnCetak">Cetak Langsung</button>
  </div>

  <section class="halaman">
    <!-- diulang 5 baris -->
    <div class="baris">
      <article class="kartu kartu-peserta">...</article>  <!-- kiri -->
      <article class="kartu kartu-meja">...</article>      <!-- kanan -->
    </div>
  </section>
</body>
```

### CSS utama

```css
@page {
  size: A4 portrait;
  margin: 10mm;            /* margin kiri, kanan, atas, bawah rapi */
}

* { box-sizing: border-box; }

html, body { margin: 0; padding: 0; }

.halaman {
  width: 190mm;
  height: 277mm;
  display: grid;
  grid-template-rows: repeat(5, 53mm);
  row-gap: 3mm;
  page-break-after: always;
  break-after: page;
}

.baris {
  display: grid;
  grid-template-columns: 93mm 93mm;   /* kiri: Kartu Peserta, kanan: Kartu Meja */
  column-gap: 4mm;
}

.kartu {
  width: 93mm;
  height: 53mm;
  border: 0.3mm solid #000;
  padding: 3mm;
  position: relative;
  overflow: hidden;
  break-inside: avoid;
  page-break-inside: avoid;
}

/* Agar warna/garis ikut tercetak persis */
* { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

@media print {
  .no-print { display: none !important; }
}
```

### Tata letak Kartu Meja
- Header: logo (kiri) + judul 3 baris (tengah)
- Label "KARTU MEJA": kotak hitam teks putih, pojok kanan atas
- Foto: tengah, ± 20 × 26 mm (rasio 3:4)
- Nomor peserta: tebal, di bawah foto, di atas garis
- Garis horizontal tebal sebagai pemisah
- Nama peserta: tebal, tengah
- Badge ruang: kotak berborder putus-putus, latar abu-abu muda, rounded

### Tata letak Kartu Peserta
- Header sama dengan Kartu Meja
- Label "KARTU PESERTA": kotak outline, pojok kanan atas
- Data memakai grid 2 kolom (label | `: nilai`), nomor peserta ditebalkan
- Foto: kiri bawah, ± 20 × 26 mm
- Blok tanda tangan: tengah-kanan (tempat, tanggal, jabatan, nama dengan garis bawah, NIP)
- QR Code: kanan bawah, ± 16 × 16 mm, berisi nomor peserta

---

## 4. Fitur Aktif / Nonaktif Cetak dengan Foto

### Perilaku
- Toggle **"Cetak dengan foto"** pada toolbar (tidak ikut tercetak).
- **Aktif (default)**: foto peserta tampil di Kartu Meja & Kartu Peserta.
- **Nonaktif**: foto disembunyikan, **tata letak tidak bergeser** (ruang foto tetap dipertahankan sebagai bingkai kosong, berguna jika foto akan ditempel manual). Sediakan opsi tambahan "Sembunyikan bingkai juga" bila ingin ruang kosong total.
- Pilihan disimpan (localStorage) agar diingat saat aplikasi dibuka lagi.

### Implementasi

```css
.foto {
  width: 20mm;
  height: 26mm;
  border: 0.2mm solid #999;
  background: #fff;
  object-fit: cover;
}

/* Mode tanpa foto: gambar disembunyikan, bingkai & ukuran tetap */
body.tanpa-foto .foto img { visibility: hidden; }

/* Opsi: sembunyikan bingkai juga */
body.tanpa-foto.tanpa-bingkai .foto { border: 0; }
```

```js
const chk = document.getElementById('toggleFoto');

function terapkanFoto() {
  document.body.classList.toggle('tanpa-foto', !chk.checked);
  try { localStorage.setItem('cetakFoto', chk.checked ? '1' : '0'); } catch (e) {}
}

try { chk.checked = localStorage.getItem('cetakFoto') !== '0'; } catch (e) {}
chk.addEventListener('change', terapkanFoto);
terapkanFoto();
```

Jika foto belum tersedia untuk peserta tertentu, tampilkan bingkai kosong netral (bukan kotak merah).

---

## 5. Cetak Langsung Tanpa Preview

> **Penting:** `window.print()` di browser **selalu** membuka dialog cetak/preview. Browser sengaja memblokir cetak diam-diam demi keamanan. Agar benar-benar langsung mencetak, ada 3 opsi di bawah.

### Opsi A — Chrome/Edge mode Kiosk Printing (paling sederhana, tanpa instalasi tambahan)
Jalankan aplikasi lewat shortcut dengan flag:

```
chrome.exe --kiosk-printing --app=http://localhost:8000/kartu.html
```

- Tombol **Cetak Langsung** cukup memanggil `window.print()`; cetak langsung terkirim ke **printer default** tanpa dialog.
- Pastikan printer default sudah diatur ke **A4**, orientasi portrait, skala 100%.
- Cocok untuk komputer panitia/operator tetap.

### Opsi B — QZ Tray (cetak silent dari web, bisa memilih printer)
- Pasang aplikasi QZ Tray di komputer klien, lalu kirim dokumen (HTML/PDF) via JavaScript.
- Bisa memilih printer tertentu dan pengaturan ukuran kertas dari kode.
- Perlu sertifikat/izin agar tidak muncul prompt konfirmasi.

### Opsi C — Cetak dari sisi server (PDF → printer)
- Server membuat PDF A4 (mis. Puppeteer / wkhtmltopdf / library PDF) lalu mengirim ke printer via CUPS (`lp`) atau perintah print Windows.
- Paling stabil untuk jaringan banyak klien; hasil identik di semua perangkat.

**Rekomendasi:** mulai dengan **Opsi A** (cepat, tanpa dependensi), pindah ke Opsi B/C jika butuh pilih printer otomatis atau multi-komputer.

### Kode tombol

```js
document.getElementById('btnCetak').addEventListener('click', () => {
  terapkanFoto();       // pastikan status foto terbaru
  window.print();       // dengan --kiosk-printing: langsung cetak tanpa preview
});
```

---

## 6. Alur Data

1. Ambil data peserta: `nomor`, `nama`, `kelas`, `ruang`, `fotoUrl`.
2. Ambil data sekolah: nama kota, tanggal, nama Kepala Satuan Pendidikan, NIP, logo.
3. Bagi data menjadi kelompok **5 peserta per halaman**.
4. Untuk tiap peserta render satu `.baris` (Kartu Peserta di kiri + Kartu Meja di kanan).
5. Buat QR Code dari nomor peserta (library QR, di-render sebagai SVG/canvas agar tajam saat cetak).
6. Jika jumlah peserta tidak habis dibagi 5, baris sisa dibiarkan kosong (jangan merusak margin).

---

## 7. Daftar Tugas (Checklist)

- [ ] Siapkan logo sekolah/instansi (PNG/SVG) dan foto peserta
- [ ] Buat template HTML Kartu Peserta (kolom kiri)
- [ ] Buat template HTML Kartu Meja (kolom kanan)
- [ ] Terapkan CSS `@page` A4 dengan margin 10 mm di semua sisi
- [ ] Atur grid 5 baris × 2 kolom (kartu 93 × 53 mm, gap 4 mm / 3 mm)
- [ ] Tambah QR Code pada Kartu Peserta
- [ ] Tambah toggle "Cetak dengan foto" + simpan preferensi
- [ ] Tambah tombol "Cetak Langsung"
- [ ] Atur mode cetak tanpa preview (Kiosk Printing / QZ Tray / server)
- [ ] Uji cetak 1 halaman di kertas biasa lalu cek margin & potongan kartu
- [ ] Uji data nama panjang (teks tidak boleh keluar kartu; gunakan pengecilan font otomatis)
- [ ] Uji tanpa foto (toggle nonaktif) dan dengan foto

---

## 8. Pengujian & Kriteria Selesai

- Margin kiri = kanan = atas = bawah = 10 mm (ukur dengan penggaris pada hasil cetak).
- Semua kartu berukuran sama (93 × 53 mm) dan tidak terpotong di tepi kertas.
- Tidak ada kartu yang terpecah antar halaman.
- Toggle foto bekerja dan tata letak tidak bergeser.
- Tombol cetak langsung mengirim ke printer tanpa preview pada mode yang dipilih.
- Hasil cetak mengikuti desain lampiran (judul, label, badge ruang, QR, tanda tangan).

---

## 9. Pengaturan Printer yang Disarankan

- Ukuran kertas: **A4**
- Orientasi: **Portrait**
- Skala: **100% / Actual size** (jangan "Fit to page")
- Margin: **Default / None** (margin sudah diatur lewat CSS `@page`)
- Opsi *Background graphics*: **aktif** (agar label hitam dan badge abu-abu tercetak)
- Header & footer browser: **nonaktif**

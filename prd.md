# PRD: Aplikasi CBT Sekolah

Dokumen kebutuhan produk untuk aplikasi Computer Based Test (CBT) berbasis CodeIgniter 4 dan MySQL.

| Item | Isi |
|---|---|
| Versi dokumen | 1.0 (draf) |
| Tanggal | 14 Oktober 2026 |
| Framework | CodeIgniter 4 (PHP 8.1 atau lebih baru) |
| Database | MySQL 8 (wajib), engine InnoDB, charset utf8mb4 |
| Referensi desain | Artboard "Dashboard CBT": aplikasi dibuat mirip dengan desain ini (sidebar gelap, kartu ringkasan, tabel ujian, tanpa gradien) |

---

## 1. Latar belakang dan tujuan

Sekolah membutuhkan aplikasi ujian berbasis komputer yang mudah dioperasikan guru, terpantau oleh proktor, dan nyaman dipakai siswa di lab komputer maupun jaringan lokal. Aplikasi harus rapi, cepat, dan tidak membingungkan di semua peran.

Tujuan produk:

1. Guru dapat menyusun bank soal dan menjadwalkan ujian tanpa bantuan teknis.
2. Proktor dapat memantau peserta secara langsung dan menangani kendala saat ujian.
3. Siswa dapat mengerjakan ujian dengan tampilan sederhana, jawaban tersimpan otomatis, dan tidak kehilangan data saat koneksi terputus sebentar.
4. Admin dapat mengelola seluruh data master dan melihat rekap hasil.

Indikator keberhasilan:

- Satu ujian dengan 300 peserta serentak berjalan tanpa kehilangan jawaban.
- Guru dapat membuat ujian lengkap (soal sampai jadwal) kurang dari 15 menit setelah soal siap.
- Tidak ada halaman utama yang membutuhkan scroll horizontal pada layar 1280 px.

## 2. Ruang lingkup

Termasuk dalam versi 1.0:

- Autentikasi dan otorisasi berbasis peran (admin, proktor, guru, siswa).
- Manajemen data master: pengguna, kelas, mata pelajaran, ruang/sesi.
- Bank soal: pilihan ganda, pilihan ganda kompleks, benar/salah, isian singkat, uraian.
- Penjadwalan ujian, token ujian, dan pengacakan soal dan opsi.
- Pelaksanaan ujian siswa dengan timer, simpan jawaban otomatis, dan deteksi pelanggaran.
- Monitoring langsung oleh proktor.
- Penilaian otomatis (soal objektif) dan manual (uraian), rekap nilai, ekspor Excel dan PDF.
- Impor data (siswa, soal) dari Excel.

Di luar versi 1.0:

- Aplikasi mobile native.
- Proctoring dengan kamera atau AI.
- Integrasi dengan sistem akademik eksternal (Dapodik, dan sejenisnya).
- Pembayaran atau multi-sekolah (multi-tenant).

## 3. Peran dan hak akses

| Kemampuan | Admin | Proktor | Guru | Siswa |
|---|:---:|:---:|:---:|:---:|
| Kelola pengguna, kelas, mapel, ruang | Ya | - | - | - |
| Kelola bank soal | Ya (semua) | - | Ya (milik sendiri) | - |
| Buat dan jadwalkan ujian | Ya | - | Ya | - |
| Lihat dan reset token ujian | Ya | Ya | Ya (ujian sendiri) | - |
| Monitoring ujian langsung | Ya | Ya | Ya (ujian sendiri) | - |
| Reset login, beri waktu tambahan, hentikan peserta | Ya | Ya | - | - |
| Koreksi uraian dan ubah nilai | Ya | - | Ya (ujian sendiri) | - |
| Lihat rekap nilai | Ya (semua) | Ya (baca saja) | Ya (ujian sendiri) | Ya (nilai sendiri, jika diizinkan) |
| Kerjakan ujian | - | - | - | Ya |
| Pengaturan sistem dan log aktivitas | Ya | - | - | - |

Aturan: satu akun memiliki satu peran utama. Pembatasan dilakukan di sisi server melalui CI4 Filter, bukan hanya dengan menyembunyikan menu.

## 4. Kebutuhan fungsional

### 4.1 Autentikasi

- FR-A1: Login dengan username dan kata sandi. Siswa dapat login memakai NIS atau NISN sebagai username.
- FR-A2: Kata sandi disimpan dengan `password_hash` (bcrypt atau argon2).
- FR-A3: Batas percobaan login gagal (5 kali, kunci 5 menit) dan notifikasi lewat SweetAlert2.
- FR-A4: Satu siswa hanya boleh memiliki satu sesi aktif. Login dari perangkat kedua ditolak kecuali di-reset proktor.
- FR-A5: Ganti kata sandi mandiri untuk admin, proktor, dan guru. Siswa direset oleh admin atau proktor.
- FR-A6: Setelah login, pengguna diarahkan ke dashboard sesuai perannya.

### 4.2 Dashboard per peran

Semua dashboard memakai kerangka yang sama: sidebar, header, kartu ringkasan, lalu panel isi. Rincian visual ada di bagian 7.

**Admin**

- Kartu: peserta online, ujian berlangsung, jumlah paket soal, rata-rata nilai.
- Panel: ujian hari ini (dengan progres), jadwal hari ini, rata-rata nilai per mapel, daftar peserta yang perlu perhatian.

**Proktor**

- Kartu: peserta online, peserta belum login, peserta dengan pelanggaran, ujian berlangsung.
- Panel utama: tabel peserta per ruang/sesi dengan status (belum login, mengerjakan, selesai, terkunci), tombol reset login, tambah waktu, dan hentikan.
- Panel samping: token aktif beserta tombol salin dan buat ulang.

**Guru**

- Kartu: paket soal saya, ujian aktif, ujian menunggu koreksi, rata-rata nilai.
- Panel: ujian saya, antrean koreksi uraian, grafik nilai per kelas.

**Siswa**

- Kartu: ujian hari ini, ujian mendatang, ujian selesai.
- Panel: daftar ujian dengan tombol "Mulai" yang aktif hanya pada jadwalnya, riwayat nilai (jika ditampilkan sekolah).

### 4.3 Data master (Admin)

- FR-M1: CRUD pengguna (admin, proktor, guru, siswa) lewat DataTables dan modal.
- FR-M2: Impor siswa dan guru dari Excel dengan templat unduhan, validasi baris, dan laporan baris gagal.
- FR-M3: CRUD kelas dan rombel, penempatan siswa ke kelas (satu siswa satu kelas aktif per tahun ajaran).
- FR-M4: CRUD mata pelajaran dan penugasan guru ke mapel dan kelas.
- FR-M5: CRUD ruang dan sesi ujian (nama ruang, kapasitas, jam sesi).
- FR-M6: Pengaturan tahun ajaran dan semester aktif.
- FR-M7: Hapus data memakai soft delete bila sudah dipakai ujian, agar nilai lama tetap utuh.

### 4.4 Bank soal (Admin, Guru)

- FR-B1: Paket soal memiliki nama, mapel, tingkat, dan pemilik.
- FR-B2: Tipe soal: pilihan ganda, pilihan ganda kompleks, benar/salah, isian singkat, uraian.
- FR-B3: Editor soal mendukung teks kaya, gambar, dan rumus (KaTeX). Unggah gambar maksimal 2 MB, format JPG, PNG, WebP.
- FR-B4: Setiap soal memiliki bobot dan kunci jawaban. Uraian memiliki pedoman penilaian.
- FR-B5: Impor soal dari Excel dan templat Word sederhana.
- FR-B6: Pratinjau soal persis seperti tampilan siswa.
- FR-B7: Duplikat paket soal dan duplikat soal.
- FR-B8: Soal yang sudah dipakai ujian aktif tidak dapat dihapus, hanya dinonaktifkan.

### 4.5 Penjadwalan ujian (Admin, Guru)

- FR-U1: Ujian memiliki nama, paket soal, kelas peserta, tanggal, jam mulai dan selesai, durasi, dan nilai minimal.
- FR-U2: Opsi pengacakan urutan soal dan urutan pilihan jawaban.
- FR-U3: Opsi tampilkan hasil ke siswa setelah selesai (langsung, setelah ujian ditutup, atau tidak).
- FR-U4: Token ujian 6 karakter, dibuat otomatis, dapat dibuat ulang, dan memiliki masa berlaku.
- FR-U5: Batas toleransi keterlambatan masuk (menit) dan batas maksimal pelanggaran sebelum peserta otomatis terkunci.
- FR-U6: Status ujian: draf, terjadwal, berlangsung, selesai, dibatalkan.
- FR-U7: Ujian susulan atau remedial dibuat dengan menyalin ujian dan memilih peserta tertentu.

### 4.6 Pelaksanaan ujian (Siswa)

- FR-P1: Siswa memilih ujian dari dashboard, memasukkan token, lalu menyetujui tata tertib.
- FR-P2: Layar ujian menampilkan nomor soal, timer hitung mundur, penanda ragu-ragu, dan navigasi nomor.
- FR-P3: Jawaban disimpan otomatis ke server setiap kali berubah (AJAX), dengan antrean ulang bila koneksi putus dan indikator status simpan.
- FR-P4: Timer dihitung dari waktu server, bukan jam perangkat siswa.
- FR-P5: Bila siswa keluar dari tab atau layar penuh, sistem mencatat pelanggaran, menampilkan peringatan SweetAlert2, dan menambah hitungan pelanggaran.
- FR-P6: Bila batas pelanggaran terlampaui, peserta dikunci dan hanya bisa dibuka proktor.
- FR-P7: Bila siswa terputus lalu masuk kembali, ujian dilanjutkan dengan sisa waktu yang benar.
- FR-P8: Tombol "Selesai" meminta konfirmasi dan menampilkan jumlah soal yang belum dijawab.
- FR-P9: Waktu habis memicu pengumpulan otomatis.

### 4.7 Monitoring (Proktor, Guru, Admin)

- FR-O1: Tabel peserta langsung (DataTables) dengan kolom nama, kelas, ruang, status, progres jawaban, jumlah pelanggaran, dan terakhir aktif.
- FR-O2: Pembaruan data otomatis tiap 10 detik (polling), tanpa memuat ulang halaman.
- FR-O3: Aksi proktor lewat modal konfirmasi: reset login, buka kunci, tambah waktu (menit), paksa selesai.
- FR-O4: Setiap aksi proktor tercatat di log aktivitas dengan nama pelaku dan waktu.

### 4.8 Penilaian dan laporan

- FR-N1: Soal objektif dinilai otomatis saat pengumpulan.
- FR-N2: Uraian dikoreksi guru lewat layar koreksi per soal atau per siswa.
- FR-N3: Rekap nilai per ujian, per kelas, dan per siswa, dengan analisis butir soal sederhana (persentase benar per soal).
- FR-N4: Ekspor Excel dan PDF untuk rekap nilai dan daftar hadir ujian.
- FR-N5: Perubahan nilai manual wajib mengisi alasan dan tercatat di log.

## 5. Spesifikasi teknis

### 5.1 Stack

| Lapisan | Pilihan |
|---|---|
| Backend | CodeIgniter 4, pola MVC, Model memakai `CodeIgniter\Model` |
| Database | MySQL 8 dengan migrasi dan seeder CI4 |
| Templating | CI4 View Layout (`extend`, `section`) dengan layout utama dan layout ujian siswa |
| CSS | Bootstrap 5 untuk grid dan modal, dengan tema kustom sesuai bagian 7 |
| Tabel | DataTables (server-side processing untuk data besar) |
| Notifikasi | SweetAlert2 untuk semua notifikasi dan konfirmasi |
| Modal | Bootstrap 5 Modal untuk formulir tambah, ubah, dan detail |
| Ikon | Set ikon garis (stroke) konsisten, tanpa emoji |
| Ekspor | PhpSpreadsheet (Excel), Dompdf atau mPDF (PDF) |
| Server | Nginx atau Apache, PHP-FPM, dapat berjalan di jaringan lokal sekolah |

### 5.2 Pola antarmuka yang wajib seragam

**DataTables**

- Semua daftar data memakai DataTables dengan pencarian, pengurutan, dan paginasi.
- Data lebih dari 1.000 baris (siswa, hasil, log) memakai server-side processing.
- Bahasa antarmuka Indonesia. Kolom aksi di paling kanan berisi tombol ikon dengan tooltip.
- Pada layar sempit, tabel berada di dalam wadah yang dapat di-scroll, bukan memperlebar halaman.

**Modal**

- Tambah, ubah, dan lihat detail dilakukan lewat modal, bukan pindah halaman, untuk data master sederhana.
- Formulir modal divalidasi di sisi klien dan di sisi server. Galat server ditampilkan di bawah kolom terkait.
- Setelah berhasil: modal tertutup, tabel dimuat ulang, dan muncul toast sukses.
- Editor soal yang kompleks memakai halaman penuh, bukan modal.

**SweetAlert2**

- Konfirmasi hapus: ikon peringatan, teks jelas tentang akibatnya, tombol "Hapus" dan "Batal".
- Toast sukses atau gagal di pojok kanan atas, hilang otomatis dalam 3 detik.
- Peringatan pelanggaran saat ujian bersifat modal dan harus dikonfirmasi siswa.
- Teks tombol dan pesan memakai bahasa Indonesia yang singkat dan spesifik.

### 5.3 Struktur data (MySQL)

Konvensi: nama tabel `snake_case` jamak, kunci utama `id` (BIGINT UNSIGNED), kolom `created_at`, `updated_at`, dan `deleted_at` untuk soft delete pada tabel master.

| Tabel | Kolom utama | Keterangan |
|---|---|---|
| `users` | id, nama, username (unik), password_hash, role (admin, proktor, guru, siswa), nis, nisn, aktif, last_login_at, session_token | Satu tabel untuk semua peran |
| `tahun_ajaran` | id, nama, semester, aktif | Satu baris aktif |
| `kelas` | id, nama, tingkat, tahun_ajaran_id | |
| `siswa_kelas` | id, user_id, kelas_id, tahun_ajaran_id | Penempatan siswa |
| `mapel` | id, kode, nama | |
| `guru_mapel_kelas` | id, guru_id, mapel_id, kelas_id | Penugasan mengajar |
| `ruang` | id, nama, kapasitas | |
| `paket_soal` | id, nama, mapel_id, tingkat, pemilik_id | |
| `soal` | id, paket_id, tipe, pertanyaan, bobot, pedoman, urutan, aktif | |
| `soal_opsi` | id, soal_id, label, isi, benar | Untuk pilihan ganda, kompleks, benar/salah |
| `ujian` | id, nama, paket_id, pembuat_id, mulai_at, selesai_at, durasi_menit, acak_soal, acak_opsi, tampil_hasil, nilai_minimal, batas_pelanggaran, token, token_berlaku_sampai, status | |
| `ujian_kelas` | id, ujian_id, kelas_id | Kelas peserta |
| `ujian_peserta` | id, ujian_id, user_id, ruang_id, status, mulai_at, selesai_at, tambahan_menit, pelanggaran, terkunci, last_seen_at | Satu baris per siswa per ujian |
| `jawaban` | id, peserta_id, soal_id, jawaban, ragu, skor, dinilai_oleh, updated_at | Unik pada (peserta_id, soal_id) |
| `urutan_soal` | id, peserta_id, soal_id, urutan, urutan_opsi (JSON) | Hasil pengacakan per siswa |
| `pelanggaran` | id, peserta_id, jenis, keterangan, created_at | Keluar tab, keluar layar penuh, dan sejenisnya |
| `hasil` | id, peserta_id, skor_objektif, skor_uraian, nilai_akhir, status_koreksi | |
| `log_aktivitas` | id, user_id, aksi, target, detail (JSON), ip, created_at | Audit |
| `pengaturan` | kunci, nilai | Nama sekolah, logo, kebijakan ujian |

Indeks wajib: `users.username`, `ujian_peserta (ujian_id, user_id)` unik, `jawaban (peserta_id, soal_id)` unik, `ujian (status, mulai_at)`.

### 5.4 Struktur modul CI4

```
app/
  Controllers/  Auth, Dashboard, Admin/*, Proktor/*, Guru/*, Siswa/*
  Filters/      AuthFilter, RoleFilter
  Models/       UserModel, UjianModel, SoalModel, JawabanModel, ...
  Views/        layout/main.php, layout/ujian.php, dashboard/*, partials/*
  Database/     Migrations, Seeds
public/assets/  css, js, img
```

Rute dikelompokkan per peran dengan prefiks (`/admin`, `/proktor`, `/guru`, `/siswa`) dan dilindungi `RoleFilter`.

## 6. Kebutuhan non-fungsional

| Area | Target |
|---|---|
| Kinerja | Halaman dashboard termuat kurang dari 2 detik. Simpan jawaban kurang dari 300 ms pada jaringan lokal. |
| Kapasitas | 300 peserta serentak pada satu server dengan 4 vCPU dan 8 GB RAM. |
| Ketersediaan | Tidak ada kehilangan jawaban saat koneksi terputus kurang dari 5 menit. |
| Keamanan | CSRF aktif, escaping keluaran, query berparameter, batas ukuran dan tipe unggahan, header keamanan, HTTPS bila di luar jaringan lokal. |
| Privasi | Kunci jawaban tidak pernah dikirim ke peramban siswa. Penilaian dilakukan di server. |
| Cadangan | Cadangan database harian otomatis dan tombol ekspor manual untuk admin. |
| Audit | Aksi sensitif (reset, ubah nilai, hapus) tercatat di `log_aktivitas`. |
| Kompatibilitas | Chrome, Edge, dan Firefox versi terbaru. Layar 1280 px sebagai acuan, tetap dapat dipakai di tablet. |
| Aksesibilitas | Kontras teks minimal 4,5:1, fokus keyboard terlihat, target sentuh minimal 44 px. |

## 7. Desain antarmuka

Acuan visual adalah artboard "Dashboard CBT". Prinsip: rapi, bersih, datar.

### 7.1 Aturan visual

- Tidak ada gradien di mana pun. Semua warna datar.
- Tidak ada bayangan berat. Pemisahan memakai garis tipis dan perbedaan latar.
- Satu warna aksen, warna semantik hanya untuk status.
- Ruang putih lega: jarak antar kartu 16 px, padding kartu 20 sampai 24 px.
- Angka rata kolom memakai angka tabular.

### 7.2 Token desain

| Token | Nilai |
|---|---|
| Latar halaman | `#F3F6F7` |
| Permukaan kartu | `#FFFFFF` dengan garis `#E1E8EB` |
| Teks utama | `#10222B` |
| Teks sekunder | `#52666F` |
| Aksen | `#0E7C86` |
| Sidebar | `#0F2229`, item aktif `#1D3B45` |
| Sukses | latar `#DCFCE7`, teks `#166534` |
| Peringatan | latar `#FEF3C7`, teks `#92400E` |
| Bahaya | latar `#FEE2E2`, teks `#991B1B` |
| Netral (terjadwal, selesai) | latar `#E8EEF0`, teks `#3D515A` |
| Huruf judul dan angka besar | Sora (600 dan 700) |
| Huruf isi | Plus Jakarta Sans (400 sampai 700) |
| Radius | kartu 14 px, tombol dan input 12 px, pil status penuh |
| Tinggi kontrol | 44 px |

### 7.3 Kerangka halaman

1. Sidebar kiri 256 px: logo, grup menu "Menu" dan "Pengelolaan", kartu pengguna di bawah. Menu mengikuti peran.
2. Header: judul halaman, tanggal dan semester, kolom cari, notifikasi, dan tombol aksi utama (misalnya "Buat Ujian").
3. Baris kartu ringkasan: 4 kartu, masing-masing label, ikon, angka besar, dan keterangan singkat.
4. Panel isi: kartu putih berisi tabel, daftar, atau grafik. Judul panel memakai Sora 17 px.
5. Pada layar sempit, sidebar tumpuk di atas dan panel tersusun satu kolom.

### 7.4 Komponen

- Pil status: Berlangsung (hijau), Hampir selesai (kuning), Terjadwal dan Selesai (netral), Terkunci dan Tinggi (merah).
- Bilah progres 8 px berwarna aksen untuk kemajuan peserta.
- Kartu token ujian berlatar gelap dengan kode besar dan tombol "Salin token".
- Grafik batang horizontal sederhana untuk rata-rata nilai per mapel.
- Tombol utama berlatar aksen, tombol sekunder berlatar putih dengan garis.

### 7.5 Layar ujian siswa

- Layout terpisah tanpa sidebar: header berisi nama ujian, nama siswa, dan timer.
- Area soal di kiri, panel navigasi nomor di kanan (belum dijawab, dijawab, ragu-ragu).
- Tombol "Sebelumnya", "Ragu-ragu", "Selanjutnya", dan "Selesai" tetap terlihat.
- Indikator simpan otomatis ("Tersimpan" atau "Menyimpan...") di header.

## 8. Alur utama

**Guru membuat ujian**: buat paket soal, tambah atau impor soal, buka Jadwal Ujian, klik "Buat Ujian", pilih paket, kelas, waktu, dan opsi, simpan. Token dibuat otomatis.

**Proktor menjalankan ujian**: buka Monitoring, pilih ujian, bagikan token ke peserta, pantau status, tangani reset atau pelanggaran lewat modal, tutup ujian saat selesai.

**Siswa mengerjakan**: login, buka dashboard, klik "Mulai" pada ujian yang aktif, masukkan token, setujui tata tertib, kerjakan, konfirmasi selesai, lihat ringkasan (bila diizinkan).

**Guru mengoreksi**: buka antrean koreksi, nilai uraian per soal, simpan, nilai akhir terhitung otomatis, ekspor rekap.

## 9. Rencana rilis

| Tahap | Isi | Perkiraan |
|---|---|---|
| M1 | Fondasi: migrasi database, autentikasi, layout, dashboard kosong per peran | 1 minggu |
| M2 | Data master: pengguna, kelas, mapel, ruang, impor Excel (DataTables dan modal) | 1,5 minggu |
| M3 | Bank soal dan editor, impor soal | 2 minggu |
| M4 | Penjadwalan ujian, token, pengacakan | 1 minggu |
| M5 | Layar ujian siswa, simpan otomatis, deteksi pelanggaran | 2 minggu |
| M6 | Monitoring proktor | 1 minggu |
| M7 | Penilaian, rekap, ekspor Excel dan PDF | 1,5 minggu |
| M8 | Uji beban, perbaikan, dokumentasi, pelatihan pengguna | 1 minggu |

Perkiraan di atas untuk satu pengembang penuh waktu dan perlu disesuaikan dengan tim.

## 10. Kriteria penerimaan

- Setiap peran hanya dapat membuka menu dan rute yang menjadi haknya. Akses langsung lewat URL ke rute peran lain ditolak.
- Semua daftar data memakai DataTables, semua aksi tambah dan ubah memakai modal, semua konfirmasi dan notifikasi memakai SweetAlert2.
- Dashboard setiap peran tampil tanpa elemen terpotong atau scroll horizontal pada 1280 px, dan tetap terbaca pada 768 px.
- Tidak ada gradien pada seluruh antarmuka.
- Uji beban 300 peserta serentak selesai tanpa kehilangan jawaban.
- Timer tetap akurat setelah peserta menutup dan membuka kembali peramban.
- Kunci jawaban tidak muncul pada respons jaringan yang diterima peramban siswa.
- Semua aksi reset, ubah nilai, dan hapus tercatat di log aktivitas.

## 11. Risiko dan pertanyaan terbuka

| Risiko atau pertanyaan | Mitigasi atau keputusan yang dibutuhkan |
|---|---|
| Jaringan sekolah tidak stabil | Antrean simpan jawaban di sisi klien dan lanjutkan otomatis saat tersambung |
| Beban puncak saat ujian dimulai serentak | Indeks database, caching, dan uji beban sebelum rilis |
| Kecurangan lewat berpindah tab | Deteksi dan batas pelanggaran. Pengawasan fisik tetap diperlukan |
| Apakah siswa boleh melihat nilai langsung? | Keputusan sekolah, dibuat sebagai opsi per ujian |
| Apakah perlu mode luring (tanpa internet, server lokal)? | Perlu dipastikan, memengaruhi cara pemasangan dan cadangan |
| Format impor soal yang diinginkan guru | Kumpulkan contoh berkas guru sebelum M3 |

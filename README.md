# 🎓 CBT Sekolah — Sistem Ujian Berbasis Komputer

Sistem Computer Based Test (CBT) modern berbasis web yang dirancang khusus untuk kebutuhan sekolah (SMP, SMA, SMK) dengan performa tinggi, tampilan bersih dan intuitif, serta sistem keamanan anti-cheat yang ketat.

---

## 🚀 Teknologi yang Digunakan

- **Backend Framework**: CodeIgniter 4 (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Frontend / UI**:
  - CSS kustom berpedoman *PRD design tokens* (Sora & Plus Jakarta Sans, flat colors, tanpa gradien mencolok)
  - Bootstrap 5 (Grid & Utilitas Komponen)
  - DataTables dengan fitur server-side / client-side search & pagination
  - SweetAlert2 untuk modal konfirmasi dan notifikasi toast
  - Quill.js untuk editor soal teks kaya
  - KaTeX untuk rendering rumus matematika & simbol ilmiah
- **Pengolahan Dokumen & Laporan**:
  - `phpoffice/phpspreadsheet`: Impor dan ekspor Excel (.xlsx) data siswa, guru, butir soal, dan rekap nilai
  - `dompdf/dompdf`: Cetak dan ekspor lembar rekapitulasi nilai resmi sekolah ke PDF

---

## 👥 4 Peran Pengguna (Role-Based Access)

| Peran | Akses & Kemampuan Utama |
|---|---|
| **Admin** | Manajemen pengguna (Siswa, Guru, Proktor, Admin), kelas & rombel, mata pelajaran, penugasan mengajar, ruang ujian, tahun ajaran aktif, audit log aktivitas, dan pengaturan identitas sekolah. |
| **Proktor** | Live monitoring pengerjaan ujian secara real-time dengan polling per 10 detik, pemantauan status peserta, persentase progres, pelanggaran anti-cheat, aksi reset login, buka kunci, penambahan waktu, dan paksa selesai. |
| **Guru** | Bank paket soal, editor butir soal halaman penuh (Pilihan Ganda, PG Kompleks, Benar/Salah, Isian Singkat, Uraian), impor/ekspor soal Excel, pembuatan jadwal ujian, koreksi uraian manual, analisis butir soal, dan cetak nilai. |
| **Siswa** | Dashboard ujian aktif dan mendatang, verifikasi token ujian, layar pengerjaan ujian bebas distraksi (tanpa sidebar), navigasi nomor soal, autosave berkala, pendeteksi pelanggaran tab/fullscreen, dan ringkasan nilai. |

---

## 🔐 Akun Default untuk Pengujian

Semua akun terdaftar menggunakan kata sandi default: `password` (atau `admin123` / `siswa123`):

| Peran | Username | Password |
|---|---|---|
| **Admin** | `admin` | `password` |
| **Proktor** | `proktor` | `password` |
| **Guru** | `guru` | `password` |
| **Siswa** | `siswa1` (atau NIS `1001`) | `password` |

---

## 🛠️ Langkah Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
- PHP >= 8.2 dengan ekstensi: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `gd`
- Web Server (Apache via XAMPP atau Nginx)
- MySQL Database

### 2. Konfigurasi Database & Environment
Buka file `.env` di direktori root aplikasi, sesuaikan kredensial database Anda:
```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/cbt/public/'

database.default.hostname = localhost
database.default.database = cbt_sekolah
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 3. Migrasi Database & Seeding Data Awal
Jalankan perintah berikut melalui terminal / powershell di folder project:
```bash
php spark migrate
php spark db:seed InitialSeeder
```

### 4. Menjalankan Aplikasi
Anda dapat mengakses aplikasi melalui XAMPP Apache:
```
http://localhost/cbt/public/
```
Atau menggunakan server bawaan CodeIgniter:
```bash
php spark serve
```
Dan buka di browser: `http://localhost:8080/`

---

## ✨ Fitur-Fitur Utama Sesuai Spesifikasi PRD

1. **Keamanan Login & Single Session**:
   - Pembatasan 5 kali gagal login → akun terkunci otomatis selama 5 menit.
   - Deteksi single-session per siswa untuk mencegah login ganda bersamaan.
2. **Autosave Real-Time**:
   - Setiap perubahan jawaban langsung tersimpan ke database via AJAX asynchronous.
3. **Pencegahan Kecurangan (Anti-Cheat Engine)**:
   - Deteksi perpindahan tab browser (`visibilitychange`).
   - Deteksi keluar dari mode layar penuh (`fullscreenchange`).
   - Pencatatan jumlah pelanggaran dan penguncian otomatis jika melebihi batas toleransi.
4. **Acak Soal & Opsi**:
   - Pengacakan urutan soal dan opsi jawaban per masing-masing peserta ujian.
5. **Koreksi Otomatis & Manual**:
   - Penilaian otomatis untuk Pilihan Ganda, PG Kompleks, Benar/Salah, dan Isian Singkat.
   - Halaman koreksi khusus bagi guru untuk menilai butir uraian berdasarkan pedoman rubrik.
6. **Ekspor & Cetak Laporan**:
   - Ekspor nilai lengkap ke Excel (.xlsx) dengan PhpSpreadsheet.
   - Cetak PDF resmi dengan kop surat sekolah dan kolom tanda tangan kepala sekolah/guru menggunakan Dompdf.

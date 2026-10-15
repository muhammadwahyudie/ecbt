<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DummyDataSeeder
 *
 * Inserts a complete set of dummy data:
 * - 10 Siswa (students)
 * - 4 Kelas (classes)
 * - 5 Mata Pelajaran (subjects)
 * - 2 Bank Soal / Paket Soal (question packages)
 *   - Matematika: 30 soal (20 PG, 5 PGK, 3 B/S, 2 Isian Singkat)
 *   - Bahasa Indonesia: 20 soal (15 PG, 3 B/S, 2 Uraian)
 * - 3 Jadwal Ujian
 * - Peserta Ujian + Hasil Nilai
 */
class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        echo "=== DummyDataSeeder mulai ===\n";

        // Ambil / pastikan Tahun Ajaran Aktif
        $ta   = $db->table('tahun_ajaran')->where('aktif', 1)->get()->getRowArray();
        $taId = $ta ? $ta['id'] : 1;
        echo "Tahun Ajaran ID: {$taId}\n";

        // Ambil Guru
        $guruUser = $db->table('users')->where('role', 'guru')->get()->getRowArray();
        $guruId   = $guruUser ? $guruUser['id'] : 1;
        echo "Guru ID: {$guruId}\n";

        // ─── 1. Siswa (10 siswa baru) ────────────────────────────────────────
        $siswaData = [
            ['nama' => 'Aditya Nugraha',   'username' => 'aditya.nugraha',  'nis' => '2024001', 'nisn' => '0012345601'],
            ['nama' => 'Bella Safitri',    'username' => 'bella.safitri',   'nis' => '2024002', 'nisn' => '0012345602'],
            ['nama' => 'Cahyo Prabowo',    'username' => 'cahyo.prabowo',   'nis' => '2024003', 'nisn' => '0012345603'],
            ['nama' => 'Dina Rahmawati',   'username' => 'dina.rahmawati',  'nis' => '2024004', 'nisn' => '0012345604'],
            ['nama' => 'Eko Prasetyo',     'username' => 'eko.prasetyo',    'nis' => '2024005', 'nisn' => '0012345605'],
            ['nama' => 'Fitri Handayani',  'username' => 'fitri.handayani', 'nis' => '2024006', 'nisn' => '0012345606'],
            ['nama' => 'Gilang Ramadhan',  'username' => 'gilang.ramadhan', 'nis' => '2024007', 'nisn' => '0012345607'],
            ['nama' => 'Hana Kusuma',      'username' => 'hana.kusuma',     'nis' => '2024008', 'nisn' => '0012345608'],
            ['nama' => 'Irfan Maulana',    'username' => 'irfan.maulana',   'nis' => '2024009', 'nisn' => '0012345609'],
            ['nama' => 'Julia Anggraini',  'username' => 'julia.anggraini', 'nis' => '2024010', 'nisn' => '0012345610'],
        ];

        foreach ($siswaData as $s) {
            $exists = $db->table('users')->where('username', $s['username'])->countAllResults();
            if ($exists === 0) {
                $db->table('users')->insert([
                    'nama'          => $s['nama'],
                    'username'      => $s['username'],
                    'password_hash' => password_hash('siswa123', PASSWORD_BCRYPT),
                    'role'          => 'siswa',
                    'nis'           => $s['nis'],
                    'nisn'          => $s['nisn'],
                    'aktif'         => 1,
                    'login_gagal'   => 0,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
        echo "Siswa seeded\n";

        // Ambil semua siswa
        $allSiswa = $db->table('users')->where('role', 'siswa')->get()->getResultArray();

        // ─── 2. Kelas ────────────────────────────────────────────────────────
        $kelasList = [
            ['nama' => 'X-IPA-1',   'tingkat' => 10, 'tahun_ajaran_id' => $taId],
            ['nama' => 'X-IPA-2',   'tingkat' => 10, 'tahun_ajaran_id' => $taId],
            ['nama' => 'XI-IPA-1',  'tingkat' => 11, 'tahun_ajaran_id' => $taId],
            ['nama' => 'XII-IPS-1', 'tingkat' => 12, 'tahun_ajaran_id' => $taId],
        ];
        foreach ($kelasList as $k) {
            $exists = $db->table('kelas')
                ->where('nama', $k['nama'])
                ->where('tahun_ajaran_id', $taId)
                ->countAllResults();
            if ($exists === 0) {
                $db->table('kelas')->insert(array_merge($k, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
        $kelas1  = $db->table('kelas')->where('nama', 'X-IPA-1')->get()->getRowArray();
        $kelasId = $kelas1 ? $kelas1['id'] : 1;
        echo "Kelas seeded (ID utama: {$kelasId})\n";

        // ─── 3. Daftarkan Siswa ke Kelas ────────────────────────────────────
        foreach ($allSiswa as $s) {
            $exists = $db->table('siswa_kelas')
                ->where('user_id', $s['id'])
                ->where('tahun_ajaran_id', $taId)
                ->countAllResults();
            if ($exists === 0) {
                $db->table('siswa_kelas')->insert([
                    'user_id'         => $s['id'],
                    'kelas_id'        => $kelasId,
                    'tahun_ajaran_id' => $taId,
                    'created_at'      => $now,
                ]);
            }
        }
        echo "Siswa didaftarkan ke kelas\n";

        // ─── 4. Mata Pelajaran ───────────────────────────────────────────────
        $mapelData = [
            ['kode' => 'MAT', 'nama' => 'Matematika'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'IPA', 'nama' => 'Ilmu Pengetahuan Alam'],
            ['kode' => 'IPS', 'nama' => 'Ilmu Pengetahuan Sosial'],
        ];
        foreach ($mapelData as $m) {
            $exists = $db->table('mapel')->where('kode', $m['kode'])->countAllResults();
            if ($exists === 0) {
                $db->table('mapel')->insert(array_merge($m, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
        $mapelMat   = $db->table('mapel')->where('kode', 'MAT')->get()->getRowArray();
        $mapelBin   = $db->table('mapel')->where('kode', 'BIN')->get()->getRowArray();
        $mapelMatId = $mapelMat ? $mapelMat['id'] : 1;
        $mapelBinId = $mapelBin ? $mapelBin['id'] : 2;
        echo "Mata Pelajaran seeded\n";

        // ─── 5. Guru Mapel Kelas ─────────────────────────────────────────────
        foreach ([$mapelMatId, $mapelBinId] as $mId) {
            $exists = $db->table('guru_mapel_kelas')
                ->where('guru_id', $guruId)
                ->where('mapel_id', $mId)
                ->where('kelas_id', $kelasId)
                ->countAllResults();
            if ($exists === 0) {
                $db->table('guru_mapel_kelas')->insert([
                    'guru_id'    => $guruId,
                    'mapel_id'   => $mId,
                    'kelas_id'   => $kelasId,
                    'created_at' => $now,
                ]);
            }
        }

        // ─── 6. Ruang ────────────────────────────────────────────────────────
        $ruangData = [
            ['nama' => 'Lab Komputer 1', 'kapasitas' => 36],
            ['nama' => 'Lab Komputer 2', 'kapasitas' => 36],
            ['nama' => 'Lab Komputer 3', 'kapasitas' => 30],
        ];
        foreach ($ruangData as $r) {
            $exists = $db->table('ruang')->where('nama', $r['nama'])->countAllResults();
            if ($exists === 0) {
                $db->table('ruang')->insert(array_merge($r, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
        $ruang1  = $db->table('ruang')->where('nama', 'Lab Komputer 1')->get()->getRowArray();
        $ruangId = $ruang1 ? $ruang1['id'] : 1;
        echo "Ruang seeded\n";

        // ─────────────────────────────────────────────────────────────────────
        // ─── 7. PAKET SOAL 1: MATEMATIKA (30 Soal) ──────────────────────────
        // ─────────────────────────────────────────────────────────────────────
        $paketMatNama = 'Bank Soal Matematika Kelas X - Semester Ganjil';
        if ($db->table('paket_soal')->where('nama', $paketMatNama)->countAllResults() === 0) {
            $db->table('paket_soal')->insert([
                'nama'       => $paketMatNama,
                'mapel_id'   => $mapelMatId,
                'tingkat'    => 10,
                'pemilik_id' => $guruId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $paketMat   = $db->table('paket_soal')->where('nama', $paketMatNama)->get()->getRowArray();
        $paketMatId = $paketMat ? $paketMat['id'] : 1;

        if ($db->table('soal')->where('paket_id', $paketMatId)->countAllResults() === 0) {
            echo "Menyisipkan 30 soal Matematika...\n";
            $this->insertSoalMatematika($db, $paketMatId, $now);
        }
        echo "Paket Soal Matematika (30 soal) seeded\n";

        // ─────────────────────────────────────────────────────────────────────
        // ─── 8. PAKET SOAL 2: BAHASA INDONESIA (20 Soal) ────────────────────
        // ─────────────────────────────────────────────────────────────────────
        $paketBinNama = 'Bank Soal Bahasa Indonesia Kelas X - Semester Ganjil';
        if ($db->table('paket_soal')->where('nama', $paketBinNama)->countAllResults() === 0) {
            $db->table('paket_soal')->insert([
                'nama'       => $paketBinNama,
                'mapel_id'   => $mapelBinId,
                'tingkat'    => 10,
                'pemilik_id' => $guruId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $paketBin   = $db->table('paket_soal')->where('nama', $paketBinNama)->get()->getRowArray();
        $paketBinId = $paketBin ? $paketBin['id'] : 2;

        if ($db->table('soal')->where('paket_id', $paketBinId)->countAllResults() === 0) {
            echo "Menyisipkan 20 soal Bahasa Indonesia...\n";
            $this->insertSoalBahasaIndonesia($db, $paketBinId, $now);
        }
        echo "Paket Soal Bahasa Indonesia (20 soal) seeded\n";

        // ─────────────────────────────────────────────────────────────────────
        // ─── 9. JADWAL UJIAN ────────────────────────────────────────────────
        // ─────────────────────────────────────────────────────────────────────
        $ujianData = [
            [
                'nama'                 => 'PH 1 Matematika - X IPA 1',
                'paket_id'             => $paketMatId,
                'mulai_at'             => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'selesai_at'           => date('Y-m-d H:i:s', strtotime('+60 minutes')),
                'durasi_menit'         => 90,
                'token'                => 'MAT001',
                'token_berlaku_sampai' => date('Y-m-d H:i:s', strtotime('+5 hours')),
                'status'               => 'berlangsung',
                'tampil_hasil'         => 'langsung',
                'nilai_minimal'        => 75.00,
                'batas_terlambat'      => 15,
                'batas_pelanggaran'    => 3,
                'acak_soal'            => 1,
                'acak_opsi'            => 1,
            ],
            [
                'nama'                 => 'UTS Matematika - X IPA 1',
                'paket_id'             => $paketMatId,
                'mulai_at'             => date('Y-m-d 08:00:00', strtotime('+2 days')),
                'selesai_at'           => date('Y-m-d 10:00:00', strtotime('+2 days')),
                'durasi_menit'         => 120,
                'token'                => 'UTS202',
                'token_berlaku_sampai' => date('Y-m-d 12:00:00', strtotime('+2 days')),
                'status'               => 'terjadwal',
                'tampil_hasil'         => 'setelah_tutup',
                'nilai_minimal'        => 70.00,
                'batas_terlambat'      => 30,
                'batas_pelanggaran'    => 3,
                'acak_soal'            => 1,
                'acak_opsi'            => 1,
            ],
            [
                'nama'                 => 'PH 1 Bahasa Indonesia - X IPA 1',
                'paket_id'             => $paketBinId,
                'mulai_at'             => date('Y-m-d 08:00:00', strtotime('-3 days')),
                'selesai_at'           => date('Y-m-d 09:30:00', strtotime('-3 days')),
                'durasi_menit'         => 90,
                'token'                => 'BIN303',
                'token_berlaku_sampai' => date('Y-m-d 11:00:00', strtotime('-3 days')),
                'status'               => 'selesai',
                'tampil_hasil'         => 'setelah_tutup',
                'nilai_minimal'        => 75.00,
                'batas_terlambat'      => 15,
                'batas_pelanggaran'    => 3,
                'acak_soal'            => 1,
                'acak_opsi'            => 0,
            ],
        ];

        foreach ($ujianData as $u) {
            if ($db->table('ujian')->where('nama', $u['nama'])->countAllResults() === 0) {
                $db->table('ujian')->insert(array_merge($u, [
                    'pembuat_id' => $guruId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
                $ujianId = $db->insertID();

                // Assign kelas
                $db->table('ujian_kelas')->insert(['ujian_id' => $ujianId, 'kelas_id' => $kelasId]);

                // Buat peserta
                $this->insertPeserta($db, $ujianId, $allSiswa, $ruangId, $u['status'], $now);
            }
        }
        echo "Ujian seeded (3 jadwal)\n";

        echo "\n=== DummyDataSeeder SELESAI ===\n";
        echo "Total: 10 siswa, 4 kelas, 5 mapel, 2 paket soal (50 soal total), 3 ujian.\n";
        echo "Password siswa: siswa123\n";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ─── 30 Soal Matematika ──────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    private function insertSoalMatematika($db, int $paketId, string $now): void
    {
        // === SOAL 1-20: PILIHAN GANDA =========================================
        $pgSoal = [
            [
                'q'       => '<p>Nilai <em>x</em> yang memenuhi persamaan <strong>2x + 6 = 14</strong> adalah...</p>',
                'op'      => ['A' => '2', 'B' => '4', 'C' => '6', 'D' => '8'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Hasil dari <strong>(-3) × (-4) + 5</strong> adalah...</p>',
                'op'      => ['A' => '7', 'B' => '17', 'C' => '12', 'D' => '-7'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Jika <em>f(x) = 3x² − 2x + 1</em>, maka nilai <em>f(2)</em> adalah...</p>',
                'op'      => ['A' => '5', 'B' => '7', 'C' => '9', 'D' => '11'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Himpunan penyelesaian dari pertidaksamaan <strong>3x − 5 &gt; 7</strong> adalah...</p>',
                'op'      => ['A' => 'x &gt; 4', 'B' => 'x &gt; 2', 'C' => 'x &lt; 4', 'D' => 'x &lt; 2'],
                'correct' => 'A',
            ],
            [
                'q'       => '<p>Diketahui barisan aritmetika: 3, 7, 11, 15, ... Suku ke-10 barisan tersebut adalah...</p>',
                'op'      => ['A' => '39', 'B' => '41', 'C' => '43', 'D' => '45'],
                'correct' => 'A',
            ],
            [
                'q'       => '<p>Jumlah semua bilangan bulat antara 1 sampai 100 adalah...</p>',
                'op'      => ['A' => '4950', 'B' => '5000', 'C' => '5050', 'D' => '5100'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Akar-akar persamaan <strong>x² − 5x + 6 = 0</strong> adalah...</p>',
                'op'      => ['A' => '2 dan 3', 'B' => '1 dan 6', 'C' => '-2 dan -3', 'D' => '-1 dan 6'],
                'correct' => 'A',
            ],
            [
                'q'       => '<p>Sebuah segitiga siku-siku mempunyai kaki 6 cm dan 8 cm. Panjang hipotenusa segitiga tersebut adalah...</p>',
                'op'      => ['A' => '9 cm', 'B' => '10 cm', 'C' => '12 cm', 'D' => '14 cm'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Hasil dari <strong>log₂ 64</strong> adalah...</p>',
                'op'      => ['A' => '4', 'B' => '5', 'C' => '6', 'D' => '7'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Diketahui sin 30° = 1/2. Nilai dari sin 150° adalah...</p>',
                'op'      => ['A' => '-1/2', 'B' => '0', 'C' => '1/2', 'D' => 'akar(3)/2'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Gradien garis yang melalui titik (2, 3) dan (6, 11) adalah...</p>',
                'op'      => ['A' => '1', 'B' => '2', 'C' => '3', 'D' => '4'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Volume kubus dengan rusuk 5 cm adalah...</p>',
                'op'      => ['A' => '25 cm³', 'B' => '75 cm³', 'C' => '100 cm³', 'D' => '125 cm³'],
                'correct' => 'D',
            ],
            [
                'q'       => '<p>Bentuk sederhana dari <strong>akar(48)</strong> adalah...</p>',
                'op'      => ['A' => '2akar(12)', 'B' => '4akar(3)', 'C' => '6akar(2)', 'D' => '3akar(4)'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>FPB dari 24 dan 36 adalah...</p>',
                'op'      => ['A' => '6', 'B' => '8', 'C' => '12', 'D' => '18'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>KPK dari 12 dan 18 adalah...</p>',
                'op'      => ['A' => '24', 'B' => '36', 'C' => '48', 'D' => '72'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Nilai dari <strong>2³ × 2² ÷ 2⁴</strong> adalah...</p>',
                'op'      => ['A' => '1', 'B' => '2', 'C' => '4', 'D' => '8'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Jika suatu bilangan dikurangi 15, hasilnya adalah 23. Bilangan tersebut adalah...</p>',
                'op'      => ['A' => '8', 'B' => '38', 'C' => '42', 'D' => '48'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Luas lingkaran dengan diameter 14 cm adalah... (gunakan π = 22/7)</p>',
                'op'      => ['A' => '44 cm²', 'B' => '88 cm²', 'C' => '154 cm²', 'D' => '196 cm²'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Sebuah persegi panjang mempunyai panjang 12 cm dan lebar 8 cm. Kelilingnya adalah...</p>',
                'op'      => ['A' => '20 cm', 'B' => '40 cm', 'C' => '96 cm', 'D' => '48 cm'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Rata-rata (mean) dari data: 6, 8, 10, 12, 14 adalah...</p>',
                'op'      => ['A' => '8', 'B' => '9', 'C' => '10', 'D' => '11'],
                'correct' => 'C',
            ],
        ];

        foreach ($pgSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'pilihan_ganda',
                'pertanyaan' => $s['q'],
                'bobot'      => 2.00,
                'urutan'     => $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $soalId = $db->insertID();
            $opsi   = [];
            $no     = 1;
            foreach ($s['op'] as $label => $isi) {
                $opsi[] = [
                    'soal_id' => $soalId,
                    'label'   => $label,
                    'isi'     => $isi,
                    'benar'   => ($label === $s['correct']) ? 1 : 0,
                    'urutan'  => $no++,
                ];
            }
            $db->table('soal_opsi')->insertBatch($opsi);
        }

        // === SOAL 21-25: PILIHAN GANDA KOMPLEKS ==============================
        $pgkSoal = [
            [
                'q'       => '<p>Manakah dari bilangan-bilangan berikut yang termasuk bilangan prima? <em>(Pilih semua yang benar)</em></p>',
                'op'      => ['A' => '2', 'B' => '9', 'C' => '13', 'D' => '17', 'E' => '21'],
                'correct' => ['A', 'C', 'D'],
            ],
            [
                'q'       => '<p>Pernyataan mana yang benar tentang fungsi kuadrat <em>f(x) = x² − 4x + 3</em>? <em>(Pilih semua yang benar)</em></p>',
                'op'      => ['A' => 'Akar-akarnya adalah x = 1 dan x = 3', 'B' => 'Nilai minimum fungsi adalah -2', 'C' => 'Titik puncak berada di (2, -1)', 'D' => 'Grafik terbuka ke atas'],
                'correct' => ['A', 'C', 'D'],
            ],
            [
                'q'       => '<p>Manakah dari berikut yang merupakan bilangan rasional? <em>(Pilih semua yang benar)</em></p>',
                'op'      => ['A' => 'akar(4)', 'B' => 'akar(2)', 'C' => '22/7', 'D' => 'pi (π)'],
                'correct' => ['A', 'C'],
            ],
            [
                'q'       => '<p>Sifat-sifat mana yang berlaku pada segitiga sama sisi? <em>(Pilih semua yang benar)</em></p>',
                'op'      => ['A' => 'Semua sisinya sama panjang', 'B' => 'Semua sudutnya 60 derajat', 'C' => 'Mempunyai simetri putar tingkat 3', 'D' => 'Mempunyai tepat satu sumbu simetri'],
                'correct' => ['A', 'B', 'C'],
            ],
            [
                'q'       => '<p>Manakah pernyataan yang benar tentang matriks identitas ordo 2x2? <em>(Pilih semua yang benar)</em></p>',
                'op'      => ['A' => 'Elemennya di diagonal utama bernilai 1', 'B' => 'Elemennya di luar diagonal utama bernilai 0', 'C' => 'Determinannya adalah 1', 'D' => 'Termasuk matriks singular'],
                'correct' => ['A', 'B', 'C'],
            ],
        ];

        foreach ($pgkSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'pilihan_ganda_kompleks',
                'pertanyaan' => $s['q'],
                'bobot'      => 3.00,
                'urutan'     => 20 + $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $soalId = $db->insertID();
            $opsi   = [];
            $no     = 1;
            foreach ($s['op'] as $label => $isi) {
                $opsi[] = [
                    'soal_id' => $soalId,
                    'label'   => $label,
                    'isi'     => $isi,
                    'benar'   => in_array($label, $s['correct']) ? 1 : 0,
                    'urutan'  => $no++,
                ];
            }
            $db->table('soal_opsi')->insertBatch($opsi);
        }

        // === SOAL 26-28: BENAR / SALAH =======================================
        $bsSoal = [
            ['q' => '<p>Hasil dari (-5)² adalah -25.</p>',                        'benar' => 'S'],
            ['q' => '<p>Jumlah sudut dalam segitiga selalu 180 derajat.</p>',     'benar' => 'B'],
            ['q' => '<p>Bilangan nol (0) termasuk bilangan prima.</p>',           'benar' => 'S'],
        ];

        foreach ($bsSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'benar_salah',
                'pertanyaan' => $s['q'],
                'bobot'      => 1.00,
                'urutan'     => 25 + $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $soalId = $db->insertID();
            $db->table('soal_opsi')->insertBatch([
                ['soal_id' => $soalId, 'label' => 'B', 'isi' => 'Benar', 'benar' => ($s['benar'] === 'B') ? 1 : 0, 'urutan' => 1],
                ['soal_id' => $soalId, 'label' => 'S', 'isi' => 'Salah', 'benar' => ($s['benar'] === 'S') ? 1 : 0, 'urutan' => 2],
            ]);
        }

        // === SOAL 29-30: ISIAN SINGKAT =======================================
        $isianSoal = [
            ['q' => '<p>Berapakah hasil dari <strong>15 × 4 − 20 ÷ 5</strong>?</p>', 'jawaban' => '56'],
            ['q' => '<p>Tulis nilai dari <em>3³ + 2⁴</em>!</p>',                     'jawaban' => '43'],
        ];

        foreach ($isianSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'isian_singkat',
                'pertanyaan' => $s['q'],
                'bobot'      => 2.00,
                'urutan'     => 28 + $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $soalId = $db->insertID();
            $db->table('soal_opsi')->insert([
                'soal_id' => $soalId, 'label' => '', 'isi' => $s['jawaban'], 'benar' => 1, 'urutan' => 1,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ─── 20 Soal Bahasa Indonesia ────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    private function insertSoalBahasaIndonesia($db, int $paketId, string $now): void
    {
        // === SOAL 1-15: PILIHAN GANDA =========================================
        $pgSoal = [
            [
                'q'       => '<p>Kata "efisien" dalam kalimat "Cara kerja mesin itu sangat efisien" mengandung makna...</p>',
                'op'      => ['A' => 'Cepat dan tepat', 'B' => 'Mampu menghemat waktu, tenaga, dan biaya', 'C' => 'Murah dan berkualitas', 'D' => 'Modern dan canggih'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Kalimat "Para siswa-siswa mengerjakan ulangan dengan tenang." mengandung kesalahan karena...</p>',
                'op'      => ['A' => 'Kata "siswa" tidak tepat', 'B' => 'Penggunaan kata "para" dan "siswa-siswa" menyebabkan pemborosan kata', 'C' => 'Kata "ulangan" seharusnya "ujian"', 'D' => 'Tidak ada subjek dalam kalimat'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Teks yang berisi informasi faktual dengan tujuan menginformasikan pembaca tentang suatu peristiwa nyata disebut teks...</p>',
                'op'      => ['A' => 'Narasi', 'B' => 'Deskripsi', 'C' => 'Eksposisi', 'D' => 'Berita'],
                'correct' => 'D',
            ],
            [
                'q'       => '<p>Konjungsi "walaupun" dalam kalimat "Walaupun hujan deras, ia tetap berangkat sekolah" berfungsi untuk menyatakan...</p>',
                'op'      => ['A' => 'Sebab', 'B' => 'Akibat', 'C' => 'Pertentangan', 'D' => 'Penambahan'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Struktur teks prosedur yang benar adalah...</p>',
                'op'      => ['A' => 'Orientasi - Komplikasi - Resolusi', 'B' => 'Tujuan - Bahan/Alat - Langkah-langkah', 'C' => 'Pernyataan umum - Argumentasi - Simpulan', 'D' => 'Abstrak - Orientasi - Insiden - Koda'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Majas yang menyatakan sesuatu dengan cara berlebih-lebihan disebut majas...</p>',
                'op'      => ['A' => 'Metafora', 'B' => 'Personifikasi', 'C' => 'Hiperbola', 'D' => 'Simile'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Kalimat aktif yang tepat adalah...</p>',
                'op'      => ['A' => 'Buku itu dibaca oleh Andi.', 'B' => 'Andi membaca buku itu.', 'C' => 'Buku dibaca.', 'D' => 'Terbacanya buku oleh Andi.'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Bagian akhir dari sebuah cerpen yang berisi penyelesaian masalah disebut...</p>',
                'op'      => ['A' => 'Orientasi', 'B' => 'Komplikasi', 'C' => 'Resolusi', 'D' => 'Koda'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Persamaan kata (sinonim) dari kata "besar" adalah...</p>',
                'op'      => ['A' => 'Kecil', 'B' => 'Agung', 'C' => 'Tebal', 'D' => 'Luas'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Penulisan kata depan "di" yang benar terdapat pada kalimat...</p>',
                'op'      => ['A' => 'Ia pergi kepasar tadi pagi.', 'B' => 'Buku itu diletakkan dimeja.', 'C' => 'Ia belajar di perpustakaan.', 'D' => 'Semua jawaban salah.'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Teks argumentatif adalah teks yang bertujuan untuk...</p>',
                'op'      => ['A' => 'Menjelaskan urutan kejadian', 'B' => 'Meyakinkan pembaca melalui bukti dan alasan', 'C' => 'Mendeskripsikan suatu objek', 'D' => 'Menceritakan pengalaman pribadi'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Unsur intrinsik yang berkaitan dengan nilai-nilai kehidupan dalam suatu karya sastra disebut...</p>',
                'op'      => ['A' => 'Tema', 'B' => 'Amanat', 'C' => 'Latar', 'D' => 'Alur'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Kalimat yang mengandung majas personifikasi adalah...</p>',
                'op'      => ['A' => 'Badannya kurus seperti tiang bambu.', 'B' => 'Angin berbisik lembut di telinganya.', 'C' => 'Ia adalah singa di lapangan sepak bola.', 'D' => 'Hatinya sekeras batu.'],
                'correct' => 'B',
            ],
            [
                'q'       => '<p>Penulisan judul karangan yang tepat berdasarkan EYD adalah...</p>',
                'op'      => ['A' => 'manfaat belajar di pagi hari', 'B' => 'Manfaat Belajar Di Pagi Hari', 'C' => 'Manfaat Belajar di Pagi Hari', 'D' => 'MANFAAT BELAJAR DI PAGI HARI'],
                'correct' => 'C',
            ],
            [
                'q'       => '<p>Kalimat yang efektif dan benar adalah...</p>',
                'op'      => ['A' => 'Kami membahas tentang masalah lingkungan.', 'B' => 'Kami membahas masalah lingkungan.', 'C' => 'Kami mendiskusikan tentang masalah lingkungan.', 'D' => 'Kami berdiskusi perihal tentang masalah lingkungan.'],
                'correct' => 'B',
            ],
        ];

        foreach ($pgSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'pilihan_ganda',
                'pertanyaan' => $s['q'],
                'bobot'      => 2.00,
                'urutan'     => $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $soalId = $db->insertID();
            $opsi   = [];
            $no     = 1;
            foreach ($s['op'] as $label => $isi) {
                $opsi[] = [
                    'soal_id' => $soalId,
                    'label'   => $label,
                    'isi'     => $isi,
                    'benar'   => ($label === $s['correct']) ? 1 : 0,
                    'urutan'  => $no++,
                ];
            }
            $db->table('soal_opsi')->insertBatch($opsi);
        }

        // === SOAL 16-18: BENAR / SALAH ========================================
        $bsSoal = [
            ['q' => '<p>Kata "di" dalam "di rumah" harus ditulis terpisah dari kata berikutnya.</p>',              'benar' => 'B'],
            ['q' => '<p>Teks eksposisi bertujuan untuk menghibur pembaca.</p>',                                    'benar' => 'S'],
            ['q' => '<p>Majas simile adalah majas perbandingan yang menggunakan kata "seperti" atau "bagai".</p>', 'benar' => 'B'],
        ];

        foreach ($bsSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'benar_salah',
                'pertanyaan' => $s['q'],
                'bobot'      => 1.00,
                'urutan'     => 15 + $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $soalId = $db->insertID();
            $db->table('soal_opsi')->insertBatch([
                ['soal_id' => $soalId, 'label' => 'B', 'isi' => 'Benar', 'benar' => ($s['benar'] === 'B') ? 1 : 0, 'urutan' => 1],
                ['soal_id' => $soalId, 'label' => 'S', 'isi' => 'Salah', 'benar' => ($s['benar'] === 'S') ? 1 : 0, 'urutan' => 2],
            ]);
        }

        // === SOAL 19-20: URAIAN ===============================================
        $uraianSoal = [
            [
                'q'       => '<p>Tulislah sebuah paragraf deskripsi (minimal 5 kalimat) tentang kondisi lingkungan sekolahmu!</p>',
                'pedoman' => 'Penilaian: Kesesuaian isi dengan judul (10), penggunaan kalimat efektif (5), ejaan dan tanda baca (5), kosakata bervariasi (5), kohesi dan koherensi (5). Total: 30.',
            ],
            [
                'q'       => '<p>Jelaskan perbedaan antara teks prosedur dan teks eksposisi, serta berikan masing-masing satu contoh judulnya!</p>',
                'pedoman' => 'Penilaian: Penjelasan teks prosedur benar (10), penjelasan teks eksposisi benar (10), contoh judul teks prosedur (5), contoh judul teks eksposisi (5). Total: 30.',
            ],
        ];

        foreach ($uraianSoal as $idx => $s) {
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'uraian',
                'pertanyaan' => $s['q'],
                'bobot'      => 10.00,
                'pedoman'    => $s['pedoman'],
                'urutan'     => 18 + $idx + 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ─── Helper: Insert Peserta Ujian ────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    private function insertPeserta($db, int $ujianId, array $allSiswa, int $ruangId, string $statusUjian, string $now): void
    {
        foreach ($allSiswa as $idx => $s) {
            $exists = $db->table('ujian_peserta')
                ->where('ujian_id', $ujianId)
                ->where('user_id', $s['id'])
                ->countAllResults();
            if ($exists > 0) continue;

            if ($statusUjian === 'berlangsung') {
                if ($idx === 0) {
                    $statusPeserta = 'belum_login';
                } elseif ($idx <= 2) {
                    $statusPeserta = 'mengerjakan';
                } else {
                    $statusPeserta = 'selesai';
                }
            } elseif ($statusUjian === 'selesai') {
                $statusPeserta = 'selesai';
            } else {
                $statusPeserta = 'belum_login';
            }

            $mulaiPeserta   = ($statusPeserta !== 'belum_login') ? date('Y-m-d H:i:s', strtotime('-20 minutes')) : null;
            $selesaiPeserta = ($statusPeserta === 'selesai') ? date('Y-m-d H:i:s', strtotime('-5 minutes')) : null;

            $db->table('ujian_peserta')->insert([
                'ujian_id'     => $ujianId,
                'user_id'      => $s['id'],
                'ruang_id'     => $ruangId,
                'status'       => $statusPeserta,
                'mulai_at'     => $mulaiPeserta,
                'selesai_at'   => $selesaiPeserta,
                'pelanggaran'  => ($idx % 4 === 0 && $statusPeserta === 'selesai') ? 1 : 0,
                'terkunci'     => 0,
                'last_seen_at' => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $pesertaId = $db->insertID();

            if ($statusPeserta === 'selesai') {
                $nilaiObjektif = rand(60, 95);
                $nilaiUraian   = rand(10, 30);
                $nilaiAkhir    = min(100, $nilaiObjektif + $nilaiUraian * 0.2);

                $db->table('hasil')->insert([
                    'peserta_id'     => $pesertaId,
                    'skor_objektif'  => $nilaiObjektif,
                    'skor_uraian'    => $nilaiUraian,
                    'nilai_akhir'    => round($nilaiAkhir, 2),
                    'status_koreksi' => 'selesai',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        }
    }
}

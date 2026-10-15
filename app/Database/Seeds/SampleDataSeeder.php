<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        // 1. Tahun Ajaran Aktif
        $ta = $db->table('tahun_ajaran')->where('aktif', 1)->get()->getRowArray();
        $taId = $ta ? $ta['id'] : 1;

        // 2. Tambah Sample Siswa tambahan
        $sampleStudents = [
            ['nama' => 'Ahmad Pratama', 'username' => 'siswa2', 'nis' => '1002', 'nisn' => '0098765432'],
            ['nama' => 'Siti Nurhaliza', 'username' => 'siswa3', 'nis' => '1003', 'nisn' => '0098765433'],
            ['nama' => 'Budi Santoso', 'username' => 'siswa4', 'nis' => '1004', 'nisn' => '0098765434'],
            ['nama' => 'Dewi Lestari', 'username' => 'siswa5', 'nis' => '1005', 'nisn' => '0098765435'],
        ];

        foreach ($sampleStudents as $s) {
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

        // Ambil ID semua siswa
        $allSiswa = $db->table('users')->where('role', 'siswa')->get()->getResultArray();
        $guruUser = $db->table('users')->where('role', 'guru')->get()->getRowArray();
        $guruId = $guruUser ? $guruUser['id'] : 1;

        // 3. Tambah Kelas
        $kelasList = [
            ['nama' => 'X-MIPA-1', 'tingkat' => 10, 'tahun_ajaran_id' => $taId],
            ['nama' => 'X-MIPA-2', 'tingkat' => 10, 'tahun_ajaran_id' => $taId],
            ['nama' => 'XI-MIPA-1', 'tingkat' => 11, 'tahun_ajaran_id' => $taId],
        ];

        foreach ($kelasList as $k) {
            $exists = $db->table('kelas')->where('nama', $k['nama'])->where('tahun_ajaran_id', $taId)->countAllResults();
            if ($exists === 0) {
                $db->table('kelas')->insert(array_merge($k, ['created_at' => $now, 'updated_at' => $now]));
            }
        }

        $kelas1 = $db->table('kelas')->where('nama', 'X-MIPA-1')->get()->getRowArray();
        $kelasId = $kelas1 ? $kelas1['id'] : 1;

        // 4. Masukkan siswa ke kelas
        foreach ($allSiswa as $idx => $s) {
            $exists = $db->table('siswa_kelas')->where('user_id', $s['id'])->where('tahun_ajaran_id', $taId)->countAllResults();
            if ($exists === 0) {
                $db->table('siswa_kelas')->insert([
                    'user_id'         => $s['id'],
                    'kelas_id'        => $kelasId,
                    'tahun_ajaran_id' => $taId,
                    'created_at'      => $now,
                ]);
            }
        }

        // 5. Tambah Mata Pelajaran
        $mapelList = [
            ['kode' => 'MAT', 'nama' => 'Matematika'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'IPA', 'nama' => 'Ilmu Pengetahuan Alam'],
        ];

        foreach ($mapelList as $m) {
            $exists = $db->table('mapel')->where('kode', $m['kode'])->countAllResults();
            if ($exists === 0) {
                $db->table('mapel')->insert(array_merge($m, ['created_at' => $now, 'updated_at' => $now]));
            }
        }

        $mapelMat = $db->table('mapel')->where('kode', 'MAT')->get()->getRowArray();
        $mapelId = $mapelMat ? $mapelMat['id'] : 1;

        // Guru mengajar mapel kelas
        $gmkExists = $db->table('guru_mapel_kelas')
            ->where('guru_id', $guruId)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->countAllResults();
        if ($gmkExists === 0) {
            $db->table('guru_mapel_kelas')->insert([
                'guru_id'    => $guruId,
                'mapel_id'   => $mapelId,
                'kelas_id'   => $kelasId,
                'created_at' => $now,
            ]);
        }

        // 6. Ruang Ujian
        $ruangList = [
            ['nama' => 'Lab Komputer 1', 'kapasitas' => 36],
            ['nama' => 'Lab Komputer 2', 'kapasitas' => 36],
        ];
        foreach ($ruangList as $r) {
            $exists = $db->table('ruang')->where('nama', $r['nama'])->countAllResults();
            if ($exists === 0) {
                $db->table('ruang')->insert(array_merge($r, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
        $ruang1 = $db->table('ruang')->where('nama', 'Lab Komputer 1')->get()->getRowArray();
        $ruangId = $ruang1 ? $ruang1['id'] : 1;

        // 7. Paket Soal
        $paketExists = $db->table('paket_soal')->where('nama', 'Penilaian Harian Matematika Wajib')->countAllResults();
        if ($paketExists === 0) {
            $db->table('paket_soal')->insert([
                'nama'       => 'Penilaian Harian Matematika Wajib',
                'mapel_id'   => $mapelId,
                'tingkat'    => 10,
                'pemilik_id' => $guruId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $paket = $db->table('paket_soal')->where('nama', 'Penilaian Harian Matematika Wajib')->get()->getRowArray();
        $paketId = $paket ? $paket['id'] : 1;

        // 8. Soal & Opsi Soal
        $soalCount = $db->table('soal')->where('paket_id', $paketId)->countAllResults();
        if ($soalCount === 0) {
            // Soal 1: Pilihan Ganda
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'pilihan_ganda',
                'pertanyaan' => '<p>Nilai $x$ yang memenuhi persamaan linier $2x + 6 = 14$ adalah...</p>',
                'bobot'      => 20.00,
                'urutan'     => 1,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $s1Id = $db->insertID();
            $db->table('soal_opsi')->insertBatch([
                ['soal_id' => $s1Id, 'label' => 'A', 'isi' => '2', 'benar' => 0, 'urutan' => 1],
                ['soal_id' => $s1Id, 'label' => 'B', 'isi' => '4', 'benar' => 1, 'urutan' => 2],
                ['soal_id' => $s1Id, 'label' => 'C', 'isi' => '6', 'benar' => 0, 'urutan' => 3],
                ['soal_id' => $s1Id, 'label' => 'D', 'isi' => '8', 'benar' => 0, 'urutan' => 4],
            ]);

            // Soal 2: Pilihan Ganda Kompleks
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'pilihan_ganda_kompleks',
                'pertanyaan' => '<p>Manakah dari bilangan berikut yang merupakan bilangan prima? (Pilih semua yang benar)</p>',
                'bobot'      => 20.00,
                'urutan'     => 2,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $s2Id = $db->insertID();
            $db->table('soal_opsi')->insertBatch([
                ['soal_id' => $s2Id, 'label' => 'A', 'isi' => '2', 'benar' => 1, 'urutan' => 1],
                ['soal_id' => $s2Id, 'label' => 'B', 'isi' => '9', 'benar' => 0, 'urutan' => 2],
                ['soal_id' => $s2Id, 'label' => 'C', 'isi' => '13', 'benar' => 1, 'urutan' => 3],
                ['soal_id' => $s2Id, 'label' => 'D', 'isi' => '17', 'benar' => 1, 'urutan' => 4],
            ]);

            // Soal 3: Benar / Salah
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'benar_salah',
                'pertanyaan' => '<p>Hasil dari $(-5)^2$ adalah sama dengan $-25$.</p>',
                'bobot'      => 20.00,
                'urutan'     => 3,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $s3Id = $db->insertID();
            $db->table('soal_opsi')->insertBatch([
                ['soal_id' => $s3Id, 'label' => 'B', 'isi' => 'Benar', 'benar' => 0, 'urutan' => 1],
                ['soal_id' => $s3Id, 'label' => 'S', 'isi' => 'Salah', 'benar' => 1, 'urutan' => 2],
            ]);

            // Soal 4: Isian Singkat
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'isian_singkat',
                'pertanyaan' => '<p>Berapakah hasil dari $15 \\times 4 - 20$?</p>',
                'bobot'      => 20.00,
                'urutan'     => 4,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $s4Id = $db->insertID();
            $db->table('soal_opsi')->insert([
                'soal_id' => $s4Id, 'label' => '', 'isi' => '40', 'benar' => 1, 'urutan' => 1
            ]);

            // Soal 5: Uraian
            $db->table('soal')->insert([
                'paket_id'   => $paketId,
                'tipe'       => 'uraian',
                'pertanyaan' => '<p>Jelaskan langkah-langkah dalam menyelesaikan sistem persamaan dua variabel dengan metode eliminasi!</p>',
                'bobot'      => 20.00,
                'pedoman'    => 'Menyebutkan penyamaan koefisien (skor 10), operasi penjumlahan/pengurangan (skor 5), dan substitusi nilai (skor 5).',
                'urutan'     => 5,
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 9. Buat Jadwal Ujian (1 Berlangsung Hari Ini, 1 Terjadwal)
        $ujian1Exists = $db->table('ujian')->where('nama', 'Penilaian Harian 1 - Matematika')->countAllResults();
        if ($ujian1Exists === 0) {
            $mulaiAt = date('Y-m-d H:i:s', strtotime('-15 minutes'));
            $selesaiAt = date('Y-m-d H:i:s', strtotime('+75 minutes'));
            $tokenExp = date('Y-m-d H:i:s', strtotime('+4 hours'));

            $db->table('ujian')->insert([
                'nama'                 => 'Penilaian Harian 1 - Matematika',
                'paket_id'             => $paketId,
                'pembuat_id'           => $guruId,
                'mulai_at'             => $mulaiAt,
                'selesai_at'           => $selesaiAt,
                'durasi_menit'         => 90,
                'acak_soal'            => 1,
                'acak_opsi'            => 1,
                'tampil_hasil'         => 'langsung',
                'nilai_minimal'        => 75.00,
                'batas_terlambat'      => 30,
                'batas_pelanggaran'    => 3,
                'token'                => 'CBT202',
                'token_berlaku_sampai' => $tokenExp,
                'status'               => 'berlangsung',
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
            $u1Id = $db->insertID();

            // Ujian kelas
            $db->table('ujian_kelas')->insert([
                'ujian_id' => $u1Id,
                'kelas_id' => $kelasId,
            ]);

            // Peserta Ujian
            foreach ($allSiswa as $idx => $s) {
                $statusPeserta = ($idx === 0) ? 'belum_login' : (($idx === 1) ? 'mengerjakan' : 'selesai');
                $mulaiPeserta = ($statusPeserta !== 'belum_login') ? date('Y-m-d H:i:s', strtotime('-10 minutes')) : null;
                $selesaiPeserta = ($statusPeserta === 'selesai') ? date('Y-m-d H:i:s', strtotime('-2 minutes')) : null;

                $db->table('ujian_peserta')->insert([
                    'ujian_id'     => $u1Id,
                    'user_id'      => $s['id'],
                    'ruang_id'     => $ruangId,
                    'status'       => $statusPeserta,
                    'mulai_at'     => $mulaiPeserta,
                    'selesai_at'   => $selesaiPeserta,
                    'pelanggaran'  => ($idx === 2) ? 1 : 0,
                    'terkunci'     => 0,
                    'last_seen_at' => $now,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
                $pesertaId = $db->insertID();

                if ($statusPeserta === 'selesai') {
                    $db->table('hasil')->insert([
                        'peserta_id'     => $pesertaId,
                        'skor_objektif'  => 80.00,
                        'skor_uraian'    => 15.00,
                        'nilai_akhir'    => 95.00,
                        'status_koreksi' => 'selesai',
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            // Ujian 2 (Terjadwal Besok)
            $db->table('ujian')->insert([
                'nama'                 => 'Simulasi Ujian Tengah Semester',
                'paket_id'             => $paketId,
                'pembuat_id'           => $guruId,
                'mulai_at'             => date('Y-m-d 08:00:00', strtotime('+1 day')),
                'selesai_at'           => date('Y-m-d 10:00:00', strtotime('+1 day')),
                'durasi_menit'         => 90,
                'acak_soal'            => 1,
                'acak_opsi'            => 1,
                'tampil_hasil'         => 'setelah_tutup',
                'nilai_minimal'        => 75.00,
                'token'                => 'SIM123',
                'token_berlaku_sampai' => date('Y-m-d 12:00:00', strtotime('+1 day')),
                'status'               => 'terjadwal',
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
            $u2Id = $db->insertID();
            $db->table('ujian_kelas')->insert([
                'ujian_id' => $u2Id,
                'kelas_id' => $kelasId,
            ]);
        }

        echo "Sample data successfully seeded!\n";
    }
}

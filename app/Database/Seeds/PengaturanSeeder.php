<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            'nama_sekolah'    => 'SMA Negeri 1 Contoh',
            'logo'            => '',
            'alamat'          => 'Jl. Pendidikan No. 1',
            'tata_tertib'     => "1. Kerjakan soal secara mandiri.\n2. Dilarang membuka tab atau jendela lain.\n3. Dilarang meninggalkan halaman ujian.\n4. Jawaban tersimpan otomatis.\n5. Klik \"Selesai\" bila sudah selesai mengerjakan.",
            'max_upload_mb'   => '2',
            'session_timeout' => '7200',
        ];

        foreach ($defaults as $kunci => $nilai) {
            $exists = $this->db->table('pengaturan')
                ->where('kunci', $kunci)
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('pengaturan')->insert([
                    'kunci' => $kunci,
                    'nilai' => $nilai,
                ]);
            }
        }
    }
}

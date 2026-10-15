<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run()
    {
        // Idempotent: skip jika sudah ada data
        if ($this->db->table('tahun_ajaran')->countAllResults() > 0) {
            return;
        }

        $this->db->table('tahun_ajaran')->insert([
            'nama'       => '2026/2027',
            'semester'   => '1',
            'aktif'      => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

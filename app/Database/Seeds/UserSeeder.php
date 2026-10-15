<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now  = date('Y-m-d H:i:s');
        $rows = [
            [
                'nama'          => 'Administrator',
                'username'      => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                'role'          => 'admin',
                'nis'           => null,
                'nisn'          => null,
                'email'         => null,
                'aktif'         => 1,
                'login_gagal'   => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'nama'          => 'Proktor Utama',
                'username'      => 'proktor',
                'password_hash' => password_hash('proktor123', PASSWORD_BCRYPT),
                'role'          => 'proktor',
                'nis'           => null,
                'nisn'          => null,
                'email'         => null,
                'aktif'         => 1,
                'login_gagal'   => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'nama'          => 'Guru Matematika',
                'username'      => 'guru',
                'password_hash' => password_hash('guru123', PASSWORD_BCRYPT),
                'role'          => 'guru',
                'nis'           => null,
                'nisn'          => null,
                'email'         => null,
                'aktif'         => 1,
                'login_gagal'   => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'nama'          => 'Siswa Demo',
                'username'      => 'siswa',
                'password_hash' => password_hash('siswa123', PASSWORD_BCRYPT),
                'role'          => 'siswa',
                'nis'           => '1234567890',
                'nisn'          => '0012345678',
                'email'         => null,
                'aktif'         => 1,
                'login_gagal'   => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        foreach ($rows as $row) {
            // Skip jika username sudah ada
            $exists = $this->db->table('users')
                ->where('username', $row['username'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('users')->insert($row);
            }
        }
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterTables extends Migration
{
    public function up()
    {
        // --- tahun_ajaran ---
        $this->forge->addField([
            'id'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 20],   // e.g. 2026/2027
            'semester' => ['type' => 'ENUM', 'constraint' => ['1', '2'], 'default' => '1'],
            'aktif'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tahun_ajaran');

        // --- kelas ---
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'nama'           => ['type' => 'VARCHAR', 'constraint' => 50],
            'tingkat'        => ['type' => 'TINYINT', 'constraint' => 2, 'null' => true],
            'tahun_ajaran_id'=> ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tahun_ajaran_id');
        $this->forge->createTable('kelas');

        // --- siswa_kelas ---
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'kelas_id'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'tahun_ajaran_id'=> ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'tahun_ajaran_id']);
        $this->forge->createTable('siswa_kelas');

        // --- mapel ---
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'kode'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mapel');

        // --- guru_mapel_kelas ---
        $this->forge->addField([
            'id'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'guru_id'  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'mapel_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'kelas_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['guru_id', 'mapel_id']);
        $this->forge->createTable('guru_mapel_kelas');

        // --- ruang ---
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'kapasitas'  => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'default' => 40],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ruang');

        // --- pengaturan ---
        $this->forge->addField([
            'kunci' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nilai' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('kunci', true);
        $this->forge->createTable('pengaturan');
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan');
        $this->forge->dropTable('ruang');
        $this->forge->dropTable('guru_mapel_kelas');
        $this->forge->dropTable('mapel');
        $this->forge->dropTable('siswa_kelas');
        $this->forge->dropTable('kelas');
        $this->forge->dropTable('tahun_ajaran');
    }
}

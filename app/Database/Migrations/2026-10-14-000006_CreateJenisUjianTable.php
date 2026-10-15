<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisUjianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis_ujian');

        // Seed default data
        $this->db->table('jenis_ujian')->insertBatch([
            ['nama' => 'Ulangan Harian', 'deskripsi' => 'Ujian harian rutin', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Ujian Tengah Semester (UTS)', 'deskripsi' => 'Ujian mid semester', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Ujian Akhir Semester (UAS)', 'deskripsi' => 'Ujian akhir semester', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Ujian Kenaikan Kelas (UKK)', 'deskripsi' => 'Ujian kenaikan kelas', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Try Out', 'deskripsi' => 'Latihan ujian nasional', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('jenis_ujian');
    }
}

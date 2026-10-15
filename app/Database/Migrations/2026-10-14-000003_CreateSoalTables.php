<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoalTables extends Migration
{
    public function up()
    {
        // --- paket_soal ---
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'mapel_id'   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'tingkat'    => ['type' => 'TINYINT', 'constraint' => 2, 'null' => true],
            'pemilik_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['pemilik_id', 'mapel_id']);
        $this->forge->createTable('paket_soal');

        // --- soal ---
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'paket_id'   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'tipe'       => ['type' => 'ENUM', 'constraint' => ['pilihan_ganda', 'pilihan_ganda_kompleks', 'benar_salah', 'isian_singkat', 'uraian']],
            'pertanyaan' => ['type' => 'LONGTEXT'],
            'bobot'      => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '1.00'],
            'pedoman'    => ['type' => 'LONGTEXT', 'null' => true],  // rubrik uraian
            'urutan'     => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'default' => 0],
            'aktif'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['paket_id', 'urutan']);
        $this->forge->createTable('soal');

        // --- soal_opsi ---
        $this->forge->addField([
            'id'      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'soal_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'label'   => ['type' => 'VARCHAR', 'constraint' => 5],   // A, B, C, D, E
            'isi'     => ['type' => 'LONGTEXT'],
            'benar'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'urutan'  => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('soal_id');
        $this->forge->createTable('soal_opsi');
    }

    public function down()
    {
        $this->forge->dropTable('soal_opsi');
        $this->forge->dropTable('soal');
        $this->forge->dropTable('paket_soal');
    }
}

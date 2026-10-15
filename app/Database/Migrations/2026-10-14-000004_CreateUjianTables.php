<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUjianTables extends Migration
{
    public function up()
    {
        // --- ujian ---
        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'nama'                => ['type' => 'VARCHAR', 'constraint' => 200],
            'paket_id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'pembuat_id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'mulai_at'            => ['type' => 'DATETIME'],
            'selesai_at'          => ['type' => 'DATETIME'],
            'durasi_menit'        => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'default' => 90],
            'acak_soal'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'acak_opsi'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'tampil_hasil'        => ['type' => 'ENUM', 'constraint' => ['langsung', 'setelah_tutup', 'tidak'], 'default' => 'tidak'],
            'nilai_minimal'       => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
            'batas_terlambat'     => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'default' => 0],
            'batas_pelanggaran'   => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 3],
            'token'               => ['type' => 'CHAR', 'constraint' => 6, 'null' => true],
            'token_berlaku_sampai'=> ['type' => 'DATETIME', 'null' => true],
            'status'              => ['type' => 'ENUM', 'constraint' => ['draf', 'terjadwal', 'berlangsung', 'selesai', 'dibatalkan'], 'default' => 'draf'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'mulai_at']);  // indeks wajib dari PRD
        $this->forge->addKey('token');
        $this->forge->createTable('ujian');

        // --- ujian_kelas ---
        $this->forge->addField([
            'id'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'ujian_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'kelas_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['ujian_id', 'kelas_id']);
        $this->forge->createTable('ujian_kelas');

        // --- ujian_peserta ---
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'ujian_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'user_id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'ruang_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['belum_login', 'mengerjakan', 'selesai', 'terkunci'], 'default' => 'belum_login'],
            'mulai_at'        => ['type' => 'DATETIME', 'null' => true],
            'selesai_at'      => ['type' => 'DATETIME', 'null' => true],
            'tambahan_menit'  => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'default' => 0],
            'pelanggaran'     => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 0],
            'terkunci'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'last_seen_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['ujian_id', 'user_id']);  // indeks wajib dari PRD
        $this->forge->addKey('status');
        $this->forge->createTable('ujian_peserta');

        // --- jawaban ---
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'peserta_id'   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'soal_id'      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'jawaban'      => ['type' => 'TEXT', 'null' => true],
            'ragu'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'skor'         => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'dinilai_oleh' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['peserta_id', 'soal_id']);  // indeks wajib dari PRD
        $this->forge->createTable('jawaban');

        // --- urutan_soal ---
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'peserta_id'  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'soal_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'urutan'      => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'urutan_opsi' => ['type' => 'JSON', 'null' => true],  // JSON array urutan opsi
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['peserta_id', 'urutan']);
        $this->forge->createTable('urutan_soal');

        // --- pelanggaran ---
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'peserta_id'  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'jenis'       => ['type' => 'ENUM', 'constraint' => ['keluar_tab', 'keluar_fullscreen', 'lainnya'], 'default' => 'lainnya'],
            'keterangan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('peserta_id');
        $this->forge->createTable('pelanggaran');

        // --- hasil ---
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'peserta_id'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'unique' => true],
            'skor_objektif'    => ['type' => 'DECIMAL', 'constraint' => '7,2', 'default' => '0.00'],
            'skor_uraian'      => ['type' => 'DECIMAL', 'constraint' => '7,2', 'default' => '0.00'],
            'nilai_akhir'      => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
            'status_koreksi'   => ['type' => 'ENUM', 'constraint' => ['belum', 'sebagian', 'selesai'], 'default' => 'belum'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hasil');
    }

    public function down()
    {
        $this->forge->dropTable('hasil');
        $this->forge->dropTable('pelanggaran');
        $this->forge->dropTable('urutan_soal');
        $this->forge->dropTable('jawaban');
        $this->forge->dropTable('ujian_peserta');
        $this->forge->dropTable('ujian_kelas');
        $this->forge->dropTable('ujian');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogTable extends Migration
{
    public function up()
    {
        // --- log_aktivitas ---
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'aksi'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'target'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'detail'     => ['type' => 'JSON', 'null' => true],
            'ip'         => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('aksi');
        $this->forge->createTable('log_aktivitas');
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas');
    }
}

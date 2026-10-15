<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAktivitasModel extends Model
{
    protected $table         = 'log_aktivitas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id', 'aksi', 'target', 'detail', 'ip', 'created_at'
    ];

    /**
     * Catat aktivitas pengguna
     */
    public static function catat(string $aksi, ?string $target = null, ?array $detail = null, ?int $userId = null): void
    {
        $db = \Config\Database::connect();
        $uid = $userId ?? session()->get('user_id');
        $ip  = service('request')->getIPAddress();

        $db->table('log_aktivitas')->insert([
            'user_id'    => $uid,
            'aksi'       => $aksi,
            'target'     => $target,
            'detail'     => $detail ? json_encode($detail) : null,
            'ip'         => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

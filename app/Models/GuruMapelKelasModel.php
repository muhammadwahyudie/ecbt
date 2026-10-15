<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruMapelKelasModel extends Model
{
    protected $table         = 'guru_mapel_kelas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'guru_id', 'mapel_id', 'kelas_id', 'created_at'
    ];

    /**
     * Dapatkan daftar penugasan guru lengkap dengan detail
     */
    public function getPenugasan(): array
    {
        return $this->select('guru_mapel_kelas.*, u.nama as nama_guru, m.nama as nama_mapel, m.kode as kode_mapel, k.nama as nama_kelas')
            ->join('users u', 'u.id = guru_mapel_kelas.guru_id')
            ->join('mapel m', 'm.id = guru_mapel_kelas.mapel_id')
            ->join('kelas k', 'k.id = guru_mapel_kelas.kelas_id')
            ->orderBy('u.nama ASC, m.nama ASC')
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table         = 'tahun_ajaran';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'nama', 'semester', 'aktif'
    ];

    protected $validationRules = [
        'nama'     => 'required|max_length[20]',
        'semester' => 'required|in_list[1,2]',
        'aktif'    => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Dapatkan tahun ajaran yang sedang aktif
     */
    public function getAktif(): ?array
    {
        return $this->where('aktif', 1)->first();
    }

    /**
     * Set aktif satu tahun ajaran, dan nonaktifkan yang lain
     */
    public function setAktif(int $id): bool
    {
        $this->db->transStart();
        $this->builder()->update(['aktif' => 0]);
        $this->builder()->where('id', $id)->update(['aktif' => 1]);
        $this->db->transComplete();

        return $this->db->transStatus();
    }
}

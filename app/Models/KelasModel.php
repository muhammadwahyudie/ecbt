<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table         = 'kelas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'nama', 'tingkat', 'tahun_ajaran_id'
    ];

    protected $validationRules = [
        'nama'            => 'required|min_length[1]|max_length[50]',
        'tingkat'         => 'permit_empty|integer',
        'tahun_ajaran_id' => 'required|integer',
    ];

    /**
     * Ambil kelas beserta relasi tahun ajaran dan jumlah siswa
     */
    public function getWithDetails(?int $tahunAjaranId = null): array
    {
        $builder = $this->select('kelas.*, ta.nama as tahun_ajaran, ta.semester, COUNT(sk.id) as jumlah_siswa')
            ->join('tahun_ajaran ta', 'ta.id = kelas.tahun_ajaran_id')
            ->join('siswa_kelas sk', 'sk.kelas_id = kelas.id', 'left')
            ->where('kelas.deleted_at', null)
            ->groupBy('kelas.id');

        if ($tahunAjaranId) {
            $builder->where('kelas.tahun_ajaran_id', $tahunAjaranId);
        }

        return $builder->orderBy('kelas.tingkat ASC, kelas.nama ASC')->findAll();
    }
}

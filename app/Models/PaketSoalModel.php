<?php

namespace App\Models;

use CodeIgniter\Model;

class PaketSoalModel extends Model
{
    protected $table         = 'paket_soal';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'nama', 'mapel_id', 'tingkat', 'pemilik_id'
    ];

    protected $validationRules = [
        'nama'       => 'required|min_length[3]|max_length[200]',
        'mapel_id'   => 'required|integer',
        'pemilik_id' => 'required|integer',
    ];

    /**
     * Ambil paket soal dengan detail nama mapel, pemilik, dan jumlah soal
     */
    public function getWithDetails(?int $userId = null, ?string $role = null): array
    {
        $builder = $this->select('paket_soal.*, m.nama as nama_mapel, m.kode as kode_mapel, u.nama as nama_pemilik, COUNT(s.id) as jumlah_soal')
            ->join('mapel m', 'm.id = paket_soal.mapel_id', 'left')
            ->join('users u', 'u.id = paket_soal.pemilik_id', 'left')
            ->join('soal s', 's.paket_id = paket_soal.id AND s.deleted_at IS NULL', 'left')
            ->where('paket_soal.deleted_at', null)
            ->groupBy('paket_soal.id')
            ->orderBy('paket_soal.id', 'DESC');

        if ($role === 'guru' && $userId !== null) {
            $builder->where('paket_soal.pemilik_id', $userId);
        }

        return $builder->findAll();
    }
}

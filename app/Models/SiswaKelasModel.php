<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaKelasModel extends Model
{
    protected $table         = 'siswa_kelas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id', 'kelas_id', 'tahun_ajaran_id', 'created_at'
    ];

    /**
     * Dapatkan daftar siswa dalam suatu kelas
     */
    public function getSiswaByKelas(int $kelasId): array
    {
        return $this->select('siswa_kelas.id as siswa_kelas_id, u.id as user_id, u.nama, u.username, u.nis, u.nisn')
            ->join('users u', 'u.id = siswa_kelas.user_id')
            ->where('siswa_kelas.kelas_id', $kelasId)
            ->where('u.deleted_at', null)
            ->orderBy('u.nama', 'ASC')
            ->findAll();
    }

    /**
     * Daftarkan atau pindahkan siswa ke kelas (1 siswa 1 kelas per tahun ajaran)
     */
    public function assignSiswa(int $userId, int $kelasId, int $tahunAjaranId): bool
    {
        $existing = $this->where('user_id', $userId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], ['kelas_id' => $kelasId]);
        }

        return (bool) $this->insert([
            'user_id'         => $userId,
            'kelas_id'        => $kelasId,
            'tahun_ajaran_id' => $tahunAjaranId,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }
}

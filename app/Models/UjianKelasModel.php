<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianKelasModel extends Model
{
    protected $table         = 'ujian_kelas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'ujian_id', 'kelas_id'
    ];

    /**
     * Daftarkan kelas-kelas peserta ujian dan generate record peserta otomatis
     */
    public function syncKelasDanPeserta(int $ujianId, array $kelasIds): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus relasi kelas lama
        $this->where('ujian_id', $ujianId)->delete();

        // 2. Insert kelas baru
        foreach ($kelasIds as $kId) {
            $this->insert([
                'ujian_id' => $ujianId,
                'kelas_id' => (int)$kId,
            ]);
        }

        // 3. Masukkan semua siswa dari kelas terpilih ke ujian_peserta jika belum ada
        $pesertaModel = new UjianPesertaModel();
        $siswaList = $db->table('siswa_kelas')
            ->select('user_id')
            ->whereIn('kelas_id', $kelasIds)
            ->get()->getResultArray();

        foreach ($siswaList as $s) {
            $exists = $pesertaModel->where('ujian_id', $ujianId)->where('user_id', $s['user_id'])->first();
            if (!$exists) {
                $pesertaModel->insert([
                    'ujian_id'       => $ujianId,
                    'user_id'        => $s['user_id'],
                    'status'         => 'belum_login',
                    'tambahan_menit' => 0,
                    'pelanggaran'    => 0,
                    'terkunci'       => 0,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $db->transComplete();
    }
}

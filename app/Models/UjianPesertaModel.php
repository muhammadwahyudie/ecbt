<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianPesertaModel extends Model
{
    protected $table         = 'ujian_peserta';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'ujian_id', 'user_id', 'ruang_id', 'status',
        'mulai_at', 'selesai_at', 'tambahan_menit',
        'pelanggaran', 'terkunci', 'last_seen_at'
    ];

    /**
     * Ambil data monitoring peserta untuk suatu ujian
     */
    public function getMonitoringData(int $ujianId, ?int $ruangId = null, ?string $status = null): array
    {
        $builder = $this->select('ujian_peserta.*, u.nama, u.username, u.nis, k.nama as nama_kelas, r.nama as nama_ruang,
                                  COUNT(DISTINCT j.id) as dijawab,
                                  COUNT(DISTINCT us.id) as total_soal')
            ->join('users u', 'u.id = ujian_peserta.user_id')
            ->join('siswa_kelas sk', 'sk.user_id = u.id', 'left')
            ->join('kelas k', 'k.id = sk.kelas_id', 'left')
            ->join('ruang r', 'r.id = ujian_peserta.ruang_id', 'left')
            ->join('urutan_soal us', 'us.peserta_id = ujian_peserta.id', 'left')
            ->join('jawaban j', 'j.peserta_id = ujian_peserta.id AND j.jawaban IS NOT NULL AND j.jawaban != ""', 'left')
            ->where('ujian_peserta.ujian_id', $ujianId)
            ->groupBy('ujian_peserta.id')
            ->orderBy('u.nama', 'ASC');

        if ($ruangId) {
            $builder->where('ujian_peserta.ruang_id', $ruangId);
        }

        if ($status) {
            $builder->where('ujian_peserta.status', $status);
        }

        return $builder->findAll();
    }
}

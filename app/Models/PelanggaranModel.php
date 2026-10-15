<?php

namespace App\Models;

use CodeIgniter\Model;

class PelanggaranModel extends Model
{
    protected $table         = 'pelanggaran';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'peserta_id', 'jenis', 'keterangan', 'created_at'
    ];

    /**
     * Catat pelanggaran siswa dan update status terkunci jika melebihi batas
     */
    public function catatPelanggaran(int $pesertaId, string $jenis, ?string $keterangan = null): array
    {
        $this->insert([
            'peserta_id' => $pesertaId,
            'jenis'      => $jenis,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $pesertaModel = new UjianPesertaModel();
        $ujianModel   = new UjianModel();

        $peserta = $pesertaModel->find($pesertaId);
        $totalPelanggaran = ($peserta['pelanggaran'] ?? 0) + 1;

        $ujian = $ujianModel->find($peserta['ujian_id']);
        $maxPelanggaran = $ujian['batas_pelanggaran'] ?? 3;

        $isLocked = ($totalPelanggaran >= $maxPelanggaran);

        $pesertaModel->update($pesertaId, [
            'pelanggaran' => $totalPelanggaran,
            'terkunci'    => $isLocked ? 1 : 0,
            'status'      => $isLocked ? 'terkunci' : $peserta['status'],
        ]);

        return [
            'total'    => $totalPelanggaran,
            'terkunci' => $isLocked,
            'sisa'     => max(0, $maxPelanggaran - $totalPelanggaran),
        ];
    }
}

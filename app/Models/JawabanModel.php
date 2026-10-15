<?php

namespace App\Models;

use CodeIgniter\Model;

class JawabanModel extends Model
{
    protected $table         = 'jawaban';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'peserta_id', 'soal_id', 'jawaban', 'ragu', 'skor', 'dinilai_oleh', 'updated_at'
    ];

    /**
     * Autosave jawaban siswa
     */
    public function simpanJawaban(int $pesertaId, int $soalId, ?string $jawaban, int $ragu = 0): bool
    {
        $existing = $this->where('peserta_id', $pesertaId)->where('soal_id', $soalId)->first();

        $data = [
            'peserta_id' => $pesertaId,
            'soal_id'    => $soalId,
            'jawaban'    => $jawaban,
            'ragu'       => $ragu,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return (bool) $this->insert($data);
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class UrutanSoalModel extends Model
{
    protected $table         = 'urutan_soal';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'peserta_id', 'soal_id', 'urutan', 'urutan_opsi'
    ];

    /**
     * Inisialisasi susunan urutan soal dan opsi untuk satu peserta saat pertama kali masuk ujian
     */
    public function generateUrutanPeserta(int $pesertaId, int $paketId, bool $acakSoal, bool $acakOpsi): array
    {
        // Cek apakah sudah pernah di-generate sebelumnya
        $existing = $this->where('peserta_id', $pesertaId)->orderBy('urutan', 'ASC')->findAll();
        if (!empty($existing)) {
            return $existing;
        }

        $soalModel = new SoalModel();
        $opsiModel = new SoalOpsiModel();

        $soalList = $soalModel->where('paket_id', $paketId)->where('deleted_at', null)->where('aktif', 1)->findAll();

        if ($acakSoal) {
            shuffle($soalList);
        } else {
            usort($soalList, fn($a, $b) => $a['urutan'] <=> $b['urutan']);
        }

        $generated = [];
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($soalList as $idx => $s) {
            $urutanOpsi = null;
            if ($s['tipe'] === 'pilihan_ganda' || $s['tipe'] === 'pilihan_ganda_kompleks') {
                $opsiList = $opsiModel->where('soal_id', $s['id'])->findAll();
                $opsiIds = array_column($opsiList, 'id');
                if ($acakOpsi) {
                    shuffle($opsiIds);
                }
                $urutanOpsi = json_encode($opsiIds);
            }

            $id = $this->insert([
                'peserta_id'  => $pesertaId,
                'soal_id'     => $s['id'],
                'urutan'      => $idx + 1,
                'urutan_opsi' => $urutanOpsi,
            ]);

            $generated[] = [
                'id'          => $id,
                'peserta_id'  => $pesertaId,
                'soal_id'     => $s['id'],
                'urutan'      => $idx + 1,
                'urutan_opsi' => $urutanOpsi,
            ];
        }

        $db->transComplete();

        return $generated;
    }
}

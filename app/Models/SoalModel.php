<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalModel extends Model
{
    protected $table         = 'soal';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'paket_id', 'tipe', 'pertanyaan', 'bobot', 'pedoman', 'urutan', 'aktif'
    ];

    protected $validationRules = [
        'paket_id'   => 'required|integer',
        'tipe'       => 'required|in_list[pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,uraian]',
        'pertanyaan' => 'required',
        'bobot'      => 'required|numeric',
    ];

    /**
     * Ambil soal beserta semua opsi jawabannya
     */
    public function getSoalWithOpsi(int $soalId): ?array
    {
        $soal = $this->find($soalId);
        if (!$soal) {
            return null;
        }

        $opsiModel = new SoalOpsiModel();
        $soal['opsi'] = $opsiModel->where('soal_id', $soalId)->orderBy('urutan ASC, id ASC')->findAll();

        return $soal;
    }

    /**
     * Ambil semua soal dalam suatu paket beserta opsinya
     */
    public function getAllByPaket(int $paketId): array
    {
        $soalList = $this->where('paket_id', $paketId)
            ->where('deleted_at', null)
            ->orderBy('urutan ASC, id ASC')
            ->findAll();

        if (empty($soalList)) {
            return [];
        }

        $opsiModel = new SoalOpsiModel();
        foreach ($soalList as &$s) {
            $s['opsi'] = $opsiModel->where('soal_id', $s['id'])->orderBy('urutan ASC, id ASC')->findAll();
        }

        return $soalList;
    }
}

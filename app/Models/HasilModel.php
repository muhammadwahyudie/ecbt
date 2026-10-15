<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilModel extends Model
{
    protected $table         = 'hasil';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'peserta_id', 'skor_objektif', 'skor_uraian', 'nilai_akhir', 'status_koreksi'
    ];

    /**
     * Hitung otomatis skor objektif dan nilai akhir siswa saat submit ujian
     */
    public function hitungHasilUjian(int $pesertaId): array
    {
        $db = \Config\Database::connect();
        $pesertaModel = new UjianPesertaModel();
        $peserta = $pesertaModel->find($pesertaId);

        if (!$peserta) {
            return [];
        }

        // Ambil semua soal dalam paket ujian
        $ujian = $db->table('ujian')->where('id', $peserta['ujian_id'])->get()->getRowArray();
        $soalList = $db->table('soal')->where('paket_id', $ujian['paket_id'])->where('deleted_at', null)->get()->getResultArray();

        $totalBobot = 0.0;
        $totalSkorObjektif = 0.0;
        $adaUraian = false;

        $jawabanList = $db->table('jawaban')->where('peserta_id', $pesertaId)->get()->getResultArray();
        $jawabanMap = [];
        foreach ($jawabanList as $j) {
            $jawabanMap[$j['soal_id']] = $j;
        }

        foreach ($soalList as $s) {
            $bobot = (float)$s['bobot'];
            $totalBobot += $bobot;

            $soalId = $s['id'];
            $jwb = $jawabanMap[$soalId]['jawaban'] ?? null;
            $skorSoal = 0.0;

            if ($s['tipe'] === 'uraian') {
                $adaUraian = true;
                continue; // Dinilai manual oleh guru
            }

            if ($jwb !== null && trim($jwb) !== '') {
                if ($s['tipe'] === 'pilihan_ganda') {
                    // Cari kunci jawaban
                    $kunci = $db->table('soal_opsi')
                        ->where('soal_id', $soalId)
                        ->where('benar', 1)
                        ->get()->getRowArray();

                    if ($kunci && strtoupper(trim($jwb)) === strtoupper(trim($kunci['label']))) {
                        $skorSoal = $bobot;
                    }
                } elseif ($s['tipe'] === 'pilihan_ganda_kompleks') {
                    // Cek multi-kunci
                    $kunciList = $db->table('soal_opsi')
                        ->where('soal_id', $soalId)
                        ->where('benar', 1)
                        ->get()->getResultArray();
                    $kunciLabels = array_map('strtoupper', array_column($kunciList, 'label'));
                    sort($kunciLabels);

                    $userLabels = array_filter(array_map('trim', explode(',', strtoupper($jwb))));
                    sort($userLabels);

                    if ($kunciLabels === $userLabels) {
                        $skorSoal = $bobot;
                    }
                } elseif ($s['tipe'] === 'isian_singkat') {
                    $kunciList = $db->table('soal_opsi')
                        ->where('soal_id', $soalId)
                        ->get()->getResultArray();

                    foreach ($kunciList as $k) {
                        if (strcasecmp(trim($jwb), trim($k['isi'])) === 0) {
                            $skorSoal = $bobot;
                            break;
                        }
                    }
                } elseif ($s['tipe'] === 'benar_salah') {
                    // Matriks per baris: format user "1:1,2:0,3:1"
                    $kunciList = $db->table('soal_opsi')->where('soal_id', $soalId)->get()->getResultArray();
                    $userRows = [];
                    foreach (explode(',', $jwb) as $pair) {
                        $parts = explode(':', $pair);
                        if (count($parts) === 2) {
                            $userRows[trim($parts[0])] = trim($parts[1]);
                        }
                    }

                    $semuaBenar = true;
                    foreach ($kunciList as $k) {
                        $rowLabel = $k['label'];
                        if (!isset($userRows[$rowLabel]) || (int)$userRows[$rowLabel] !== (int)$k['benar']) {
                            $semuaBenar = false;
                            break;
                        }
                    }

                    if ($semuaBenar) {
                        $skorSoal = $bobot;
                    }
                }
            }

            // Update skor pada baris jawaban
            if (isset($jawabanMap[$soalId])) {
                $db->table('jawaban')->where('id', $jawabanMap[$soalId]['id'])->update(['skor' => $skorSoal]);
            }
            $totalSkorObjektif += $skorSoal;
        }

        // Hitung skala 0 - 100
        $nilaiAkhir = $totalBobot > 0 ? round(($totalSkorObjektif / $totalBobot) * 100, 2) : 0.00;
        $statusKoreksi = $adaUraian ? 'belum' : 'selesai';

        $existing = $this->where('peserta_id', $pesertaId)->first();
        $hasilData = [
            'peserta_id'     => $pesertaId,
            'skor_objektif'  => $totalSkorObjektif,
            'skor_uraian'    => 0.00,
            'nilai_akhir'    => $nilaiAkhir,
            'status_koreksi' => $statusKoreksi,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->update($existing['id'], $hasilData);
        } else {
            $hasilData['created_at'] = date('Y-m-d H:i:s');
            $this->insert($hasilData);
        }

        return $hasilData;
    }
}

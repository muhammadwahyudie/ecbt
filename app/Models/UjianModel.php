<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
    protected $table         = 'ujian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'nama', 'paket_id', 'pembuat_id', 'mulai_at', 'selesai_at',
        'durasi_menit', 'acak_soal', 'acak_opsi', 'tampil_hasil',
        'nilai_minimal', 'batas_terlambat', 'batas_pelanggaran',
        'token', 'token_berlaku_sampai', 'status'
    ];

    protected $validationRules = [
        'nama'         => 'required|min_length[3]|max_length[200]',
        'paket_id'     => 'required|integer',
        'mulai_at'     => 'required',
        'selesai_at'   => 'required',
        'durasi_menit' => 'required|integer|greater_than[0]',
    ];

    /**
     * Generate 6-karakter token unik huruf besar & angka (menghindari karakter rancu 0/O, 1/I)
     */
    public static function generateToken(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $token = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < 6; $i++) {
            $token .= $chars[random_int(0, $max)];
        }
        return $token;
    }

    /**
     * Otomatis perbarui status ujian berdasarkan waktu sekarang
     */
    public function syncStatusWaktu(): void
    {
        $now = date('Y-m-d H:i:s');

        // Transisi ke berlangsung
        $this->builder()
            ->where('status', 'terjadwal')
            ->where('mulai_at <=', $now)
            ->where('selesai_at >=', $now)
            ->update(['status' => 'berlangsung']);

        // Transisi ke selesai
        $this->builder()
            ->whereIn('status', ['terjadwal', 'berlangsung'])
            ->where('selesai_at <', $now)
            ->update(['status' => 'selesai']);
    }

    /**
     * Dapatkan daftar ujian dengan relasi paket dan pembuat
     */
    public function getWithDetails(?int $userId = null, ?string $role = null): array
    {
        $this->syncStatusWaktu();

        $builder = $this->select('ujian.*, ps.nama as nama_paket, m.nama as nama_mapel, u.nama as nama_pembuat, COUNT(DISTINCT up.id) as total_peserta')
            ->join('paket_soal ps', 'ps.id = ujian.paket_id')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->join('users u', 'u.id = ujian.pembuat_id', 'left')
            ->join('ujian_peserta up', 'up.ujian_id = ujian.id', 'left')
            ->where('ujian.deleted_at', null)
            ->groupBy('ujian.id')
            ->orderBy('ujian.mulai_at', 'DESC');

        if ($role === 'guru' && $userId !== null) {
            $builder->where('ujian.pembuat_id', $userId);
        }

        return $builder->findAll();
    }
}

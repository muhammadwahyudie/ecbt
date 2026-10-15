<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table      = 'pengaturan';
    protected $primaryKey = 'kunci';
    protected $returnType = 'array';

    protected $allowedFields = [
        'kunci', 'nilai'
    ];

    /**
     * Dapatkan nilai pengaturan berdasarkan key
     */
    public function getVal(string $kunci, string $default = ''): string
    {
        $row = $this->where('kunci', $kunci)->first();
        return $row ? ($row['nilai'] ?? $default) : $default;
    }

    /**
     * Set nilai pengaturan (insert jika belum ada, update jika sudah ada)
     */
    public function setVal(string $kunci, ?string $nilai): bool
    {
        $existing = $this->where('kunci', $kunci)->first();
        if ($existing) {
            return (bool) $this->update($kunci, ['nilai' => $nilai]);
        }
        return (bool) $this->insert(['kunci' => $kunci, 'nilai' => $nilai]);
    }

    /**
     * Dapatkan semua pengaturan sebagai asosiasi key => value
     */
    public function getAllAsMap(): array
    {
        $rows = $this->findAll();
        $map  = [];
        foreach ($rows as $r) {
            $map[$r['kunci']] = $r['nilai'];
        }
        return $map;
    }
}

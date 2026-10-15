<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'nama', 'username', 'password_hash', 'role',
        'nis', 'nisn', 'email', 'aktif',
        'login_gagal', 'dikunci_sampai',
        'last_login_at', 'session_token',
    ];

    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[150]',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'role'     => 'required|in_list[admin,proktor,guru,siswa]',
    ];

    protected $validationMessages = [
        'username' => ['is_unique' => 'Username sudah digunakan.'],
    ];

    // ─── Auth helpers ────────────────────────────────────────────────────────

    /**
     * Cari user berdasarkan username atau NIS/NISN (untuk siswa).
     */
    public function findByLogin(string $login): ?array
    {
        return $this->where('aktif', 1)
            ->groupStart()
                ->where('username', $login)
                ->orWhere('nis', $login)
                ->orWhere('nisn', $login)
            ->groupEnd()
            ->first();
    }

    /**
     * Cek apakah akun sedang terkunci karena terlalu banyak login gagal.
     */
    public function isLoginLocked(array $user): bool
    {
        if ($user['dikunci_sampai'] === null) {
            return false;
        }
        return strtotime($user['dikunci_sampai']) > time();
    }

    /**
     * Tambah hitungan login gagal. Kunci selama 5 menit setelah 5 kali gagal.
     */
    public function incrementLoginGagal(int $userId): void
    {
        $user = $this->find($userId);
        $gagal = ($user['login_gagal'] ?? 0) + 1;
        $data  = ['login_gagal' => $gagal];
        if ($gagal >= 5) {
            $data['dikunci_sampai'] = date('Y-m-d H:i:s', time() + 300); // 5 menit
        }
        $this->update($userId, $data);
    }

    /**
     * Reset hitungan login gagal setelah berhasil login.
     */
    public function resetLoginGagal(int $userId): void
    {
        $this->update($userId, [
            'login_gagal'    => 0,
            'dikunci_sampai' => null,
            'last_login_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Set session token untuk deteksi sesi ganda.
     */
    public function setSessionToken(int $userId, string $token): void
    {
        $this->update($userId, ['session_token' => $token]);
    }

    /**
     * Hapus session token saat logout.
     */
    public function clearSessionToken(int $userId): void
    {
        $this->update($userId, ['session_token' => null]);
    }
}

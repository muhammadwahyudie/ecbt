<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // GET /login
    public function index()
    {
        // Sudah login? arahkan ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to($this->getDashboardUrl(session()->get('role')));
        }
        return view('auth/login', [
            'title' => 'Login — CBT Sekolah',
        ]);
    }

    // POST /login
    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $login    = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByLogin($login);

        if (! $user) {
            return redirect()->back()->withInput()
                ->with('error', 'Username atau kata sandi salah.');
        }

        // Verifikasi password
        if (! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Username atau kata sandi salah.');
        }

        // Cek akun aktif
        if (! $user['aktif']) {
            return redirect()->back()->withInput()
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // Siswa: cek sesi ganda
        if ($user['role'] === 'siswa' && ! empty($user['session_token'])) {
            // Sesi lain sudah ada — tolak
            return redirect()->back()->withInput()
                ->with('error', 'Anda sudah login dari perangkat lain. Hubungi proktor untuk mereset sesi.');
        }

        // Buat session token unik
        $sessionToken = bin2hex(random_bytes(32));

        // Reset login gagal & simpan token
        $this->userModel->resetLoginGagal($user['id']);
        $this->userModel->setSessionToken($user['id'], $sessionToken);

        // Set session
        session()->set([
            'logged_in'    => true,
            'user_id'      => $user['id'],
            'nama'         => $user['nama'],
            'username'     => $user['username'],
            'role'         => $user['role'],
            'session_token'=> $sessionToken,
        ]);

        // Log aktivitas
        $this->logAktivitas($user['id'], 'login', 'auth');

        return redirect()->to($this->getDashboardUrl($user['role']));
    }

    // GET /logout
    public function logout()
    {
        $userId = session()->get('user_id');
        if ($userId) {
            $this->userModel->clearSessionToken($userId);
            $this->logAktivitas($userId, 'logout', 'auth');
        }
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function getDashboardUrl(string $role): string
    {
        return match ($role) {
            'admin'   => '/admin/dashboard',
            'proktor' => '/proktor/dashboard',
            'guru'    => '/guru/dashboard',
            'siswa'   => '/siswa/dashboard',
            default   => '/login',
        };
    }

    private function logAktivitas(int $userId, string $aksi, string $target): void
    {
        $db = \Config\Database::connect();
        $db->table('log_aktivitas')->insert([
            'user_id'    => $userId,
            'aksi'       => $aksi,
            'target'     => $target,
            'detail'     => json_encode(['ip' => $this->request->getIPAddress()]),
            'ip'         => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

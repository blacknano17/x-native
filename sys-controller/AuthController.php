<?php

class AuthController extends webController
{
    /**
     * Tampilkan Halaman Login
     * GET /login
     */
    public function showLogin($core)
    {
        AuthWebMiddleware::guest();

        $this->render('pages/login.php', [
            'pageTitle' => 'Masuk ke Dashboard'
        ], 'auth');
    }

    /**
     * Proses Login User
     * POST /login
     */
    public function login($core)
    {
        AuthWebMiddleware::guest();

        // 1. Rate limiting login: Maksimal 5 percobaan gagal per menit
        RateLimiter::check(5, 60, 'login_' . ($_SERVER['REMOTE_ADDR'] ?? ''));

        // 2. Validasi input
        $username = trim($this->input('username', ''));
        $password = trim($this->input('password', ''));

        if (empty($username) || empty($password)) {
            $this->redirect('/login', 'Username dan password wajib diisi.', 'error');
        }

        // 3. Authentikasi: Cek ke Database MySQL Utama
        try {
            $userRows = $this->db()->fetchObject(
                "SELECT id, name, username, email, password, role, company, avatar, status FROM users WHERE username = ? AND status = 'active' LIMIT 1",
                [$username]
            );

            if (!empty($userRows)) {
                $dbUser = (array)$userRows[0];
                if (password_verify($password, $dbUser['password'])) {
                    Session::regenerate();
                    unset($dbUser['password']);
                    Session::set('user', $dbUser);
                    Logger::info("User login sukses (via Database): {$username}", ['user_id' => $dbUser['id']]);
                    $this->redirect('/dashboard', "Selamat datang kembali, {$dbUser['name']}!", 'success');
                }
            }
        } catch (Exception $e) {
            // Jika tabel users belum dimigrasi di MySQL, fallback ke akun demo bawaan
        }

        // 4. Fallback Akun Demo Bawaan (Jika database belum disetup)
        $mockUsers = [
            'admin' => [
                'id'       => 1,
                'name'     => 'Danu Superadmin',
                'username' => 'admin',
                'password' => 'password123',
                'role'     => 'superadmin',
                'company'  => 'PT Sinar Maju Bersama',
                'avatar'   => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'
            ],
            'manager' => [
                'id'       => 2,
                'name'     => 'Budi Logistik Manager',
                'username' => 'manager',
                'password' => 'manager123',
                'role'     => 'manager',
                'company'  => 'PT Sinar Maju Bersama',
                'avatar'   => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
            ]
        ];

        if (isset($mockUsers[$username]) && $mockUsers[$username]['password'] === $password) {
            Session::regenerate();
            $userData = $mockUsers[$username];
            unset($userData['password']);
            Session::set('user', $userData);
            Logger::info("User login sukses (Demo Account): {$username}", ['user_id' => $userData['id']]);
            $this->redirect('/dashboard', "Selamat datang kembali, {$userData['name']}!", 'success');
        }

        Logger::warning("Percobaan login gagal untuk username: {$username}");
        $this->redirect('/login', 'Username atau password yang Anda masukkan salah.', 'error');
    }

    /**
     * Logout User
     * POST /logout atau GET /logout
     */
    public function logout($core)
    {
        $currentUser = Session::user();
        if ($currentUser) {
            Logger::info("User logout: {$currentUser['username']}");
        }

        Session::destroy();
        $this->redirect('/login', 'Anda telah berhasil keluar dari sistem.', 'info');
    }
}

<?php

class AuthWebMiddleware
{
    /**
     * Wajibkan user sudah login untuk mengakses halaman
     */
    public static function handle(): array
    {
        Session::start();
        if (!Session::check()) {
            Flash::warning('Sesi Anda telah berakhir. Silakan login kembali.');
            $core = Core::instance();
            $core->reroute('/login');
            exit();
        }

        return Session::user();
    }

    /**
     * Khusus halaman publik (Login) - jika sudah login langsung diarahkan ke Dashboard
     */
    public static function guest(): void
    {
        Session::start();
        if (Session::check()) {
            $core = Core::instance();
            $core->reroute('/dashboard');
            exit();
        }
    }

    /**
     * Pengecekan role / hak akses user
     */
    public static function role(string ...$allowedRoles): void
    {
        $user = self::handle();
        $userRole = strtolower($user['role'] ?? '');

        $allowed = array_map('strtolower', $allowedRoles);
        if (!in_array($userRole, $allowed, true)) {
            http_response_code(403);
            Flash::error('Akses ditolak: Anda tidak memiliki wewenang untuk membuka halaman ini.');
            $core = Core::instance();
            $core->reroute('/dashboard');
            exit();
        }
    }
}

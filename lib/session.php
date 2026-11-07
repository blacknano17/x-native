<?php

class Session
{
    private static $started = false;

    /**
     * Inisialisasi session dengan pengamanan ketat (OWASP Session Security)
     */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            ($_SERVER['SERVER_PORT'] ?? '') == 443 ||
            ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        );

        // Konfigurasi cookie session aman
        session_set_cookie_params([
            'lifetime' => (int)Env::get('SESSION_LIFETIME', 7200), // Default 2 jam
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,                 // Cegah pencurian session via JavaScript XSS
            'samesite' => 'Lax'                 // Cegah serangan CSRF cross-origin
        ]);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_start();
        self::$started = true;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Regenerasi Session ID untuk mencegah Session Fixation Attack
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Hancurkan seluruh session (Logout)
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Dapatkan user yang sedang login
     */
    public static function user()
    {
        return self::get('user');
    }

    /**
     * Cek apakah user sedang login
     */
    public static function check(): bool
    {
        return self::has('user') && !empty(self::get('user'));
    }
}

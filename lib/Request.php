<?php

class Request
{
    private static $parsedJson = null;

    /**
     * Dapatkan HTTP Method request saat ini
     */
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Dapatkan IP address klien yang aman
     */
    public static function ip(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',  // Load balancer / proxy
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Dapatkan nilai header HTTP
     */
    public static function header(string $name, $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$normalized])) {
            return $_SERVER[$normalized];
        }
        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $k => $v) {
                if (strcasecmp($k, $name) === 0) {
                    return $v;
                }
            }
        }
        return $default;
    }

    /**
     * Ekstrak Bearer Token dari header Authorization
     */
    public static function bearerToken(): ?string
    {
        $auth = self::header('Authorization');
        if (!$auth) {
            // Cek mod_rewrite fallback
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
        }

        if ($auth && preg_match('/Bearer\s+(\S+)/i', $auth, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Ambil data JSON body request (POST/PUT/PATCH/DELETE)
     * Dilengkapi proteksi ukuran payload (maksimal 2MB untuk mencegah DoS)
     */
    public static function json(?string $key = null, $default = null)
    {
        if (self::$parsedJson === null) {
            $raw = file_get_contents('php://input');

            // Proteksi ukuran payload DoS (maks 2MB)
            if (strlen($raw) > 2 * 1024 * 1024) {
                Response::error('Payload Too Large. Maksimal 2MB.', 413);
            }

            if (empty($raw)) {
                self::$parsedJson = [];
            } else {
                $decoded = json_decode($raw, true);
                if (json_last_error() !== JSON_ERROR_NONE && !empty(trim($raw))) {
                    Response::error('Malformed JSON Body: ' . json_last_error_msg(), 400);
                }
                self::$parsedJson = is_array($decoded) ? $decoded : [];
            }
        }

        if ($key === null) {
            return self::$parsedJson;
        }

        return self::$parsedJson[$key] ?? $default;
    }

    /**
     * Ambil seluruh input (menggabungkan Query Param GET dan Body JSON/POST)
     */
    public static function all(): array
    {
        $query = $_GET ?? [];
        $post = $_POST ?? [];
        $json = self::json();

        return array_merge($query, $post, $json);
    }

    /**
     * Ambil input spesifik dengan fallback default
     */
    public static function input(string $key, $default = null)
    {
        $all = self::all();
        return $all[$key] ?? $default;
    }
}

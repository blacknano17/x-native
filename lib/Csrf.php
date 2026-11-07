<?php

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Dapatkan atau generate CSRF Token
     */
    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (!$token || !is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    /**
     * Helper menghasilkan elemen form input HTML tersembunyi
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
    }

    /**
     * Validasi kecocokan token CSRF
     */
    public static function verify(?string $token = null): bool
    {
        $stored = Session::get(self::SESSION_KEY);
        if (!$stored) {
            return false;
        }

        if ($token === null) {
            $token = $_POST['_csrf_token'] ??
                     $_SERVER['HTTP_X_CSRF_TOKEN'] ??
                     $_SERVER['HTTP_X_XSRF_TOKEN'] ??
                     null;
        }

        if (!$token || !is_string($token)) {
            return false;
        }

        return hash_equals($stored, $token);
    }
}

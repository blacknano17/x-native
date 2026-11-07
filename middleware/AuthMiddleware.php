<?php

class AuthMiddleware
{
    /**
     * Verifikasi Bearer JWT Token pada request
     *
     * @return object Payload user yang berhasil di-decode
     */
    public static function authenticate()
    {
        $token = Request::bearerToken();

        if (empty($token)) {
            Response::error('Unauthorized: Bearer token tidak ditemukan pada header Authorization.', 401);
        }

        $core = Core::instance();
        $secretKey = $core->get('JWT_SECRET') ?: 'default-secret-key-change-in-production';

        try {
            $decoded = JWT::decode($token, $secretKey, ['HS256']);

            // Simpan data user terautentikasi ke dalam Hive Core
            $core->set('AUTH_USER', $decoded);

            return $decoded;
        } catch (ExpiredException $e) {
            Response::error('Unauthorized: Token telah kadaluarsa (expired). Silakan login kembali.', 401);
        } catch (SignatureInvalidException $e) {
            Response::error('Unauthorized: Tanda tangan token tidak valid (signature mismatch).', 401);
        } catch (Exception $e) {
            Response::error('Unauthorized: Token tidak valid atau format salah (' . $e->getMessage() . ').', 401);
        }
    }

    /**
     * Dapatkan user yang sedang login saat ini
     */
    public static function user()
    {
        $core = Core::instance();
        return $core->exists('AUTH_USER') ? $core->get('AUTH_USER') : null;
    }
}

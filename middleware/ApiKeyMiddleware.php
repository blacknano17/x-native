<?php

class ApiKeyMiddleware
{
    /**
     * Verifikasi API Key (dan opsi HMAC Signature) untuk komunikasi B2B / Webhook
     *
     * @param bool $requireSignature Apakah wajib memeriksa X-SIGNATURE
     * @return void
     */
    public static function verify(bool $requireSignature = false)
    {
        $core = Core::instance();
        $apiKey = Request::header('X-API-KEY');

        if (empty($apiKey)) {
            Response::error('Unauthorized: Header X-API-KEY tidak ditemukan.', 401);
        }

        $rawKeys = $core->get('API_KEYS') ?: [];
        $validKeys = is_array($rawKeys) ? $rawKeys : explode(',', (string)$rawKeys);
        $validKeys = array_map('trim', $validKeys);

        if (!in_array($apiKey, $validKeys, true)) {
            Response::error('Unauthorized: API Key tidak valid.', 401);
        }

        // Jika endpoint mewajibkan HMAC-SHA256 Signature
        if ($requireSignature) {
            $timestamp = Request::header('X-TIMESTAMP');
            $signature = Request::header('X-SIGNATURE');

            if (empty($timestamp) || empty($signature)) {
                Response::error('Unauthorized: Header X-TIMESTAMP dan X-SIGNATURE wajib disertakan.', 401);
            }

            // Proteksi Replay Attack: toleransi waktu maksimal 5 menit (300 detik)
            if (abs(time() - (int)$timestamp) > 300) {
                Response::error('Unauthorized: Timestamp kadaluarsa atau deviasi waktu terlalu besar (Replay Attack Prevention).', 401);
            }

            $secret = $core->get('HMAC_SECRET') ?: 'default-hmac-secret';
            $method = Request::method();
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $rawBody = file_get_contents('php://input');

            // Susun canonical string untuk ditandatangani
            $payloadToSign = $method . ':' . $path . ':' . $timestamp . ':' . $rawBody;
            $expectedSignature = hash_hmac('sha256', $payloadToSign, $secret);

            if (!hash_equals($expectedSignature, $signature)) {
                Response::error('Unauthorized: Signature HMAC tidak cocok (Data Tampering Detected).', 401);
            }
        }
    }
}

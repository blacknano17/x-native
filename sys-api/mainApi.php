<?php

#[\AllowDynamicProperties]
class mainApi
{
    public $core;
    public $dbc;
    public $dbc2;
    public $msg;
    public $vf;
    public $user;

    protected $ss;
    protected $jwt;
    protected $jwtkey;
    protected $secretKey;

    public function __construct()
    {
        $core = Core::instance();

        $db_dns = $core->exists('db_dns') ? $core->get('db_dns') : '';
        $db_user = $core->exists('db_user') ? $core->get('db_user') : '';
        $db_pass = $core->exists('db_pass') ? $core->get('db_pass') : '';
        $db_dns2 = $core->exists('db_dns2') ? $core->get('db_dns2') : null;
        $dev_mode = $core->exists('dev_mode') ? $core->get('dev_mode') : 'dev';

        $dbc = new Dbconfig5($db_dns, $db_user, $db_pass, $db_dns2, $dev_mode);

        $db_dns_pengiriman = $core->exists('db_dns_pengiriman') ? $core->get('db_dns_pengiriman') : null;
        $dbc2 = $db_dns_pengiriman ? new Dbconfig5($db_dns_pengiriman, $db_user, $db_pass, $db_dns2, $dev_mode) : null;

        $errorConfig = new ErrorConfig(
            $dev_mode,
            $core->get('SESSION.user_id'),
            $core->get('SESSION.username'),
            $core->get('SESSION.lokasi')
        );

        $this->core = $core;
        $this->vf = $core;
        $this->dbc = $dbc;
        $this->dbc2 = $dbc2;
        $this->msg = $errorConfig;
        $this->ss = new safeString;
        $this->jwt = new JWT;
        $this->jwtkey = $core->get('JWT_SECRET') ?: 'default-jwt-secret-key';
        $this->secretKey = $this->jwtkey;
        $this->user = AuthMiddleware::user() ?: Session::user();
    }

    /**
     * Wajibkan autentikasi Bearer JWT untuk method/controller ini
     */
    protected function requireAuth()
    {
        $this->user = AuthMiddleware::authenticate();
        return $this->user;
    }

    /**
     * Wajibkan verifikasi API Key
     */
    protected function requireApiKey(bool $requireSignature = false)
    {
        ApiKeyMiddleware::verify($requireSignature);
    }

    /**
     * Terapkan Rate Limit khusus pada method ini
     */
    protected function rateLimit(int $maxRequests, int $decaySeconds = 60, ?string $customKey = null)
    {
        RateLimiter::check($maxRequests, $decaySeconds, $customKey);
    }

    /**
     * Validasi input request menggunakan schema rules
     */
    protected function validate(array $rules, ?array $data = null): array
    {
        $target = $data !== null ? $data : Request::all();
        return Validator::check($target, $rules);
    }

    /**
     * Ambil data JSON body
     */
    protected function jsonBody(?string $key = null, $default = null)
    {
        return Request::json($key, $default);
    }

    /**
     * Ambil seluruh input (GET, POST, JSON)
     */
    protected function input(?string $key = null, $default = null)
    {
        if ($key === null) {
            return Request::all();
        }
        return Request::input($key, $default);
    }

    /**
     * Respon Sukses Terstandarisasi Modern (HTTP 200/201)
     */
    public function success($data = null, string $message = 'Success', int $statusCode = 200, array $meta = [])
    {
        Response::success($data, $message, $statusCode, $meta);
    }

    /**
     * Respon Error Terstandarisasi Modern (HTTP 4xx/5xx)
     */
    public function error($message = 'Error', int $statusCode = 400, $errors = null, $devMsg = null)
    {
        // Auto-rollback jika ada transaksi aktif di database utama (dbc)
        if (isset($this->dbc) && is_object($this->dbc) && method_exists($this->dbc, 'inTransaction') && $this->dbc->inTransaction()) {
            $this->dbc->close();
        }

        // Auto-rollback jika ada transaksi aktif di database kedua (dbc2)
        if (isset($this->dbc2) && is_object($this->dbc2) && method_exists($this->dbc2, 'inTransaction') && $this->dbc2->inTransaction()) {
            $this->dbc2->close();
        }

        // Jika dipanggil dengan format lama SIMA: error($msg, $params = [], $dev_msg = [])
        if (is_array($statusCode) || is_string($statusCode)) {
            $params = is_array($statusCode) ? $statusCode : [];
            $dev_msg = is_array($errors) ? $errors : [];
            return $this->msg->error($message, $params, $dev_msg);
        }

        Response::error($message, $statusCode, $errors, $devMsg);
    }

    /**
     * Kompatibilitas mundur dengan SIMA lama (typo succes)
     */
    public function succes($msg, $params = [], $dev_msg = [])
    {
        return $this->msg->succes($msg, $params, $dev_msg);
    }

    /**
     * Generate secure random token
     */
    public function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Verifikasi token constant-time
     */
    public function verifyToken($token, $expectedToken)
    {
        return hash_equals($token, $expectedToken);
    }

    /**
     * Helper Logging Terstruktur
     */
    protected function logInfo(string $message, array $context = [])
    {
        Logger::info($message, $context);
    }

    protected function logError(string $message, array $context = [])
    {
        Logger::error($message, $context);
    }

    /**
     * Helper Paginasi Aman (Anti SQL Injection pada ORDER BY)
     */
    protected function paginate(string $baseSql, string $selectFields = '*', array $params = [], int $defaultLimit = 10, array $allowedSort = ['id'], string $defaultSort = 'id'): array
    {
        $page = (int)Request::input('page', 1);
        $limit = (int)Request::input('limit', $defaultLimit);
        $sortBy = Request::input('sort_by', $defaultSort);
        $sortDir = Request::input('sort_dir', 'DESC');

        return Pagination::paginate($this->dbc, $baseSql, $selectFields, $params, $page, $limit, $sortBy, $sortDir, $allowedSort, $defaultSort);
    }

    /**
     * Generate JWT Token untuk user login
     */
    public function createJwtToken(array $payload, int $ttlSeconds = 3600): string
    {
        $now = time();
        $claims = array_merge([
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
            'iss' => $this->core->get('site') ?: 'M-Native'
        ], $payload);

        return JWT::encode($claims, $this->jwtkey, 'HS256');
    }
}

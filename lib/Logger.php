<?php

class Logger
{
    private static $sensitiveKeys = [
        'password', 'password_confirmation', 'pin', 'token', 'access_token',
        'refresh_token', 'secret', 'authorization', 'credit_card', 'cvv',
        'x-api-key', 'x-signature'
    ];

    /**
     * Tulis log umum (info, warning, error)
     */
    public static function log(string $level, string $message, array $context = [], string $filename = 'app')
    {
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $file = $logDir . '/' . $filename . '_' . date('Y-m-d') . '.log';
        $entry = [
            'timestamp'  => date('Y-m-d H:i:s'),
            'level'      => strtoupper($level),
            'message'    => $message,
            'client_ip'  => Request::ip(),
            'context'    => self::redactSensitiveData($context)
        ];

        $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = [])
    {
        self::log('info', $message, $context, 'app');
    }

    public static function warning(string $message, array $context = [])
    {
        self::log('warning', $message, $context, 'app');
    }

    public static function error(string $message, array $context = [])
    {
        self::log('error', $message, $context, 'error');
    }

    /**
     * Tulis log audit hit request & response
     */
    public static function audit(string $method, string $uri, int $statusCode, float $durationMs, array $requestBody = [])
    {
        $core = Core::instance();
        if ($core->exists('LOG_AUDIT_ENABLED') && $core->get('LOG_AUDIT_ENABLED') == false) {
            return;
        }

        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $file = $logDir . '/audit_' . date('Y-m-d') . '.log';
        $entry = [
            'timestamp'    => date('Y-m-d H:i:s'),
            'method'       => $method,
            'uri'          => $uri,
            'status_code'  => $statusCode,
            'duration_ms'  => round($durationMs, 2),
            'client_ip'    => Request::ip(),
            'user'         => AuthMiddleware::user() ? (AuthMiddleware::user()->username ?? 'authenticated') : 'guest',
            'payload'      => self::redactSensitiveData($requestBody)
        ];

        $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Sensor / masking otomatis terhadap field kredensial sensitif
     */
    public static function redactSensitiveData(array $data): array
    {
        $redacted = [];
        foreach ($data as $key => $val) {
            $lowerKey = strtolower((string)$key);
            if (in_array($lowerKey, self::$sensitiveKeys, true)) {
                $redacted[$key] = '********';
            } elseif (is_array($val)) {
                $redacted[$key] = self::redactSensitiveData($val);
            } else {
                $redacted[$key] = $val;
            }
        }
        return $redacted;
    }
}

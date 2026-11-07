<?php

class RateLimiter
{
    /**
     * Periksa apakah request melebihi batasan (Rate Limit Throttling)
     *
     * @param int|null $maxRequests Jumlah request maksimal (default dari config atau 60)
     * @param int|null $decaySeconds Rentang waktu per detik (default 60 detik)
     * @param string|null $customKey Identifier unik (default menggunakan IP address)
     * @return void
     */
    public static function check(?int $maxRequests = null, ?int $decaySeconds = null, ?string $customKey = null)
    {
        $core = Core::instance();

        // Cek apakah rate limiter diaktifkan di config
        if ($core->exists('RATE_LIMIT_ENABLED') && $core->get('RATE_LIMIT_ENABLED') == 0) {
            return;
        }

        $max = $maxRequests ?? (int)($core->get('RATE_LIMIT_MAX') ?: 60);
        $decay = $decaySeconds ?? (int)($core->get('RATE_LIMIT_DECAY') ?: 60);

        $ip = Request::ip();
        $key = $customKey ? md5($customKey) : md5($ip);

        $cacheDir = __DIR__ . '/../storage/cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $file = $cacheDir . '/ratelimit_' . $key . '.json';
        $now = time();

        $data = [
            'count' => 0,
            'reset' => $now + $decay
        ];

        if (file_exists($file)) {
            $content = @file_get_contents($file);
            $parsed = @json_decode($content, true);
            if (is_array($parsed) && isset($parsed['reset']) && $parsed['reset'] > $now) {
                $data = $parsed;
            }
        }

        $data['count']++;
        @file_put_contents($file, json_encode($data), LOCK_EX);

        $remaining = max(0, $max - $data['count']);
        $retryAfter = max(1, $data['reset'] - $now);

        header("X-RateLimit-Limit: $max");
        header("X-RateLimit-Remaining: $remaining");
        header("X-RateLimit-Reset: " . $data['reset']);

        if ($data['count'] > $max) {
            header("Retry-After: $retryAfter");
            Response::error("Too Many Requests. Batas $max request per $decay detik tercapai.", 429, [
                'retry_after_seconds' => $retryAfter,
                'client_ip' => $ip
            ]);
        }
    }
}

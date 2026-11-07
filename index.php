<?php
/**
 * X-Native - Enterprise Fullstack Web & Dashboard Engine
 */

date_default_timezone_set('Asia/Jakarta');

// 1. Security Headers (OWASP Web Security Standard)
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// 2. Composer Autoloader jika ada
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/helper/vendor/autoload.php')) {
    require_once __DIR__ . '/helper/vendor/autoload.php';
}

// 3. Load Environment Variables (.env)
require_once __DIR__ . '/lib/Env.php';
Env::load(__DIR__ . '/.env');

// 4. Inisialisasi Core Engine & Autoloader
$core = require(__DIR__ . '/lib/core.php');
$core->config(__DIR__ . '/config/config.ini');

if ($envAppName = Env::get('APP_NAME')) $core->set('site', $envAppName);
if ($envDebug = Env::get('DEBUG')) $core->set('DEBUG', (int)$envDebug);
if ($envUrl = Env::get('APP_URL')) $core->set('API_HOST', $envUrl);

// 5. Inisialisasi Sesi Hardened
Session::start();

// 6. CSRF Protection Middleware Otomatis untuk Semua Request Mutasi (POST/PUT/DELETE)
CsrfMiddleware::handle();

// 7. Global Exception & Error Handler
$core->set('ONERROR', function($f3) {
    $error = $f3->get('ERROR');
    $statusCode = $error['code'] ?: 500;
    if ($statusCode < 100 || $statusCode > 599) {
        $statusCode = 500;
    }

    // Auto-rollback semua transaksi database yang mungkin masih terbuka
    Database::rollbackAll();

    // Catat log error internal
    Logger::error($error['text'] ?? 'Unknown Web Error', [
        'code'  => $statusCode,
        'file'  => $error['file'] ?? '-',
        'line'  => $error['line'] ?? 0,
        'trace' => explode("\n", $error['trace'] ?? '')
    ]);

    if (ob_get_length()) {
        ob_clean();
    }
    http_response_code($statusCode);

    $isDev = ($f3->get('DEBUG') > 0 && Env::get('APP_ENV', 'dev') === 'dev');

    ?>
<!DOCTYPE html>
<html lang="id" class="bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= $statusCode ?> &mdash; X-Native</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase bg-rose-500/10 text-rose-400 border border-rose-500/20 mb-4">
            HTTP Status <?= $statusCode ?>
        </div>
        <h1 class="text-xl font-bold text-white mb-2">
            <?= $statusCode === 404 ? 'Halaman Tidak Ditemukan' : 'Terjadi Kesalahan Server' ?>
        </h1>
        <p class="text-xs text-slate-400 mb-6">
            <?= $isDev ? htmlspecialchars($error['text'] ?? 'Internal Error') : 'Sistem mengalami kendala saat memproses halaman. Rincian telah dicatat ke log server.' ?>
        </p>

        <?php if ($isDev && !empty($error['file'])): ?>
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-mono text-slate-300 mb-6 overflow-x-auto">
                <div class="text-rose-400 mb-1">Lokasi File: <?= htmlspecialchars($error['file']) ?> : Line <?= $error['line'] ?></div>
                <div class="text-slate-500 whitespace-pre-wrap"><?= htmlspecialchars(substr($error['trace'] ?? '', 0, 500)) ?>...</div>
            </div>
        <?php endif; ?>

        <div class="flex gap-3">
            <a href="javascript:history.back()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-white transition">
                &larr; Kembali
            </a>
            <a href="<?= Env::get('APP_URL', '/') ?>" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-xs font-semibold text-white transition">
                Ke Dashboard Utama
            </a>
        </div>
    </div>
</body>
</html>
    <?php
    exit();
});

// 8. Auto-rollback hook pada shutdown
register_shutdown_function(function() {
    Database::rollbackAll();
});

// 9. Load Route & Jalankan Engine
$core->config(__DIR__ . '/route/route.ini');
$core->run();

<?php

class CsrfMiddleware
{
    /**
     * Verifikasi token CSRF untuk seluruh request mutasi (POST, PUT, PATCH, DELETE)
     */
    public static function handle(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (!Csrf::verify()) {
                header("HTTP/1.1 419 Page Expired");
                http_response_code(419);
                Flash::error('Sesi form kadaluarsa (CSRF token invalid). Silakan muat ulang halaman.');

                $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                          (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => false,
                        'code'    => 419,
                        'message' => 'CSRF Token Mismatch or Expired.'
                    ]);
                    exit();
                }

                if (!empty($_SERVER['HTTP_REFERER'])) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit();
                }

                // Tampilan error 419 langsung jika tanpa referer
                ?>
<!DOCTYPE html>
<html lang="id" class="bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <title>419 Page Expired &mdash; X-Native</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl p-8 text-center shadow-2xl">
        <div class="text-4xl mb-3">🛡️</div>
        <h1 class="text-xl font-bold text-white mb-2">419 | Sesi Form Kadaluarsa</h1>
        <p class="text-xs text-slate-400 mb-6">Token keamanan CSRF tidak valid atau sesi Anda telah habis masa berlakunya demi keamanan.</p>
        <a href="javascript:history.back()" class="inline-block px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-xs font-semibold text-white transition">
            &larr; Muat Ulang Form
        </a>
    </div>
</body>
</html>
                <?php
                exit();
            }
        }
    }
}

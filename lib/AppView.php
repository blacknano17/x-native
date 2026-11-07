<?php

class AppView
{
    /**
     * Render view page dengan master layout
     *
     * @param string $pagePath Path ke file halaman di dalam sys-view/ (contoh: 'pages/dashboard.php')
     * @param array $data Data yang diteruskan ke halaman
     * @param string $layout Nama layout di sys-view/layouts/ (contoh: 'app', 'auth')
     * @return void
     */
    public static function render(string $pagePath, array $data = [], string $layout = 'app'): void
    {
        $baseDir = __DIR__ . '/../sys-view';
        $pageFile = $baseDir . '/' . ltrim($pagePath, '/');

        if (!file_exists($pageFile)) {
            throw new Exception("View page [$pagePath] tidak ditemukan di $pageFile");
        }

        // Global variables yang otomatis tersedia di seluruh view
        $core = Core::instance();
        $globalData = [
            'appName'       => Env::get('APP_NAME', 'X-Native'),
            'baseUrl'       => rtrim(Env::get('APP_URL', $core->get('API_HOST') ?: '/'), '/') . '/',
            'currentUser'   => Session::user(),
            'flashMessages' => Flash::all(),
            'csrfToken'     => Csrf::token(),
            'currentPath'   => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH)
        ];

        // Ekstrak data ke dalam scope
        $scope = array_merge($globalData, $data);
        extract($scope, EXTR_SKIP);

        // Tangkap output konten halaman utama
        ob_start();
        include $pageFile;
        $content = ob_get_clean();

        // Jika layout diminta, bungkus ke dalam layout master
        if ($layout) {
            $layoutFile = $baseDir . '/layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
                return;
            }
        }

        // Jika tanpa layout, tampilkan konten langsung
        echo $content;
    }

    /**
     * Sertakan komponen parsial (misal: navbar, sidebar, alert)
     */
    public static function component(string $componentName, array $data = []): void
    {
        $file = __DIR__ . '/../sys-view/components/' . ltrim($componentName, '/') . '.php';
        if (file_exists($file)) {
            extract($data, EXTR_SKIP);
            include $file;
        }
    }
}

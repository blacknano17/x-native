<?php

#[\AllowDynamicProperties]
class webController
{
    public $core;
    public $user;

    public function __construct()
    {
        $this->core = Core::instance();
        $this->user = Session::user();
    }

    /**
     * Akses koneksi database Dbconfig5 dinamis (Multi-Database Support)
     *
     * Contoh:
     * $this->db()               -> Koneksi Database Utama
     * $this->db('logistik')     -> Koneksi Database Logistik
     * $this->db('report')       -> Koneksi Database Reporting
     *
     * @param string|null $connectionName
     * @return Dbconfig5
     */
    public function db(?string $connectionName = 'default'): Dbconfig5
    {
        return Database::connection($connectionName);
    }

    /**
     * Wajibkan autentikasi web session
     */
    protected function requireAuth(): array
    {
        $this->user = AuthWebMiddleware::handle();
        return $this->user;
    }

    /**
     * Wajibkan role tertentu
     */
    protected function requireRole(string ...$roles): void
    {
        AuthWebMiddleware::role(...$roles);
    }

    /**
     * Render view dengan layout master
     */
    protected function render(string $page, array $data = [], string $layout = 'app'): void
    {
        AppView::render($page, $data, $layout);
    }

    /**
     * Helper redirect dengan pesan flash
     */
    protected function redirect(string $url, ?string $message = null, string $type = 'success'): void
    {
        if ($message !== null) {
            Flash::add($type, $message);
        }
        $this->core->reroute($url);
        exit();
    }

    /**
     * Validasi input request POST/GET
     */
    protected function validate(array $rules, ?array $data = null): array
    {
        $target = $data !== null ? $data : array_merge($_GET, $_POST);
        return Validator::check($target, $rules);
    }

    /**
     * Ambil input data
     */
    protected function input(?string $key = null, $default = null)
    {
        $all = array_merge($_GET, $_POST);
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    /**
     * Respon JSON untuk interaksi AJAX / Alpine.js
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit();
    }
}

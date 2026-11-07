<?php

class exampleApi extends mainApi
{
    /**
     * Endpoint Ping / Healthcheck
     * GET /api/ping
     */
    public function ping($core)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo $this->succes('Framework-X API is alive and running smoothly!', [
            'app' => $core->get('site'),
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'api_host' => $core->get('API_HOST')
        ]);
    }

    /**
     * Endpoint Contoh Get Data
     * GET /api/hello/@name
     */
    public function hello($core, $params)
    {
        header('Content-Type: application/json; charset=utf-8');
        $name = isset($params['name']) ? htmlspecialchars($params['name']) : 'World';
        echo $this->succes("Halo, $name! Selamat datang di Framework-X.", [
            'name' => $name,
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
    }
}

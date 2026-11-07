<?php

class Response
{
    /**
     * Kirim respon JSON terstandarisasi dengan HTTP Status Code yang tepat
     *
     * @param mixed $data
     * @param int $statusCode
     * @param string $message
     * @param array $meta
     * @param array $headers
     * @return void
     */
    public static function json($data = null, int $statusCode = 200, string $message = 'Success', array $meta = [], array $headers = [])
    {
        // Bersihkan output buffer sebelumnya jika ada agar response murni JSON
        if (ob_get_length()) {
            ob_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        foreach ($headers as $headerKey => $headerVal) {
            header("$headerKey: $headerVal");
        }

        $payload = [
            'success'   => ($statusCode >= 200 && $statusCode < 300),
            'code'      => $statusCode,
            'message'   => $message,
            'data'      => $data,
            'meta'      => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'version'   => '1.0.0'
            ], $meta)
        ];

        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Helper Respon Sukses (HTTP 200 / 201)
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200, array $meta = [])
    {
        self::json($data, $statusCode, $message, $meta);
    }

    /**
     * Helper Respon Error (HTTP 4xx / 5xx)
     */
    public static function error(string $message = 'Error', int $statusCode = 400, $errors = null, $devMsg = null)
    {
        if (ob_get_length()) {
            ob_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $payload = [
            'success'   => false,
            'code'      => $statusCode,
            'message'   => $message,
            'errors'    => $errors,
            'meta'      => [
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];

        // Tampilkan dev_msg hanya saat mode dev
        $core = Core::instance();
        if ($devMsg !== null && $core->get('dev_mode') === 'dev') {
            $payload['dev_msg'] = $devMsg;
        }

        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit();
    }
}

<?php

class Database
{
    private static $connections = [];
    private static $configs = [];

    /**
     * Dapatkan koneksi Dbconfig5 berdasarkan nama koneksi
     *
     * @param string|null $name Nama koneksi ('default', 'logistik', 'report', dll)
     * @return Dbconfig5
     */
    public static function connection(?string $name = 'default'): Dbconfig5
    {
        $name = strtolower($name ?: 'default');

        if (!isset(self::$connections[$name])) {
            $config = self::getConfig($name);
            $devMode = Env::get('APP_ENV') === 'dev' ? 'dev' : 'prod';

            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4;",
                $config['host'],
                $config['port'],
                $config['database']
            );

            self::$connections[$name] = new Dbconfig5(
                $dsn,
                $config['username'],
                $config['password'],
                null,
                $devMode
            );
        }

        return self::$connections[$name];
    }

    /**
     * Daftarkan konfigurasi koneksi kustom secara programatis
     */
    public static function register(string $name, array $config): void
    {
        self::$configs[strtolower($name)] = array_merge([
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'database' => '',
            'username' => 'root',
            'password' => ''
        ], $config);
    }

    /**
     * Ambil konfigurasi database dari .env
     */
    public static function getConfig(string $name): array
    {
        $name = strtolower($name);

        if (isset(self::$configs[$name])) {
            return self::$configs[$name];
        }

        if ($name === 'default' || $name === 'main') {
            return [
                'host'     => Env::get('DB_HOST', '127.0.0.1'),
                'port'     => Env::get('DB_PORT', 3306),
                'database' => Env::get('DB_NAME', 'test'),
                'username' => Env::get('DB_USER', 'root'),
                'password' => Env::get('DB_PASS', '')
            ];
        }

        $upper = strtoupper($name);
        return [
            'host'     => Env::get("DB_{$upper}_HOST", Env::get('DB_HOST', '127.0.0.1')),
            'port'     => Env::get("DB_{$upper}_PORT", Env::get('DB_PORT', 3306)),
            'database' => Env::get("DB_{$upper}_NAME", "test_{$name}"),
            'username' => Env::get("DB_{$upper}_USER", Env::get('DB_USER', 'root')),
            'password' => Env::get("DB_{$upper}_PASS", Env::get('DB_PASS', ''))
        ];
    }

    /**
     * Uji status koneksi database (apakah MySQL online dan credentials benar)
     */
    public static function testConnection(string $name = 'default'): array
    {
        $config = self::getConfig($name);
        $start = microtime(true);

        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4;",
                $config['host'],
                $config['port'],
                $config['database']
            );

            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2
            ]);

            $latency = round((microtime(true) - $start) * 1000, 2);
            $version = $pdo->query('SELECT VERSION()')->fetchColumn();

            return [
                'name'       => $name,
                'status'     => 'connected',
                'database'   => $config['database'],
                'host'       => $config['host'],
                'port'       => $config['port'],
                'latency_ms' => $latency,
                'version'    => $version,
                'error'      => null
            ];
        } catch (Exception $e) {
            return [
                'name'       => $name,
                'status'     => 'disconnected',
                'database'   => $config['database'],
                'host'       => $config['host'],
                'port'       => $config['port'],
                'latency_ms' => null,
                'version'    => null,
                'error'      => $e->getMessage()
            ];
        }
    }

    /**
     * Dapatkan daftar seluruh koneksi database yang terkonfigurasi di sistem
     */
    public static function listConfigured(): array
    {
        $list = ['default'];

        // Cari semua variabel DB_*_NAME di $_ENV / $_SERVER
        $allKeys = array_merge(array_keys($_ENV), array_keys($_SERVER));
        foreach ($allKeys as $key) {
            if (preg_match('/^DB_([A-Z0-9_]+)_NAME$/', $key, $matches)) {
                $alias = strtolower($matches[1]);
                if ($alias !== 'default' && $alias !== 'main' && !in_array($alias, $list, true)) {
                    $list[] = $alias;
                }
            }
        }

        return $list;
    }

    /**
     * Auto-rollback semua transaksi yang sedang aktif di seluruh pool koneksi
     */
    public static function rollbackAll(): void
    {
        foreach (self::$connections as $name => $conn) {
            if ($conn && method_exists($conn, 'inTransaction') && $conn->inTransaction()) {
                try {
                    $conn->close();
                } catch (Exception $e) {
                    // Ignore during shutdown
                }
            }
        }
    }
}

<?php

class Uploader
{
    private const DEFAULT_MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    private const MIME_MAP = [
        'image/jpeg'        => 'jpg',
        'image/png'         => 'png',
        'image/webp'        => 'webp',
        'image/gif'         => 'gif',
        'application/pdf'   => 'pdf',
        'application/zip'   => 'zip',
        'text/csv'          => 'csv',
        'text/plain'        => 'txt',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-excel' => 'xls'
    ];

    /**
     * Upload file dengan validasi keamanan tingkat tinggi (Magic Bytes Inspection)
     *
     * @param array $file Array dari $_FILES['nama_field']
     * @param string $subfolder Subfolder di dalam uploads/ (contoh: 'avatars', 'invoices')
     * @param array|null $allowedMimes Daftar MIME types yang diizinkan
     * @param int|null $maxSize Ukuran maksimal dalam bytes
     * @return array [ 'success' => bool, 'filename' => string, 'path' => string, 'url' => string, 'error' => ?string ]
     */
    public static function upload(
        array $file,
        string $subfolder = 'documents',
        ?array $allowedMimes = null,
        ?int $maxSize = null
    ): array {
        // 1. Cek error bawaan PHP upload
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Parameter file tidak valid.'];
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'error' => 'Tidak ada file yang dipilih untuk diunggah.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'error' => 'Ukuran file melebihi batas maksimal server.'];
            default:
                return ['success' => false, 'error' => 'Terjadi kesalahan sistem saat mengunggah file.'];
        }

        // 2. Cek ukuran file
        $limit = $maxSize ?? self::DEFAULT_MAX_SIZE;
        if ($file['size'] > $limit) {
            $maxMb = round($limit / 1024 / 1024, 1);
            return ['success' => false, 'error' => "Ukuran file terlalu besar. Maksimal {$maxMb} MB."];
        }

        // 3. Validasi REAL MIME TYPE melalui Fileinfo (Magic Bytes) - BUKAN percaya ekstensi klien!
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);

        $allowed = $allowedMimes ?? array_keys(self::MIME_MAP);
        if (!in_array($realMime, $allowed, true)) {
            return [
                'success' => false,
                'error'   => "Tipe file tidak diizinkan ({$realMime}). Hanya mendukung format: " . implode(', ', array_unique(array_intersect_key(self::MIME_MAP, array_flip($allowed))))
            ];
        }

        // 4. Tentukan ekstensi aman berdasarkan MIME type asli
        $ext = self::MIME_MAP[$realMime] ?? 'bin';

        // Cegah ekstensi script berbahaya apapun
        if (in_array(strtolower($ext), ['php', 'phtml', 'phar', 'exe', 'sh', 'bat', 'cmd'], true)) {
            return ['success' => false, 'error' => 'Ekstensi file dilarang demi keamanan.'];
        }

        // 5. Buat direktori tujuan aman
        $uploadBase = __DIR__ . '/../uploads';
        $targetDir = $uploadBase . '/' . trim($subfolder, '/');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        // 6. Generate nama file unik acak (Cryptographic Random)
        $newFilename = date('Ymd_His') . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
        $destination = $targetDir . '/' . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Gagal memindahkan file ke penyimpanan server.'];
        }

        $core = Core::instance();
        $baseUrl = rtrim(Env::get('APP_URL', $core->get('API_HOST') ?: '/'), '/') . '/';
        $relativeUrl = 'uploads/' . trim($subfolder, '/') . '/' . $newFilename;

        return [
            'success'       => true,
            'filename'      => $newFilename,
            'original_name' => htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'),
            'mime'          => $realMime,
            'size'          => $file['size'],
            'path'          => $destination,
            'url'           => $baseUrl . $relativeUrl,
            'error'         => null
        ];
    }
}

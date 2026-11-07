<?php

class Csv
{
    /**
     * Export data ke file CSV dengan streaming (Hemat memori untuk ratusan ribu data)
     *
     * @param string $filename Nama file download (contoh: 'laporan-penjualan.csv')
     * @param array $headers Header kolom (contoh: ['ID', 'Nama', 'Total'])
     * @param iterable $rows Baris data (array atau generator/cursor database)
     * @return void
     */
    public static function export(string $filename, array $headers, iterable $rows): void
    {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // Tulis UTF-8 BOM agar Microsoft Excel membaca karakter Indonesia / UTF-8 dengan benar
        fputs($out, "\xEF\xBB\xBF");

        // Tulis header
        fputcsv($out, $headers);

        // Tulis baris data secara streaming
        foreach ($rows as $row) {
            if (is_object($row)) {
                $row = (array)$row;
            }
            fputcsv($out, (array)$row);
        }

        fclose($out);
        exit();
    }
}

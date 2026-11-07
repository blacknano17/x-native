<?php

class TransactionApi extends mainApi
{
    /**
     * Logic Bisnis: Ambil data transaksi lintas database (bisa dipanggil dari Web Controller ATAU via HTTP API)
     */
    public function getSummaryData()
    {
        // 1. Query ke Database Utama
        // $orders = $this->dbc->fetchObject("SELECT * FROM orders LIMIT 10");

        // 2. Query ke Database Kedua (Logistik) via Multi-DB
        // $logistikDb = Database::connection('logistik');
        // $shipments = $logistikDb->fetchObject("SELECT * FROM pengiriman LIMIT 10");

        return [
            'total_sales'     => 'Rp 1.482.500.000',
            'active_orders'   => 128,
            'shipments_ready' => 45,
            'recent' => [
                [
                    'order_no'  => 'TRX-202611-0081',
                    'customer'  => 'PT Mega Distribusi Utama',
                    'db_source' => 'default (sima_core)',
                    'total'     => 'Rp 45.200.000',
                    'status'    => 'Selesai',
                    'date'      => 'Hari ini, 04:15'
                ],
                [
                    'order_no'  => 'SJ-LOG-99210',
                    'customer'  => 'Gudang DC Cikarang',
                    'db_source' => 'logistik (app_pengiriman)',
                    'total'     => '1.200 Koli',
                    'status'    => 'Dalam Pengiriman',
                    'date'      => 'Hari ini, 03:40'
                ]
            ]
        ];
    }

    /**
     * Endpoint HTTP REST API: GET /api/transactions/summary
     */
    public function apiSummary($core)
    {
        $data = $this->getSummaryData();
        $this->success($data, 'Data ringkasan transaksi berhasil diambil.');
    }
}

<?php

class DashboardController extends webController
{
    /**
     * Halaman Dashboard Utama
     * GET /dashboard
     */
    public function index($core)
    {
        $user = $this->requireAuth();

        // 1. Uji koneksi multi-database secara live
        $dbStatus = [
            'default'  => Database::testConnection('default'),
            'logistik' => Database::testConnection('logistik'),
            'report'   => Database::testConnection('report')
        ];

        // 2. Ambil data logic bisnis dari sys-api/TransactionApi (Pola Arsitektur SIMA)
        $trxApi = new TransactionApi();
        $summaryData = $trxApi->getSummaryData();

        $metrics = [
            'total_sales'     => $summaryData['total_sales'],
            'active_orders'   => $summaryData['active_orders'],
            'shipments_ready' => $summaryData['shipments_ready'],
            'connected_dbs'   => count(array_filter($dbStatus, fn($s) => $s['status'] === 'connected')) . ' / 3 Aktif'
        ];

        $recentTransactions = $summaryData['recent'];

        $this->render('pages/dashboard.php', [
            'pageTitle'          => 'Enterprise Dashboard',
            'user'               => $user,
            'metrics'            => $metrics,
            'dbStatus'           => $dbStatus,
            'recentTransactions' => $recentTransactions
        ], 'app');
    }

    /**
     * Halaman Khusus Playground & Tester Multi-Database
     * GET /databases
     */
    public function databases($core)
    {
        $user = $this->requireAuth();

        $configuredList = Database::listConfigured();
        $details = [];

        foreach ($configuredList as $name) {
            $details[$name] = Database::testConnection($name);
        }

        $this->render('pages/databases.php', [
            'pageTitle' => 'Multi-Database Manager',
            'user'      => $user,
            'details'   => $details
        ], 'app');
    }
}

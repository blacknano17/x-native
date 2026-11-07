<?php

class CustomerController extends webController
{
    /**
     * Halaman List Customer
     * GET /customer
     */
    public function index($core)
    {
        $user = $this->requireAuth();

        // Panggil logic bisnis dari sys-api/CustomerApi
        $api = new CustomerApi();
        $items = $api->getList();

        $this->render('pages/customer/index.php', [
            'pageTitle' => 'Manajemen Customer',
            'user'      => $user,
            'items'     => $items
        ], 'app');
    }

    /**
     * Simpan Customer Baru
     * POST /customer/create
     */
    public function store($core)
    {
        $this->requireAuth();

        $input = $this->validate([
            'name' => 'required|string|min:3'
        ]);

        $api = new CustomerApi();
        $api->createItem($input);

        $this->redirect('/customer', 'Customer berhasil ditambahkan!', 'success');
    }
}
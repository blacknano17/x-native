<?php

class CustomerApi extends mainApi
{
    /**
     * Logic: Ambil daftar data Customer
     */
    public function getList()
    {
        // Query Database Utama:
        // return $this->dbc->fetchObject("SELECT * FROM customers ORDER BY id DESC LIMIT 50");

        // Mock data starter:
        return [
            ['id' => 1, 'name' => 'Contoh Customer A', 'status' => 'Aktif', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'name' => 'Contoh Customer B', 'status' => 'Pending', 'created_at' => date('Y-m-d H:i:s')]
        ];
    }

    /**
     * Logic: Simpan data baru Customer
     */
    public function createItem(array $data)
    {
        // $this->dbc->beginTransaction();
        // $id = $this->dbc->insert("INSERT INTO customers (name, status) VALUES (?, ?)", [$data['name'], $data['status']]);
        // $this->dbc->commit();
        return ['id' => rand(10, 99), 'name' => $data['name']];
    }

    /**
     * Endpoint API JSON: GET /api/customer
     */
    public function apiList($core)
    {
        $data = $this->getList();
        $this->success($data, 'Data customer berhasil diambil.');
    }
}
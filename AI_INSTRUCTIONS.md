# AI Instructions & Code Recipes for X-Native

This document provides system prompts, code conventions, and copy-pasteable patterns for **AI Coding Assistants** (Antigravity, Cursor, Claude, ChatGPT, GitHub Copilot) working in the **X-Native** codebase.

---

## 🤖 System Prompt Directive for AI Agents

```text
You are an expert fullstack engineer specializing in high-performance enterprise PHP (PHP 8.4+).
You are working on the X-Native codebase.
X-Native uses Fat-Free Core (SIMA engine), multi-database connection pooling via PDO, Tailwind CSS, Alpine.js, and strict server-side CSRF validation.
Follow the existing patterns: Controllers extend webController, Logic services extend mainApi, all POST forms must include <?= Csrf::field() ?>, and routes must be declared in route/route.ini.
```

---

## 📋 Code Generation Recipes

### Recipe 1: Standard Web Controller (`sys-controller/`)

```php
<?php

class ExampleController extends webController
{
    private ExampleApi $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = new ExampleApi();
    }

    public function index($core)
    {
        // 1. Enforce authentication and role
        $user = $this->requireAuth();
        $this->requireRole('superadmin', 'manager');

        // 2. Fetch data via API service layer
        $items = $this->api->getAll();

        // 3. Render page inside master layout 'app'
        $this->render('pages/example/index.php', [
            'title' => 'Daftar Example',
            'user'  => $user,
            'items' => $items
        ], 'app');
    }

    public function store($core)
    {
        $this->requireAuth();

        $nama = Request::post('nama');
        $kode = Request::post('kode');

        if (empty($nama) || empty($kode)) {
            $this->redirect('/example', 'Nama dan Kode wajib diisi!', 'warning');
            return;
        }

        $this->api->create(['nama' => $nama, 'kode' => $kode]);

        $this->redirect('/example', 'Data baru berhasil disimpan.', 'success');
    }
}
```

---

### Recipe 2: Business Logic / Multi-Database Service (`sys-api/`)

```php
<?php

class ExampleApi extends mainApi
{
    /**
     * Query data from primary database
     */
    public function getAll(): array
    {
        return $this->dbc->fetchObject(
            "SELECT * FROM examples ORDER BY id DESC LIMIT 50"
        ) ?: [];
    }

    /**
     * Cross-Database Query Example (Primary DB + Logistik DB)
     */
    public function getWithLogisticsData(int $id): array
    {
        // Primary DB query
        $example = $this->dbc->fetchRow(
            "SELECT * FROM examples WHERE id = ?",
            [$id]
        );

        if (!$example) {
            return [];
        }

        // Secondary DB (Logistik) query via connection pool
        $logistikDb = Database::connection('logistik');
        $shipping = $logistikDb->fetchRow(
            "SELECT * FROM pengiriman WHERE ref_id = ?",
            [$id]
        );

        $example['shipping_status'] = $shipping['status'] ?? 'Belum Diproses';
        return $example;
    }

    /**
     * Insert with prepared statement
     */
    public function create(array $data): bool
    {
        return $this->dbc->exec(
            "INSERT INTO examples (nama, kode, created_at) VALUES (?, ?, NOW())",
            [$data['nama'], $data['kode']]
        );
    }
}
```

---

### Recipe 3: Reactive Blade/PHP View with Alpine.js & Tailwind (`sys-view/pages/`)

```html
<div x-data="{ openModal: false }" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Example</h1>
            <p class="text-sm text-slate-500">Kelola master data dengan multi-database support.</p>
        </div>
        <button @click="openModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
            + Tambah Data
        </button>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nama</th>
                    <th class="p-4">Kode</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400">Belum ada data tersedia.</td>
                </tr>
                <?php else: foreach ($items as $item): ?>
                <tr class="hover:bg-slate-50">
                    <td class="p-4 font-mono text-slate-500">#<?= htmlspecialchars($item['id']) ?></td>
                    <td class="p-4 font-semibold text-slate-800"><?= htmlspecialchars($item['nama']) ?></td>
                    <td class="p-4 text-slate-600"><?= htmlspecialchars($item['kode']) ?></td>
                    <td class="p-4 text-right">
                        <form action="/example/delete/<?= $item['id'] ?>" method="POST" onsubmit="return confirm('Hapus data ini?');" class="inline">
                            <?= Csrf::field() ?>
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Alpine.js Modal Form -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Data Baru</h3>
            <form action="/example/store" method="POST" class="space-y-4">
                <!-- MANDATORY CSRF FIELD -->
                <?= Csrf::field() ?>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama</label>
                    <input type="text" name="nama" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Kode</label>
                    <input type="text" name="kode" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-slate-600 text-sm font-medium hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

---

### Recipe 4: Route Declaration (`route/route.ini`)

Always register new controller methods in `route/route.ini`:

```ini
[routes]
; Example Module
GET /example = ExampleController->index
POST /example/store = ExampleController->store
POST /example/delete/@id = ExampleController->delete
```

---

## 🚫 Blacklist / Anti-Patterns for AI Agents

1. **NEVER forget `<?= Csrf::field() ?>` inside any HTML `<form method="POST">`**. Omission results in HTTP 419 error.
2. **NEVER use raw `$_SESSION`**. Always use `Session::get($key)`, `Session::set($key, $val)`, and `Session::destroy()`.
3. **NEVER name custom View classes `View`**. Fat-Free Core defines `class View extends Prefab`. The X-Native master layout class is named `AppView`.
4. **NEVER concatenate raw SQL strings**. Always use PDO parameter binding with `?` or `$this->dbc->fetchObject($sql, $params)`.
5. **NEVER write database connection credentials in code**. Always utilize `.env` and `Database::connection($name)`.

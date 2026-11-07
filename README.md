# 🚀 X-Native — Enterprise Fullstack Web & Dashboard Framework

**X-Native** adalah framework fullstack web dan dashboard enterprise berbasis engine **SIMA (Fat-Free Core)** yang telah dimodernisasi penuh untuk **PHP 8.4+**.

Jika **M-Native** didedikasikan untuk headless REST API berkecepatan tinggi, **X-Native** dirancang khusus untuk **aplikasi web fullstack, portal admin, dashboard analitik, dan sistem ERP enterprise**. Framework ini memadukan kesederhanaan *server-side rendering* modern (Tailwind CSS + Alpine.js), keamanan ketat (Session Hardening + CSRF Shield), **koneksi multi-database simultan dalam satu transaksi**, dan **alat bantu CLI otomatis**.

---

## 📑 Daftar Isi
1. [Fitur Unggulan Enterprise](#-fitur-unggulan-enterprise)
2. [Persyaratan Sistem & Instalasi Cepat](#-persyaratan-sistem--instalasi-cepat)
3. [Struktur Direktori & Arsitektur](#-struktur-direktori--arsitektur)
4. [Koneksi Multi-Database Simultan](#-koneksi-multi-database-simultan)
5. [CLI Generator (Code Scaffolding)](#-cli-generator-code-scaffolding)
6. [Autentikasi & Role-Based Access Control (RBAC)](#-autentikasi--role-based-access-control-rbac)
7. [Keamanan Berlapis (CSRF, Session, File Upload)](#-keamanan-berlapis-fullstack-security)
8. [Komponen UI (Tailwind CSS, Alpine.js, Master Layout)](#-komponen-ui-modern)
9. [Streaming CSV Exporter (Anti Memory-Exhaustion)](#-streaming-csv-exporter)
10. [Tutorial: Membuat Modul Baru dari Nol](#-tutorial-membuat-modul-baru-dari-nol)

---

## ⚡ Fitur Unggulan Enterprise

| Fitur | Keterangan |
|---|---|
| **Multi-Database Pool** | Terhubung ke **banyak database MySQL sekaligus** dalam 1 controller / request (`$this->db()`, `$this->db('logistik')`, `$this->db('report')`). |
| **Ultra Ringan & Cepat** | Footprint memori hanya **~1.5 MB RAM**, waktu respon **~2-5 ms** (jauh lebih hemat dibanding framework heavy seperti Laravel). |
| **PHP 8.4+ Ready** | Bebas *deprecation warning*, atribut strict typing, dukungan MySQL 8.x. |
| **Fullstack Reactive UI** | UI responsif modern dengan Tailwind CSS + Alpine.js tanpa perlu compile npm / webpack / node.js. |
| **Automated CSRF Shield** | Verifikasi CSRF otomatis pada semua request mutasi (`POST`, `PUT`, `DELETE`). Penolakan instan dengan HTTP 419. |
| **Hardened Session** | Sesi terenkripsi dengan proteksi `HttpOnly`, `SameSite=Lax`, `Secure`, dan anti-session fixation. |
| **CLI Scaffolding Tool** | Perintah `php cli make:module <Nama>` membuat API, Controller, View, dan Route dalam 1 detik. |
| **Secure File Uploader** | Pemeriksaan tipe file asli berbasis *Magic Bytes* (bukan hanya ekstensi) + isolasi eksekusi `.htaccess`. |
| **Streaming CSV Export** | Export ratusan ribu data langsung ke browser tanpa memakan kuota RAM server. |

---

## 🛠️ Persyaratan Sistem & Instalasi Cepat

### Persyaratan:
- **PHP 8.2, 8.3, atau 8.4+**
- Ekstensi PHP: `pdo_mysql`, `curl`, `fileinfo`, `mbstring`, `openssl`
- Web Server: Apache (dengan `mod_rewrite`) atau Nginx
- MySQL 8.0+ atau MariaDB 10.4+

### Langkah Instalasi (3 Langkah):

```bash
# 1. Masuk ke folder proyek
cd C:\laragon\www\groblive\x-native

# 2. Salin environment konfigurasi
cp .env.example .env

# 3. Jalankan migrasi starter database (Opsional jika ingin menggunakan database MySQL)
php cli db:migrate
```

Buka aplikasi di peramban (browser):
- **Halaman Login**: `http://localhost/groblive/x-native/login`
- **Dashboard Enterprise**: `http://localhost/groblive/x-native/dashboard`
- **Multi-DB Playground**: `http://localhost/groblive/x-native/databases`

> **Catatan Akun Demo**: Framework ini memiliki fitur *Hybrid Auth*. Jika database belum diisi, Anda langsung dapat login dengan akun demo bawaan:
> - Superadmin: `admin` / `password123`
> - Logistik Manager: `manager` / `manager123`

---

## 📁 Struktur Direktori & Arsitektur

X-Native mengusung arsitektur modular yang memisahkan **Web Controller**, **Business Logic (sys-api)**, dan **View**:

```text
x-native/
├── .env                            # Kredensial rahasia (Multi-DB, APP_KEY)
├── .env.example                    # Template environment tim
├── cli                             # Enterprise CLI Scaffolding Tool
├── schema.sql                      # DDL tabel starter (users, audit_logs)
├── index.php                       # Entry point aplikasi & security bootstrapper
├── README.md                       # Dokumentasi Panduan Pengembang Manusia
├── llms.txt                        # Konteks ringkas untuk model AI
├── AI_INSTRUCTIONS.md              # SOP dan aturan sintaks untuk AI
├── config/
│   └── config.ini                  # Autoload engine & konfigurasi framework
├── lib/
│   ├── AppView.php                 # Master layout renderer (app & auth)
│   ├── Csrf.php                    # Token CSRF generator & verifier
│   ├── Csv.php                     # Streaming CSV/Excel exporter
│   ├── Database.php                # Multi-DB Connection Pool Manager
│   ├── Dbconfig5.php               # PDO Prepared Statements atomik
│   ├── Env.php                     # Parser .env native tanpa composer
│   ├── Flash.php                   # Flash messages & toast alert
│   ├── Logger.php                  # Structured logging & auto-masking PII
│   ├── Session.php                 # Hardened secure session manager
│   ├── Uploader.php                # Secure file uploader (MIME magic bytes)
│   └── Validator.php               # Request input validation helper
├── middleware/
│   ├── AuthWebMiddleware.php       # Guard halaman login & role check
│   ├── CsrfMiddleware.php          # Verifikator otomatis form CSRF (HTTP 419)
│   └── RateLimiter.php             # Anti brute-force login
├── route/
│   └── route.ini                   # Registrasi route URL web
├── storage/
│   ├── cache/                      # Rate limit cache
│   ├── logs/                       # File log aplikasi
│   └── sessions/                   # Direktori sesi aman
├── sys-api/                        # Business Logic Core & Multi-DB Queries
│   ├── mainApi.php                 # Base class logic & database wrapper
│   ├── TransactionApi.php          # Contoh logic transaksi lintas DB
│   └── CustomerApi.php             # Contoh CRUD data customer
├── sys-controller/                 # Controller Web UI
│   ├── webController.php           # Base Controller ($this->db(), $this->render())
│   ├── AuthController.php          # Login, Logout, Session handler
│   ├── DashboardController.php     # Controller dashboard utama & multi-db status
│   └── CustomerController.php      # Controller web customer
├── sys-view/                       # Template & Tampilan UI
│   ├── layouts/
│   │   ├── app.php                 # Master layout (Sidebar + Header + Modal)
│   │   └── auth.php                # Layout khusus halaman auth (Login)
│   └── pages/
│       ├── login.php               # Halaman Login modern
│       ├── dashboard.php           # Halaman Dashboard KPI & grafik
│       ├── databases.php           # Halaman Multi-DB diagnostic playground
│       └── customer/index.php      # Halaman CRUD data customer
└── uploads/
    └── .htaccess                   # Proteksi anti-eksekusi skrip PHP
```

---

## 🗄️ Koneksi Multi-Database Simultan

X-Native memiliki Connection Pool bawaan yang mampu menghubungkan aplikasi ke banyak database MySQL secara bersamaan dalam 1 siklus request.

### 1. Konfigurasi di `.env`
```ini
# Database Utama (Core / SIMA)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=sima
DB_USER=root
DB_PASS=

# Database Kedua: Logistik & Distribusi
DB_LOGISTIK_HOST=127.0.0.1
DB_LOGISTIK_PORT=3306
DB_LOGISTIK_NAME=app_logistik
DB_LOGISTIK_USER=root
DB_LOGISTIK_PASS=

# Database Ketiga: Reporting / Data Warehouse
DB_REPORT_HOST=127.0.0.1
DB_REPORT_PORT=3306
DB_REPORT_NAME=app_reporting
DB_REPORT_USER=root
DB_REPORT_PASS=
```

### 2. Penggunaan di Controller atau API
```php
class OrderController extends webController
{
    public function syncShipment($core)
    {
        // Akses database utama (default sima)
        $order = $this->db()->fetchRow(
            "SELECT * FROM orders WHERE id = ?", 
            [105]
        );

        // Akses database kedua (logistik)
        $shipment = $this->db('logistik')->fetchRow(
            "SELECT * FROM pengiriman WHERE no_pesanan = ?", 
            [$order['order_no']]
        );

        // Akses database ketiga (reporting)
        $this->db('report')->exec(
            "INSERT INTO audit_sync (order_no, sync_at) VALUES (?, NOW())",
            [$order['order_no']]
        );

        $this->redirect('/dashboard', 'Data berhasil disinkronisasi lintas 3 database!', 'success');
    }
}
```

### 3. Menguji Koneksi Seluruh Database
Cukup jalankan perintah CLI:
```bash
php cli db:test
```
Output akan menampilkan status koneksi dan latensi (ms) dari masing-masing database.

---

## ⚡ CLI Generator (Code Scaffolding)

X-Native menyertakan CLI native (tanpa perlu Node.js atau composer) untuk mempercepat pembuatan modul enterprise:

```bash
php cli make:module <NamaModul>
```

**Contoh:**
```bash
php cli make:module Supplier
```
Dalam 1 detik, CLI otomatis membuat:
1. `sys-api/SupplierApi.php` &rarr; Kelas logika query database.
2. `sys-controller/SupplierController.php` &rarr; Controller web terproteksi sesi & CSRF.
3. `sys-view/pages/supplier/index.php` &rarr; Tampilan antarmuka tabel data Tailwind + modal form.
4. Mendaftarkan route otomatis di `route/route.ini`:
   - `GET /supplier`
   - `POST /supplier/store`
   - `POST /supplier/delete/@id`

Daftar perintah CLI lainnya:
- `php cli list` : Melihat semua daftar perintah.
- `php cli db:test` : Tes koneksi seluruh database di `.env`.
- `php cli db:migrate` : Menjalankan migrasi database starter `schema.sql`.

---

## 🔐 Autentikasi & Role-Based Access Control (RBAC)

### 1. Proteksi Halaman pada Controller
Setiap controller web yang membutuhkan autentikasi cukup memanggil method helper berikut:

```php
class InventoryController extends webController
{
    public function index($core)
    {
        // 1. Wajib login (jika belum, redirect otomatis ke /login)
        $user = $this->requireAuth();

        // 2. Proteksi Role (Hanya superadmin dan gudang yang boleh masuk)
        $this->requireRole('superadmin', 'gudang');

        // Render halaman
        $this->render('pages/inventory.php', [
            'user' => $user,
            'title' => 'Manajemen Gudang'
        ]);
    }
}
```

### 2. Hashing Password Standar Industri
Sistem autentikasi menggunakan `password_hash($password, PASSWORD_BCRYPT)` dan verifikasi via `password_verify()`.

---

## 🛡️ Keamanan Berlapis (Fullstack Security)

### 1. Proteksi CSRF Otomatis
Setiap form HTML bermetode `POST` wajib menyertakan token CSRF:
```html
<form action="/customer/store" method="POST">
    <!-- Helper CSRF Token -->
    <?= Csrf::field() ?>

    <input type="text" name="nama" required>
    <button type="submit">Simpan</button>
</form>
```
Jika request dikirim tanpa token yang valid, sistem melalui `CsrfMiddleware` langsung membatalkan proses dengan **HTTP 419 Page Expired**.

Untuk AJAX / Alpine.js / Fetch:
```javascript
fetch('/api/action', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '<?= Csrf::token() ?>'
    },
    body: JSON.stringify(payload)
});
```

### 2. Hardened Session
Manajer sesi `lib/Session.php` menerapkan standar keamanan OWASP:
- Cookie berflag `HttpOnly` (anti XSS pencurian cookie).
- Flag `SameSite=Lax` (anti CSRF lintas domain).
- Cookie `Secure` otomatis aktif saat berjalan di HTTPS.
- `Session::regenerate()` dipanggil setiap pergantian hak akses / login.

### 3. Upload Berkas Aman (`Uploader.php`)
Fitur upload memeriksa **MIME type asli (Magic Bytes)** menggunakan PHP Fileinfo, bukan hanya memeriksa ekstensi file:
```php
$uploader = new Uploader('uploads/documents', ['pdf', 'png', 'jpg'], 5 * 1024 * 1024);
$result = $uploader->upload($_FILES['dokumen']);

if ($result['success']) {
    $filePath = $result['path'];
} else {
    $errorMsg = $result['error'];
}
```
Folder `uploads/` dilindungi oleh `.htaccess` yang secara mutlak menolak eksekusi file script `.php`, `.phtml`, atau `.phar`.

---

## 🎨 Komponen UI Modern

X-Native menggunakan layout master `AppView` yang bersih dan modern:
- **Tailwind CSS** (via CDN responsif berkecepatan tinggi)
- **Alpine.js** (untuk interaktivitas ringan seperti Modal, Dropdown, Toggle Sidebar tanpa dependensi bundle)
- **Font Lucide Icons / Heroicons**

### Merender Halaman di Controller:
```php
// Merender dengan Master Layout Dashboard (Sidebar + Header + Footer)
$this->render('pages/customer/index.php', [
    'title' => 'Daftar Pelanggan',
    'customers' => $customerList
], 'app');

// Merender halaman khusus tanpa sidebar (contoh: Login / Register)
$this->render('pages/login.php', [
    'title' => 'Masuk ke Aplikasi'
], 'auth');
```

### Flash Messages & Notifikasi Toast
Kirim pesan notifikasi instan yang langsung muncul di layout dashboard:
```php
$this->redirect('/customer', 'Data pelanggan berhasil diperbarui!', 'success');
// Tipe tersedia: 'success', 'error', 'warning', 'info'
```

---

## 📊 Streaming CSV Exporter

Mengekspor data berukuran besar sering kali menyebabkan error `Fatal error: Allowed memory size of X bytes exhausted`. X-Native menyediakan library `Csv` untuk melakukan streaming output langsung ke output buffer HTTP:

```php
public function exportData($core)
{
    $headers = ['ID', 'Nama Customer', 'Kota', 'Saldo Piutang', 'Dibuat Pada'];
    $rows = $this->db()->fetchObject("SELECT id, nama, kota, saldo, created_at FROM customers");

    Csv::export('laporan_customer_' . date('Ymd') . '.csv', $headers, $rows);
    exit;
}
```

---

## 💡 Tutorial: Membuat Modul Baru dari Nol

Mari buat modul **"Produk"** dari awal hingga selesai:

### 1. Jalankan CLI Scaffolding
```bash
php cli make:module Product
```

### 2. Sesuaikan Query di `sys-api/ProductApi.php`
```php
<?php
class ProductApi extends mainApi
{
    public function getAllProducts()
    {
        return $this->dbc->fetchObject("SELECT * FROM products ORDER BY id DESC LIMIT 50");
    }

    public function createProduct($data)
    {
        return $this->dbc->exec(
            "INSERT INTO products (nama_produk, sku, harga, stok, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$data['nama_produk'], $data['sku'], $data['harga'], $data['stok']]
        );
    }
}
```

### 3. Tangani Request di `sys-controller/ProductController.php`
```php
<?php
class ProductController extends webController
{
    private ProductApi $productApi;

    public function __construct()
    {
        parent::__construct();
        $this->productApi = new ProductApi();
    }

    public function index($core)
    {
        $user = $this->requireAuth();
        $products = $this->productApi->getAllProducts();

        $this->render('pages/product/index.php', [
            'title'    => 'Katalog Produk',
            'user'     => $user,
            'products' => $products
        ]);
    }

    public function store($core)
    {
        $this->requireAuth();

        $nama  = Request::post('nama_produk');
        $sku   = Request::post('sku');
        $harga = Request::post('harga');
        $stok  = Request::post('stok');

        $this->productApi->createProduct(compact('nama_produk', 'sku', 'harga', 'stok'));
        $this->redirect('/product', 'Produk berhasil ditambahkan!', 'success');
    }
}
```

### 4. Buka di Browser
Akses `http://localhost/groblive/x-native/product` — Modul Anda sudah siap digunakan lengkap dengan sistem autentikasi, navigasi, form CSRF, dan tabel data responsif!

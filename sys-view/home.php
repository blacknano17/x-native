<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Framework-X') ?></title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.2);
            --accent: #10b981;
            --border-color: #334155;
            --code-bg: #0b1120;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            max-width: 860px;
            width: 100%;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--accent);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-bottom: 12px;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.lead {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.2s, border-color 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .card h3 {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .card p {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .card-code {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .card-code h3 {
            font-size: 1rem;
            color: var(--text-main);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        pre {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 14px;
            overflow-x: auto;
            color: #38bdf8;
            font-size: 0.875rem;
            font-family: "Courier New", Courier, monospace;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #0f172a;
        }

        .btn-primary:hover {
            background-color: #7dd3fc;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .btn-secondary {
            background-color: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
        }

        footer {
            margin-top: 40px;
            font-size: 0.8125rem;
            color: var(--text-muted);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge-status">
            <span class="dot"></span> Framework-X Siap Digunakan (Tanpa Error)
        </div>

        <h1>Framework-X</h1>
        <p class="lead"><?= htmlspecialchars($description ?? '') ?></p>

        <div class="grid">
            <div class="card">
                <h3>⚡ Super Ringan</h3>
                <p>Seluruh modul berat, helper lama, dan puluhan controller bisnis telah dibersihkan. Hanya tersisa core engine esensial.</p>
            </div>
            <div class="card">
                <h3>🔌 Siap Teknologi Baru</h3>
                <p>Mendukung Composer autoloader, template engine baru (Vue/React/Tailwind), atau integrasi microservices.</p>
            </div>
            <div class="card">
                <h3>🚀 Dedicated REST API</h3>
                <p>Dilengkapi base class <code>mainApi</code> dengan standar <code>succes()</code>, <code>error()</code>, serta transaksi atomik database.</p>
            </div>
        </div>

        <div class="card-code">
            <h3>Contoh Pengujian API Langsung</h3>
            <pre>GET <?= $base_url ?>api/ping
GET <?= $base_url ?>api/hello/Dunia</pre>
            <div style="margin-top: 16px;" class="btn-group">
                <a href="<?= $base_url ?>api/ping" target="_blank" class="btn btn-primary">Uji /api/ping (JSON) &rarr;</a>
                <a href="<?= $base_url ?>api/hello/Developer" target="_blank" class="btn btn-secondary">Uji /api/hello/:name &rarr;</a>
            </div>
        </div>

        <footer>
            Framework-X &bull; PHP <?= $php_version ?> &bull; Powered by SIMA Core Engine
        </footer>
    </div>
</body>
</html>

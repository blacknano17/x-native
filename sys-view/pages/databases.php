<div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white tracking-tight">Multi-Database Manager</h1>
        <p class="text-xs text-slate-400 mt-1">Konfigurasi & Diagnostic Pool Koneksi Database Simultan pada X-Native.</p>
    </div>

    <!-- Diagnostic Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php foreach ($details as $alias => $info): ?>
            <?php 
                $isOk = ($info['status'] === 'connected');
                $border = $isOk ? 'border-emerald-500/30 bg-emerald-950/20' : 'border-amber-500/30 bg-amber-950/20';
                $badge = $isOk ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30';
            ?>
            <div class="p-6 rounded-2xl border <?= $border ?> flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold uppercase bg-slate-900 border border-slate-700 text-white">
                            Alias: <?= htmlspecialchars($alias) ?>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?= $badge ?>">
                            <?= strtoupper($info['status']) ?>
                        </span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between border-b border-slate-800/60 pb-1.5">
                            <span class="text-slate-400">Database Name:</span>
                            <span class="font-mono text-white font-semibold"><?= htmlspecialchars($info['database']) ?></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800/60 pb-1.5">
                            <span class="text-slate-400">Host Server:</span>
                            <span class="font-mono text-slate-200"><?= htmlspecialchars($info['host']) ?>:<?= $info['port'] ?></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800/60 pb-1.5">
                            <span class="text-slate-400">Response Latency:</span>
                            <span class="font-mono <?= $isOk ? 'text-emerald-400' : 'text-slate-500' ?>"><?= $info['latency_ms'] !== null ? $info['latency_ms'] . ' ms' : '-' ?></span>
                        </div>
                        <div class="flex justify-between pt-1">
                            <span class="text-slate-400">Server Version:</span>
                            <span class="font-mono text-slate-300 text-[11px] truncate"><?= htmlspecialchars($info['version'] ?? 'Offline / Belum Dibuat') ?></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/60 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Method pemanggilan:</span>
                    <code class="px-2 py-0.5 rounded bg-slate-900 text-sky-400 font-mono">$this->db('<?= htmlspecialchars($alias) ?>')</code>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Code Tutorial Section -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800">
        <h3 class="text-sm font-bold text-white mb-2">💡 Cara Menggunakan Fitur Multi-Database di Controller Anda</h3>
        <p class="text-xs text-slate-400 mb-4">Anda bisa melakukan transaksi atomik lintas database secara simultan dalam 1 controller:</p>

        <pre class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 text-xs font-mono text-sky-300 overflow-x-auto leading-relaxed">// 1. Query Database Utama (Default)
$orders = $this->db()->fetchObject("SELECT * FROM orders WHERE status = 'paid'");

// 2. Query Database Logistik / Pengiriman
$couriers = $this->db('logistik')->fetchObject("SELECT * FROM kurir WHERE aktif = 1");

// 3. Query Database Reporting / Analytic
$summary = $this->db('report')->fetchObject("SELECT SUM(omset) as total FROM sales_daily");

// 4. Transaksi Atomik Terisolasi
$this->db('logistik')->beginTransaction();
try {
    $this->db('logistik')->insert("INSERT INTO surat_jalan (...) VALUES (...)");
    $this->db('logistik')->commit();
} catch (Exception $e) {
    $this->db('logistik')->close(); // Rollback otomatis
}</pre>
    </div>

</div>

<div x-data="{ modalOpen: false, search: '' }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Enterprise Overview</h1>
            <p class="text-xs text-slate-400 mt-1"><?= htmlspecialchars($user['company'] ?? 'PT Sinar Maju') ?> &bull; Role: <span class="text-sky-400 uppercase font-semibold"><?= htmlspecialchars($user['role'] ?? '') ?></span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= $baseUrl ?>databases" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:border-sky-500 text-xs font-semibold text-slate-300 transition flex items-center gap-2">
                <span>🗄️</span> Kelola Database
            </a>
            <button @click="modalOpen = true" class="px-4 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-xs font-semibold text-white shadow-lg shadow-sky-500/25 transition">
                + Entri Transaksi Baru
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1 -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-3">
                <span>Omset Bulan Berjalan</span>
                <span class="text-emerald-400 font-semibold bg-emerald-500/10 px-2 py-0.5 rounded-md text-[10px]">+14.2%</span>
            </div>
            <div class="text-xl font-bold text-white"><?= $metrics['total_sales'] ?></div>
            <div class="text-[11px] text-slate-500 mt-2">Sumber: Database Utama (sima_core)</div>
        </div>

        <!-- Card 2 -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-3">
                <span>Pesanan Menunggu Kirim</span>
                <span class="text-sky-400 font-semibold bg-sky-500/10 px-2 py-0.5 rounded-md text-[10px]">Normal</span>
            </div>
            <div class="text-xl font-bold text-white"><?= $metrics['active_orders'] ?> Pesanan</div>
            <div class="text-[11px] text-slate-500 mt-2">Prioritas pengiriman hari ini</div>
        </div>

        <!-- Card 3 -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-3">
                <span>Surat Jalan Logistik</span>
                <span class="text-indigo-400 font-semibold bg-indigo-500/10 px-2 py-0.5 rounded-md text-[10px]">Realtime</span>
            </div>
            <div class="text-xl font-bold text-white"><?= $metrics['shipments_ready'] ?> Koli</div>
            <div class="text-[11px] text-slate-500 mt-2">Sumber: Database Logistik (app_pengiriman)</div>
        </div>

        <!-- Card 4 (Multi-DB Status) -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-3">
                <span>Multi-DB Connection Pool</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <div class="text-xl font-bold text-emerald-400"><?= $metrics['connected_dbs'] ?></div>
            <div class="text-[11px] text-slate-500 mt-2">Koneksi simultan aktif tanpa delay</div>
        </div>
    </div>

    <!-- Live Multi-Database Health Widget -->
    <div class="mb-8 p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>🗄️</span> Status Koneksi Multi-Database (Simultan)
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">X-Native mendukung query ke lebih dari 1 database sekaligus dalam 1 request yang sama.</p>
            </div>
            <a href="<?= $baseUrl ?>databases" class="text-xs text-sky-400 hover:underline font-medium">Lihat Detail Playground &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($dbStatus as $alias => $status): ?>
                <?php 
                    $isOk = ($status['status'] === 'connected');
                    $border = $isOk ? 'border-emerald-500/30 bg-emerald-950/20' : 'border-amber-500/30 bg-amber-950/20';
                    $dot = $isOk ? 'bg-emerald-400' : 'bg-amber-400';
                ?>
                <div class="p-4 rounded-xl border <?= $border ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-mono font-bold uppercase text-white">DB: <?= htmlspecialchars($alias) ?></span>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold <?= $isOk ? 'text-emerald-400' : 'text-amber-400' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $dot ?>"></span>
                            <?= strtoupper($status['status']) ?>
                        </span>
                    </div>
                    <div class="text-xs text-slate-300 font-mono truncate">Database: <strong><?= htmlspecialchars($status['database']) ?></strong></div>
                    <div class="text-[11px] text-slate-400 mt-1 flex justify-between">
                        <span>Host: <?= htmlspecialchars($status['host']) ?></span>
                        <span><?= $status['latency_ms'] !== null ? $status['latency_ms'] . ' ms' : 'N/A' ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Operasional Data Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800/80 overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-white">Aktivitas Transaksi Lintas Database</h3>
                <p class="text-xs text-slate-400 mt-0.5">Menampilkan penggabungan data langsung dari multi-database</p>
            </div>
            <div class="w-full sm:w-64">
                <input type="text" x-model="search" placeholder="Cari transaksi..."
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">No. Referensi</th>
                        <th class="px-6 py-3.5">Klien / Entitas</th>
                        <th class="px-6 py-3.5">Sumber Database</th>
                        <th class="px-6 py-3.5">Nilai / Volume</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    <?php foreach ($recentTransactions as $trx): ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono font-medium text-white"><?= htmlspecialchars($trx['order_no']) ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-200"><?= htmlspecialchars($trx['customer']) ?></td>
                            <td class="px-6 py-4 font-mono text-sky-400"><?= htmlspecialchars($trx['db_source']) ?></td>
                            <td class="px-6 py-4 font-bold text-white"><?= htmlspecialchars($trx['total']) ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                    $badge = match($trx['status']) {
                                        'Selesai', 'Tersinkron' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'Dalam Pengiriman'     => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
                                        default                 => 'bg-amber-500/10 text-amber-400 border-amber-500/20'
                                    };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border <?= $badge ?>">
                                    <?= htmlspecialchars($trx['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400"><?= htmlspecialchars($trx['date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Action Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <h3 class="text-base font-bold text-white">Buat Entri Transaksi Enterprise</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>
            
            <form method="POST" action="#" class="space-y-4">
                <?= Csrf::field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Target Database Penyimpanan</label>
                    <select class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white">
                        <option value="default">Database Utama (sima_core)</option>
                        <option value="logistik">Database Logistik (app_pengiriman)</option>
                        <option value="report">Database Reporting (sima_reporting)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Customer / Cabang</label>
                    <input type="text" placeholder="PT Contoh Mitra Sentosa" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nominal Transaksi (Rp)</label>
                    <input type="number" placeholder="50000000" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">Batal</button>
                    <button type="button" @click="modalOpen = false; alert('Fitur demo berhasil di-trigger!');" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-xs font-semibold text-white">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

</div>

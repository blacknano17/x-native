<div x-data="{ modalOpen: false, search: '' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Customer</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola data customer perusahaan</p>
        </div>
        <button @click="modalOpen = true" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-xs font-semibold text-white shadow-lg shadow-sky-500/20 transition">
            + Tambah Customer
        </button>
    </div>

    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">Nama Customer</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    <?php if (empty($items)): ?>
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada data customer.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4 font-mono text-white">#<?= $item['id'] ?></td>
                                <td class="px-6 py-4 font-semibold text-slate-200"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400"><?= htmlspecialchars($item['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-white mb-4 pb-2 border-b border-slate-800">Tambah Customer Baru</h3>
            <form method="POST" action="<?= $baseUrl ?>customer/create" class="space-y-4">
                <?= Csrf::field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Customer</label>
                    <input type="text" name="name" required placeholder="Contoh Customer..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-xs font-medium text-slate-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-xs font-semibold text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
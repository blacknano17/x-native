<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> &mdash; <?= htmlspecialchars($appName) ?></title>
    <!-- Tailwind CSS CDN Standalone -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col bg-slate-950 font-sans" x-data="{ sidebarOpen: false, userDropdown: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- ============================================================== -->
        <!-- SIDEBAR NAVIGASI                                              -->
        <!-- ============================================================== -->
        <!-- Backdrop mobile -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 flex flex-col transition-transform duration-200 ease-in-out">
            
            <!-- Logo Header -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800/80">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shadow-sky-500/20">
                    X
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-white block">X-Native</span>
                    <span class="text-[10px] text-sky-400 font-semibold uppercase tracking-wider block">Enterprise Web</span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Navigasi Utama</div>
                
                <a href="<?= $baseUrl ?>dashboard" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= ($currentPath === '/groblive/x-native/dashboard' || $currentPath === '/dashboard') ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>

                <a href="<?= $baseUrl ?>databases" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= ($currentPath === '/groblive/x-native/databases' || $currentPath === '/databases') ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                    <span>🗄️</span>
                    <span>Multi-Database</span>
                    <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-semibold border border-emerald-500/20">3 DB</span>
                </a>

                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-3 mt-6 mb-2">Modul Enterprise</div>

                <a href="<?= $baseUrl ?>customer" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= (strpos($currentPath, 'customer') !== false) ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                    <span>👥</span>
                    <span>Data Customer</span>
                    <span class="ml-auto text-[9px] px-1.5 py-0.5 rounded bg-sky-500/10 text-sky-400 font-mono">Scaffold</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <span>📦</span>
                    <span>Manajemen Logistik</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <span>📑</span>
                    <span>Laporan & Pajak</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <span>👥</span>
                    <span>Pengguna & Role</span>
                </a>
            </nav>

            <!-- Bottom User Profile Pill -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" 
                         class="w-9 h-9 rounded-full object-cover border border-slate-700">
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-semibold text-white block truncate"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                        <span class="text-[10px] text-slate-400 block truncate capitalize"><?= htmlspecialchars($currentUser['role'] ?? 'Guest') ?></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ============================================================== -->
        <!-- MAIN CONTENT AREA                                             -->
        <!-- ============================================================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-950">

            <!-- TOP NAVBAR -->
            <header class="h-16 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 flex items-center justify-between px-6 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <span class="text-xl">☰</span>
                    </button>
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-medium">Lingkungan:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Development (Local)
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Multi-DB Quick Status Pill -->
                    <a href="<?= $baseUrl ?>databases" class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-800 text-xs text-slate-300 border border-slate-700/60 transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Multi-DB Pool: <strong class="text-white">3 Aktif</strong></span>
                    </a>

                    <!-- User Menu Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2.5 focus:outline-none">
                            <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" 
                                 class="w-8 h-8 rounded-full object-cover border border-slate-700">
                            <span class="hidden sm:block text-xs font-medium text-slate-300"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                            <span class="text-xs text-slate-500">▼</span>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false" 
                             class="absolute right-0 mt-3 w-48 bg-slate-900 border border-slate-800 rounded-xl shadow-xl shadow-black/60 py-1.5 text-xs z-50">
                            <div class="px-4 py-2 border-b border-slate-800 text-slate-400">
                                <div>Signed in as</div>
                                <div class="font-bold text-white truncate"><?= htmlspecialchars($currentUser['username'] ?? '') ?></div>
                            </div>
                            <a href="<?= $baseUrl ?>databases" class="block px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                🗄️ Multi-DB Status
                            </a>
                            <form method="POST" action="<?= $baseUrl ?>logout" class="block">
                                <?= Csrf::field() ?>
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-400 hover:bg-rose-500/10 transition font-medium">
                                    🚪 Logout Sesi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- BODY CONTENT SCROLLABLE -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                
                <!-- Flash Message Toasts -->
                <?php if (!empty($flashMessages)): ?>
                    <div class="mb-6 space-y-2">
                        <?php foreach ($flashMessages as $msg): ?>
                            <?php 
                                $bgColor = match($msg['type']) {
                                    'success' => 'bg-emerald-950/80 border-emerald-500/50 text-emerald-200',
                                    'error'   => 'bg-rose-950/80 border-rose-500/50 text-rose-200',
                                    'warning' => 'bg-amber-950/80 border-amber-500/50 text-amber-200',
                                    default   => 'bg-sky-950/80 border-sky-500/50 text-sky-200'
                                };
                            ?>
                            <div class="p-4 rounded-xl border text-sm flex items-center justify-between backdrop-blur-md <?= $bgColor ?>" x-data="{ show: true }" x-show="show">
                                <span><?= htmlspecialchars($msg['message']) ?></span>
                                <button type="button" @click="show = false" class="text-xs opacity-60 hover:opacity-100">&times;</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?= $content ?>

            </main>

            <!-- FOOTER -->
            <footer class="h-10 bg-slate-900 border-t border-slate-800/80 flex items-center justify-between px-6 text-[11px] text-slate-500">
                <div><?= htmlspecialchars($appName) ?> &bull; PHP <?= PHP_VERSION ?> &bull; Multi-Database PDO</div>
                <div class="flex items-center gap-3">
                    <span>Memory: <strong class="text-slate-400"><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</strong></span>
                    <span>Status: <strong class="text-emerald-400">Enterprise Ready</strong></span>
                </div>
            </footer>

        </div>
    </div>

</body>
</html>

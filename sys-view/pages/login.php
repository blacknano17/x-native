<div x-data="{ 
    showPassword: false,
    username: '',
    password: '',
    setDemo(u, p) {
        this.username = u;
        this.password = p;
    }
}" class="bg-slate-900/90 border border-slate-800 backdrop-blur-xl rounded-2xl p-8 shadow-2xl shadow-black/80">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-bold text-xl mb-3 shadow-lg shadow-sky-500/30">
            X
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Selamat Datang</h1>
        <p class="text-sm text-slate-400 mt-1">Masuk ke akun enterprise Anda</p>
    </div>

    <!-- Quick Fill Demo Pills -->
    <div class="mb-6 p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 text-xs">
        <span class="text-slate-400 block mb-2 font-medium">Klik untuk isi akun demo:</span>
        <div class="flex gap-2">
            <button type="button" @click="setDemo('admin', 'password123')" class="px-2.5 py-1 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-500/30 font-mono transition">
                👑 Admin (Superadmin)
            </button>
            <button type="button" @click="setDemo('manager', 'manager123')" class="px-2.5 py-1 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-mono transition">
                💼 Manager (Logistik)
            </button>
        </div>
    </div>

    <form method="POST" action="<?= $baseUrl ?>login" class="space-y-5">
        <?= Csrf::field() ?>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Username</label>
            <input type="text" name="username" x-model="username" required autocomplete="username" placeholder="Masukkan username"
                class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm">
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
            </div>
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required autocomplete="current-password" placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm pr-11">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 p-1">
                    <span x-text="showPassword ? '🙈' : '👁️'" class="text-sm"></span>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-slate-400 select-none">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-sky-500 focus:ring-0 focus:ring-offset-0">
                <span>Ingat saya di perangkat ini</span>
            </label>
            <a href="#" class="text-sky-400 hover:underline">Lupa sandi?</a>
        </div>

        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-sky-500/25 transition transform active:scale-[0.98]">
            Masuk ke Sistem &rarr;
        </button>
    </form>
</div>

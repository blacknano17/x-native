<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Autentikasi') ?> &mdash; <?= htmlspecialchars($appName) ?></title>
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
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black text-slate-100">

    <div class="w-full max-w-md">
        <!-- Notifikasi Flash Message -->
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
                    <div class="p-4 rounded-xl border text-sm flex items-center gap-3 backdrop-blur-md <?= $bgColor ?>">
                        <span><?= htmlspecialchars($msg['message']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $content ?>

        <p class="text-center text-xs text-slate-500 mt-8">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($appName) ?> &bull; Enterprise Fullstack Framework
        </p>
    </div>

</body>
</html>

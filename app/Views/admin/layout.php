<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — <?= esc((string) cfg('company_name')) ?></title>
    <link rel="icon" href="/favicon.ico?v=<?= filemtime(FCPATH . 'favicon.ico') ?>">
    <?= vite('resources/js/app.js') ?>
</head>
<body class="bg-gray-50">

    <header class="sticky top-0 z-50 bg-white border-b border-gray-200">
        <nav class="max-w-7xl mx-auto px-4 md:px-6 flex items-center justify-between h-16">
            <div class="flex items-center gap-x-3">
                <img src="<?= base_url(cfg('logo_url', '/images/logo.svg')) ?>" alt="Logo" class="h-7 w-auto">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Admin</span>
            </div>
            <div class="flex items-center gap-x-6">
                <nav class="flex items-center gap-x-1">
                    <?php
                    $currentUri = ltrim(str_replace(site_url(), '', current_url()), '/');
                    $navLinks   = ['admin' => 'Tableau de bord', 'admin/status' => 'État du système', 'admin/config' => 'Configuration'];
                    foreach ($navLinks as $uri => $label):
                        $active = $currentUri === $uri;
                    ?>
                        <a href="<?= site_url($uri) ?>" class="px-3 py-1.5 rounded-lg text-sm font-medium transition <?= $active ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach ?>
                </nav>
                <div class="flex items-center gap-x-4">
                    <span class="text-sm text-gray-500"><?= esc((string) session()->get('admin_login')) ?></span>
                    <a href="<?= site_url('admin/logout') ?>" class="text-sm text-red-600 hover:underline">Déconnexion</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-8">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <?= esc((string) session()->getFlashdata('success')) ?>
            </div>
        <?php endif ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                <?= esc((string) session()->getFlashdata('error')) ?>
            </div>
        <?php endif ?>

        <?= $this->renderSection('content') ?>

    </main>

    <footer class="max-w-7xl mx-auto px-4 md:px-6 py-4 text-center text-xs text-gray-400">
        © <?= date('Y') ?> <?= esc((string) cfg('company_name')) ?> — Administration
    </footer>

</body>
</html>

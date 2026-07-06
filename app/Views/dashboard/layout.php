<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Espace client') ?></title>
    <link rel="icon" href="/favicon.ico?v=<?= filemtime(FCPATH . 'favicon.ico') ?>">

    <?= vite('resources/js/app.js') ?>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 flex flex-wrap md:justify-start md:flex-nowrap w-full bg-white border-b border-gray-200">
        <nav class="relative max-w-7xl w-full mx-auto px-4 md:px-6 flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="<?= site_url('dashboard') ?>" class="flex-none">
                <img src="<?= base_url(cfg('logo_url', '/images/logo.svg')) ?>" alt="Logo" class="h-8 w-auto">
            </a>

            <!-- Menu utilisateur (droite) -->
            <?php
            $docPages   = ['dashboard/invoices', 'dashboard/proposals', 'dashboard/orders'];
            $isDocActive = in_array(ltrim(str_replace(site_url(), '', current_url()), '/'), $docPages);
            ?>
            <div class="hs-dropdown relative inline-flex">
                <button type="button" class="hs-dropdown-toggle inline-flex items-center justify-center size-10 rounded-lg transition <?= $isDocActive ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' ?>">
                    <svg class="shrink-0 size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 transition-[opacity,margin] duration-150 opacity-0 hidden min-w-48 bg-white shadow-md rounded-xl border border-gray-200 p-1 mt-2 end-0 z-10">

                    <!-- Tableau de bord -->
                    <a href="<?= site_url('dashboard') ?>" class="<?= current_url() === site_url('dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                        <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                        </svg>
                        Tableau de bord
                    </a>

                    <!-- Mon compte (société Dolibarr) -->
                    <a href="<?= site_url('dashboard/company') ?>" class="<?= current_url() === site_url('dashboard/company') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                        <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                        Mon compte
                    </a>

                    <!-- Mon profil (paramètres utilisateur) -->
                    <a href="<?= site_url('dashboard/account') ?>" class="<?= current_url() === site_url('dashboard/account') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                        <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                        Mon profil
                    </a>

                    <!-- Séparateur -->
                    <div class="my-1 border-t border-gray-100"></div>

                    <!-- Documents -->
                    <div class="px-3 py-1.5">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Documents</p>
                    </div>
                    <?php if (cfg('uploads_page_enabled', true)): ?>
                        <a href="<?= site_url('dashboard/uploads') ?>" class="<?= current_url() === site_url('dashboard/uploads') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                            <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                            Mes fichiers
                        </a>
                    <?php endif ?>
                    <a href="<?= site_url('dashboard/invoices') ?>" class="<?= current_url() === site_url('dashboard/invoices') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                        <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                        </svg>
                        Factures
                    </a>
                    <a href="<?= site_url('dashboard/proposals') ?>" class="<?= current_url() === site_url('dashboard/proposals') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                        <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                        </svg>
                        Devis
                    </a>
                    <a href="<?= site_url('dashboard/orders') ?>" class="<?= current_url() === site_url('dashboard/orders') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?> flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium transition">
                        <svg class="size-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 1-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 1-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                        </svg>
                        Commandes
                    </a>

                    <!-- Séparateur -->
                    <div class="my-1 border-t border-gray-100"></div>

                    <!-- Déconnexion -->
                    <a href="<?= site_url('auth/logout') ?>" class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition">
                        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/>
                        </svg>
                        Déconnexion
                    </a>
                </div>
            </div>

        </nav>
    </header>

    <!-- Contenu principal -->
    <main class="max-w-7xl mx-auto px-4 md:px-6 py-8">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <?= esc((string) session()->getFlashdata('success')) ?>
            </div>
        <?php endif ?>

        <?= $this->renderSection('content') ?>

    </main>

    <footer class="max-w-7xl mx-auto px-4 md:px-6 py-4 text-center text-xs text-gray-400">
        © <?= date('Y') ?> <?= esc((string) cfg('company_name')) ?>
    </footer>

</body>
</html>

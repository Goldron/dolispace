<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Espace client') ?></title>
    <link rel="icon" type="image/png" href="/images/favicon-96x96.png?v=<?= filemtime(FCPATH . 'images/favicon-96x96.png') ?>" sizes="96x96">
    <link rel="shortcut icon" href="/images/favicon.ico?v=<?= filemtime(FCPATH . 'images/favicon.ico') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png?v=<?= filemtime(FCPATH . 'images/apple-touch-icon.png') ?>">
    <link rel="manifest" href="/site.webmanifest">

    <?= vite('resources/js/app.js') ?>
    <style>
        @keyframes kenburns {
            0%   { transform: scale(1)    translateX(0)     translateY(0); }
            50%  { transform: scale(1.08) translateX(-1.5%) translateY(-1%); }
            100% { transform: scale(1)    translateX(0)     translateY(0); }
        }
        .animate-kenburns {
            animation: kenburns 20s ease-in-out infinite;
            will-change: transform;
        }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- Colonne gauche : image -->
    <div class="hidden md:block md:w-[60%] relative overflow-hidden bg-blue-900">
        <img src="<?= base_url(versioned_asset(cfg('background_url', '/images/back.jpg'))) ?>" alt="" class="absolute inset-0 w-full h-full object-cover<?= cfg('background_animate', 'true') === 'true' ? ' animate-kenburns' : '' ?>">
        <?php if (cfg('label_url')): ?>
            <img src="<?= base_url(versioned_asset(cfg('label_url'))) ?>" alt="Label" class="absolute bottom-4 right-4 h-25 w-auto z-10">
        <?php endif ?>
    </div>

    <!-- Colonne droite : formulaire -->
    <div class="relative flex items-center justify-center w-full min-h-screen md:w-[40%] px-8 sm:px-16 lg:px-24 py-12 bg-white">

        <div class="max-w-sm w-full">

            <!-- Logo -->
            <div class="flex justify-center mb-10">
                <img src="<?= base_url(versioned_asset(cfg('logo_url', '/images/logo.svg'))) ?>" alt="Logo" class="h-16 w-auto">
            </div>

            <!-- Accroche -->
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Votre espace client sécurisé</h2>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Accédez à vos factures, devis et commandes en toute sécurité depuis un seul espace dédié.
                </p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-5 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                    <?= esc((string) session()->getFlashdata('error')) ?>
                </div>
            <?php endif ?>

            <?= $this->renderSection('content') ?>

        </div>

        <p class="absolute bottom-4 text-xs text-gray-400">
            © <?= date('Y') ?> <?= esc((string) cfg('company_name')) ?>
        </p>

    </div>

</body>
</html>

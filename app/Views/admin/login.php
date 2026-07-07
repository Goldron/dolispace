<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — <?= esc((string) cfg('company_name')) ?></title>
    <link rel="icon" type="image/png" href="/images/favicon-96x96.png?v=<?= filemtime(FCPATH . 'images/favicon-96x96.png') ?>" sizes="96x96">
    <link rel="shortcut icon" href="/images/favicon.ico?v=<?= filemtime(FCPATH . 'images/favicon.ico') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png?v=<?= filemtime(FCPATH . 'images/apple-touch-icon.png') ?>">
    <?= vite('resources/js/app.js') ?>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 px-4">

    <div class="w-full max-w-sm">

        <div class="flex justify-center mb-8">
            <img src="<?= base_url(versioned_asset(cfg('logo_url', '/images/logo.svg'))) ?>" alt="Logo" class="h-10 w-auto">
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-8">

            <h1 class="text-lg font-semibold text-gray-800 mb-1">Administration</h1>
            <p class="text-sm text-gray-500 mb-6">Accès réservé aux administrateurs.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-5 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                    <?= esc((string) session()->getFlashdata('error')) ?>
                </div>
            <?php endif ?>

            <?= form_open(admin_url('login')) ?>

                <div class="mb-4">
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-1.5">Identifiant</label>
                    <input
                        type="text"
                        id="login"
                        name="login"
                        required
                        autofocus
                        autocomplete="username"
                        class="py-2.5 px-3 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="py-2.5 px-3 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <button type="submit" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer">
                    Se connecter
                </button>

            <?= form_close() ?>

        </div>

        <p class="mt-6 text-center text-xs text-gray-400">
            © <?= date('Y') ?> <?= esc((string) cfg('company_name')) ?>
        </p>

    </div>

</body>
</html>

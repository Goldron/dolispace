<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<!-- En-tête -->
<div class="mb-8">
    <h1 class="text-xl font-semibold text-gray-900">
        Bonjour, <?= esc(session()->get('user_name')) ?> 👋
    </h1>
    <p class="mt-1 text-sm text-gray-500">Voici un aperçu de votre activité.</p>
</div>

<!-- Cartes de statistiques -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

    <!-- Factures -->
    <a href="<?= site_url('dashboard/invoices') ?>" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-blue-200 hover:shadow-sm transition block">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Factures</span>
            <div class="flex items-center justify-center size-10 rounded-lg bg-blue-50">
                <svg class="size-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= count($invoices) ?></p>
        <p class="text-xs text-gray-400 mt-1">récentes</p>
    </a>

    <!-- Devis -->
    <a href="<?= site_url('dashboard/proposals') ?>" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-amber-200 hover:shadow-sm transition block">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Devis</span>
            <div class="flex items-center justify-center size-10 rounded-lg bg-amber-50">
                <svg class="size-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= count($proposals) ?></p>
        <p class="text-xs text-gray-400 mt-1">récents</p>
    </a>

    <!-- Commandes -->
    <a href="<?= site_url('dashboard/orders') ?>" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-green-200 hover:shadow-sm transition block">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Commandes</span>
            <div class="flex items-center justify-center size-10 rounded-lg bg-green-50">
                <svg class="size-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 1-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 1-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?= count($orders) ?></p>
        <p class="text-xs text-gray-400 mt-1">récentes</p>
    </a>

</div>

<!-- Activité récente -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800">Activité récente</h2>
    </div>

    <?php if (empty($recentLogs)): ?>
        <div class="px-5 py-10 text-center text-sm text-gray-400">Aucune activité enregistrée.</div>
    <?php else: ?>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($recentLogs as $log): ?>
                <li class="flex items-center gap-x-4 px-5 py-3">
                    <div class="flex items-center justify-center size-7 rounded-full bg-gray-100 shrink-0">
                        <?php if ($log['action'] === 'login'): ?>
                            <svg class="size-3.5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                            </svg>
                        <?php elseif ($log['action'] === 'logout'): ?>
                            <svg class="size-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"/>
                            </svg>
                        <?php else: ?>
                            <svg class="size-3.5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                            </svg>
                        <?php endif ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700"><?= esc(_log_label($log['action'])) ?></p>
                        <?php if ($log['ip']): ?>
                            <p class="text-xs text-gray-400"><?= esc($log['ip']) ?></p>
                        <?php endif ?>
                    </div>
                    <time class="text-xs text-gray-400 shrink-0"><?= esc($log['created_at']) ?></time>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>

<?php
function _log_label(string $action): string
{
    return match ($action) {
        'login'            => 'Connexion',
        'logout'           => 'Déconnexion',
        'register'         => 'Création de compte',
        'password_renewed' => 'Renouvellement du mot de passe',
        default            => ucfirst(str_replace('_', ' ', $action)),
    };
}
?>

<?= $this->endSection() ?>

<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array $stats */
/** @var array $recentLogs */
/** @var array $userList */
/** @var \CodeIgniter\Pager\Pager $logPager */
/** @var string $search */
?>

<div class="mb-8">
    <h1 class="text-xl font-semibold text-gray-900">Tableau de bord</h1>
    <p class="mt-1 text-sm text-gray-500">Vue d'ensemble de l'application.</p>
</div>

<!-- Cartes de statistiques -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm font-medium text-gray-500 mb-1">Utilisateurs</p>
        <p class="text-3xl font-bold text-gray-900"><?= $stats['users'] ?></p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm font-medium text-gray-500 mb-1">Fichiers déposés</p>
        <p class="text-3xl font-bold text-gray-900"><?= $stats['uploads'] ?></p>
    </div>

</div>

<!-- Utilisateurs -->
<div class="bg-white rounded-xl border border-gray-200 mb-8">
    <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Utilisateurs enregistrés</h2>
            <p class="mt-0.5 text-xs text-gray-400">
                <?= $search !== '' ? 'Résultats de recherche' : 'Les 20 derniers inscrits' ?>
            </p>
        </div>
        <div class="flex items-center gap-x-2">
            <form action="<?= site_url('admin') ?>" method="get" class="flex items-center gap-x-1.5">
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Rechercher un user ou une société…"
                       class="py-1.5 px-2.5 text-xs border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="py-1.5 px-2.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Rechercher</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= site_url('admin') ?>" class="py-1.5 px-2.5 text-xs font-medium rounded-lg text-gray-500 hover:bg-gray-100 transition">Réinitialiser</a>
                <?php endif ?>
            </form>
            <button type="button" data-hs-overlay="#hs-clear-users-modal"
                    class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                Vider la table
            </button>
        </div>
    </div>
    <?php if (empty($userList)): ?>
        <p class="px-5 py-8 text-center text-sm text-gray-400">Aucun utilisateur.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                        <th class="px-5 py-3">Nom</th>
                        <th class="px-5 py-3">Société</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Vérifié</th>
                        <th class="px-5 py-3 text-right">Dernière connexion</th>
                        <th class="px-5 py-3 text-right">Inscription</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($userList as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800"><?= esc((string)($user['name'] ?? '—')) ?></td>
                            <td class="px-5 py-3 text-gray-600"><?= esc((string)($user['company_name'] ?? '—')) ?></td>
                            <td class="px-5 py-3 text-gray-600"><?= esc((string)$user['email']) ?></td>
                            <td class="px-5 py-3">
                                <?php if ($user['email_verified_at']): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Oui</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Non</span>
                                <?php endif ?>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-right"><?= esc((string)($user['last_login_at'] ?? '—')) ?></td>
                            <td class="px-5 py-3 text-gray-500 text-right"><?= esc((string)$user['created_at']) ?></td>
                            <td class="px-5 py-3 text-right">
                                <form action="<?= site_url('admin/users/' . $user['id'] . '/delete') ?>" method="post"
                                      onsubmit="return confirm('Supprimer l\'utilisateur « <?= esc($user['email'], 'js') ?> » ?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Supprimer">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>

<!-- Activité récente -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
        <h2 class="text-sm font-semibold text-gray-800">Activité récente</h2>
        <button type="button" data-hs-overlay="#hs-clear-logs-modal"
                class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
            Vider la table
        </button>
    </div>
    <?php if (empty($recentLogs)): ?>
        <p class="px-5 py-8 text-center text-sm text-gray-400">Aucune activité.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                        <th class="px-5 py-3">Utilisateur</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3 text-right">IP</th>
                        <th class="px-5 py-3 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($recentLogs as $log): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-800">
                                <p class="font-medium"><?= esc((string)($log['user_name'] ?? '—')) ?></p>
                                <p class="text-xs text-gray-400"><?= esc((string)($log['email'] ?? '')) ?></p>
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-800"><?= esc((string)$log['action']) ?></td>
                            <td class="px-5 py-3 text-gray-500 text-right"><?= esc((string)($log['ip'] ?? '—')) ?></td>
                            <td class="px-5 py-3 text-gray-500 text-right"><?= esc((string)$log['created_at']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php
            $currentPage = $logPager->getCurrentPage('logs');
            $totalPages  = $logPager->getPageCount('logs');
        ?>
        <?php if ($totalPages > 1): ?>
            <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-x-4">
                <p class="text-xs text-gray-400">Page <?= $currentPage ?> sur <?= $totalPages ?></p>
                <div class="flex items-center gap-x-1">

                    <?php if ($currentPage > 1): ?>
                        <a href="<?= $logPager->getPageURI($currentPage - 1, 'logs') ?>" class="inline-flex items-center justify-center size-8 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">&#8592;</a>
                    <?php else: ?>
                        <span class="inline-flex items-center justify-center size-8 rounded-lg border border-gray-100 text-sm text-gray-300 cursor-not-allowed">&#8592;</span>
                    <?php endif ?>

                    <?php for ($p = 1; $p <= $totalPages; $p++):
                        if ($totalPages > 7 && $p > 2 && $p < $totalPages - 1 && abs($p - $currentPage) > 1):
                            if ($p === 3 || $p === $totalPages - 2): ?>
                                <span class="inline-flex items-center justify-center size-8 text-sm text-gray-400">…</span>
                            <?php endif; continue;
                        endif; ?>
                        <a href="<?= $logPager->getPageURI($p, 'logs') ?>"
                           class="inline-flex items-center justify-center size-8 rounded-lg border text-sm transition
                                  <?= $p === $currentPage ? 'border-blue-500 bg-blue-50 text-blue-600 font-semibold' : 'border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= $logPager->getPageURI($currentPage + 1, 'logs') ?>" class="inline-flex items-center justify-center size-8 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">&#8594;</a>
                    <?php else: ?>
                        <span class="inline-flex items-center justify-center size-8 rounded-lg border border-gray-100 text-sm text-gray-300 cursor-not-allowed">&#8594;</span>
                    <?php endif ?>

                </div>
            </div>
        <?php endif ?>
    <?php endif ?>
</div>

<!-- Modal de confirmation : vider la table des utilisateurs -->
<div id="hs-clear-users-modal" class="hs-overlay hidden size-full fixed top-0 inset-s-0 z-80 overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Vider la table des utilisateurs ?</h3>
                <button type="button" class="size-7 inline-flex justify-center items-center rounded-full text-gray-400 hover:bg-gray-100 transition" data-hs-overlay="#hs-clear-users-modal">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-600">
                    Cette action supprime <strong>définitivement</strong> tous les utilisateurs enregistrés. Cette opération est irréversible.
                </p>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-100">
                <button type="button" data-hs-overlay="#hs-clear-users-modal"
                        class="py-2 px-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    Annuler
                </button>
                <form action="<?= site_url('admin/users/clear') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                        Oui, tout supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation : vider le journal d'activité -->
<div id="hs-clear-logs-modal" class="hs-overlay hidden size-full fixed top-0 inset-s-0 z-80 overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Vider le journal d'activité ?</h3>
                <button type="button" class="size-7 inline-flex justify-center items-center rounded-full text-gray-400 hover:bg-gray-100 transition" data-hs-overlay="#hs-clear-logs-modal">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-600">
                    Cette action supprime <strong>définitivement</strong> tout l'historique d'activité. Cette opération est irréversible.
                </p>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-100">
                <button type="button" data-hs-overlay="#hs-clear-logs-modal"
                        class="py-2 px-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    Annuler
                </button>
                <form action="<?= site_url('admin/logs/clear') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                        Oui, tout supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<?php
$invoices ??= [];
if (! cfg('show_drafts', false)) {
    $invoices = array_filter($invoices, fn($f) => (int)($f['statut'] ?? 0) !== 0);
}

$statusLabels = [
    0 => ['label' => 'Brouillon',  'class' => 'bg-gray-100 text-gray-600'],
    1 => ['label' => 'Impayée',    'class' => 'bg-red-100 text-red-700'],
    2 => ['label' => 'Payée',      'class' => 'bg-green-100 text-green-700'],
    3 => ['label' => 'Abandonnée', 'class' => 'bg-gray-100 text-gray-400'],
];
$downloadableStatuts = [1, 2];

?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Factures</h1>
        <p class="mt-1 text-sm text-gray-500"><?= count($invoices) ?> facture<?= count($invoices) > 1 ? 's' : '' ?> trouvée<?= count($invoices) > 1 ? 's' : '' ?></p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<?php if (empty($invoices)): ?>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
        <svg class="mx-auto size-10 text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
        </svg>
        <p class="text-sm text-gray-400">Aucune facture disponible.</p>
    </div>

<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($invoices as $i => $f): ?>
            <?php
            $statut     = (int)($f['statut'] ?? 0);
            $status     = $statusLabels[$statut] ?? $statusLabels[0];
            $date       = ! empty($f['date']) ? date('d/m/Y', (int)$f['date']) : '—';
            $echeance   = ! empty($f['date_lim_reglement']) ? date('d/m/Y', (int)$f['date_lim_reglement']) : '—';
            $ttc        = number_format((float)($f['total_ttc'] ?? 0), 2, ',', ' ') . ' €';
            $canExpand  = in_array($statut, $downloadableStatuts);
            $collapseId = 'lines-' . $i;
            $lines      = $f['lines'] ?? [];
            $hasDoc     = $canExpand && ! empty($f['last_main_doc']);
            ?>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-x-2">
                        <span class="text-sm font-medium text-gray-900"><?= esc((string)($f['ref'] ?? '')) ?></span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $status['class'] ?>">
                            <?= $status['label'] ?>
                        </span>
                        <?php if ($hasDoc): ?>
                            <a href="<?= site_url('dashboard/invoices/' . $f['id'] . '/download') ?>"
                               title="Télécharger le PDF"
                               class="size-6 inline-flex items-center justify-center rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                </svg>
                            </a>
                        <?php endif ?>
                    </div>
                    <?php if ($canExpand && ! empty($lines)): ?>
                        <button type="button"
                            class="hs-collapse-toggle size-6 inline-flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition"
                            data-hs-collapse="#<?= $collapseId ?>">
                            <svg class="hs-collapse-open:rotate-180 size-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                    <?php endif ?>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Le <?= $date ?><?= $echeance !== '—' ? ' · Échéance le ' . $echeance : '' ?></span>
                    <span class="font-semibold text-gray-900"><?= $ttc ?></span>
                </div>

                <?php if ($canExpand && ! empty($lines)): ?>
                    <div id="<?= $collapseId ?>" class="hs-collapse hidden overflow-hidden transition-[height] duration-200">
                        <div class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                            <?php foreach ($lines as $line): ?>
                                <?php
                                $lineDesc  = esc((string)($line['description'] ?? $line['desc'] ?? '—'));
                                $lineQty   = (float)($line['qty'] ?? 0);
                                $linePrice = number_format((float)($line['subprice'] ?? 0), 2, ',', ' ') . ' €';
                                $lineTtc   = number_format((float)($line['total_ttc'] ?? 0), 2, ',', ' ') . ' €';
                                ?>
                                <div class="flex items-start justify-between gap-x-3 text-xs">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-700 font-medium truncate"><?= $lineDesc ?></p>
                                        <p class="text-gray-400"><?= $lineQty ?> × <?= $linePrice ?> HT</p>
                                    </div>
                                    <span class="shrink-0 font-semibold text-gray-900"><?= $lineTtc ?></span>
                                </div>
                            <?php endforeach ?>

                            <div class="flex items-center justify-between pt-2 mt-1 border-t border-gray-200 text-xs">
                                <span class="text-gray-500 font-medium">Total HT</span>
                                <span class="font-bold text-gray-900"><?= number_format((float)($f['total_ht'] ?? 0), 2, ',', ' ') ?> €</span>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?= $this->endSection() ?>

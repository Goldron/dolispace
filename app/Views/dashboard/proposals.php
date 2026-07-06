<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<?php
$proposals ??= [];
if (! cfg('show_drafts', false)) {
    $proposals = array_filter($proposals, fn($p) => (int)($p['statut'] ?? 0) !== 0);
}

$statusLabels = [
    0 => ['label' => 'Brouillon',  'class' => 'bg-gray-100 text-gray-600'],
    1 => ['label' => 'En cours',   'class' => 'bg-amber-100 text-amber-700'],
    2 => ['label' => 'Signé',      'class' => 'bg-green-100 text-green-700'],
    3 => ['label' => 'Refusé',     'class' => 'bg-red-100 text-red-700'],
];
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Devis</h1>
        <p class="mt-1 text-sm text-gray-500"><?= count($proposals) ?> devis trouvé<?= count($proposals) > 1 ? 's' : '' ?></p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<?php if (empty($proposals)): ?>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
        <svg class="mx-auto size-10 text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
        </svg>
        <p class="text-sm text-gray-400">Aucun devis disponible.</p>
    </div>

<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($proposals as $i => $p): ?>
            <?php
            $status   = $statusLabels[(int)($p['statut'] ?? 0)] ?? $statusLabels[0];
            $date     = $p['datep'] ? date('d/m/Y', (int)$p['datep']) : '—';
            $expiry   = $p['fin_validite'] ? date('d/m/Y', (int)$p['fin_validite']) : '—';
            $ttc      = number_format((float)($p['total_ttc'] ?? 0), 2, ',', ' ') . ' €';
            $isEnCours   = in_array((int)($p['statut'] ?? 0), [1, 2]);
            $collapseId  = 'lines-' . $i;
            $lines       = $p['lines'] ?? [];
            $hasDoc      = $isEnCours && ! empty($p['last_main_doc']);
            ?>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-x-2">
                        <span class="text-sm font-medium text-gray-900"><?= esc((string)($p['ref'] ?? '')) ?></span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $status['class'] ?>">
                            <?= $status['label'] ?>
                        </span>
                        <?php if ($hasDoc): ?>
                            <a href="<?= site_url('dashboard/proposals/' . $p['id'] . '/download') ?>"
                               title="Télécharger le PDF"
                               class="size-6 inline-flex items-center justify-center rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                </svg>
                            </a>
                        <?php endif ?>
                    </div>
                    <?php if ($isEnCours && ! empty($lines)): ?>
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
                    <span>Le <?= $date ?> · Valide jusqu'au <?= $expiry ?></span>
                    <span class="font-semibold text-gray-900"><?= $ttc ?></span>
                </div>

                <?php if ($isEnCours && ! empty($lines)): ?>
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
                                <span class="font-bold text-gray-900"><?= number_format((float)($p['total_ht'] ?? 0), 2, ',', ' ') ?> €</span>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?= $this->endSection() ?>

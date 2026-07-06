<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<?php
$orders ??= [];
if (! cfg('show_drafts', false)) {
    $orders = array_filter($orders, fn($o) => (int)($o['statut'] ?? 0) !== 0);
}

$statusLabels = [
    -1 => ['label' => 'Annulée',           'class' => 'bg-red-100 text-red-700'],
     0 => ['label' => 'Brouillon',          'class' => 'bg-gray-100 text-gray-600'],
     1 => ['label' => 'Validée',            'class' => 'bg-amber-100 text-amber-700'],
     2 => ['label' => 'En cours',           'class' => 'bg-blue-100 text-blue-700'],
     3 => ['label' => 'Livrée',             'class' => 'bg-green-100 text-green-700'],
];
$downloadableStatuts = [1, 2, 3];

$shipmentStatusLabels = [
    -1 => ['label' => 'Annulée',         'class' => 'bg-red-100 text-red-700'],
     0 => ['label' => 'Brouillon',       'class' => 'bg-gray-100 text-gray-600'],
     1 => ['label' => 'Envoi en cours',  'class' => 'bg-green-100 text-green-700'],
     2 => ['label' => 'Clôturée',        'class' => 'bg-blue-100 text-blue-700'],
];
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Commandes</h1>
        <p class="mt-1 text-sm text-gray-500"><?= count($orders) ?> commande<?= count($orders) > 1 ? 's' : '' ?> trouvée<?= count($orders) > 1 ? 's' : '' ?></p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<?php if (empty($orders)): ?>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
        <svg class="mx-auto size-10 text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 1-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 1-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
        </svg>
        <p class="text-sm text-gray-400">Aucune commande disponible.</p>
    </div>

<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($orders as $i => $o): ?>
            <?php
            $statut     = (int)($o['statut'] ?? 0);
            $status     = $statusLabels[$statut] ?? $statusLabels[0];
            $date       = ! empty($o['date']) ? date('d/m/Y', (int)$o['date']) : '—';
            $delivery   = ! empty($o['date_livraison']) ? date('d/m/Y', (int)$o['date_livraison']) : '—';
            $ttc        = number_format((float)($o['total_ttc'] ?? 0), 2, ',', ' ') . ' €';
            $canExpand  = in_array($statut, $downloadableStatuts);
            $collapseId = 'lines-' . $i;
            $lines        = $o['lines'] ?? [];
            $hasDoc       = $canExpand && ! empty($o['last_main_doc']);
            $certificates = $o['certificates'] ?? [];
            $shipments    = array_filter($o['shipments'] ?? [], fn($s) => (int)($s['statut'] ?? 0) !== 0);
            ?>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-x-2">
                        <span class="text-sm font-medium text-gray-900">
                            <?= esc((string)($o['ref'] ?? '')) ?>
                            <?php if (! empty($o['ref_client'])): ?>
                                <span class="font-normal text-gray-400">(<?= esc((string)$o['ref_client']) ?>)</span>
                            <?php endif ?>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $status['class'] ?>">
                            <?= $status['label'] ?>
                        </span>
                        <?php if ($hasDoc): ?>
                            <?php $docLabel = basename((string)$o['last_main_doc']); ?>
                            <a href="<?= site_url('dashboard/orders/' . $o['id'] . '/download') ?>"
                               title="Télécharger le PDF : <?= esc($docLabel) ?>"
                               class="inline-flex items-center gap-x-1 pl-1 pr-2 py-0.5 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                </svg>
                                <span class="text-xs truncate max-w-40"><?= esc($docLabel) ?></span>
                            </a>
                        <?php endif ?>
                        <?php foreach ($certificates as $cert): ?>
                            <?php $certLabel = (string)($cert['label'] ?? $cert['filename'] ?? 'Certificat'); ?>
                            <span class="inline-flex items-center gap-x-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Certificat</span>
                                <a href="<?= site_url('dashboard/orders/certificates/' . $cert['id'] . '/download') ?>"
                                   title="Télécharger le certificat : <?= esc($certLabel) ?>"
                                   class="inline-flex items-center gap-x-1 pl-1 pr-2 py-0.5 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                    <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                    </svg>
                                    <span class="text-xs truncate max-w-40"><?= esc($certLabel) ?></span>
                                </a>
                            </span>
                        <?php endforeach ?>
                    </div>
                    <?php $canToggle = $canExpand && ! empty($lines); ?>
                    <?php if ($canToggle): ?>
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
                    <span>Le <?= $date ?><?= $delivery !== '—' ? ' · Livraison le ' . $delivery : '' ?></span>
                    <span class="font-semibold text-gray-900"><?= $ttc ?></span>
                </div>

                <?php if ($canToggle): ?>
                    <div id="<?= $collapseId ?>" class="hs-collapse hidden overflow-hidden transition-[height] duration-200">
                        <?php if (! empty($lines)): ?>
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
                                <span class="font-bold text-gray-900"><?= number_format((float)($o['total_ht'] ?? 0), 2, ',', ' ') ?> €</span>
                            </div>
                        </div>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <?php if (! empty($shipments)): ?>
                    <div class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                        <p class="text-xs font-medium text-gray-500">Expéditions</p>
                        <?php foreach ($shipments as $shipment): ?>
                            <?php
                            $shipRef        = (string)($shipment['ref'] ?? '');
                            $shipStatut     = (int)($shipment['statut'] ?? 0);
                            $shipStatus     = $statut === 3 ? $statusLabels[3] : ($shipmentStatusLabels[$shipStatut] ?? $shipmentStatusLabels[0]);
                            $shipDate       = ! empty($shipment['date_creation']) ? date('d/m/Y', (int)$shipment['date_creation']) : '—';
                            $shipExpedDate  = ! empty($shipment['date_delivery']) ? date('d/m/Y', (int)$shipment['date_delivery']) : '—';
                            $shipWeight     = ! empty($shipment['weight']) ? $shipment['weight'] . ' kg' : '—';
                            $shipTracking   = ! empty($shipment['tracking_number']) ? $shipment['tracking_number'] : '—';
                            $shipMethod     = ! empty($shipment['shipping_method']) ? $shipment['shipping_method'] : '—';
                            $shipDocLabel   = ! empty($shipment['last_main_doc']) ? basename((string)$shipment['last_main_doc']) : $shipRef . '.pdf';
                            ?>
                            <div class="rounded-lg border border-gray-100 p-3 text-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-x-1.5">
                                        <span class="font-medium text-gray-700"><?= esc($shipRef) ?></span>
                                        <a href="<?= site_url('dashboard/orders/shipments/' . $shipment['id'] . '/download') ?>"
                                           title="Télécharger le PDF : <?= esc($shipDocLabel) ?>"
                                           class="inline-flex items-center gap-x-1 pl-1 pr-2 py-0.5 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                            <svg class="size-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                            </svg>
                                            <span class="text-xs truncate max-w-40"><?= esc($shipDocLabel) ?></span>
                                        </a>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $shipStatus['class'] ?>">
                                        <?= $shipStatus['label'] ?>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-y-1 gap-x-3 text-gray-500">
                                    <span>Date : <span class="text-gray-700"><?= $shipDate ?></span></span>
                                    <span>Date d'expédition : <span class="text-gray-700"><?= $shipExpedDate ?></span></span>
                                    <span>Poids : <span class="text-gray-700"><?= esc((string)$shipWeight) ?></span></span>
                                    <span>Méthode : <span class="text-gray-700"><?= esc((string)$shipMethod) ?></span></span>
                                    <span class="col-span-2">N° de suivi : <span class="text-gray-700"><?= esc((string)$shipTracking) ?></span></span>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?= $this->endSection() ?>

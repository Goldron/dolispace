<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array $config */
$config ??= [];

$inputClass    = 'py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none';
$fileClass     = 'block w-full border border-gray-300 rounded-lg text-sm focus:z-10 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 file:bg-gray-50 file:border-0 file:me-4 file:py-2 file:px-3 file:text-sm file:font-medium file:cursor-pointer disabled:opacity-50 disabled:pointer-events-none';
$checkboxClass = 'shrink-0 size-4 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none';
$selectClass   = 'py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none disabled:opacity-50 disabled:pointer-events-none';

// Ajoute ?v=filemtime pour invalider le cache navigateur après chaque upload
$imgSrc = static function (string $path): string {
    if (empty($path)) return '';
    $abs = FCPATH . ltrim($path, '/');
    return esc($path) . (is_file($abs) ? '?v=' . filemtime($abs) : '');
};

// Fonctionnalités mises en évidence en tête de page (toggles des modules Dolibarr optionnels)
$featureLabels = [
    'expedition_enabled'         => 'Expéditions',
    'certificatsclients_enabled' => 'Certificats clients',
    'commande_enabled'           => 'Commandes',
    'propal_enabled'             => 'Devis / Propositions',
    'facture_enabled'            => 'Factures',
];
$configByKey = array_column($config, null, 'config_key');
$tableConfig = array_values(array_filter($config, fn($row) => ! isset($featureLabels[$row['config_key']])));
?>

<div class="mb-8">
    <h1 class="text-xl font-semibold text-gray-900">Configuration</h1>
    <p class="mt-1 text-sm text-gray-500">Paramètres de l'application stockés en base de données.</p>
</div>

<!-- Fonctionnalités -->
<div class="bg-white rounded-xl border border-gray-200 mb-8">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800">Fonctionnalités</h2>
        <p class="mt-0.5 text-xs text-gray-400">Active ou désactive les modules Dolibarr optionnels de l'espace client.</p>
    </div>
    <ul class="divide-y divide-gray-100">
        <?php foreach ($featureLabels as $key => $label): ?>
            <?php
            $row = $configByKey[$key] ?? null;
            if (! $row || ! ($featureModulesAvailable[$key] ?? true)) continue;
            ?>
            <li class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-800"><?= esc($label) ?></p>
                    <?php if (! empty($row['description'])): ?>
                        <p class="text-xs text-gray-400"><?= esc($row['description']) ?></p>
                    <?php endif ?>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="<?= esc($key) ?>" value="" form="config-form">
                    <input type="checkbox" name="<?= esc($key) ?>" value="true" form="config-form"
                           <?= $row['config_value'] === 'true' ? 'checked' : '' ?>
                           class="<?= $checkboxClass ?>">
                </label>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="px-5 py-4 border-t border-gray-100 flex justify-end">
        <button type="submit" form="config-form" class="inline-flex items-center gap-x-2 py-2 px-4 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9"/>
            </svg>
            Enregistrer les modifications
        </button>
    </div>
</div>

<!-- Tableau des clés existantes -->
<div class="bg-white rounded-xl border border-gray-200 mb-8">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800">Clés de configuration</h2>
    </div>

    <?php if (empty($tableConfig)): ?>
        <p class="px-5 py-8 text-center text-sm text-gray-400">Aucune entrée.</p>
    <?php else: ?>
        <form id="config-form" action="<?= site_url(admin_url('config/update')) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-5 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide w-1/5">Clé</th>
                            <th scope="col" class="px-5 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide w-28">Hook</th>
                            <th scope="col" class="px-5 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide w-20">Type</th>
                            <th scope="col" class="px-5 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Valeur</th>
                            <th scope="col" class="px-5 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide w-1/5">Description</th>
                            <th scope="col" class="px-5 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $currentHook = null; foreach ($tableConfig as $row): ?>
                            <?php if ($row['config_hook'] !== $currentHook): $currentHook = $row['config_hook']; ?>
                                <tr class="bg-gray-50">
                                    <td colspan="6" class="px-5 py-2 text-xs font-semibold text-gray-500 uppercase tracking-widest">
                                        <?= esc($currentHook ?? 'Général') ?>
                                    </td>
                                </tr>
                            <?php endif ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-mono text-xs text-gray-800 align-middle whitespace-nowrap">
                                    <?= esc($row['config_key']) ?>
                                </td>
                                <td class="px-5 py-3 align-middle">
                                    <input type="text" name="_hook[<?= $row['id'] ?>]"
                                           value="<?= esc($row['config_hook'] ?? '') ?>"
                                           placeholder="—"
                                           class="<?= $inputClass ?> font-mono text-xs">
                                </td>
                                <td class="px-5 py-3 align-middle">
                                    <span class="inline-flex items-center py-1 px-2.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        <?= esc($row['value_type']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 align-middle">
                                    <?php if ($row['value_type'] === 'bool'): ?>
                                        <input type="hidden" name="<?= esc($row['config_key']) ?>" value="">
                                        <input type="checkbox" name="<?= esc($row['config_key']) ?>" value="true"
                                               <?= $row['config_value'] === 'true' ? 'checked' : '' ?>
                                               class="<?= $checkboxClass ?>">
                                    <?php elseif ($row['value_type'] === 'json'): ?>
                                        <textarea name="<?= esc($row['config_key']) ?>" rows="3"
                                                  class="<?= $inputClass ?> font-mono"><?= esc($row['config_value'] ?? '') ?></textarea>
                                    <?php elseif ($row['config_key'] === 'logo_url'): ?>
                                        <input type="hidden" name="logo_url" value="<?= esc($row['config_value'] ?? '') ?>">
                                        <div class="flex items-center gap-x-4">
                                            <div class="w-1/2 flex items-center justify-center h-14 rounded border border-gray-200 bg-gray-50">
                                                <img src="<?= $imgSrc($row['config_value'] ?? '') ?>" alt="Logo"
                                                     id="preview-logo_url"
                                                     class="max-h-12 max-w-full object-contain<?= empty($row['config_value']) ? ' hidden' : '' ?>">
                                            </div>
                                            <div class="w-1/2 flex flex-col gap-y-1.5">
                                                <label class="inline-flex items-center gap-x-1.5 cursor-pointer py-1.5 px-3 rounded-lg border border-dashed border-gray-300 text-xs font-medium text-gray-500 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                                    <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                                    <span id="label-logo_url">Choisir un logo…</span>
                                                    <input type="file" name="logo_url_file" class="sr-only"
                                                           accept="image/svg+xml,image/png,image/jpeg,image/webp,image/gif"
                                                           onchange="previewUpload(this,'preview-logo_url','label-logo_url')">
                                                </label>
                                                <?php if (! empty($row['config_value'])): ?>
                                                    <label class="flex items-center gap-x-1.5 text-xs text-gray-400 cursor-pointer">
                                                        <input type="checkbox" name="_clear[logo_url]" value="1" class="<?= $checkboxClass ?>"> Vider
                                                    </label>
                                                <?php endif ?>
                                                <p class="text-xs text-gray-400">SVG, PNG, JPG, WebP ou GIF</p>
                                            </div>
                                        </div>
                                    <?php elseif ($row['config_key'] === 'label_url'): ?>
                                        <input type="hidden" name="label_url" value="<?= esc($row['config_value'] ?? '') ?>">
                                        <div class="flex items-center gap-x-4">
                                            <div class="w-1/2 flex items-center justify-center h-14 rounded border border-gray-200 bg-gray-50">
                                                <img src="<?= $imgSrc($row['config_value'] ?? '') ?>" alt="Label"
                                                     id="preview-label_url"
                                                     class="max-h-12 max-w-full object-contain<?= empty($row['config_value']) ? ' hidden' : '' ?>">
                                            </div>
                                            <div class="w-1/2 flex flex-col gap-y-1.5">
                                                <label class="inline-flex items-center gap-x-1.5 cursor-pointer py-1.5 px-3 rounded-lg border border-dashed border-gray-300 text-xs font-medium text-gray-500 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                                    <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                                    <span id="label-label_url">Choisir un label…</span>
                                                    <input type="file" name="label_url_file" class="sr-only"
                                                           accept="image/svg+xml,image/png,image/jpeg,image/webp,image/gif"
                                                           onchange="previewUpload(this,'preview-label_url','label-label_url')">
                                                </label>
                                                <?php if (! empty($row['config_value'])): ?>
                                                    <label class="flex items-center gap-x-1.5 text-xs text-gray-400 cursor-pointer">
                                                        <input type="checkbox" name="_clear[label_url]" value="1" class="<?= $checkboxClass ?>"> Vider
                                                    </label>
                                                <?php endif ?>
                                                <p class="text-xs text-gray-400">SVG, PNG, JPG, WebP ou GIF</p>
                                            </div>
                                        </div>
                                    <?php elseif ($row['config_key'] === 'background_url'): ?>
                                        <input type="hidden" name="background_url" value="<?= esc($row['config_value'] ?? '') ?>">
                                        <div class="flex items-center gap-x-4">
                                            <div class="w-1/2 h-14 rounded border border-gray-200 overflow-hidden bg-gray-50">
                                                <img src="<?= $imgSrc($row['config_value'] ?? '') ?>" alt="Fond"
                                                     id="preview-background_url"
                                                     class="w-full h-full object-cover<?= empty($row['config_value']) ? ' hidden' : '' ?>">
                                            </div>
                                            <div class="w-1/2 flex flex-col gap-y-1.5">
                                                <label class="inline-flex items-center gap-x-1.5 cursor-pointer py-1.5 px-3 rounded-lg border border-dashed border-gray-300 text-xs font-medium text-gray-500 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                                    <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                                    <span id="label-background_url">Choisir une image de fond…</span>
                                                    <input type="file" name="background_url_file" class="sr-only"
                                                           accept="image/png,image/jpeg,image/webp,image/gif"
                                                           onchange="previewUpload(this,'preview-background_url','label-background_url')">
                                                </label>
                                                <?php if (! empty($row['config_value'])): ?>
                                                    <label class="flex items-center gap-x-1.5 text-xs text-gray-400 cursor-pointer">
                                                        <input type="checkbox" name="_clear[background_url]" value="1" class="<?= $checkboxClass ?>"> Vider
                                                    </label>
                                                <?php endif ?>
                                                <p class="text-xs text-gray-400">PNG, JPG, WebP ou GIF</p>
                                            </div>
                                        </div>
                                    <?php elseif (in_array($row['config_key'], ['smtp_pass', 'dolibarr_api_token'], true)): ?>
                                        <input type="password" name="<?= esc($row['config_key']) ?>"
                                               value="<?= esc($row['config_value'] ?? '') ?>"
                                               autocomplete="off"
                                               class="<?= $inputClass ?>">
                                        <?php if (! empty($row['config_value'])): ?>
                                            <p class="mt-1 text-xs text-gray-400 font-mono">…<?= esc(substr($row['config_value'], -5)) ?></p>
                                        <?php endif ?>
                                    <?php else: ?>
                                        <input type="text" name="<?= esc($row['config_key']) ?>"
                                               value="<?= esc($row['config_value'] ?? '') ?>"
                                               class="<?= $inputClass ?>">
                                    <?php endif ?>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500 align-middle">
                                    <?= esc($row['description'] ?? '') ?>
                                </td>
                                <td class="px-5 py-3 align-middle text-end">
                                    <?php if ((bool) ($row['protected'] ?? true)): ?>
                                        <span class="text-gray-300 cursor-not-allowed" title="Clé protégée">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                                            </svg>
                                        </span>
                                    <?php else: ?>
                                        <button type="submit" form="delete-row-<?= $row['id'] ?>"
                                                onclick="return confirm('Supprimer « <?= esc($row['config_key']) ?> » ?')"
                                                class="text-gray-400 hover:text-red-500 transition" title="Supprimer">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-x-2 py-2 px-4 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    <?php endif ?>
</div>

<?php foreach ($config as $row): if (! (bool) ($row['protected'] ?? true)): ?>
    <form id="delete-row-<?= $row['id'] ?>"
          action="<?= site_url(admin_url('config/' . $row['id'] . '/delete')) ?>"
          method="post" hidden>
        <?= csrf_field() ?>
    </form>
<?php endif; endforeach ?>

<!-- Test d'envoi d'email -->
<div class="bg-white rounded-xl border border-gray-200 mb-8">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800">Test d'envoi d'email</h2>
        <p class="mt-0.5 text-xs text-gray-400">Envoie un email de test avec la configuration SMTP actuelle.</p>
    </div>
    <form action="<?= site_url(admin_url('config/test-email')) ?>" method="post" class="p-5">
        <?= csrf_field() ?>
        <div class="flex items-end gap-x-3">
            <div class="flex-1 max-w-sm">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Adresse destinataire</label>
                <input type="email" name="test_email_to" required placeholder="test@exemple.fr"
                       value="<?= esc(session()->getFlashdata('test_email_to') ?? '') ?>"
                       class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit" class="inline-flex items-center gap-x-2 py-2 px-4 rounded-lg bg-gray-800 text-white text-sm font-medium hover:bg-gray-900 transition whitespace-nowrap">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                </svg>
                Envoyer un test
            </button>
        </div>
    </form>
</div>

<!-- Ajout d'une nouvelle clé -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800">Ajouter une clé</h2>
    </div>
    <form action="<?= site_url(admin_url('config/store')) ?>" method="post" class="p-5">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Clé <span class="text-red-500">*</span></label>
                <input type="text" name="config_key" required placeholder="ma_cle"
                       class="<?= $inputClass ?> font-mono">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Hook</label>
                <input type="text" name="config_hook" placeholder="ex: smtp"
                       class="<?= $inputClass ?> font-mono">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Type</label>
                <select name="value_type" class="<?= $selectClass ?>">
                    <option value="string">string</option>
                    <option value="bool">bool</option>
                    <option value="int">int</option>
                    <option value="float">float</option>
                    <option value="json">json</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Valeur</label>
                <input type="text" name="config_value" placeholder="valeur"
                       class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Description</label>
                <input type="text" name="description" placeholder="Description courte"
                       class="<?= $inputClass ?>">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-x-2 py-2 px-4 rounded-lg bg-gray-800 text-white text-sm font-medium hover:bg-gray-900 transition">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Ajouter
            </button>
        </div>
    </form>
</div>

<script>
function previewUpload(input, previewId, labelId) {
    const preview = document.getElementById(previewId);
    const label   = document.getElementById(labelId);
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    label.textContent = file.name;
    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}
</script>

<?= $this->endSection() ?>

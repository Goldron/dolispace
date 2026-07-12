<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<?php
$company ??= [];
$val = fn(string $key) => esc((string)($company[$key] ?? ''));
?>

<div class="mb-8 flex flex-col gap-y-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-gray-900"><?= esc(lang('Dashboard.myAccountTitle')) ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?= esc(lang('Dashboard.crmInfo')) ?></p>
    </div>
    <?php if (! empty($company)): ?>
        <div class="flex items-center gap-x-2 self-end sm:self-auto">
            <button type="button" id="btn-cancel" class="hidden py-2 px-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <?= esc(lang('Dashboard.cancel')) ?>
            </button>
            <button type="button" id="btn-edit"
                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                </svg>
                <?= esc(lang('Auth.edit')) ?>
            </button>
            <button type="submit" form="form-company" id="btn-save"
                class="hidden py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                </svg>
                <?= esc(lang('Dashboard.save')) ?>
            </button>
        </div>
    <?php endif ?>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<?php if (empty($company)): ?>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
        <p class="text-sm text-gray-400"><?= esc(lang('Dashboard.unableToLoadCompany')) ?></p>
    </div>
<?php else: ?>

    <form id="form-company" action="<?= site_url('dashboard/company') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-6">

            <!-- Identité -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.identity')) ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.companyName')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('name') ?: '—' ?></p>
                        <input type="text" name="name" value="<?= $val('name') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.commercialName')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('name_alias') ?: '—' ?></p>
                        <input type="text" name="name_alias" value="<?= $val('name_alias') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.customerCode')) ?></p>
                        <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('code_client') ?: '—' ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.customerSince')) ?></p>
                        <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= ! empty($company['date_creation']) ? date('d/m/Y', (int)$company['date_creation']) : '—' ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.discount')) ?></p>
                        <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('remise_percent') ? $val('remise_percent') . ' %' : '—' ?></p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            <!-- Coordonnées -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.contactDetails')) ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">E-mail</p>
                        <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('email') ?: '—' ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.website')) ?></p>
                        <?php $url = (string)($company['url'] ?? ''); ?>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                            <?= $url ? '<a href="' . esc($url) . '" target="_blank" class="text-blue-600 hover:underline">' . esc(preg_replace('#^https?://#', '', $url)) . '</a>' : '<span class="text-gray-900">—</span>' ?>
                        </p>
                        <div class="field-edit hidden flex rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-blue-500 overflow-hidden">
                            <span class="inline-flex items-center px-3 bg-gray-50 border-r border-gray-300 text-xs text-gray-400 shrink-0">https://</span>
                            <input type="text" name="url" value="<?= esc(preg_replace('#^https?://#', '', $url)) ?>"
                                class="flex-1 px-3 py-2 text-sm focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.phone')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('phone') ?: '—' ?></p>
                        <input type="text" name="phone" value="<?= $val('phone') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.mobile')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('phone_mobile') ?: '—' ?></p>
                        <input type="text" name="phone_mobile" value="<?= $val('phone_mobile') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            <!-- Adresse -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.address')) ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.address')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('address') ?: '—' ?></p>
                        <input type="text" name="address" value="<?= $val('address') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.zip')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('zip') ?: '—' ?></p>
                        <input type="text" name="zip" value="<?= $val('zip') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.town')) ?></p>
                        <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('town') ?: '—' ?></p>
                        <input type="text" name="town" value="<?= $val('town') ?>" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.country')) ?></p>
                        <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('country_code') ?: '—' ?></p>
                    </div>
                </div>
            </div>

            <?php if (cfg('vat_field_enabled', true)): ?>
            <div class="border-t border-gray-100"></div>

            <!-- Fiscal -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.fiscal')) ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.vatNumber')) ?></p>
                        <div class="flex items-center gap-x-2">
                            <p class="flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= $val('tva_intra') ?: '—' ?></p>
                            <button type="button" id="btn-tva-edit"
                                class="shrink-0 size-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 transition">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>

        </div>

    </form>

    <?php if (cfg('vat_field_enabled', true)): ?>
    <!-- Modal TVA -->
    <div id="modal-tva" class="hidden fixed inset-0 z-[90] overflow-y-auto">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" id="modal-tva-backdrop"></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-200">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900"><?= esc(lang('Dashboard.editVatModalTitle')) ?></h3>
                    <button type="button" id="modal-tva-close" class="size-8 inline-flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Étape 1 : saisie -->
                <div id="tva-step-input" class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.vatNumber')) ?></label>
                        <input type="text" id="tva-input" placeholder="FR12345678901"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    </div>
                    <div id="tva-error" class="hidden flex items-start gap-x-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm text-red-700">
                        <svg class="size-4 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/>
                        </svg>
                        <span id="tva-error-msg"></span>
                    </div>
                    <div class="flex justify-end gap-x-2">
                        <button type="button" id="modal-tva-cancel" class="py-2 px-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition"><?= esc(lang('Dashboard.cancel')) ?></button>
                        <button type="button" id="btn-tva-verify"
                            class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                            <span id="tva-verify-label"><?= esc(lang('Dashboard.verify')) ?></span>
                            <svg id="tva-spinner" class="hidden animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Étape 2 : confirmation VIES -->
                <div id="tva-step-confirm" class="hidden p-5 space-y-4">
                    <div class="flex items-start gap-x-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                        <svg class="size-4 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <span><?= esc(lang('Dashboard.vatValidVies')) ?></span>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 space-y-1">
                        <p class="text-xs text-gray-400"><?= esc(lang('Dashboard.number')) ?></p>
                        <p id="vies-vat" class="text-sm font-medium text-gray-900"></p>
                        <div id="vies-name-row" class="hidden pt-1">
                            <p class="text-xs text-gray-400"><?= esc(lang('Dashboard.companyName')) ?></p>
                            <p id="vies-name" class="text-sm text-gray-900"></p>
                        </div>
                        <div id="vies-address-row" class="hidden pt-1">
                            <p class="text-xs text-gray-400"><?= esc(lang('Dashboard.address')) ?></p>
                            <p id="vies-address" class="text-sm text-gray-900 whitespace-pre-line"></p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400"><?= esc(lang('Dashboard.saveThisVat')) ?></p>
                    <form action="<?= site_url('dashboard/company/update-tva') ?>" method="POST" id="form-tva-confirm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tva_intra" id="tva-confirm-value">
                        <input type="hidden" name="name" id="tva-confirm-name">
                        <div class="flex flex-col sm:flex-row justify-end gap-x-2 gap-y-2">
                            <button type="button" id="btn-tva-back" class="py-2 px-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition"><?= esc(lang('Dashboard.back')) ?></button>
                            <button type="submit" id="btn-tva-confirm"
                                class="py-2 px-4 text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                                <?= esc(lang('Dashboard.confirm')) ?>
                            </button>
                            <button type="submit" id="btn-tva-confirm-name"
                                class="hidden py-2 px-4 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                <?= esc(lang('Dashboard.confirmAndUpdateName')) ?>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <?php endif ?>

    <script>
        const i18n = {
            enterVat: <?= json_encode(lang('Dashboard.enterVatNumber')) ?>,
            verifying: <?= json_encode(lang('Dashboard.verifying')) ?>,
            verify: <?= json_encode(lang('Dashboard.verify')) ?>,
            networkError: <?= json_encode(lang('Dashboard.networkError')) ?>,
        };

        const btnEdit   = document.getElementById('btn-edit');
        const btnSave   = document.getElementById('btn-save');
        const btnCancel = document.getElementById('btn-cancel');
        const views     = document.querySelectorAll('.field-view');
        const edits     = document.querySelectorAll('.field-edit');

        function enterEdit() {
            views.forEach(el => el.classList.add('hidden'));
            edits.forEach(el => el.classList.remove('hidden'));
            btnEdit.classList.add('hidden');
            btnSave.classList.remove('hidden');
            btnCancel.classList.remove('hidden');
        }

        function exitEdit() {
            edits.forEach(el => el.classList.add('hidden'));
            views.forEach(el => el.classList.remove('hidden'));
            btnSave.classList.add('hidden');
            btnCancel.classList.add('hidden');
            btnEdit.classList.remove('hidden');
        }

        btnEdit.addEventListener('click', enterEdit);
        btnCancel.addEventListener('click', exitEdit);

        <?php if (cfg('vat_field_enabled', true)): ?>
        // --- Modal TVA ---
        const modalTva        = document.getElementById('modal-tva');
        const tvaStepInput    = document.getElementById('tva-step-input');
        const tvaStepConfirm  = document.getElementById('tva-step-confirm');
        const tvaInput        = document.getElementById('tva-input');
        const tvaError        = document.getElementById('tva-error');
        const tvaErrorMsg     = document.getElementById('tva-error-msg');
        const tvaSpinner      = document.getElementById('tva-spinner');
        const tvaVerifyLabel  = document.getElementById('tva-verify-label');
        const validateUrl     = '<?= site_url('dashboard/company/validate-tva') ?>';

        function openTvaModal() {
            tvaInput.value = '';
            tvaError.classList.add('hidden');
            tvaStepInput.classList.remove('hidden');
            tvaStepConfirm.classList.add('hidden');
            modalTva.classList.remove('hidden');
            setTimeout(() => tvaInput.focus(), 50);
        }

        function closeTvaModal() {
            modalTva.classList.add('hidden');
        }

        function showTvaError(msg) {
            tvaErrorMsg.textContent = msg;
            tvaError.classList.remove('hidden');
        }

        function setVerifyLoading(loading) {
            tvaSpinner.classList.toggle('hidden', !loading);
            tvaVerifyLabel.textContent = loading ? i18n.verifying : i18n.verify;
            document.getElementById('btn-tva-verify').disabled = loading;
        }

        document.getElementById('btn-tva-edit').addEventListener('click', openTvaModal);
        document.getElementById('modal-tva-close').addEventListener('click', closeTvaModal);
        document.getElementById('modal-tva-cancel').addEventListener('click', closeTvaModal);
        document.getElementById('modal-tva-backdrop').addEventListener('click', closeTvaModal);

        document.getElementById('btn-tva-back').addEventListener('click', () => {
            tvaStepConfirm.classList.add('hidden');
            tvaStepInput.classList.remove('hidden');
        });

        // "Confirmer" seul : vide le champ name avant soumission
        document.getElementById('btn-tva-confirm').addEventListener('click', () => {
            document.getElementById('tva-confirm-name').value = '';
        });

        document.getElementById('btn-tva-verify').addEventListener('click', async () => {
            const tva = tvaInput.value.trim().toUpperCase();
            tvaError.classList.add('hidden');

            if (! tva) {
                showTvaError(i18n.enterVat);
                return;
            }

            setVerifyLoading(true);

            try {
                const res    = await fetch(validateUrl + '?tva=' + encodeURIComponent(tva));
                const result = await res.json();

                if (result.valid) {
                    document.getElementById('vies-vat').textContent = result.vat_number;
                    document.getElementById('tva-confirm-value').value = result.vat_number;

                    const nameRow = document.getElementById('vies-name-row');
                    if (result.name) {
                        document.getElementById('vies-name').textContent = result.name;
                        nameRow.classList.remove('hidden');
                    } else {
                        nameRow.classList.add('hidden');
                    }

                    const addressRow = document.getElementById('vies-address-row');
                    if (result.address) {
                        document.getElementById('vies-address').textContent = result.address;
                        addressRow.classList.remove('hidden');
                    } else {
                        addressRow.classList.add('hidden');
                    }

                    document.getElementById('tva-confirm-name').value = result.name ?? '';
                    const btnConfirmName = document.getElementById('btn-tva-confirm-name');
                    btnConfirmName.classList.toggle('hidden', ! result.name);

                    tvaStepInput.classList.add('hidden');
                    tvaStepConfirm.classList.remove('hidden');
                } else {
                    showTvaError(result.error);
                }
            } catch (e) {
                showTvaError(i18n.networkError);
            }

            setVerifyLoading(false);
        });

        tvaInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') document.getElementById('btn-tva-verify').click();
        });
        <?php endif ?>
    </script>

<?php endif ?>

<?= $this->endSection() ?>

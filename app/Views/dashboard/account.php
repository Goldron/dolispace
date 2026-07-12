<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<?php $user ??= []; ?>

<div class="mb-8 flex flex-col gap-y-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-gray-900"><?= esc(lang('Dashboard.myProfileTitle')) ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?= esc(lang('Dashboard.profileSettings')) ?></p>
    </div>
    <div class="flex items-center gap-x-2 self-end sm:self-auto">
        <button type="button" id="btn-cancel" class="hidden py-2 px-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition"><?= esc(lang('Dashboard.cancel')) ?></button>
        <button type="button" id="btn-edit"
            class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
            </svg>
            <?= esc(lang('Auth.edit')) ?>
        </button>
        <button type="submit" form="form-profile" id="btn-save"
            class="hidden py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
            </svg>
            <?= esc(lang('Dashboard.save')) ?>
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/>
        </svg>
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<!-- Notice : changement d'email en attente -->
<?php if (! empty($user['email_pending'])): ?>
    <div class="mb-4 flex items-start justify-between gap-x-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
        <div class="flex items-start gap-x-3">
            <svg class="size-4 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
            <span><?= lang('Dashboard.emailChangePendingNotice', [esc((string)$user['email_pending'])]) ?></span>
        </div>
        <form action="<?= site_url('dashboard/account/cancel-email') ?>" method="POST" class="shrink-0">
            <?= csrf_field() ?>
            <button type="submit" class="text-amber-600 hover:text-amber-800 underline whitespace-nowrap text-xs transition"><?= esc(lang('Dashboard.cancel')) ?></button>
        </form>
    </div>
<?php endif ?>

<!-- Notice : changement de mot de passe en attente -->
<?php if (! empty($user['password_pending_token'])): ?>
    <div class="mb-4 flex items-start justify-between gap-x-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
        <div class="flex items-start gap-x-3">
            <svg class="size-4 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
            <span><?= lang('Dashboard.passwordChangePendingNotice', [esc((string)($user['email'] ?? ''))]) ?></span>
        </div>
        <form action="<?= site_url('dashboard/account/cancel-password') ?>" method="POST" class="shrink-0">
            <?= csrf_field() ?>
            <button type="submit" class="text-amber-600 hover:text-amber-800 underline whitespace-nowrap text-xs transition"><?= esc(lang('Dashboard.cancel')) ?></button>
        </form>
    </div>
<?php endif ?>

<form id="form-profile" action="<?= site_url('dashboard/account/update-profile') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-6">

        <!-- Identité -->
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.identity')) ?></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.firstName')) ?></p>
                    <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= esc((string)($user['first_name'] ?? '')) ?: '—' ?></p>
                    <input type="text" name="first_name" value="<?= esc((string)($user['first_name'] ?? '')) ?>"
                           class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.lastName')) ?></p>
                    <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= esc((string)($user['name'] ?? '')) ?: '—' ?></p>
                    <input type="text" name="name" value="<?= esc((string)($user['name'] ?? '')) ?>"
                           class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        <!-- Adresse email -->
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.emailAddress')) ?></p>
            <?php if (! empty($user['email_pending'])): ?>
                <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 italic"><?= esc(lang('Dashboard.pendingConfirmation')) ?></p>
            <?php else: ?>
                <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= esc((string)($user['email'] ?? '')) ?></p>
                <input type="email" name="email" value="<?= esc((string)($user['email'] ?? '')) ?>"
                       class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="field-edit hidden mt-1 text-xs text-gray-400"><?= esc(lang('Dashboard.modifyToChangeEmail')) ?></p>
            <?php endif ?>
        </div>

        <div class="border-t border-gray-100"></div>

        <!-- Mot de passe -->
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.password')) ?></p>
            <?php if (! empty($user['password_pending_token'])): ?>
                <p class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 italic"><?= esc(lang('Dashboard.pendingConfirmation')) ?></p>
            <?php else: ?>
                <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 tracking-widest">••••••••</p>
                <div class="field-edit hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.newPassword')) ?></label>
                        <input type="password" name="new_password" placeholder="••••••••" minlength="8" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.confirmPassword')) ?></label>
                        <input type="password" name="confirm_password" placeholder="••••••••" minlength="8" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <p class="field-edit hidden mt-1 text-xs text-gray-400"><?= esc(lang('Dashboard.leaveBlankToKeepPassword')) ?></p>
            <?php endif ?>
        </div>

        <div class="border-t border-gray-100"></div>

        <!-- Langue -->
        <?php
        $localeLabels = [
            ''   => lang('Dashboard.languageAuto'),
            'fr' => lang('Dashboard.languageFr'),
            'en' => lang('Dashboard.languageEn'),
        ];
        $currentLocale = (string)($user['locale'] ?? '');
        ?>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Dashboard.language')) ?></p>
            <p class="field-view w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900"><?= esc($localeLabels[$currentLocale] ?? $localeLabels['']) ?></p>
            <select name="locale" class="field-edit hidden w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php foreach ($localeLabels as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $currentLocale === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>

    </div>

</form>

<script>
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
</script>

<?= $this->endSection() ?>

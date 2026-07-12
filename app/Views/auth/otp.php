<?= $this->extend('auth/layout_split') ?>
<?= $this->section('content') ?>
<?php $email ??= ''; ?>

<h2 class="text-lg font-semibold text-gray-800 mb-1"><?= esc(lang('Auth.otpTitle')) ?></h2>
<p class="text-sm text-gray-500 mb-6">
    <?= esc(lang('Auth.otpSentToPrefix')) ?> <span class="font-medium text-gray-700"><?= esc($email) ?></span>. <?= lang('Auth.otpValidMinutes', [(string) ((int) cfg('otp_ttl', 900) / 60)]) ?>
</p>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-5 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/>
        </svg>
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<?= form_open('auth/verify-otp') ?>

    <div class="mb-5">
        <label for="otp" class="block text-sm font-medium text-gray-700 mb-1.5"><?= esc(lang('Auth.otpCodeLabel')) ?></label>
        <input
            type="text"
            id="otp"
            name="otp"
            inputmode="numeric"
            maxlength="6"
            placeholder="000000"
            required
            autofocus
            autocomplete="one-time-code"
            class="py-2.5 px-3 block w-full border border-gray-200 rounded-lg text-sm tracking-widest text-center font-mono focus:border-blue-500 focus:ring-blue-500"
        >
    </div>

    <button type="submit" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer">
        <?= esc(lang('Auth.signIn')) ?>
    </button>

<?= form_close() ?>

<div class="mt-5 text-center">
    <a href="<?= site_url('auth/password') ?>" class="text-sm text-gray-400 hover:text-gray-600 underline transition">
        <?= esc(lang('Auth.backToPassword')) ?>
    </a>
</div>

<?= $this->endSection() ?>

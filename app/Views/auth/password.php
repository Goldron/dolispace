<?= $this->extend('auth/layout_split') ?>
<?= $this->section('content') ?>
<?php $email ??= ''; ?>

<h2 class="text-lg font-semibold text-gray-800 mb-1">Connexion</h2>
<p class="text-sm text-gray-500 mb-6">
    Connecté en tant que <span class="font-medium text-gray-700"><?= esc($email) ?></span>.
    <a href="<?= site_url('auth') ?>" class="text-blue-600 hover:underline ml-1">Modifier</a>
</p>

<?= form_open('auth/login') ?>

    <div class="mb-5">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            autofocus
            autocomplete="current-password"
            class="py-2.5 px-3 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
        >
    </div>

    <button type="submit" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 transition cursor-pointer">
        Se connecter
    </button>

<?= form_close() ?>

<div class="relative my-5">
    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
    <div class="relative flex justify-center text-xs"><span class="bg-white px-3 text-gray-400">ou</span></div>
</div>

<?= form_open('auth/request-otp') ?>
    <button type="submit" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition cursor-pointer">
        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
        </svg>
        Recevoir un code par email
    </button>
<?= form_close() ?>

<?= $this->endSection() ?>

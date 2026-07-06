<?= $this->extend('auth/layout_split') ?>
<?= $this->section('content') ?>
<?php $email ??= ''; ?>

<div class="text-center">
    <div class="flex justify-center mb-4">
        <div class="flex items-center justify-center size-12 rounded-full bg-blue-100">
            <svg class="size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-2">Vérifiez votre boîte mail</h2>
    <p class="text-sm text-gray-500 mb-1">
        Un lien d'accès a été envoyé à
    </p>
    <p class="text-sm font-medium text-gray-800 mb-4"><?= esc($email) ?></p>
    <p class="text-sm text-gray-400">
        Cliquez sur le lien contenu dans l'email pour créer votre compte. Le lien est valable 24&nbsp;heures.
    </p>
</div>

<?= $this->endSection() ?>

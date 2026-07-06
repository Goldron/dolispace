<?= $this->extend('auth/layout_split') ?>
<?= $this->section('content') ?>

<div class="text-center">
    <div class="flex justify-center mb-4">
        <div class="flex items-center justify-center size-12 rounded-full bg-gray-100">
            <svg class="size-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
            </svg>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-2">Accès réservé</h2>
    <p class="text-sm text-gray-500">
        <?= esc($message ?? "L'espace client est réservé à nos clients.") ?>
    </p>

    <a href="<?= site_url('auth') ?>" class="mt-6 inline-flex items-center gap-x-1.5 text-sm text-blue-600 hover:underline">
        <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        Retour
    </a>
</div>

<?= $this->endSection() ?>

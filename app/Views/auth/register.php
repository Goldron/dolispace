<?= $this->extend('auth/layout_split') ?>
<?= $this->section('content') ?>
<?php $email ??= ''; $token ??= '';?>

<h2 class="text-lg font-semibold text-gray-800 mb-1"><?= esc(lang('Auth.createYourAccount')) ?></h2>
<p class="text-sm text-gray-500 mb-6"><?= esc(lang('Auth.completeInfoBelow')) ?></p>

<?= form_open('auth/register') ?>
    <input type="hidden" name="token" value="<?= esc($token) ?>">

    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5"><?= esc(lang('Auth.emailAddress')) ?></label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= esc($email) ?>"
            readonly
            class="py-2.5 px-3 block w-full border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
        >
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5"><?= esc(lang('Auth.firstName')) ?></label>
            <input
                type="text"
                id="first_name"
                name="first_name"
                value="<?= esc(old('first_name')) ?>"
                required
                autofocus
                autocomplete="given-name"
                class="py-2.5 px-3 block w-full border <?= isset($validation) && $validation->hasError('first_name') ? 'border-red-400' : 'border-gray-200' ?> rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <?php if (isset($validation) && $validation->hasError('first_name')): ?>
                <p class="mt-1 text-xs text-red-600"><?= esc($validation->getError('first_name')) ?></p>
            <?php endif ?>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5"><?= esc(lang('Auth.lastName')) ?></label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= esc(old('name')) ?>"
                required
                autocomplete="family-name"
                class="py-2.5 px-3 block w-full border <?= isset($validation) && $validation->hasError('name') ? 'border-red-400' : 'border-gray-200' ?> rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <?php if (isset($validation) && $validation->hasError('name')): ?>
                <p class="mt-1 text-xs text-red-600"><?= esc($validation->getError('name')) ?></p>
            <?php endif ?>
        </div>
    </div>

    <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5"><?= esc(lang('Auth.passwordLabel')) ?></label>
        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="new-password"
            class="py-2.5 px-3 block w-full border <?= isset($validation) && $validation->hasError('password') ? 'border-red-400' : 'border-gray-200' ?> rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
        >
        <?php if (isset($validation) && $validation->hasError('password')): ?>
            <p class="mt-1 text-xs text-red-600"><?= esc($validation->getError('password')) ?></p>
        <?php else: ?>
            <p class="mt-1 text-xs text-gray-400"><?= esc(lang('Auth.minPasswordLength')) ?></p>
        <?php endif ?>
    </div>

    <button type="submit" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 transition cursor-pointer">
        <?= esc(lang('Auth.createMyAccount')) ?>
    </button>

<?= form_close() ?>

<?= $this->endSection() ?>

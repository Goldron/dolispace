<?= $this->extend('auth/layout_split') ?>
<?= $this->section('content') ?>

<h2 class="text-lg font-semibold text-gray-800 mb-1"><?= esc(lang('Auth.loginTitle')) ?></h2>
<p class="text-sm text-gray-500 mb-6"><?= esc(lang('Auth.emailPrompt')) ?></p>

<?= form_open('auth/check-email') ?>

    <div class="mb-5">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5"><?= esc(lang('Auth.emailAddress')) ?></label>
        <input
            type="email"
            id="email"
            name="email"
            required
            autofocus
            autocomplete="email"
            value="<?= esc(old('email', '')) ?>"
            class="py-2.5 px-3 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
        >
    </div>

    <button type="submit" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 transition cursor-pointer">
        <?= esc(lang('Auth.loginOrRegister')) ?>
    </button>

<?= form_close() ?>

<?= $this->endSection() ?>

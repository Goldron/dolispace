<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array<string, list<array{key: string, value: string}>> $envVars */
/** @var bool        $clamEnabled */
/** @var bool        $clamAvailable */
/** @var string|null $clamVersion */
/** @var array{host: string, port: string, user: string, from: string, active: bool} $smtpStatus */
/** @var string      $apiUrl */
/** @var array<string, string|int>|null $dolibarrInfo */
/** @var array<string, array{ok: bool, error: string|null}> $dolibarr */
/** @var array<string, bool> $dolibarrModules */
?>
<?php
function badge(bool $ok, ?string $yes = null, ?string $no = null): string {
    $yes ??= lang('Admin.ok');
    $no  ??= lang('Admin.errorLabel');
    return $ok
        ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">' . $yes . '</span>'
        : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">'   . $no  . '</span>';
}
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold text-gray-900"><?= esc(lang('Admin.systemStatus')) ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?= esc(lang('Admin.diagnosticsSubtitle')) ?></p>
    </div>
    <span class="text-xs font-mono text-gray-400">v<?= APP_VERSION ?></span>
</div>

<!-- Services -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

    <!-- ClamAV -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Admin.clamavAntivirus')) ?></p>
        <dl class="space-y-2">
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-500"><?= esc(lang('Admin.availableOnServer')) ?></dt>
                <dd><?= badge($clamAvailable, lang('Admin.yes'), lang('Admin.no')) ?></dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-500"><?= esc(lang('Admin.enabledInApp')) ?></dt>
                <dd><?= badge($clamEnabled, lang('Admin.yes'), lang('Admin.no')) ?></dd>
            </div>
        </dl>
        <?php if ($clamVersion): ?>
            <p class="text-xs text-gray-400 mt-3"><?= esc($clamVersion) ?></p>
        <?php endif ?>
    </div>

    <!-- SMTP -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">SMTP</p>
        <div class="flex items-center gap-x-2 mb-3">
            <?= badge($smtpStatus['active'], lang('Admin.configured'), lang('Admin.notConfigured')) ?>
        </div>
        <?php if ($smtpStatus['active']): ?>
            <dl class="text-xs text-gray-500 space-y-1">
                <div class="flex gap-x-2"><dt class="text-gray-400 w-10"><?= esc(lang('Admin.host')) ?></dt><dd><?= esc($smtpStatus['host']) ?>:<?= esc($smtpStatus['port']) ?></dd></div>
                <div class="flex gap-x-2"><dt class="text-gray-400 w-10">From</dt><dd><?= esc($smtpStatus['from'] ?? '') ?></dd></div>
            </dl>
        <?php endif ?>
    </div>

    <!-- Dolibarr API -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?= esc(lang('Admin.dolibarrApi')) ?></p>
        <?php $allOk = array_reduce($dolibarr, fn($c, $e) => $c && (bool)$e['ok'], true) ?>
        <div class="flex items-center gap-x-2 mb-3">
            <?= badge((bool)$allOk, lang('Admin.operational'), lang('Admin.degraded')) ?>
        </div>
        <p class="text-xs text-gray-400 truncate mb-3"><?= esc($apiUrl) ?></p>
        <?php if ($dolibarrInfo): ?>
            <dl class="text-xs text-gray-500 space-y-1">
                <div class="flex justify-between"><dt class="text-gray-400"><?= esc(lang('Admin.version')) ?></dt><dd><?= esc((string)($dolibarrInfo['dolibarr_version'] ?? '—')) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-400"><?= esc(lang('Admin.environment')) ?></dt><dd><?= esc((string)($dolibarrInfo['environment'] ?? '—')) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-400"><?= esc(lang('Admin.accessLocked')) ?></dt><dd><?= esc(($dolibarrInfo['access_locked'] ?? '1') === '0' ? lang('Admin.no') : lang('Admin.yes')) ?></dd></div>
            </dl>
        <?php endif ?>
    </div>

</div>

<!-- Endpoints Dolibarr -->
<div class="bg-white rounded-xl border border-gray-200 mb-8">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800"><?= esc(lang('Admin.dolibarrEndpoints')) ?></h2>
    </div>
    <ul class="divide-y divide-gray-100">
        <!-- Résultat du endpoint /status -->
        <li class="flex items-center justify-between px-5 py-3">
            <span class="text-sm font-mono text-gray-700">/status</span>
            <div class="flex items-center gap-x-3">
                <?php if (! $dolibarrInfo): ?>
                    <span class="text-xs text-red-500"><?= esc(lang('Admin.invalidOrUnreachableResponse')) ?></span>
                <?php endif ?>
                <?= badge($dolibarrInfo !== null) ?>
            </div>
        </li>
        <?php foreach ($dolibarr as $name => $result): ?>
            <li class="flex items-center justify-between px-5 py-3">
                <span class="text-sm font-mono text-gray-700">/<?= esc((string)$name) ?></span>
                <div class="flex items-center gap-x-3">
                    <?php if ($result['error']): ?>
                        <span class="text-xs text-red-500"><?= esc((string)$result['error']) ?></span>
                    <?php endif ?>
                    <?= badge((bool)$result['ok']) ?>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
</div>

<!-- Modules Dolibarr -->
<div class="bg-white rounded-xl border border-gray-200 mb-8">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800"><?= esc(lang('Admin.dolibarrModules')) ?></h2>
        <p class="mt-0.5 text-xs text-gray-400"><?= lang('Admin.modulesDetectedVia', ['<code class="font-mono">GET /setup/modules</code>']) ?></p>
    </div>
    <ul class="divide-y divide-gray-100">
        <?php foreach ($dolibarrModules as $moduleName => $moduleEnabled): ?>
            <li class="flex items-center justify-between px-5 py-3">
                <span class="text-sm font-mono text-gray-700"><?= esc((string)$moduleName) ?></span>
                <?= badge((bool)$moduleEnabled, lang('Admin.activated'), lang('Admin.notDetected')) ?>
            </li>
        <?php endforeach ?>
    </ul>
</div>

<!-- Variables .env -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800"><?= esc(lang('Admin.configVariables')) ?></h2>
        <p class="text-xs text-gray-400 mt-0.5"><?= esc(lang('Admin.sensitiveValuesHidden')) ?></p>
    </div>
    <?php foreach ($envVars as $section => $vars): ?>
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide"><?= esc($section) ?></p>
        </div>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($vars as $var): ?>
                <li class="flex items-center justify-between px-5 py-2.5">
                    <span class="text-sm font-mono text-gray-600"><?= esc((string)$var['key']) ?></span>
                    <span class="text-sm text-gray-800 font-mono <?= $var['value'] === '••••••••' ? 'text-gray-300' : '' ?>">
                        <?= esc((string)$var['value']) ?>
                    </span>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endforeach ?>
</div>

<?= $this->endSection() ?>

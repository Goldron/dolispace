<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>

<?php
$files         ??= [];
$proposals     ??= [];
$orders        ??= [];
$hasRefs         = ! empty($proposals) || ! empty($orders);
$allowDelete     = (bool) cfg('allow_upload_delete', false);

$formatSize = function(int $bytes): string {
    if ($bytes >= 1_048_576) return number_format($bytes / 1_048_576, 1) . ' ' . lang('Dashboard.unitMB');
    if ($bytes >= 1_024)     return number_format($bytes / 1_024, 1) . ' ' . lang('Dashboard.unitKB');
    return $bytes . ' ' . lang('Dashboard.unitB');
};

$fileIcon = function(string $name): string {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    return match(true) {
        in_array($ext, ['jpg','jpeg','png','gif','webp']) =>
            '<svg class="size-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>',
        in_array($ext, ['xls','xlsx','csv']) =>
            '<svg class="size-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125v-5.25a1.125 1.125 0 0 1 1.125-1.125h13.5A1.125 1.125 0 0 1 21 13.25V18m-18 .125h18M6 9.75h12M6 12.75h12"/></svg>',
        in_array($ext, ['doc','docx','txt']) =>
            '<svg class="size-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>',
        $ext === 'pdf' =>
            '<svg class="size-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>',
        $ext === 'zip' =>
            '<svg class="size-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>',
        default =>
            '<svg class="size-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 12h6m-6 3h6m-6 3h3M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>',
    };
};
?>

<div class="mb-8">
    <h1 class="text-xl font-semibold text-gray-900"><?= esc(lang('Dashboard.myFilesTitle')) ?></h1>
    <p class="mt-1 text-sm text-gray-500"><?= esc(lang('Dashboard.depositAndViewDocs')) ?></p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 flex items-center gap-x-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/>
        </svg>
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif ?>

<!-- Zone d'envoi -->
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <form action="<?= site_url('dashboard/uploads') ?>" method="POST" enctype="multipart/form-data" id="upload-form">
        <?= csrf_field() ?>
        <label for="file-input"
            id="drop-zone"
            class="flex flex-col items-center justify-center gap-y-3 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
            <svg class="size-10 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-gray-700" id="drop-label"><?= esc(lang('Dashboard.clickOrDropFile')) ?></p>
                <p class="text-xs text-gray-400 mt-1"><?= esc(lang('Dashboard.maxFileSize', [(string) (int) cfg('max_upload_size', 10)])) ?></p>
            </div>
            <input type="file" name="file" id="file-input" class="sr-only">
        </label>

        <?php if ($hasRefs): ?>
        <div class="mt-4">
            <label for="ref-select" class="block text-xs font-medium text-gray-600 mb-1"><?= esc(lang('Dashboard.associateWith')) ?></label>
            <select name="ref" id="ref-select"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value=""><?= esc(lang('Dashboard.noAssociation')) ?></option>
                <?php if (! empty($proposals)): ?>
                <optgroup label="<?= esc(lang('Dashboard.proposalsNav')) ?>">
                    <?php foreach ($proposals as $p): ?>
                    <option value="proposal:<?= (int) $p['id'] ?>"><?= esc($p['ref']) ?></option>
                    <?php endforeach ?>
                </optgroup>
                <?php endif ?>
                <?php if (! empty($orders)): ?>
                <optgroup label="<?= esc(lang('Dashboard.ordersNav')) ?>">
                    <?php foreach ($orders as $o): ?>
                    <option value="order:<?= (int) $o['id'] ?>"><?= esc($o['ref']) ?></option>
                    <?php endforeach ?>
                </optgroup>
                <?php endif ?>
            </select>
        </div>
        <?php endif ?>

        <div id="file-preview" class="hidden mt-4 flex flex-col gap-y-3 sm:flex-row sm:items-center sm:justify-between rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
            <div class="flex items-center gap-x-3 min-w-0">
                <svg class="size-5 text-blue-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                <span id="file-name" class="text-sm text-gray-700 truncate"></span>
                <span id="file-size" class="text-xs text-gray-400 shrink-0"></span>
            </div>
            <button type="submit"
                class="w-full sm:w-auto shrink-0 py-1.5 px-4 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <?= esc(lang('Dashboard.send')) ?>
            </button>
        </div>
    </form>
</div>

<!-- Liste des fichiers -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800"><?= esc(lang('Dashboard.filesSent')) ?></h2>
        <span class="text-xs text-gray-400"><?= count($files) ?> <?= esc(count($files) > 1 ? lang('Dashboard.files') : lang('Dashboard.file')) ?></span>
    </div>

    <?php if (empty($files)): ?>
        <div class="px-5 py-16 text-center text-sm text-gray-400"><?= esc(lang('Dashboard.noFilesSentYet')) ?></div>
    <?php else: ?>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($files as $file): ?>
                <li class="flex items-center gap-x-4 px-5 py-3">
                    <div class="shrink-0">
                        <?= $fileIcon(esc($file['original_name'])) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-x-2">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= esc($file['original_name']) ?></p>
                            <?php if (! empty($file['ref_folder'])): ?>
                            <span class="shrink-0 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset
                                <?= ($file['ref_type'] ?? '') === 'proposal' ? 'bg-blue-50 text-blue-700 ring-blue-200' : 'bg-amber-50 text-amber-700 ring-amber-200' ?>">
                                <?= esc(strtoupper($file['ref_folder'])) ?>
                            </span>
                            <?php endif ?>
                        </div>
                        <p class="text-xs text-gray-400">
                            <?= $formatSize((int)$file['size']) ?>
                            &middot;
                            <?= date('d/m/Y H:i', strtotime($file['created_at'])) ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-x-2 shrink-0">
                        <a href="<?= site_url('dashboard/uploads/' . $file['id'] . '/download') ?>"
                            class="size-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                            title="<?= esc(lang('Dashboard.download')) ?>">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                            </svg>
                        </a>
                        <?php if ($allowDelete): ?>
                        <form action="<?= site_url('dashboard/uploads/' . $file['id'] . '/delete') ?>" method="POST"
                            onsubmit="return confirm(<?= esc(json_encode(lang('Dashboard.confirmDeleteFile', [$file['original_name']])), 'attr') ?>)">
                            <?= csrf_field() ?>
                            <button type="submit"
                                class="size-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                title="<?= esc(lang('Dashboard.delete')) ?>">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="size-8 inline-flex items-center justify-center rounded-lg text-gray-200 cursor-not-allowed" title="<?= esc(lang('Dashboard.deletionDisabled')) ?>">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </span>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>

<script>
    const i18n = {
        changeFile: <?= json_encode(lang('Dashboard.changeFile')) ?>,
        unitMB: <?= json_encode(lang('Dashboard.unitMB')) ?>,
        unitKB: <?= json_encode(lang('Dashboard.unitKB')) ?>,
        unitB: <?= json_encode(lang('Dashboard.unitB')) ?>,
    };

    const fileInput  = document.getElementById('file-input');
    const dropZone   = document.getElementById('drop-zone');
    const filePreview = document.getElementById('file-preview');
    const fileName   = document.getElementById('file-name');
    const fileSize   = document.getElementById('file-size');
    const dropLabel  = document.getElementById('drop-label');

    function formatBytes(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' ' + i18n.unitMB;
        if (bytes >= 1024)    return (bytes / 1024).toFixed(1) + ' ' + i18n.unitKB;
        return bytes + ' ' + i18n.unitB;
    }

    function showPreview(file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatBytes(file.size);
        filePreview.classList.remove('hidden');
        dropLabel.textContent = i18n.changeFile;
    }

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) showPreview(fileInput.files[0]);
    });

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        const dt = e.dataTransfer;
        if (dt.files[0]) {
            const transfer = new DataTransfer();
            transfer.items.add(dt.files[0]);
            fileInput.files = transfer.files;
            showPreview(dt.files[0]);
        }
    });
</script>

<?= $this->endSection() ?>

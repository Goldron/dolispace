<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= esc(lang('Admin.smtpTestTitle')) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 40px 16px; }
        .container { max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 40px; border: 1px solid #e5e7eb; }
        .logo { text-align: center; margin-bottom: 32px; }
        h1 { font-size: 20px; font-weight: 600; color: #111827; margin: 0 0 12px; }
        p { font-size: 14px; color: #6b7280; line-height: 1.6; margin: 0 0 16px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; margin-bottom: 20px; }
        .footer { font-size: 12px; color: #9ca3af; margin-top: 32px; border-top: 1px solid #f3f4f6; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $logo = logo_for_email();
        if ($logo): ?>
            <div class="logo"><img src="<?= $logo ?>" alt="<?= esc(cfg('company_name', '')) ?>" style="width:140px;height:auto;display:block;margin:0 auto;"></div>
        <?php else: ?>
            <div class="logo" style="font-size:18px;font-weight:bold;color:#111827;"><?= esc(cfg('company_name', '')) ?></div>
        <?php endif ?>

        <span class="badge"><?= esc(lang('Admin.smtpTestTitle')) ?></span>
        <h1><?= esc(lang('Admin.emailConfigOperational')) ?></h1>

        <p><?= esc(lang('Admin.helloComma')) ?></p>
        <p><?= esc(lang('Admin.smtpConfigWorks')) ?></p>
        <p><?= esc(lang('Admin.recipientTested')) ?> <strong><?= esc($to) ?></strong></p>

        <div class="footer">
            <p><?= esc(lang('Admin.sentFromAdminInterface')) ?> <?= esc(cfg('company_name', '')) ?></p>
        </div>
    </div>
</body>
</html>

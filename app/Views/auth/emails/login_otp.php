<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(lang('Emails.otpEmailTitle')) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 40px 16px; }
        .container { max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 40px; border: 1px solid #e5e7eb; }
        .logo { text-align: center; margin-bottom: 32px; }
        h1 { font-size: 20px; font-weight: 600; color: #111827; margin: 0 0 12px; }
        p { font-size: 14px; color: #6b7280; line-height: 1.6; margin: 0 0 20px; }
        .otp { display: block; text-align: center; font-size: 36px; font-weight: 700; letter-spacing: 0.25em; color: #111827; background: #f3f4f6; border-radius: 8px; padding: 20px; margin: 24px 0; font-family: monospace; }
        .footer { font-size: 12px; color: #9ca3af; margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($logo): ?><div class="logo"><img src="<?= $logo ?>" alt="<?= esc(cfg('company_name', '')) ?>" style="width:140px;height:auto;display:block;margin:0 auto;"></div>
            <?php else: ?>
            <span><?= esc(cfg('company_name', '')) ?></span>
        <?php endif ?>
        <h1><?= esc(lang('Emails.otpEmailHeading')) ?></h1>

        <p><?= esc(lang('Emails.hello')) ?></p>
        <p><?= esc(lang('Emails.otpEmailIntro')) ?></p>
        <p><?= lang('Emails.otpEmailInstructions') ?></p>

        <span class="otp"><?= esc($otp) ?></span>

        <p><?= esc(lang('Emails.ignoreIfNotRequested')) ?></p>

        <div class="footer">
            <p><?= esc(lang('Emails.automatedEmailFooter')) ?></p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès à votre espace client</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 40px 16px; }
        .container { max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 40px; border: 1px solid #e5e7eb; }
        .logo { text-align: center; margin-bottom: 32px; font-size: 18px; font-weight: bold; color: #111827; }
        h1 { font-size: 20px; font-weight: 600; color: #111827; margin: 0 0 12px; }
        p { font-size: 14px; color: #6b7280; line-height: 1.6; margin: 0 0 20px; }
        .btn { display: block; text-align: center; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; margin: 24px 0; }
        .footer { font-size: 12px; color: #9ca3af; margin-top: 32px; }
        .url { font-size: 12px; color: #9ca3af; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($logo): ?><div class="logo"><img src="<?= $logo ?>" alt="<?= esc(cfg('company_name', '')) ?>" style="width:140px;height:auto;display:block;margin:0 auto;"></div>
            <?php else: ?>
            <span><?= esc(cfg('company_name', '')) ?></span>
        <?php endif ?>
        <h1>Validez votre accès</h1>

        <p>Bonjour,</p>
        <p>
            Nous avons reçu une demande d'accès à l'espace client pour l'adresse
            <strong><?= esc($email) ?></strong>.
        </p>
        <p>Cliquez sur le bouton ci-dessous pour créer votre compte. Ce lien est valable <strong>24&nbsp;heures</strong>.</p>

        <a href="<?= esc($link) ?>" class="btn">Créer mon compte</a>

        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>

        <div class="footer">
            <p>Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur&nbsp;:</p>
            <p class="url"><?= esc($link) ?></p>
        </div>
    </div>
</body>
</html>

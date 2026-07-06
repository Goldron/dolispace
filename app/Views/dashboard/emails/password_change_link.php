<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmer votre nouveau mot de passe</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 40px 16px; }
        .container { max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 40px; border: 1px solid #e5e7eb; }
        .logo { text-align: center; margin-bottom: 32px; }
        h1 { font-size: 20px; font-weight: 600; color: #111827; margin: 0 0 12px; }
        p { font-size: 14px; color: #6b7280; line-height: 1.6; margin: 0 0 20px; }
        .btn { display: block; text-align: center; background: #2563eb; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 14px 24px; border-radius: 8px; margin: 28px 0; }
        .link { font-size: 12px; color: #9ca3af; word-break: break-all; }
        .footer { font-size: 12px; color: #9ca3af; margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo"><?php if ($logo): ?><img src="<?= $logo ?>" alt="<?= esc(cfg('company_name', '')) ?>" style="width:140px;height:auto;display:block;margin:0 auto;"><?php endif ?></div>

        <h1>Confirmez votre nouveau mot de passe</h1>

        <p>Bonjour,</p>
        <p>Vous avez demandé à modifier votre mot de passe sur votre espace client.</p>
        <p>Cliquez sur le bouton ci-dessous pour confirmer ce changement. Le lien est valable <strong>1&nbsp;heure</strong>.</p>

        <a href="<?= esc($confirmUrl) ?>" class="btn">Confirmer mon nouveau mot de passe</a>

        <p>Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
        <p class="link"><?= esc($confirmUrl) ?></p>

        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email. Votre mot de passe actuel ne sera pas modifié.</p>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>

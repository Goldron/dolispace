<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f9fafb; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 48px 40px; max-width: 440px; width: 100%; text-align: center; }
        .code { font-size: 64px; font-weight: 700; color: #e5e7eb; line-height: 1; margin-bottom: 16px; }
        h1 { font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px; }
        p { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
        a { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 10px 24px; border-radius: 8px; }
        a:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">404</div>
        <h1>Page introuvable</h1>
        <p>
            <?php if (ENVIRONMENT !== 'production'): ?>
                <?= nl2br(esc($message)) ?>
            <?php else: ?>
                La page que vous recherchez n'existe pas ou a été déplacée.
            <?php endif ?>
        </p>
        <a href="/">Retour à l'accueil</a>
    </div>
</body>
</html>

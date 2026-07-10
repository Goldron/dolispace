# Déploiement sur un nouveau serveur

## Prérequis serveur

- PHP 8.2+ avec extensions : `intl`, `mbstring`, `sqlite3`, `curl`, `gd`, `fileinfo`
- Composer
- Node.js + npm (pour le build Vite)
- Nginx + PHP-FPM
- Certificat SSL (Let's Encrypt / certbot)

## 1. Cloner le projet

Le dépôt `Goldron/dolispace` est public — aucune authentification requise pour le cloner.

```bash
git clone https://github.com/Goldron/dolispace.git client
cd client
```

## 2. Dépendances

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

`--no-dev` exclut PHPUnit et les autres dépendances de test, inutiles en production.
Pour lancer les tests (en local ou en CI, avant déploiement) : `composer install` (sans `--no-dev`) puis `vendor/bin/phpunit`.

## 3. Configuration

```bash
cp env .env
```

Éditer `.env` :
- `CI_ENVIRONMENT = production`
- `app.baseURL` → domaine du nouveau serveur
- `app.admin_login` / `app.admin_password`
- `database.default.DBDriver = SQLite3`
- `database.default.database = database.db`
- `database.default.DBPrefix = coop_`

## 4. Base de données et permissions

```bash
chmod -R 775 writable
php spark migrate --all
php spark db:seed DatabaseSeeder
```

Puis se connecter à `/admin/config` pour renseigner les valeurs sensibles laissées vides par le seeder
(`dolibarr_api_token`, `smtp_user`, `smtp_pass`, `dolibarr_api_url` si différent).

Le seeder active aussi par défaut les toggles de fonctionnalités (`commande_enabled`, `propal_enabled`,
`facture_enabled`, `expedition_enabled`, `certificatsclients_enabled`) — à ajuster dans la carte
"Fonctionnalités" de `admin/config` selon les modules réellement activés côté Dolibarr (vérifiables sur
`admin/status`).

## 5. Nginx

Adapter le vhost existant (`/etc/nginx/sites-available/client.goldron.fr`) :

```nginx
server {
    listen 443 ssl http2;
    server_name NOUVEAU_DOMAINE;
    root /chemin/vers/client/public;

    client_max_body_size 20M;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        try_files $uri =404;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\. { deny all; }
    location ~ ^/(application|system|tests|writable|vendor)/ { return 403; }

    ssl_certificate /etc/letsencrypt/live/NOUVEAU_DOMAINE/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/NOUVEAU_DOMAINE/privkey.pem;
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d NOUVEAU_DOMAINE
```

## Mises à jour ultérieures

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php spark migrate --all
```

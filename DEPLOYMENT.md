# Deployment on a New Server

## Server Requirements

- PHP 8.2+ with extensions: `intl`, `mbstring`, `sqlite3`, `curl`, `gd`
- Composer
- Node.js + npm (for the Vite build)
- Nginx + PHP-FPM
- SSL certificate (Let's Encrypt / certbot)

## 1. Access to the Private Repository

The `Goldron/dolispace` repository is private. Two options are available:

**Option A — Deploy key (recommended, read-only)**

```bash
ssh-keygen -t ed25519 -f ~/.ssh/dolispace_deploy -N ""
cat ~/.ssh/dolispace_deploy.pub
```

Add this public key to GitHub → repository `dolispace` → Settings → Deploy keys → Add deploy key (without write access).

Then, on the new server, add the following to `~/.ssh/config`:

```
Host github-dolispace
    HostName github.com
    User git
    IdentityFile ~/.ssh/dolispace_deploy
```

**Option B — Authenticated gh account** (if you already ran `gh auth login` on this server)

```bash
gh repo clone Goldron/dolispace
```

## 2. Clone the Project

```bash
git clone github-dolispace:Goldron/dolispace.git client
# or: git clone git@github.com:Goldron/dolispace.git client
cd client
```

## 3. Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

`--no-dev` excludes PHPUnit and other test dependencies, which are not required in production.

To run tests (locally or in CI before deployment): run `composer install` (without `--no-dev`) then:

```bash
vendor/bin/phpunit
```

## 4. Configuration

```bash
cp env .env
```

Edit `.env`:

- `CI_ENVIRONMENT = production`
- `app.baseURL` → domain name of the new server
- `app.admin_login` / `app.admin_password`
- `database.default.DBDriver = SQLite3`
- `database.default.database = database.db`
- `database.default.DBPrefix = coop_`

## 5. Database and Permissions

```bash
chmod -R 775 writable
php spark migrate --all
php spark db:seed DatabaseSeeder
```

Then log in to `/admin/config` to fill in the sensitive values left empty by the seeder:

(`dolibarr_api_token`, `smtp_user`, `smtp_pass`, `dolibarr_api_url` if different).

The seeder also enables the feature toggles by default:

(`commande_enabled`, `propal_enabled`, `facture_enabled`, `expedition_enabled`, `certificatsclients_enabled`)

Adjust them in the **"Features"** section of `admin/config` according to the modules actually enabled on the Dolibarr side (checkable in `admin/status`).

## 6. Nginx

Adapt the existing vhost (`/etc/nginx/sites-available/client.goldron.fr`):

```nginx
server {
    listen 443 ssl http2;
    server_name NEW_DOMAIN;
    root /path/to/client/public;

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

    location ~ /\.ht { deny all; }
    location ~ ^/(application|system|tests|writable|vendor)/ { return 403; }

    ssl_certificate /etc/letsencrypt/live/NEW_DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/NEW_DOMAIN/privkey.pem;
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d NEW_DOMAIN
```

## Future Updates

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php spark migrate --all
```
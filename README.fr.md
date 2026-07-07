# Dolispace

Espace client web moderne permettant aux clients de consulter leurs devis, commandes, expéditions et factures grâce à une intégration avec l’API REST de [Dolibarr](https://www.dolibarr.org).

L’application gère ses propres comptes utilisateurs : aucun compte n’a besoin d’être créé ou administré dans Dolibarr. Elle n’expose jamais Dolibarr directement et communique uniquement avec son API REST.

Elle peut également être déployée sur un serveur indépendant de celui hébergeant Dolibarr, offrant ainsi une meilleure séparation, sécurité et flexibilité d’installation.

## Stack technique

- [CodeIgniter 4](https://codeigniter.com) (PHP)
- SQLite (base locale — utilisateurs, logs, fichiers, configuration)
- [Vite](https://vitejs.dev) + [Tailwind CSS v4](https://tailwindcss.com) + [Preline UI](https://preline.co)
- API REST Dolibarr pour les données métier (tiers, commandes, factures, expéditions)

## Fonctionnalités

- Connexion sans mot de passe pour les nouveaux comptes (vérification email + rattachement à un tiers Dolibarr)
- Connexion par mot de passe + OTP pour les comptes existants
- Consultation des devis, commandes, expéditions, certificats et factures avec téléchargement PDF
- Devis, commandes, factures, expéditions et certificats activables/désactivables individuellement
  (`admin/config` → carte "Fonctionnalités"), masqués automatiquement si le module Dolibarr correspondant
  n'est pas détecté
- Espace de dépôt de fichiers (uploads)
- Gestion du compte (email, mot de passe, coordonnées, TVA intracommunautaire via VIES)
- Interface d'administration : configuration de l'application, gestion des utilisateurs (recherche,
  suppression), journaux d'activité, fichiers uploadés, diagnostics API Dolibarr, test d'envoi SMTP, etc...

## Prérequis

- PHP 8.2+ avec les extensions : `intl`, `mbstring`, `sqlite3`, `curl`, `gd`
- Node.js + npm

## Installation

```bash
composer install
npm install
cp env .env
```

Configurer `.env` : base URL, connexion à la base SQLite, identifiants admin.

```bash
php spark migrate --all
php spark db:seed DatabaseSeeder
npm run build
```

## Développement

```bash
php spark serve
npm run dev
```

## Tests

```bash
composer install
vendor/bin/phpunit
```

## Déploiement

Voir [DEPLOYMENT.fr.md](DEPLOYMENT.fr.md).

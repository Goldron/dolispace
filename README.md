# Dolispace

Espace client web permettant aux clients de consulter leurs devis, commandes, expéditions,
certificats et factures depuis une intégration avec l'API REST de [Dolibarr](https://www.dolibarr.org).

## Stack technique

- [CodeIgniter 4](https://codeigniter.com) (PHP)
- SQLite (base locale — utilisateurs, logs, fichiers, configuration)
- [Vite](https://vitejs.dev) + [Tailwind CSS v4](https://tailwindcss.com) + [Preline UI](https://preline.co)
- API REST Dolibarr pour les données métier (tiers, commandes, factures, expéditions)

## Fonctionnalités

- Connexion sans mot de passe pour les nouveaux comptes (vérification email + rattachement à un tiers Dolibarr)
- Connexion par mot de passe + OTP pour les comptes existants
- Consultation des devis, commandes, expéditions, certificats et factures avec téléchargement PDF
- Espace de dépôt de fichiers (uploads)
- Gestion du compte (email, mot de passe, coordonnées, numéro de TVA via VIES)
- Interface d'administration : configuration de l'application, gestion des utilisateurs, journaux d'activité, test d'envoi SMTP

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

## Déploiement

Voir [DEPLOYMENT.md](DEPLOYMENT.md).

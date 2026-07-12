<?php

return [
    // layout_split
    'defaultTitle'        => 'Espace client',
    'tagline'              => 'Votre espace client sécurisé',
    'taglineDescription'   => 'Accédez à vos factures, devis et commandes en toute sécurité depuis un seul espace dédié.',
    'labelAlt'             => 'Label',

    // email.php
    'loginTitle'      => 'Connexion',
    'emailPrompt'     => 'Entrez votre adresse email pour continuer.',
    'emailAddress'    => 'Adresse email',
    'loginOrRegister' => 'Connexion ou inscription',

    // denied.php
    'accessRestricted'        => 'Accès réservé',
    'accessRestrictedDefault' => "L'espace client est réservé à nos clients.",
    'back'                    => 'Retour',

    // maintenance.php
    'maintenanceTitle'   => 'Maintenance en cours',
    'maintenanceMessage' => 'Le service est temporairement indisponible. Merci de réessayer dans quelques instants.',
    'retry'              => 'Réessayer',

    // otp.php
    'otpTitle'         => 'Code de connexion',
    'otpSentToPrefix'  => 'Un code à 6 chiffres a été envoyé à',
    'otpValidMinutes'  => 'Il est valable {0}&nbsp;minutes.',
    'otpCodeLabel'     => 'Code de vérification',
    'signIn'         => 'Se connecter',
    'backToPassword' => 'Revenir à la connexion par mot de passe',

    // password.php
    'connectedAs'         => 'Connecté en tant que',
    'edit'                => 'Modifier',
    'passwordLabel'       => 'Mot de passe',
    'or'                  => 'ou',
    'receiveCodeByEmail'  => 'Recevoir un code par email',

    // pending.php
    'checkYourInbox'          => 'Vérifiez votre boîte mail',
    'accessLinkSentTo'        => "Un lien d'accès a été envoyé à",
    'clickLinkToCreateAccount' => "Cliquez sur le lien contenu dans l'email pour créer votre compte. Le lien est valable 24&nbsp;heures.",

    // register.php
    'createYourAccount' => 'Créer votre compte',
    'completeInfoBelow' => 'Complétez les informations ci-dessous pour finaliser votre accès.',
    'firstName'         => 'Prénom',
    'lastName'          => 'Nom',
    'minPasswordLength' => '8 caractères minimum.',
    'createMyAccount'   => 'Créer mon compte',

    // AuthController messages
    'invalidEmail'          => 'Adresse email invalide.',
    'accountAlreadyExists'  => "Ce compte existe déjà. Veuillez vous connecter avec l'email de votre profil.",
    'incorrectPassword'     => 'Mot de passe incorrect.',
    'accountDisabled'       => 'Votre compte est désactivé. Contactez-nous.',
    'invalidOrExpiredLink'  => 'Le lien de validation est invalide ou a expiré. Veuillez recommencer.',
    'sessionExpired'        => 'Session expirée. Veuillez recommencer.',
    'accountCreationFailed' => 'Impossible de créer le compte. Veuillez réessayer.',
    'unableToSendCode'      => "Impossible d'envoyer le code.",
    'codeAlreadySent'       => "Un code a déjà été envoyé. Veuillez patienter avant d'en demander un nouveau.",
    'incorrectOrExpiredCode' => 'Code incorrect ou expiré.',
    'accountNotFoundOrDisabled' => 'Compte introuvable ou désactivé.',
];

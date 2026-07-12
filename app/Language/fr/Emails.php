<?php

return [
    // login_otp.php
    'otpEmailTitle'        => 'Code de connexion',
    'otpEmailHeading'      => 'Votre code de connexion',
    'hello'                => 'Bonjour,',
    'otpEmailIntro'        => 'Vous avez demandé à vous connecter à votre espace client avec un code à usage unique.',
    'otpEmailInstructions' => 'Saisissez le code ci-dessous. Il est valable <strong>15&nbsp;minutes</strong>.',
    'ignoreIfNotRequested' => "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.",
    'automatedEmailFooter' => 'Cet email a été envoyé automatiquement, merci de ne pas y répondre.',

    // verify.php
    'verifyEmailTitle'          => 'Accès à votre espace client',
    'validateAccess'            => 'Validez votre accès',
    'accessRequestReceived'     => "Nous avons reçu une demande d'accès à l'espace client pour l'adresse",
    'clickButtonToCreateAccount' => 'Cliquez sur le bouton ci-dessous pour créer votre compte. Ce lien est valable <strong>24&nbsp;heures</strong>.',
    'createMyAccountBtn'        => 'Créer mon compte',
    'ifButtonDoesNotWork'       => 'Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur&nbsp;:',

    // email_change_link.php
    'confirmEmailTitle'          => 'Confirmer votre adresse email',
    'confirmNewEmailHeading'     => 'Confirmez votre nouvelle adresse email',
    'emailChangeRequested'       => 'Vous avez demandé à modifier votre adresse email vers',
    'clickButtonToConfirmChange' => 'Cliquez sur le bouton ci-dessous pour confirmer ce changement. Le lien est valable <strong>1&nbsp;heure</strong>.',
    'confirmMyEmailBtn'          => 'Confirmer mon adresse email',
    'ignoreIfNotRequestedEmail'  => "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email. Votre adresse actuelle ne sera pas modifiée.",

    // password_change_link.php
    'confirmPasswordTitle'         => 'Confirmer votre nouveau mot de passe',
    'confirmNewPasswordHeading'    => 'Confirmez votre nouveau mot de passe',
    'passwordChangeRequested'      => 'Vous avez demandé à modifier votre mot de passe sur votre espace client.',
    'confirmMyPasswordBtn'         => 'Confirmer mon nouveau mot de passe',
    'ignoreIfNotRequestedPassword' => "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email. Votre mot de passe actuel ne sera pas modifié.",

    // Objets des emails (utilisés dans les contrôleurs)
    'loginCodeSubject'      => 'Votre code de connexion',
    'accessSubject'         => 'Accès à votre espace client',
    'confirmEmailSubject'   => 'Confirmer votre adresse email',
    'confirmPasswordSubject' => 'Confirmer votre nouveau mot de passe',
];

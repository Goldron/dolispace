<?php

return [
    // layout_split
    'defaultTitle'        => 'Customer portal',
    'tagline'              => 'Your secure customer portal',
    'taglineDescription'   => 'Access your invoices, quotes and orders securely from a single dedicated space.',
    'labelAlt'             => 'Label',

    // email.php
    'loginTitle'      => 'Login',
    'emailPrompt'     => 'Enter your email address to continue.',
    'emailAddress'    => 'Email address',
    'loginOrRegister' => 'Login or sign up',

    // denied.php
    'accessRestricted'        => 'Restricted access',
    'accessRestrictedDefault' => 'The customer portal is reserved for our customers.',
    'back'                    => 'Back',

    // maintenance.php
    'maintenanceTitle'   => 'Under maintenance',
    'maintenanceMessage' => 'The service is temporarily unavailable. Please try again shortly.',
    'retry'              => 'Retry',

    // otp.php
    'otpTitle'         => 'Login code',
    'otpSentToPrefix'  => 'A 6-digit code has been sent to',
    'otpValidMinutes'  => 'It is valid for {0}&nbsp;minutes.',
    'otpCodeLabel'     => 'Verification code',
    'signIn'         => 'Sign in',
    'backToPassword' => 'Back to password login',

    // password.php
    'connectedAs'         => 'Signed in as',
    'edit'                => 'Edit',
    'passwordLabel'       => 'Password',
    'or'                  => 'or',
    'receiveCodeByEmail'  => 'Receive a code by email',

    // pending.php
    'checkYourInbox'          => 'Check your inbox',
    'accessLinkSentTo'        => 'An access link has been sent to',
    'clickLinkToCreateAccount' => 'Click the link in the email to create your account. The link is valid for 24&nbsp;hours.',

    // register.php
    'createYourAccount' => 'Create your account',
    'completeInfoBelow' => 'Complete the information below to finish setting up your access.',
    'firstName'         => 'First name',
    'lastName'          => 'Last name',
    'minPasswordLength' => 'At least 8 characters.',
    'createMyAccount'   => 'Create my account',

    // AuthController messages
    'invalidEmail'          => 'Invalid email address.',
    'accountAlreadyExists'  => 'This account already exists. Please sign in with your profile email.',
    'duplicateEmailContactSupport' => 'This email address is registered on several accounts in our system. Please contact support.',
    'incorrectPassword'     => 'Incorrect password.',
    'accountDisabled'       => 'Your account is disabled. Please contact us.',
    'invalidOrExpiredLink'  => 'The verification link is invalid or has expired. Please start again.',
    'sessionExpired'        => 'Session expired. Please start again.',
    'accountCreationFailed' => 'Unable to create the account. Please try again.',
    'unableToSendCode'      => 'Unable to send the code.',
    'codeAlreadySent'       => 'A code has already been sent. Please wait before requesting a new one.',
    'incorrectOrExpiredCode' => 'Incorrect or expired code.',
    'accountNotFoundOrDisabled' => 'Account not found or disabled.',
];

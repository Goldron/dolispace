<?php

return [
    // login_otp.php
    'otpEmailTitle'        => 'Login code',
    'otpEmailHeading'      => 'Your login code',
    'hello'                => 'Hello,',
    'otpEmailIntro'        => 'You requested to sign in to your customer portal using a one-time code.',
    'otpEmailInstructions' => 'Enter the code below. It is valid for <strong>15&nbsp;minutes</strong>.',
    'ignoreIfNotRequested' => "If you didn't request this, simply ignore this email.",
    'automatedEmailFooter' => 'This email was sent automatically, please do not reply.',

    // verify.php
    'verifyEmailTitle'          => 'Access to your customer portal',
    'validateAccess'            => 'Confirm your access',
    'accessRequestReceived'     => 'We received a request for access to the customer portal for the address',
    'clickButtonToCreateAccount' => 'Click the button below to create your account. This link is valid for <strong>24&nbsp;hours</strong>.',
    'createMyAccountBtn'        => 'Create my account',
    'ifButtonDoesNotWork'       => "If the button doesn't work, copy this link into your browser:",

    // email_change_link.php
    'confirmEmailTitle'          => 'Confirm your email address',
    'confirmNewEmailHeading'     => 'Confirm your new email address',
    'emailChangeRequested'       => 'You requested to change your email address to',
    'clickButtonToConfirmChange' => 'Click the button below to confirm this change. The link is valid for <strong>1&nbsp;hour</strong>.',
    'confirmMyEmailBtn'          => 'Confirm my email address',
    'ignoreIfNotRequestedEmail'  => "If you didn't request this, simply ignore this email. Your current address will not be changed.",

    // password_change_link.php
    'confirmPasswordTitle'         => 'Confirm your new password',
    'confirmNewPasswordHeading'    => 'Confirm your new password',
    'passwordChangeRequested'      => 'You requested to change your password on your customer portal.',
    'confirmMyPasswordBtn'         => 'Confirm my new password',
    'ignoreIfNotRequestedPassword' => "If you didn't request this, simply ignore this email. Your current password will not be changed.",

    // Email subjects (used in controllers)
    'loginCodeSubject'      => 'Your login code',
    'accessSubject'         => 'Access to your customer portal',
    'confirmEmailSubject'   => 'Confirm your email address',
    'confirmPasswordSubject' => 'Confirm your new password',
];

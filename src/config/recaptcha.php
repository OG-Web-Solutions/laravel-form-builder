<?php

return [
    /*
    |--------------------------------------------------------------------------
    | reCaptcha Site Key
    |--------------------------------------------------------------------------
    |
    | Your reCaptcha site key used in frontend forms.
    |
    */
    'sitekey' => env('RECAPTCHA_SITE_KEY', ''),
    /*
    |--------------------------------------------------------------------------
    | reCaptcha Secret Key
    |--------------------------------------------------------------------------
    |
    | Your reCaptcha secret key used for backend verification.
    |
    */
    'secret'  => env('RECAPTCHA_SECRET', ''),
    /*
    |--------------------------------------------------------------------------
    | reCaptcha API Verification URL
    |--------------------------------------------------------------------------
    |
    | This is the endpoint used to verify reCaptcha tokens.
    |
    */
    'verify_url' => env('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify'),
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | hCaptcha Site Key
    |--------------------------------------------------------------------------
    |
    | Your hCaptcha site key used in frontend forms.
    |
    */

    'sitekey' => env('HCAPTCHA_SITE_KEY', 'd7d84f3e-025c-4356-b7e4-96d0e5bc5658'),

    /*
    |--------------------------------------------------------------------------
    | hCaptcha Secret Key
    |--------------------------------------------------------------------------
    |
    | Your hCaptcha secret key used for backend verification.
    |
    */

    'secret' => env('HCAPTCHA_SECRET', 'ES_cebedcdcc7a34ad4929a6347c341aa43'),

    /*
    |--------------------------------------------------------------------------
    | hCaptcha API Verification URL
    |--------------------------------------------------------------------------
    |
    | This is the endpoint used to verify hCaptcha tokens.
    |
    */

    'verify_url' => env('HCAPTCHA_VERIFY_URL', 'https://hcaptcha.com/siteverify'),

];

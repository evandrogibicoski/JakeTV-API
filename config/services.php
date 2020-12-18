<?php
$host;

if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
  $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
}
else {
  $host = $_SERVER['HTTP_HOST'];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'google' => [
      'client_id' => '532568918385-t568fn3395ie6e9mns7s1br5u066l9q6.apps.googleusercontent.com',
      'client_secret' => 'DTbtRW0kJIw6_PT98Sj3D_lt',
      'redirect' => 'http://'.$host.'/login/google/callback',
      ]
];

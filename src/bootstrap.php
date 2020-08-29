<?php
use Dotenv\Dotenv;

if (getenv('APP_ENV') !== 'production')
{
    $dotenv = new Dotenv(__DIR__.'/../../');
    $dotenv->load();
}

define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY'));
define('FIREBASE_CREDENTIALS', getenv('FIREBASE_CREDENTIALS'));
define('EMAIL_FROM_ADDRESS',  getenv('EMAIL_FROM_ADDRESS'));
define('EMAIL_FROM_ADDRESS_NAME',  getenv('EMAIL_FROM_ADDRESS_NAME'));
<?php
use Dotenv\Dotenv;

if (getenv('APP_ENV') !== 'production')
{
    $dotenv = new Dotenv(__DIR__.'/../../');
    $dotenv->load();
}

define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY'));
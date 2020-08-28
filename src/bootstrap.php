<?php
use Dotenv\Dotenv;

if (getenv('APP_ENV') !== 'production')
{
    $dotenv = new Dotenv(__DIR__.'/../../');
    $dotenv->load();
}
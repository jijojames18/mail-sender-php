<?php
require 'src/index.php';

use Controller\MailController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$uriSize = sizeof($uri);

$mailIndex = array_search('send-email', $uri);
$isMailRequest = $mailIndex !== null && $mailIndex === ($uriSize - 1);

if ($isMailRequest) {
    $controller = (new MailController($requestMethod))->processRequest();
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}

<?php

session_start();

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '\\..\\config');
$dotenv->load();

$_ENV['BASE_URL'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

if(!isset($_GET['url'])) {
    header('Location: ' . $_ENV['BASE_URL'] . '/login');
    exit;
}

include "../src/loadController.php";
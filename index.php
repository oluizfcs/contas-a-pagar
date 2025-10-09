<?php

session_start();

require './vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if(!isset($_GET['url'])) {
    header('Location: ' . $_ENV['BASE_URL'] . '/login');
    exit;
}

include "loadController.php";
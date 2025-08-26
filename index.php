<?php

session_start();

if(!isset($_GET['url'])) {
    header('Location: /login');
    exit;
}

require './vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include "loadController.php";
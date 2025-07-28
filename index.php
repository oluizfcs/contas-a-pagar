<?php

require './vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if(isset($_GET['url'])) {
    include "loadController.php";
} else {
    include "src/Views/login.php";
}
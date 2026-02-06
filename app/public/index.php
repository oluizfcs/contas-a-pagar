<?php

session_start();

require '../vendor/autoload.php';

if(!isset($_GET['url'])) {
    header('Location: ' . $_ENV['BASE_URL'] . '/login');
    exit;
}

include "../src/loadController.php";
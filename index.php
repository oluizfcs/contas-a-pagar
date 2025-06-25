<?php

require './vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "welcome";

if(isset($_GET['url'])) {
    include "loadController.php";
}

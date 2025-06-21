<?php

require './vendor/autoload.php';

echo "welcome";

if(isset($_GET['url'])) {
    include "loadController.php";
}

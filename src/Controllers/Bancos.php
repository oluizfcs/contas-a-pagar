<?php

namespace App\Controllers;

class Bancos
{
    public static bool $needLogin = true;

    function __construct()
    {
        $this->loadView();
    }
    
    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/bancos.php';
        include 'templates/footer.php';
    }
}
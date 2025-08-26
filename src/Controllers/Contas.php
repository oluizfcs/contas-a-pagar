<?php

namespace App\Controllers;

class Contas
{
    public static bool $needLogin = true;

    function __construct()
    {
        $this->loadView();
    }
    
    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/contas.php';
        include 'templates/footer.php';
    }
}
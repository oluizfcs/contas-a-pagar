<?php

namespace App\Controllers;

class Despesas implements Controller
{
    public bool $needLogin = true;

    function __construct()
    {
        $this->loadView();
    }
    
    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/despesas.php';
        include 'templates/footer.php';
    }
}
<?php

namespace App\Controllers;

class Despesas
{
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
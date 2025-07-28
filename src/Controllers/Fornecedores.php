<?php

namespace App\Controllers;

class Fornecedores
{
    function __construct()
    {
        $this->loadView();
    }
    
    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/fornecedores.php';
        include 'templates/footer.php';
    }
}
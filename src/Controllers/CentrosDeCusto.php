<?php

namespace App\Controllers;

class CentrosDeCusto implements Controller
{
    public bool $needLogin = true;

    function __construct()
    {
        $this->loadView();
    }
    
    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/centros-de-custo.php';
        include 'templates/footer.php';
    }
}
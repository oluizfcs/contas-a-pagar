<?php

namespace App\Controllers;

class Contas implements Controller
{
    public bool $needLogin = true;

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
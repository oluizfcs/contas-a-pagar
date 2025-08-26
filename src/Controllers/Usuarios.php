<?php

namespace App\Controllers;

class Usuarios
{
    public static bool $needLogin = true;

    function __construct()
    {
        $this->loadView();
    }
    
    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/usuarios.php';
        include 'templates/footer.php';
    }
}
<?php

namespace App\Controllers;

class Relatorios
{
    public static bool $needLogin = true;
    public static bool $onlyAdmin = true;

    function __construct(string $view = 'index', string $parm = '')
    {
        $this->loadView($view);
    }

    private function loadView($view): void
    {
        include '../src/templates/header.php';
        include "../src/Views/relatorios/$view.php";
        include '../src/templates/footer.php';
    }
}
<?php

namespace App\Controllers;

class Dashboard implements Controller
{
    public bool $needLogin = true;

    function __construct()
    {
        $this->loadView();
    }

    private function loadView() : void
    {
        include 'templates/header.php';
        include 'src/Views/dashboard.php';
        include 'templates/footer.php';
    }
}
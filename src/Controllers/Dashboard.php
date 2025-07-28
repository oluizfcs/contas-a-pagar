<?php

namespace App\Controllers;

class Dashboard
{
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
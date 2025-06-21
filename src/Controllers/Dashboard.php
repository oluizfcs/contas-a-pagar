<?php

namespace App\Controllers;

class Dashboard
{
    function __construct(String $primeiro = '1', String $segundo = '2')
    {
        echo "<br>Primeiro: $primeiro<br>";
        echo "Segundo: $segundo";
    }
}
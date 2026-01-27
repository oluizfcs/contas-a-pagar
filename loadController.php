<?php

$controllers = ['Dashboard', 'Contas', 'Usuarios', 'Fornecedores', 'Bancos', 'CentrosDeCusto', 'Login'];

$url = explode('/', rtrim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT), '/'), 5);

$controller = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower(array_shift($url)))));

if(!in_array($controller, $controllers)) {
    include './404.php';
    exit;
}

$c = 'App\\Controllers\\' . $controller;

if($c::$needLogin && !isset($_SESSION['usuario_id'])) {
    include './404.php';
    var_dump($_SESSION);
    exit;
}

new ($c)(...$url);
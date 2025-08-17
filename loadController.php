<?php

$controllers = ['Dashboard', 'Despesas', 'Usuarios', 'Fornecedores', 'Contas', 'CentrosDeCusto', 'Login'];

$url = explode('/', rtrim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT), '/'), 5);

$controller = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower(array_shift($url)))));

if(!in_array($controller, $controllers)) {
    include './404.php';
    exit;
}

$c = new ('App\\Controllers\\' . $controller)(...$url);

if($c->needLogin && !isset($_SESSION['usuario_id'])) {
    header('Location: ' . $_ENV['BASE_URL'] . '/login');
}
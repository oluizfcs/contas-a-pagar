<?php

$controllers = ['Dashboard', 'Despesas', 'Usuarios', 'Fornecedores', 'Contas', 'CentrosDeCusto'];

$url = explode('/', rtrim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT), '/'), 5);

$controller = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower(array_shift($url)))));

if (in_array($controller, $controllers)) {
    new ('App\\Controllers\\' . $controller)(...$url);
} else {
    include './404.php';
}

<?php
$controller = explode('/', $_SERVER['QUERY_STRING'])[0];
$controller = str_replace('url=', '', $controller);
?>

<h1>Cadastros</h1>
<a class="btn btn-<?= $controller == 'fornecedores' ? 'primary' : 'secondary' ?>" href="<?= $_ENV['BASE_URL'] ?>/fornecedores">Fornecedores</a>
<a class="btn btn-<?= $controller == 'centros-de-custo' ? 'primary' : 'secondary' ?>" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo">Centros de custo</a>
<a class="btn btn-<?= $controller == 'naturezas' ? 'primary' : 'secondary' ?>" href="<?= $_ENV['BASE_URL'] ?>/naturezas">Naturezas</a>
<a class="btn btn-<?= $controller == 'usuarios' ? 'primary' : 'secondary' ?>" href="<?= $_ENV['BASE_URL'] ?>/usuarios">Usuários</a>
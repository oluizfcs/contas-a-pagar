<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contas a pagar</title>
    <link rel="shortcut icon" href="<?= $_ENV['BASE_URL'] ?>/public/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= $_ENV['BASE_URL'] ?>/public/css/reset.css">
    <link rel="stylesheet" href="<?= $_ENV['BASE_URL'] ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= $_ENV['BASE_URL'] ?>/public/css/bsbuttons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
    <script src="<?= $_ENV['BASE_URL'] ?>/public/js/jquery.maskMoney.js" type="text/javascript"></script>
</head>

<body>
    <button type="button" id="toggle-sidebar" onclick="document.getElementById('sidebar').classList.toggle('sidebar-overlay');"><i class="fa-solid fa-bars"></i></button>
    <div id="main-layout">
        <div id="sidebar">
            <?php
            $controller = explode('/', $_SERVER['QUERY_STRING'])[0];
            $controller = str_replace('url=', '', $controller);

            $menuItems = [
                'dashboard' => ['icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                'contas' => ['icon' => 'fa-solid fa-receipt', 'label' => 'Contas'],
                'fornecedores' => ['icon' => 'fa-solid fa-dolly', 'label' => 'Fornecedores'],
                'centros-de-custo' => ['icon' => 'fa-solid fa-list', 'label' => 'Centros'],
                'bancos' => ['icon' => 'fa-solid fa-building-columns', 'label' => 'Bancos'],
                'usuarios' => ['icon' => 'fa-solid fa-users', 'label' => 'Usuários']
            ];

            foreach ($menuItems as $item => $info) {
                $selected = $controller == $item ? "style='background-color: hsl(214, 77%, 30%)'" : '';
                echo "<a href='" . $_ENV['BASE_URL'] . "/$item' $selected><i class='{$info['icon']}'></i><span class='link-text'>{$info['label']}</span></a>";
            }
            ?>

            <div id="sidebar-actions">
                <a id="user" href="<?= $_ENV['BASE_URL'] ?>/usuarios/detalhar/<?= $_SESSION['usuario_id'] ?>"><i class="fa-solid fa-user"></i><?= $_SESSION['usuario_nome'] ?></a>
                <a id="logout" href="<?= $_ENV['BASE_URL'] ?>/login/sair"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            </div>
        </div>
        <div id="content">
            <?php include 'templates/message.php'; ?>
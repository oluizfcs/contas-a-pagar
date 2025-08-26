<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contas a pagar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/public/css/base.css">
    <link rel="stylesheet" href="/public/css/charcounter.css">
</head>

<body>
    <header id="topbar">
        <a onclick="collapse()"><i class="fa-solid fa-bars"></i></a>
        <script>
            function collapse() {
                sidebar = document.getElementById('sidebar');

                if (sidebar.style.width == "0px") {
                    document.getElementById('sidebar').style.width = "220px";
                } else {
                    document.getElementById('sidebar').style.width = "0px";
                }
            }
        </script>
        <a href="/usuarios/detalhar/<?= $_SESSION['usuario_id'] ?? '#' ?>"><i class="fa-solid fa-user"></i></a>
        <a href="/login/sair"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
    </header>
    <div class="main-layout">
        <div id="sidebar">
            <?php
            $controller = explode('/', $_SERVER['REQUEST_URI'])[1];

            $menuItems = [
                'dashboard'   => ['icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                'contas'    => ['icon' => 'fa-solid fa-receipt', 'label' => 'Contas'],
                'fornecedores' => ['icon' => 'fa-solid fa-dolly', 'label' => 'Fornecedores'],
                'bancos'      => ['icon' => 'fa-solid fa-building-columns', 'label' => 'Bancos'],
                'usuarios'    => ['icon' => 'fa-solid fa-users', 'label' => 'Usuários']
            ];

            foreach($menuItems as $item => $info) {
                $active = $controller == $item ? '#0d3263' : '#08203f';
                echo "<a href='/$item' style='background: $active'>
                    <i class='{$info['icon']}'></i>{$info['label']}
                </a>";
            }
            ?>
        </div>
        <div class="content">
            <?php include 'templates/message.php'; ?>
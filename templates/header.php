<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contas a pagar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="/public/css/base.css">
    <link rel="stylesheet" href="/public/css/topbar.css">
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="/public/css/content.css"> -->
    <link rel="stylesheet" href="/public/css/charcounter.css">
</head>
<body>
    <header id="topbar">
        <a href="<?=$_ENV['BASE_URL']?>/login/sair"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
    </header>
    <div class="main-layout">
        <div id="sidebar">
            <a href="<?=$_ENV['BASE_URL']?>/dashboard"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="<?=$_ENV['BASE_URL']?>/despesas"><i class="fa-solid fa-receipt"></i>Despesas</a>
            <a href="<?=$_ENV['BASE_URL']?>/fornecedores"><i class="fa-solid fa-dolly"></i>Fornecedores</a>
            <a href="<?=$_ENV['BASE_URL']?>/contas"><i class="fa-solid fa-building-columns"></i>Contas</a>
            <a href="<?=$_ENV['BASE_URL']?>/usuarios"><i class="fa-solid fa-users"></i>Usuários</a>
        </div>
        <div class="content">
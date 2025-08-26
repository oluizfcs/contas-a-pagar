<link rel="stylesheet" href="/public/css/login.css">

<div id="login">
    <?php include 'templates/message.php'; ?>
    <form action="" method="post">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="<?= $_POST['cpf'] ?? '' ?>">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" value="<?= $_POST['senha'] ?? '' ?>">
        <input id="submit" type="submit" value="Entrar">
    </form>
</div>
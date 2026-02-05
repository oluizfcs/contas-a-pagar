<!DOCTYPE html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
</head>
<link rel="stylesheet" href="<?= $_ENV['BASE_URL'] ?>/src/public/css/login.css">
<link rel="shortcut icon" href="<?= $_ENV['BASE_URL'] ?>/src/public/images/favicon.ico" type="image/x-icon">

<div id="login">
    <?php include 'src/templates/message.php'; ?>
    <form action="" method="post">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" required autofocus value="<?= $_POST['cpf'] ?? '' ?>">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required value="<?= $_POST['senha'] ?? '' ?>">
        <input id="submit" type="submit" value="Entrar">
    </form>
</div>

<script>
    var cleave = new Cleave('#cpf', {
        delimiters: ['.', '.', '-'],
        blocks: [3, 3, 3, 2],
        numericOnly: true
    });
</script>
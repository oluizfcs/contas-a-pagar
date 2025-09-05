<!DOCTYPE html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
</head>
<link rel="stylesheet" href="/public/css/login.css">

<div id="login">
    <?php include 'templates/message.php'; ?>
    <form action="" method="post">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" required value="<?= $_POST['cpf'] ?? '' ?>">
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
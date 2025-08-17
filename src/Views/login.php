<form action="" method="post">
    <label for="cpf">CPF</label>
    <input type="text" id="cpf" name="cpf" value="<?= $_POST['cpf'] ?? '' ?>">
    <br>
    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" value="<?= $_POST['senha'] ?? '' ?>">
    <br>
    <input type="submit" value="entrar">
</form>
<h1>Novo Fornecedor</h1>
<form action="" method="post">
    <input type="hidden" name="type" value="create">
    <input type="hidden" name="usuario_id" value="<?= $_SESSION['usuario_id'] ?>">

    <label for="nome">Nome do fornecedor:</label>
    <div class="input-wrapper">
        <input type="text" id="nome" name="nome" required maxlength="55">
        <span class="char-counter"></span>
    </div>

    <label for="telefone">Telefone:</label>
    <div class="input-wrapper">
        <input type="text" id="telefone" name="telefone" maxlength="45">
        <span class="char-counter"></span>
    </div>

    <input type="submit" value="Adicionar">
</form>
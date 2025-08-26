<?php
$type = 'create';
$href = '/fornecedores';

if ($view == 'atualizar') {
    $type = 'update';
    $href = '/fornecedores/detalhar/' . $fornecedor->getId();
}
?>

<h1><?= ucfirst($view) ?> Fornecedor</h1>
<form action="" method="post">

    <?php if ($view == 'atualizar'): ?>
        <input type="hidden" name="entity_id" value="<?= $fornecedor->getId() ?>">
        <input type="hidden" name="old_nome" value="<?= $fornecedor->getNome() ?>">
        <input type="hidden" name="old_telefone" value="<?= $fornecedor->getTelefone() ?>">
    <?php endif; ?>

    <label for="nome">Nome do fornecedor:</label>
    <div class="input-wrapper">
        <input
            type="text"
            id="nome"
            name="nome"
            required maxlength="55"
            <?= $view == 'atualizar' ? "value=\"" . $fornecedor->getNome() . "\"" : '' ?>>
        <span class="char-counter"></span>
    </div>

    <label for="telefone">Telefone:</label>
    <div class="input-wrapper">
        <input
            type="text"
            id="telefone"
            name="telefone"
            maxlength="45"
            <?= $view == 'atualizar' ? "value=\"" . $fornecedor->getTelefone() . "\"" : '' ?>>
        <span class="char-counter"></span>
    </div>

    <button class="btn btn-primary" type="submit" name="type" value="<?= $type ?>"><?= ucfirst($view) ?></button>
    <a class="btn" href="<?= $href ?>">Cancelar</a>
</form>
<?php
$type = 'create';
$href = '/centros-de-custo';

if ($view == 'atualizar') {
    $type = 'update';
    $href = '/centros-de-custo/detalhar/' . $centro_de_custo->getId();
}
?>

<h1><?= ucfirst($view) ?> Centro de Custo</h1>
<form action="" method="post">

    <?php if ($view == 'atualizar'): ?>
        <input type="hidden" name="entity_id" value="<?= $centro_de_custo->getId() ?>">
        <input type="hidden" name="old_nome" value="<?= $centro_de_custo->getNome() ?>">
    <?php endif; ?>

    <label for="nome">Nome:</label>
    <div class="input-wrapper">
        <input
            type="text"
            id="nome"
            name="nome"
            required maxlength="45"
            <?= $view == 'atualizar' ? "value=\"" . $centro_de_custo->getNome() . "\"" : '' ?>>
        <span class="char-counter"></span>
    </div>

    <button class="btn btn-primary" type="submit" name="type" value="<?= $type ?>"><?= ucfirst($view) ?></button>
    <a class="btn" href="<?= $href ?>">Cancelar</a>
</form>
<?php
$type = 'create';
$href = '/bancos';

if ($view == 'atualizar') {
    $type = 'update';
    $href = '/bancos/detalhar/' . $banco->getId();
}
?>

<h1><?= ucfirst($view) ?> Conta bancária</h1>
<form action="" method="post">

    <?php if ($view == 'atualizar'): ?>
        <input type="hidden" name="entity_id" value="<?= $banco->getId() ?>">
        <input type="hidden" name="old_nome" value="<?= $banco->getNome() ?>">
    <?php endif; ?>

    <label for="nome">Nome da Conta/Banco:</label>
    <div class="input-wrapper">
        <input
            type="text"
            id="nome"
            name="nome"
            required maxlength="55"
            <?= $view == 'atualizar' ? "value=\"" . $banco->getNome() . "\"" : '' ?>>
        <span class="char-counter"></span>
    </div>

    <?php if($view != 'atualizar'): ?>
    <label for="saldo_em_centavos">Saldo de abertura:</label>
    <div class="input-wrapper">
        <input
            type="text"
            id="money"
            name="saldo_em_centavos"
            maxlength="22"
            data-thousands="."
            data-decimal=",">
        <span class="char-counter"></span>
    </div>
    <?php endif; ?>

    <button class="btn btn-primary" type="submit" name="type" value="<?= $type ?>"><?= ucfirst($view) ?></button>
    <a class="btn" href="<?= $href ?>">Cancelar</a>
</form>

<script>
    $(function() {
        $('#money').maskMoney({prefix:'R$ '});
    })
</script>
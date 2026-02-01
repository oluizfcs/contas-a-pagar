<?php
$href = $_ENV['BASE_URL'] . '/bancos/detalhar/' . $banco->getId();
?>

<div class="form-section">
    <div class="section">
        <h1>Depositar em <?= $banco->getNome() ?></h1>
        <form action="" method="post">
            <input type="hidden" name="banco_id" value="<?= $banco->getId() ?>">

            <label for="valor">Valor:</label>
            <div class="input-wrapper">
                <input
                    type="text"
                    id="valor"
                    name="valor"
                    class="money"
                    required
                    maxlength="15"
                    autocomplete="off"
                    autofocus>
            </div>

            <label for="descricao">Descrição:</label>
            <div class="input-wrapper">
                <input
                    type="text"
                    id="descricao"
                    name="descricao"
                    required
                    maxlength="255"
                    autocomplete="off">
            </div>

            <button class="btn btn-primary" type="submit" name="type" value="depositar">Depositar</button>
            <a class="btn btn-secondary" href="<?= $href ?>">Cancelar</a>
        </form>
    </div>
</div>
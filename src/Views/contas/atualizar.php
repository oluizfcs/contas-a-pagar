<?php
// var_dump($conta);
?>
<h1>Atualizar Conta</h1>
<form action="" method="post" style="margin-bottom: 10px;">
    <input type="hidden" name="entity_id" value="<?= $conta->getId() ?>">
    <div class="section">
        <label for="descricao">Descrição/Observações:</label>
        <div class="input-wrapper">
            <textarea id="descricao" name="descricao" required maxlength="500"
                autocomplete="off"><?= $conta->getDescricao() ?></textarea>
        </div>
    </div>
    <div style="margin-top: 8px;">
        <button class="btn btn-primary" type="submit" name="type" value="update">Atualizar</button>
        <a class="btn" href="<?= $_ENV['BASE_URL'] . '/contas/detalhar/' . $conta->getId() ?>">Cancelar</a>
    </div>
</form>

<script>
    document.forms[0].addEventListener("submit", (e) => {
        if (document.getElementById("descricao").value.length > 500) {
            e.preventDefault();
            alert("A descrição não pode exceder 500 caracteres.");
            document.getElementById("descricao").focus();
            return;
        }
    });
</script>
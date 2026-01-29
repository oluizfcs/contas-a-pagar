<div class="form-section">
    <div class="section">
        <h1>Marcar parcela como paga</h1>
        <form action="" method="post">
            <input type="hidden" name="numero_parcela" value="<?= $_POST['numero_parcela'] ?>">
            <input type="hidden" name="conta_id" value="<?= explode('/', $_SERVER['QUERY_STRING'])[2] ?>">
            <div class="input-wrapper">
                <label for="bancos">Conta bancária <span style="font-weight: 200; font-size: 13px;">(onde será
                        descontado)<span></label>
                <select id="bancos" name="banco" class="select2" style="width: 100%;">
                    <?php foreach ($bancos as $banco): ?>
                        <option value="<?= $banco['id'] ?>" <?= ($_SESSION['post_data']['banco'] ?? null) == $banco['id'] ? 'selected' : '' ?>><?= $banco['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-wrapper" style="margin-top: 0.5rem;">
                <label for="data">Data de pagamento:</label><br>
                <input type="date" name="data" id="data">
            </div>
            <button class="btn btn-primary" style="margin-top 0.5rem;" type="submit" name="type"
                value="cobrar"><?= ucfirst($view) ?></button>
            <a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] . '/contas/detalhar/' . $this->id ?>">Cancelar</a>
        </form>
    </div>
</div>

<script>
    const inputBanco = document.getElementById("bancos");
    const inputData = document.getElementById("data");

    document.addEventListener("DOMContentLoaded", () => {
        inputBanco.focus();
        inputData.value = "<?= new DateTime()->format('Y-m-d') ?>";
    });

    inputBanco.addEventListener("change", () => {
        inputData.focus();
    });

    document.forms[0].addEventListener("submit", (e) => {
        if (inputData.value == "") {
            e.preventDefault();
            alert("É necessário informar uma data de pagamento");
            inputData.focus();
        }
    });
</script>
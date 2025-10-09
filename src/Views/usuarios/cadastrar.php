<div class="form-section">
    <div class="section">
        <h1>Cadastrar Usuário</h1>
        <form action="" method="post" onsubmit="return check()">
            <label for="nome">Nome do usuário:</label>
            <div class="input-wrapper">
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    required maxlength="45"
                    value="<?= isset($_POST['nome']) ? $_POST['nome'] : '' ?>"
                    autocomplete="off"
                    autofocus>
            </div>

            <label for="cpf">CPF:</label>
            <div class="input-wrapper">
                <input
                    type="text"
                    id="cpf"
                    name="cpf"
                    required
                    value="<?= isset($_POST['cpf']) ? $_POST['cpf'] : '' ?>"
                    autocomplete="off">
            </div>

            <label for="senha">Senha:</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required maxlength="100"
                    value="<?= isset($_POST['senha']) ? $_POST['senha'] : '' ?>">
            </div>

            <label for="senha">Confirmar senha:</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="confirmarsenha"
                    required maxlength="100">
            </div>

            <button class="btn btn-primary" type="submit" name="type" value="create"><?= ucfirst($view) ?></button>
            <a class="btn btn-secondary" href="/usuarios">Cancelar</a>
        </form>
    </div>
</div>

<script>
    var cleave = new Cleave('#cpf', {
        delimiters: ['.', '.', '-'],
        blocks: [3, 3, 3, 2],
        numericOnly: true
    });

    function check() {
        let senha = document.getElementById("senha").value;
        let confirmarsenha = document.getElementById("confirmarsenha").value;
        if (senha != confirmarsenha) {
            alert("As senhas não coincidem.");
            return false;
        }
        return true;
    }
</script>
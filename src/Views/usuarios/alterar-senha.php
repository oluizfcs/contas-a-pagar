<h1>Alterar Senha</h1>
<form action="" method="post" onsubmit="return check()">

    <input type="hidden" name="entity_id" value="<?= $usuario->getId() ?>">

    <p>Usuário: <?= $usuario->getNome() ?></p>
    <p>CPF: <?= $usuario->getMaskedCpf() ?></p>
    <label for="senha">Senha:</label>
    <div class="input-wrapper">
        <input
            type="password"
            id="senha"
            name="senha"
            required maxlength="100">
        <span class="char-counter"></span>
    </div>

    <label for="senha">Confirmar senha:</label>
    <div class="input-wrapper">
        <input
            type="password"
            id="confirmarsenha"
            required maxlength="100">
        <span class="char-counter"></span>
    </div>

    <button class="btn btn-primary" type="submit" name="type" value="update">Alterar Senha</button>
    <a class="btn" href="/usuarios/detalhar/<?= $usuario->getId() ?>">Cancelar</a>
</form>

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
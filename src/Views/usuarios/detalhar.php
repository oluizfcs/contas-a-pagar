<?php
$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $usuario->getData_criacao());
if ($usuario->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $usuario->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}
?>

<h1>Usuario: <?= $usuario->getNome() ?></h1>
CPF: <?= $usuario->getMaskedCpf() ?> <br>
<br>
<a class="btn" href="/usuarios" style="margin-right: 3px;">Voltar</a> <?php if ($_SESSION['usuario_id'] == $usuario->getId() || $_SESSION['usuario_id'] == 1): ?> <a class="btn" href="/usuarios/alterar-senha/<?= $usuario->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Alterar Senha</a> <?php endif; ?>
<br><br>

<?php if ($_SESSION['usuario_id'] == 1): ?>
    <form method="POST" action="">
        <input type="hidden" name="usuario_id" value="<?= $usuario->getId() ?>">
        <?php if ($usuario->isEnabled()): ?>
            <button class="btn" name="type" value="unable" onclick="return confirm('Realmente deseja inativar este usuário?')">
                <i class="fa-solid fa-box-archive"></i> Inativar
            </button>
            <a class="btn btn-disabled">
                <i class="fa-solid fa-box-open"></i> Ativar</a>
            </a>
        <?php else: ?>
            <a class="btn btn-disabled">
                <i class="fa-solid fa-box-archive"></i> Inativar
            </a>
            <button class="btn" name="type" value="enable" onclick="return confirm('Realmente deseja ativar este usuario?')">
                <i class="fa-solid fa-box-open"></i> Ativar</a>
            </button>
        <?php endif; ?>
    </form>
<?php endif; ?>

<?php include 'templates/auditoria.php'; ?>
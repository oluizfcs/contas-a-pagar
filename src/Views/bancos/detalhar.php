<?php
$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $banco->getData_criacao());
if ($banco->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $banco->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}

$formatter = new NumberFormatter('PT_BR', NumberFormatter::SPELLOUT);
$extenso = $formatter->format($banco->getSaldo_em_centavos() / 100);
?>

<h1>Conta bancária: <?= $banco->getNome() ?></h1>
<p title="<?= $extenso ?>">Saldo: R$ <?= $banco->getSaldo_em_reais() ?></p>
<br>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/bancos">Voltar</a>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/bancos/atualizar/<?= $banco->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>

<form method="POST" action="">
    <input type="hidden" name="banco_id" value="<?= $banco->getId() ?>">
    <?php if ($banco->isEnabled()): ?>
        <button class="btn btn-secondary" name="type" value="unable" onclick="return confirm('Realmente deseja inativar esta conta bancária?')">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </button>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-open"></i> Ativar</a>
        </a>
    <?php else: ?>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </a>
        <button class="btn btn-secondary" name="type" value="enable" onclick="return confirm('Realmente deseja ativar esta conta bancária?')">
            <i class="fa-solid fa-box-open"></i> Ativar</a>
        </button>
    <?php endif; ?>
</form>

<div class="section">
    <h2><i class="fa-solid fa-up-down"></i> Extrato</h2>
</div>

<div class="section">
    <h2><i class="fa-solid fa-chart-simple"></i> Estatísticas</h2>
    <img src="/public/images/graph.jpg" alt="example graph" width="525" height="412.5">
</div>

<?php include 'templates/auditoria.php'; ?>
<?php

use App\Controllers\Services\Money;

$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $conta->getData_criacao());
if ($conta->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $conta->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}

$formatter = new NumberFormatter('PT_BR', NumberFormatter::SPELLOUT);
$extenso = $formatter->format($conta->getValor_em_centavos() / 100);
?>
<h1>Detalhes da Conta</h1>
<p title="<?= $extenso ?>">Valor total: R$ <?= Money::centavos_para_reais($conta->getValor_em_centavos()) ?></p>
<p>Descrição:</p>
<span class="description"><?= $conta->getDescricao() ?></span>
<br><br>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/contas">Voltar</a>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/contas/atualizar/<?= $conta->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>

<form method="POST" action="">
    <input type="hidden" name="conta_id" value="<?= $conta->getId() ?>">
    <?php if ($conta->isEnabled()): ?>
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
    <h2><i class="fa-solid fa-money-bill"></i> Parcelas</h2>
    <table class="non-clickable-table">
        <tr>
            <th>#</th>
            <th>Vencimento</th>
            <th>Paga em</th>
            <th>Valor (R$)</th>
        </tr>
        <?php foreach ($conta->getParcelas() as $parcela): ?>
            <tr>
                <td><?= $parcela['numero_parcela'] ?>ª</td>
                <td><?= new DateTime($parcela['data_vencimento'])->format('d/m/Y') ?></td>
                <td><?= $parcela['data_pagamento'] != null ? new DateTime($parcela['data_pagamento'])->format('d/m/Y') : '-' ?></td>
                <td><?= Money::centavos_para_reais($parcela['valor_em_centavos']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include 'templates/auditoria.php'; ?>
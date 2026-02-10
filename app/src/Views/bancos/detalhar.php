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

use App\Controllers\Services\Money;
use App\Models\Conta;

?>

<h1>Conta bancária: <?= $banco->getNome() ?></h1>
<p title="<?= $extenso ?>">Saldo: R$ <?= $banco->getSaldo_em_reais() ?></p>
<br>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/bancos">Voltar</a>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/bancos/atualizar/<?= $banco->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/bancos/depositar/<?= $banco->getId() ?>"><i class="fa-solid fa-arrow-down"></i> Depositar</a>

<form method="POST" action="">
    <input type="hidden" name="banco_id" value="<?= $banco->getId() ?>">
    <?php if ($banco->isEnabled()): ?>
        <button class="btn btn-secondary" name="type" value="unable" onclick="return confirm('Realmente deseja inativar esta conta bancária?')">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </button>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-open"></i> Ativar
        </a>
    <?php else: ?>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </a>
        <button class="btn btn-secondary" name="type" value="enable" onclick="return confirm('Realmente deseja ativar esta conta bancária?')">
            <i class="fa-solid fa-box-open"></i> Ativar
        </button>
    <?php endif; ?>
</form>

<div class="section">
    <h2><i class="fa-solid fa-arrow-down"></i> Depósitos</h2>
        <?php if (count($depositos) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Valor (R$)</th>
                    <th>Depositado em</th>
                    <th>Descrição</th>
                </tr>
                <?php foreach ($depositos as $deposito): ?>
                    <tr style="cursor: default">
                        <td><?= Money::centavos_para_reais($deposito->getValorEmCentavos()) ?></td>
                        <td><?= new DateTime($deposito->getDataDeposito())->format('d/m/Y') ?></td>
                        <td><?= $deposito->getDescricao() ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p>Nenhum registro encontrado.</p>
    <?php endif; ?>
</div>

<div class="section">
    <h2><i class="fa-solid fa-hand-holding-dollar"></i> Parcelas pagas</h2>
        <?php if (count($parcelas) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Centro de custo</th>
                    <th>Fornecedor</th>
                    <th>Valor (R$)</th>
                    <th>Data de pagamento informada</th>
                    <th>Parcela</th>
                </tr>
                <?php foreach ($parcelas as $parcela): ?>
                    <tr onclick="window.open('<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $parcela->getConta_id() ?>', '_blank')">
                        <td><?= $parcela->centro_de_custo ?></td>
                        <td><?= $parcela->fornecedor ?? '-' ?></td>
                        <td><?= Money::centavos_para_reais($parcela->getValor_em_centavos()) ?></td>
                        <td><?= new DateTime($parcela->getData_pagamento())->format('d/m/Y') ?></td>
                        <td><?= $parcela->getNumero_parcela() . '/' . count(Conta::getById($parcela->getConta_id())->getParcelas()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p>Nenhum registro encontrado.</p>
    <?php endif; ?>
</div>

<?php include '../src/templates/auditoria.php'; ?>
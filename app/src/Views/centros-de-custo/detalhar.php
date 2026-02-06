<?php
$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $centro_de_custo->getData_criacao());
if ($centro_de_custo->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $centro_de_custo->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}

use App\Controllers\Services\Money;

?>

<h1>Centro de custo: <?= $centro_de_custo->getNome() ?></h1>
<br>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo">Voltar</a>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo/atualizar/<?= $centro_de_custo->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>

<form method="POST" action="">
    <input type="hidden" name="centro_de_custo_id" value="<?= $centro_de_custo->getId() ?>">
    <?php if($centro_de_custo->isEnabled()): ?>
        <button class="btn btn-secondary" name="type" value="unable" onclick="return confirm('Realmente deseja inativar este centro de custo?')">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </button>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-open"></i> Ativar
        </a>
    <?php else: ?>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </a>
        <button class="btn btn-secondary" name="type" value="enable" onclick="return confirm('Realmente deseja ativar este centro de custo?')">
            <i class="fa-solid fa-box-open"></i> Ativar
        </button>
    <?php endif; ?>
</form>


<?php if (count($contas) > 0): ?>
    <div class="section">
        <h2><i class="fa-solid fa-receipt"></i> Contas a pagar</h2>
        <div class="table-section">
            <table class="sortable" id="contas-table">
                <tr>
                    <th>Fornecedor</th>
                    <th>Próximo vencimento</th>
                    <th>Parcelas pagas</th>
                    <th>Valor Total (R$)</th>
                </tr>
                <?php foreach ($contas as $conta): ?>
                    <?php

                    $nextInstallment = null;
                    $paidInstallments = 0;
                    $nextInstallmentPrice = null;

                    foreach ($conta->getParcelas() as $parcela) {
                        if (!$parcela->isPaid() && $nextInstallment == null) {
                            $nextInstallment = new DateTime($parcela->getData_vencimento())->format('d/m/Y');
                            $nextInstallmentPrice = Money::centavos_para_reais($parcela->getValor_em_centavos());
                        }

                        if ($parcela->isPaid()) {
                            $paidInstallments += 1;
                        }
                    }
                    ?>
                    <tr onclick="window.open('<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $conta->getId() ?>', '_blank');">
                        <td><?= $conta->fornecedor ?? '-' ?></td>
                        <?php if ($nextInstallment == null): ?>
                            <td>-</td>
                        <?php else: ?>
                            <td><?= "$nextInstallment<br><span style='color: #777; font-size: smaller;'>(R$ $nextInstallmentPrice)</span>" ?>
                        </td>
                        <?php endif; ?>
                        <td>
                            <?= $paidInstallments . '/' . count($conta->getParcelas()) ?>
                        </td>
                        <td><?= Money::centavos_para_reais($conta->getValor_em_centavos()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include '../src/templates/auditoria.php'; ?>
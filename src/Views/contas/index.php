<h1><?= ucfirst($this->rowType) ?> <?= $this->status == 'todas' ? '' : $this->status ?></h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/contas/cadastrar"><i class="fa-solid fa-plus"></i>
    Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">

            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">

            <!-- <select name="rowType" onchange="form.submit()">
                <!php
                $options = ['contas', 'parcelas'];

                foreach ($options as $option) {
                    $selected = $this->rowType == $option ? 'selected' : '';
                    echo "<option value='$option' $selected>" . ucfirst($option) . '</option>';
                }
                ?>
            </select> -->
            <input type="hidden" name="rowType" value="contas">

            <select name="status" onchange="form.submit()">
                <?php
                $options = ['a pagar', 'pagas', 'todas'];

                if ($this->rowType == 'contas') {
                    $options[] = 'inativadas';
                }

                foreach ($options as $option) {
                    $selected = $this->status == $option ? 'selected' : '';
                    echo "<option value='$option' $selected>" . ucfirst($option) . '</option>';
                }
                ?>
            </select>
        </form>
    </div>
    <?php

    use App\Controllers\Services\Money;

    if ($this->rowType == 'contas' && $contas == []) {
        echo '<p> Nenhum conta encontrada.</p>';
    }
    if ($this->rowType == 'parcelas' && $parcelas == []) {
        echo '<p> Nenhum parcela encontrada.</p>';
    }
    ?>

    <?php if (count($contas) > 0): ?>
        <div class="table-section">
            <table class="sortable" id="contas-table">
                <tr>
                    <th>Centro de Custo</th>
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
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $conta->getId() ?>';">
                        <td><?= $conta->centro_de_custo ?></td>
                        <td><?= $conta->fornecedor ?></td>
                        <?php if ($nextInstallment == null): ?>
                            <td>-</td>
                        <?php else: ?>
                            <td><?= "$nextInstallment<br><span style='color: #777; font-size: smaller;'>(R$ $nextInstallmentPrice)</span>" ?>
                            </td>
                        <?php endif; ?>
                        <td><?= $paidInstallments . '/' . count($conta->getParcelas()) ?></td>
                        <td><?= Money::centavos_para_reais($conta->getValor_em_centavos()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <?php if (count($parcelas) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Centro de custo</th>
                    <th>Valor (R$)</th>
                    <th>Vencimento</th>
                    <th>Parcela</th>
                </tr>
                <?php foreach ($parcelas as $parcela): ?>
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $parcela['conta_id'] ?>';">
                        <td><?= $parcela['centro'] ?></td>
                        <td><?= Money::centavos_para_reais($parcela['valor_em_centavos']) ?></td>
                        <td><?= new DateTime($parcela['data_vencimento'])->format('d/m/Y') ?></td>
                        <td><?= $parcela['numero_parcela'] . '/' . $parcela['total_parcelas'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
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
            <table id="contas-table">
                <tr style="cursor: default">
                    <th>Centro de Custo</th>
                    <th>Fornecedor</th>
                    <th>Próximo vencimento</th>
                    <th>Parcelas pagas</th>
                    <th>Valor Total (R$)</th>
                </tr>
                <?php foreach ($contas as $conta): ?>
                    <?php
                    $info = $conta->getNextInstallmentInfo();

                    if ($info['installment'] != null) {
                        $nextInstallment = new DateTime($info['installment']->getData_vencimento())->format('d/m/Y');
                        $nextInstallmentPrice = Money::centavos_para_reais($info['installment']->getValor_em_centavos());
                    } else {
                        $nextInstallment = null;
                    }
                    $paidInstallments = $info['paidInstallmentCount'];
                    ?>
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $conta->getId() ?>';">
                        <td><?= $conta->centro_de_custo ?></td>
                        <td><?= $conta->fornecedor ?? '-' ?></td>
                        <?php if ($nextInstallment == null): ?>
                            <td>-</td>
                        <?php else: ?>
                            <td class="nextInstallment">
                                <?= "$nextInstallment<br><span style='color: #777; font-size: smaller;'>(R$ $nextInstallmentPrice)</span>" ?>
                            </td>
                        <?php endif; ?>
                        <td><?= $paidInstallments . '/' . count($conta->getParcelas()) ?></td>
                        <td><?= Money::centavos_para_reais($conta->getValor_em_centavos()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <!-- intentionally unreachable -->
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

<style>
    .red {
        background-color: hsla(0, 100%, 85%, 0.8);
    }

    .yellow {
        background-color: hsla(60, 100%, 85%, 0.8);
    }
</style>

<?php if ($this->status == 'a pagar'): ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".nextInstallment").forEach(td => {
                let date = td.textContent.trimStart().slice(0, 10);
                date = new Date(date.split("/").reverse().join("-") + 'T03:00');

                const today = new Date();

                const diffInDays = (today - date) / (1000 * 60 * 60 * 24);

                if (diffInDays >= 0) {
                    td.parentElement.classList.add("red");
                } else if (diffInDays >= -5) {
                    td.parentElement.classList.add("yellow");
                }
            });
        });
    </script>
<?php endif;

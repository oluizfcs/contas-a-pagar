<h1><?= ucfirst($this->rowType) ?> <?= $this->status == 'todas' ? '' : $this->status ?></h1>
<a class="btn btn-success" href="/contas/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">

            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">

            <select name="rowType">
                <?php
                $options = ['contas', 'parcelas'];

                foreach ($options as $option) {
                    $selected = $this->rowType == $option ? 'selected' : '';
                    echo "<option value='$option' $selected onclick='form.submit()'>" . ucfirst($option) . '</option>';
                }
                ?>
            </select>

            <select name="status">
                <?php
                $options = ['a pagar', 'pagas', 'todas'];

                if ($this->rowType == 'contas') {
                    $options[] = 'inativadas';
                }

                foreach ($options as $option) {
                    $selected = $this->status == $option ? 'selected' : '';
                    echo "<option value='$option' $selected onclick='form.submit()'>" . ucfirst($option) . '</option>';
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
            <table class="sortable">
                <tr>
                    <th>Centro de Custo</th>
                    <th>Valor Total (R$)</th>
                    <th>Próximo vencimento</th>
                    <th>Descrição</th>
                </tr>
                <?php foreach ($contas as $conta): ?>
                    <tr onclick="window.location.href='/contas/detalhar/<?= $conta['id'] ?>';">
                        <td><?= $conta['centro'] ?></td>
                        <td><?= Money::centavos_para_reais($conta['valor_em_centavos']) ?></td>
                        <td>x</td>
                        <td><?= $conta['descricao'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <?php if (count($parcelas) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Centro de Custo</th>
                    <th>Valor (R$)</th>
                    <th>Vencimento</th>
                    <th>Parcela</th>
                </tr>
                <?php foreach ($parcelas as $parcela): ?>
                    <tr onclick="window.location.href='/contas/detalhar/<?= $parcela['conta_id'] ?>';">
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
<?php

use App\Controllers\Services\Money;

$showNaturezaColumn = true;
$showCentroColumn = true;
$showFornecedorColumn = true;

$filtrosMsg = '';

switch ($data['status']) {
    case 'unpaid':
        $filtrosMsg .= 'Somente parcelas não pagas';
        break;
    case 'paid':
        $filtrosMsg .= 'Somente parcelas pagas';
        break;
}

if ($data['natureza'] != null) {
    $showNaturezaColumn = false;
    $filtrosMsg .= 'Natureza: ' . $data['natureza'] . '<br>';
}

if ($data['centro'] != null) {
    $showCentroColumn = false;
    $filtrosMsg .= 'Centro de custo: ' . $data['centro'] . '<br>';
}

if ($data['fornecedor'] != null) {
    $showFornecedorColumn = false;
    $filtrosMsg .= 'Fornecedor: ' . $data['fornecedor'] . '<br>';
}

if ($filtrosMsg != '') {
    echo "Filtros aplicados: <br> $filtrosMsg <br><br>";
}

$weekCounter = 1;
?>
<?php foreach ($data['weeks'] as $week): ?>
    <?php asort($week['rows']); if (count($week['rows']) > 0): ?>
        <table>
            <!-- <thead> -->
                <tr>
                    <th colspan="6">Semana <?php echo $weekCounter; $weekCounter++; ?></th>
                </tr>
                <tr>
                    <th>Vencimento</th>

                    <?php if ($showNaturezaColumn): ?>
                        <th>Natureza</th>
                    <?php endif; ?>

                    <?php if ($showCentroColumn): ?>
                        <th>Centro de custo</th>
                    <?php endif; ?>

                    <?php if ($showFornecedorColumn): ?>
                        <th>Fornecedor</th>
                    <?php endif; ?>

                    <th>Descrição</th>
                    <th>Valor (R$)</th>
                </tr>
            <!-- </thead> -->
            <tbody>
                <?php foreach ($week['rows'] as $row): ?>
                    <tr>
                        <td><?= implode('/', array_reverse(explode('-', $row['data_vencimento']))) ?></td>

                        <?php if ($showNaturezaColumn): ?>
                            <td><?= $row['natureza'] ?></td>
                        <?php endif; ?>

                        <?php if ($showCentroColumn): ?>
                            <td><?= $row['centro'] ?></td>
                        <?php endif; ?>

                        <?php if ($showFornecedorColumn): ?>
                            <td><?= $row['fornecedor'] ?? '-' ?></td>
                        <?php endif; ?>

                        <?php 
                            if(strlen($row['descricao']) > 50 && !$data['descricaoCompleta']) {
                                echo '<td>' . substr($row['descricao'], 0, 50) . '...</td>';
                            } else {
                                echo '<td>' . $row['descricao'] . '</td>';
                            }
                        ?>
                        
                        <td><?= Money::centavos_para_reais($row['valor_em_centavos']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="text-align: right;">Subtotal: R$ <?= Money::centavos_para_reais($week['total']) ?></p>
        <br><br><br>
    <?php endif; ?>
<?php endforeach; ?>
<p>Total: R$ <?= Money::centavos_para_reais($data['total']) ?></p>
Período: <?= $data['start_date']->format('d/m/Y') . ' - ' . $data['end_date']->format('d/m/Y') ?>

<style>
    td {
        text-align: center;
        padding: 3px 3px;
    }
</style>
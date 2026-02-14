<h1><?= ucfirst($filters['rowType']) ?> <?= $filters['status'] == 'todas' ? '' : $filters['status'] ?></h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/contas/cadastrar"><i class="fa-solid fa-plus"></i>
    Cadastrar</a>
<div class="section">
    <div class="section search-section">

        <form method="POST" action="" style="width: 100%;">
            <div id="all-filters">
                <div id="old-filters">
                    <input type="hidden" name="type" value="search">
                    <i class="search-icon fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
                    <select name="rowType">
                        <?php
                        $options = ['contas', 'parcelas'];

                        foreach ($options as $option) {
                            $selected = $filters['rowType'] == $option ? 'selected' : '';
                            echo "<option value='$option' $selected>" . ucfirst($option) . '</option>';
                        }
                        ?>
                    </select>
                    <select name="status">
                        <?php
                        $options = ['a pagar', 'pagas', 'todas'];

                        if ($filters['rowType'] == 'contas') {
                            $options[] = 'inativadas';
                        }

                        foreach ($options as $option) {
                            $selected = $filters['status'] == $option ? 'selected' : '';
                            echo "<option value='$option' $selected>" . ucfirst($option) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="new-filters">
                    <label for="natureza">Natureza:</label>
                    <select name="natureza" id="natureza" class="select2">
                        <option value="all">Todas</option>
                        <?php foreach ($availableFilterOptions['naturezas'] as $natureza): ?>
                            <?php
                                $selected = $natureza['id'] == $filters['natureza'] ? 'selected' : '';
                            ?>
                            <option <?= $selected ?> value="<?= $natureza['id'] ?>"><?= $natureza['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <label for="centro">Centro de custo:</label>
                    <select name="centro" id="centro" class="select2">
                        <option value="all">Todos</option>
                        <?php foreach ($availableFilterOptions['centros'] as $centro): ?>
                            <?php
                                $selected = $centro['id'] == $filters['centro'] ? 'selected' : '';
                            ?>
                            <option <?= $selected ?> value="<?= $centro['id'] ?>"><?= $centro['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <label for="fornecedor">Fornecedor:</label>
                    <select name="fornecedor" id="fornecedor" class="select2">
                        <option value="all">Todos</option>
                        <?php foreach ($availableFilterOptions['fornecedores'] as $fornecedor): ?>
                            <?php
                                $selected = $fornecedor['id'] == $filters['fornecedor'] ? 'selected' : '';
                            ?>
                            <option <?= $selected ?> value="<?= $fornecedor['id'] ?>"><?= $fornecedor['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <?php

    use App\Controllers\Services\Money;

    if ($filters['rowType'] == 'contas' && $contas == []) {
        echo '<p> Nenhum conta encontrada.';

        if($filters['natureza'] != 'all' || $filters['centro'] != 'all' || $filters['fornecedor'] != 'all') {
            echo ' <span id="clear-filters">limpar filtros</span>';
        }

        echo '</p>';
    }
    if ($filters['rowType'] == 'parcelas' && $parcelas == []) {
        echo '<p> Nenhum parcela encontrada.';

        if($filters['natureza'] != 'all' || $filters['centro'] != 'all' || $filters['fornecedor'] != 'all') {
            echo ' <span id="clear-filters">limpar filtros</span>';
        }

        echo '</p>';
    }
    ?>

    <?php if (count($contas) > 0): ?>
        <div class="table-section">
            <table id="contas-table">
                <thead>
                    <tr>
                        <th>Natureza</th>
                        <th>Centro de Custo</th>
                        <th>Fornecedor</th>
                        <th>Próximo vencimento</th>
                        <th>Parcelas pagas</th>
                        <th>Valor Total (R$)</th>
                    </tr>
                </thead>
                <tbody>
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
                            <td><?= $conta->natureza ?></td>
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
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (count($parcelas) > 0): ?>
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Natureza</th>
                        <th>Centro de custo</th>
                        <th>Fornecedor</th>
                        <th>Valor (R$)</th>
                        <th>Vencimento</th>
                        <th>Parcela</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parcelas as $parcela): ?>
                        <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $parcela['conta_id'] ?>';">
                            <td><?= $parcela['natureza'] ?></td>
                            <td><?= $parcela['centro'] ?></td>
                            <td><?= $parcela['fornecedor'] ?? '-' ?></td>
                            <td><?= Money::centavos_para_reais($parcela['valor_em_centavos']) ?></td>
                            <td class="nextInstallment"><?= new DateTime($parcela['data_vencimento'])->format('d/m/Y') ?></td>
                            <td><?= $parcela['numero_parcela'] . '/' . $parcela['total_parcelas'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
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

    #all-filters {
        display: flex;
        gap: 2em;
    }

    @media (max-width: 460px) {
        #all-filters {
            flex-wrap: wrap;
        }

        #new-filters {
            text-align: start !important;
        }
    }

    #old-filters {
        width: 55%;
    }

    #new-filters {
        width: 45%;
        text-align: end;
    }

    #new-filters label {
        margin-right: 0.5em;
    }

    #new-filters select {
        width: 7rem;
    }

    #new-filters span {
        text-align: start;
    }

    #clear-filters {
        color: blue;
        text-decoration: underline;
        cursor: pointer;
    }
</style>

<script>
    document.forms[0].addEventListener("change", function(e) {
        console.log(e);
        if (e.target.nodeName == "SELECT") {
            this.submit();
        }
    });

    $('#natureza').on('change', () => {
        document.forms[0].submit();
    });
    
    $('#centro').on('change', () => {
        document.forms[0].submit();
    });

    $('#fornecedor').on('change', () => {
        document.forms[0].submit();
    });

    const clearFilters = document.getElementById("clear-filters");
    if(clearFilters) {
        clearFilters.addEventListener("click", () => {
            $('#natureza').val('all');
            $('#centro').val('all');
            $('#fornecedor').val('all');
            document.forms[0].submit();
        })
    }
</script>

<?php if ($filters['status'] == 'a pagar'): ?>
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

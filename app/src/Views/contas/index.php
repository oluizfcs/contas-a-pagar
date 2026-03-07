<h1>Contas <?= $filters['status'] ?></h1>
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
                    <select name="status">
                        <?php
                        $options = ['a pagar', 'pagas', 'inativadas'];

                        foreach ($options as $option) {
                            $selected = $filters['status'] == $option ? 'selected' : '';
                            echo "<option value='$option' $selected>" . ucfirst($option) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="new-filters">
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
                    <br>
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
                </div>
            </div>
        </form>
    </div>
    <?php

    use App\Controllers\Services\Money;

    if (empty($parcelas)) {
        echo '<p> Nenhum parcela encontrada.';

        if ($filters['natureza'] != 'all' || $filters['centro'] != 'all' || $filters['fornecedor'] != 'all') {
            echo ' <span id="clear-filters">limpar filtros</span>';
        }

        echo '</p>';
    }
    ?>

    <?php if (count($parcelas) > 0): ?>
        <div class="table-section">
            <div style="display: flex; justify-content: center; gap: 10px; margin: 0 1em 1em 1em; text-align: center;" id="hidden-columns"></div>
            <table>
                <thead>
                    <tr>
                        <td class="column column1">Vencimento</td>
                        <td class="column column2">Fornecedor</td>
                        <td class="column column3">Natureza</td>
                        <td class="column column4">Centro de custo</td>
                        <td class="column column5">Descrição</td>
                        <td class="column column6">Parcela</td>
                        <td class="column column7">Valor (R$)</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parcelas as $parcela): ?>
                        <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $parcela['conta_id'] ?>';">
                            <td class="column column1 nextInstallment"><?= new DateTime($parcela['data_vencimento'])->format('d/m/Y') ?></td>
                            <td class="column column2"><?= $parcela['fornecedor'] ?? '-' ?></td>
                            <td class="column column3"><?= $parcela['natureza'] ?></td>
                            <td class="column column4"><?= $parcela['centro'] ?></td>
                            <td class="column column5"><?= $parcela['descricao'] ?></td>
                            <td class="column column6"><?= $parcela['numero_parcela'] . '/' . $parcela['total_parcelas'] ?></td>
                            <td class="column column7"><?= Money::centavos_para_reais($parcela['valor_em_centavos']) ?></td>
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

    thead tr td:hover {
        background-color: rgb(151, 4, 4);
        cursor: pointer;
    }

    table.filter1 td.column1 { display: none; }
    table.filter2 td.column2 { display: none; }
    table.filter3 td.column3 { display: none; }
    table.filter4 td.column4 { display: none; }
    table.filter5 td.column5 { display: none; }
    table.filter6 td.column6 { display: none; }
    table.filter7 td.column7 { display: none; }
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
    if (clearFilters) {
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
<?php endif; ?>

<?php if (count($parcelas) > 0): ?>
    <script>
        const hiddenColumns = document.getElementById("hidden-columns");

        document.querySelector("thead").addEventListener("click", (e) => {
            if (e.target.classList.contains("column")) {
                const column = e.target.classList[1];
                document.querySelector("table").classList.add("filter" + column.charAt(column.length -1));
                const revertBtn = document.createElement("button");
                revertBtn.classList.add("btn", "btn-secondary");
                revertBtn.textContent = e.target.textContent;
                revertBtn.value = column;
                revertBtn.addEventListener("click", function () {
                    document.querySelector("table").classList.remove("filter" + column.charAt(column.length -1));
                    this.remove();
                });
                hiddenColumns.append(revertBtn);
            }
        });
    </script>
<?php endif; ?>

<h1>Fornecedores</h1>
<a class="btn btn-success" href="/fornecedores/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
            <select name="status">
                <?php
                $options = ['contas a pagar', 'contas pagas', 'contas pagas e não pagas', 'inativados'];

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

    if ($fornecedores == []) {
        echo '<p> Nenhum fornecedor encontrado.</p>';
    }
    ?>

    <?php if (count($fornecedores) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Total (R$)</th>
                    <th>Quantidade</th>
                    <th>Média (R$)</th>
                </tr>
                <?php foreach ($fornecedores as $fornecedor): ?>
                    <tr onclick="window.location.href='/fornecedores/detalhar/<?= $fornecedor['id'] ?>';">
                        <td><?= $fornecedor['nome'] ?></td>
                        <td><?= $fornecedor['telefone'] ?></td>
                        <td><?= Money::centavos_para_reais($fornecedor['total']) ?></td>
                        <td><?= $fornecedor['quantidade'] ?></td>
                        <td><?= Money::centavos_para_reais($fornecedor['media']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
</div>
<?php endif; ?>
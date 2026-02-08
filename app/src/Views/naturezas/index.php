<h1>Naturezas</h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/naturezas/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
            <select name="status" onchange="form.submit()">
                <?php
                $options = ['contas a pagar', 'contas pagas', 'todas', 'inativadas'];
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

    if ($naturezas == []) {
        echo '<p> Nenhum natureza encontrada.</p>';
    }
    ?>

    <?php if (count($naturezas) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Nome</th>
                    <th>Total (R$)</th>
                    <th>Quantidade</th>
                    <th>Média (R$)</th>
                </tr>
                <?php foreach ($naturezas as $natureza): ?>
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/naturezas/detalhar/<?= $natureza['id'] ?>';">
                        <td><?= $natureza['nome'] ?></td>
                        <td><?= Money::centavos_para_reais($natureza['total']) ?></td>
                        <td><?= $natureza['quantidade'] ?></td>
                        <td><?= Money::centavos_para_reais($natureza['media']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
</div>
<?php endif; ?>
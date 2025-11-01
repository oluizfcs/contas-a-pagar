<h1>Centros de Custo</h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
            <select name="status" onchange='form.submit()'>
                <?php
                $options = ['contas a pagar', 'contas pagas', 'contas pagas e não pagas', 'inativados'];

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

    if ($centrosDeCusto == []) {
        echo '<p> Nenhum centro de custo encontrado.</p>';
    }
    ?>

    <?php if (count($centrosDeCusto) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Nome</th>
                    <th>Total (R$)</th>
                    <th>Quantidade</th>
                    <th>Média (R$)</th>
                </tr>
                <?php foreach ($centrosDeCusto as $centro): ?>
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/centros-de-custo/detalhar/<?= $centro['id'] ?>';">
                        <td><?= $centro['nome'] ?></td>
                        <td><?= Money::centavos_para_reais($centro['total']) ?></td>
                        <td><?= $centro['quantidade'] ?></td>
                        <td><?= Money::centavos_para_reais($centro['media']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
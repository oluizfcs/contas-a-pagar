<h1>Centros de Custo<?= $this->mostrar == 'inativados' ? ' Inativados' : '' ?></h1>
<a class="btn" href="/centros-de-custo/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<br><br>
<div class="section">
    <form method="POST" action="">
        <input type="hidden" name="type" value="search">
        <input type="hidden" name="mostrar" id="mostrar">
        <i class="search-icon fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
        <label for="orderby">Ordenar por:</label>
        <select name="orderby" id="orderby">
            <?php
            $options = ['total', 'quantidade', 'media'];

            foreach ($options as $option) {
                $selected = $this->orderby == $option ? 'selected' : '';
                echo "<option value='$option' $selected onclick='form.submit()'>" . ucfirst($option) . '</option>';
            }
            ?>
        </select>
        <label>
            <input type="radio" name="mostrar" value="" <?= $this->mostrar == '' ? 'checked' : '' ?> onclick="form.submit()">
            contas a pagar
        </label>

        <label>
            <input type="radio" name="mostrar" value="todos" <?= $this->mostrar == 'todos' ? 'checked' : '' ?> onclick="form.submit()">
            todos
        </label>

        <label>
            <input type="radio" name="mostrar" value="inativados" <?= $this->mostrar == 'inativados' ? 'checked' : '' ?> onclick="form.submit()">
            inativados
        </label>
    </form>
</div>
<?php

use App\Controllers\Services\Money;

if ($centrosDeCusto == []) {
    echo '<p> Nenhum centro de custo encontrado.</p>';
}
?>

<?php if (count($centrosDeCusto) > 0): ?>
    <div class="section">
        <table>
            <tr>
                <th colspan="2">Informações do Centro de Custo</th>
                <th colspan="3"><?= $this->mostrar == '' ? 'Contas a pagar' : 'Todas as contas (Pagas e não pagas)' ?></th>
            </tr>
            <tr>
                <th>Nome</th>
                <th>Total (R$)</th>
                <th>Quantidade</th>
                <th>Média (R$)</th>
            </tr>
            <?php foreach ($centrosDeCusto as $centro): ?>
                <tr onclick="window.location.href='/centros-de-custo/detalhar/<?= $centro['id'] ?>';">
                    <td><?= $centro['nome'] ?></td>
                    <td><?= Money::centavos_para_reais($centro['total']) ?></td>
                    <td><?= $centro['quantidade'] ?></td>
                    <td><?= Money::centavos_para_reais($centro['media']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
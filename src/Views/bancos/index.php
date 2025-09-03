<h1>Contas bancárias<?= $this->mostrar == 'inativadas' ? ' Inativadas' : '' ?></h1>
<a class="btn" href="/bancos/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
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
            <input type="radio" name="mostrar" value="inativadas" <?= $this->mostrar == 'inativadas' ? 'checked' : '' ?> onclick="form.submit()">
            inativadas
        </label>
    </form>
</div>
<?php

use App\Controllers\Services\Money;

if ($bancos == []) {
    echo '<p> Nenhum banco encontrado.</p>';
}
?>

<?php if (count($bancos) > 0): ?>
    <div class="section">
        <table>
            <tr>
                <th colspan="2">Informações da Conta</th>
                <th colspan="3"><?= $this->mostrar == '' ? 'Contas a pagar' : 'Todas as contas (Pagas e não pagas)' ?></th>
            </tr>
            <tr>
                <th>Nome</th>
                <th>Saldo</th>
                <th>Total (R$)</th>
                <th>Quantidade</th>
                <th>Média (R$)</th>
            </tr>
            <?php foreach ($bancos as $banco): ?>
                <tr onclick="window.location.href='/bancos/detalhar/<?= $banco['id'] ?>';">
                    <td><?= $banco['nome'] ?></td>
                    <td><?= Money::centavos_para_reais($banco['saldo_em_centavos']) ?></td>
                    <td><?= Money::centavos_para_reais($banco['total']) ?></td>
                    <td><?= $banco['quantidade'] ?></td>
                    <td><?= Money::centavos_para_reais($banco['media']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
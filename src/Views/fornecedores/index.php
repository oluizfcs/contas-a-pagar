<h1>Fornecedores<?= $this->mostrar == 'inativados' ? ' Inativados' : '' ?></h1>
<a class="btn" href="/fornecedores/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
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
        <input type="checkbox" name="mostrar" value="todos" <?= $this->mostrar == 'todos' ? 'checked' : ''?> onclick="form.submit()">todos os fornecedores
        <input type="checkbox" name="mostrar" value="inativados" <?= $this->mostrar == 'inativados' ? 'checked' : ''?> onclick="form.submit()">apenas fornecedores inativados
    </form>
</div>
<?php

use App\Controllers\Services\Money;

if ($fornecedores == []) {
    echo '<p> Nenhum fornecedor encontrado.</p>';
}
?>

<?php if (count($fornecedores) > 0): ?>
    <div class="section">
        <table>
            <tr>
                <th colspan="2">Informações do Fornecedor</th>
                <th colspan="3"><?= $this->mostrar == 'emAberto' ? 'Contas a pagar' : 'Todas as contas (Pagas e não pagas)' ?></th>
            </tr>
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
                    <td><?= Money::formatar_centavos($fornecedor['total']) ?></td>
                    <td><?= $fornecedor['quantidade'] ?></td>
                    <td><?= Money::formatar_centavos($fornecedor['media']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
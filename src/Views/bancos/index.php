<h1>Contas bancárias</h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/bancos/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
            <select name="status" onchange='form.submit()'>
                <?php
                $options = ['contas a pagar', 'contas pagas', 'todos', 'inativadas'];

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

    if ($bancos == []) {
        echo '<p> Nenhum banco encontrado.</p>';
    }
    ?>

    <?php if (count($bancos) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Nome</th>
                    <th>Saldo (R$)</th>
                </tr>
                <?php foreach ($bancos as $banco): ?>
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/bancos/detalhar/<?= $banco['id'] ?>';">
                        <td><?= $banco['nome'] ?></td>
                        <td><?= Money::centavos_para_reais($banco['saldo_em_centavos']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
<h1>Contas bancárias<?= !$this->enabled ? ' inativadas' : '' ?></h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/bancos/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
            <label>
                <input type="radio" name="enabled" value="1" <?= $this->enabled ? 'checked' : '' ?> onclick="form.submit()">
                ativas
            </label>

            <label>
                <input type="radio" name="enabled" value="0" <?= !$this->enabled ? 'checked' : '' ?> onclick="form.submit()">
                inativadas
            </label>
        </form>
    </div>
    <?php

    use App\Controllers\Services\Money;

    if ($bancos == []) {
        echo '<p> Nenhuma conta bancária encontrada.</p>';
    }
    ?>

    <?php if (count($bancos) > 0): ?>
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Saldo (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bancos as $banco): ?>
                        <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/bancos/detalhar/<?= $banco['id'] ?>';">
                            <td><?= $banco['nome'] ?></td>
                            <td><?= Money::centavos_para_reais($banco['saldo_em_centavos']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
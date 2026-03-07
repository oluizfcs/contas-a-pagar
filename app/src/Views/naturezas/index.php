<h1>Naturezas<?= !$this->enabled ? ' inativadas' : ''?></h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/naturezas/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
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

    if ($naturezas == []) {
        echo '<p> Nenhuma natureza encontrada.</p>';
    }
    ?>

    <?php if (count($naturezas) > 0): ?>
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($naturezas as $natureza): ?>
                        <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/naturezas/detalhar/<?= $natureza['id'] ?>';">
                            <td><?= $natureza['nome'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
</div>
<?php endif; ?>
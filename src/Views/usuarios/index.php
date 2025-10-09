<h1>Usuarios<?= $this->mostrar == 'inativados' ? ' Inativados' : '' ?></h1>
<?php if ($_SESSION['usuario_id'] == 1): ?>
    <a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/usuarios/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<?php endif; ?>
<div class="section">
    <div class="section search-section">

        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <input type="hidden" name="mostrar" id="mostrar">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">

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

    use App\Controllers\Services\Cpf;

    if ($usuarios == []) {
        echo '<p> Nenhum usuario encontrado.</p>';
    }
    ?>

    <?php if (count($usuarios) > 0): ?>
        <div class="table-section">
            <table class="sortable">
                <tr>
                    <th>Nome</th>
                    <th class="sorttable_nosort">CPF</th>
                </tr>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/usuarios/detalhar/<?= $usuario['id'] ?>';">
                        <td><?= $usuario['nome'] ?></td>
                        <td><?= Cpf::maskCpf($usuario['cpf']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
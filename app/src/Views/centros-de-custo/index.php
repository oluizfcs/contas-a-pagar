<h1>Centros de custo<?= !$this->enabled ? ' inativados' : '' ?></h1>
<a class="btn btn-success" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo/cadastrar"><i class="fa-solid fa-plus"></i> Cadastrar</a>
<div class="section">
    <div class="section search-section">
        <form method="POST" action="">
            <input type="hidden" name="type" value="search">
            <i class="search-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="search" autocomplete="off" value="<?= $this->search ?? '' ?>">
            <label>
                <input type="radio" name="enabled" value="1" <?= $this->enabled ? 'checked' : '' ?> onclick="form.submit()">
                ativos
            </label>

            <label>
                <input type="radio" name="enabled" value="0" <?= !$this->enabled ? 'checked' : '' ?> onclick="form.submit()">
                inativados
            </label>
        </form>
    </div>
    <?php

    if ($centrosDeCusto == []) {
        echo '<p> Nenhum centro de custo encontrado.</p>';
    }
    ?>

    <?php if (count($centrosDeCusto) > 0): ?>
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($centrosDeCusto as $centro): ?>
                        <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/centros-de-custo/detalhar/<?= $centro['id'] ?>';">
                            <td style="position: relative;">
                                <?php if (count($centro['children']) > 0): ?>
                                    <i class="fa-solid fa-chevron-right toggle-btn"
                                        style="
                                            cursor: pointer;
                                            position: absolute;
                                            left: 1px; top: 50%;
                                            width: 26px;
                                            transform: translateY(-50%);"
                                        onclick="event.stopPropagation(); toggleChildren(this, 'child-of-<?= $centro['id'] ?>')"></i>
                                <?php endif; ?>
                                <?= $centro['nome'] ?>
                            </td>
                        </tr>

                        <?php if (count($centro['children']) > 0): ?>
                            <?php foreach ($centro['children'] as $child): ?>
                                <tr class="child-of-<?= $centro['id'] ?>" style="display: none; background-color: #f9f9f9;" onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/centros-de-custo/detalhar/<?= $child['id'] ?>';">
                                    <td style="position: relative;">
                                        <i class="fa-solid fa-turn-up fa-rotate-90" style="font-size: 0.6em; color: #888; position: absolute; left: 10px; top: 50%;"></i>
                                        <?= $child['nome'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleChildren(btn, className) {
        const rows = document.getElementsByClassName(className);
        let isHidden = true;

        for (let row of rows) {
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
                isHidden = false;
            } else {
                row.style.display = 'none';
            }
        }

        if (isHidden) {
            btn.classList.remove('fa-chevron-down');
            btn.classList.add('fa-chevron-right');
        } else {
            btn.classList.remove('fa-chevron-right');
            btn.classList.add('fa-chevron-down');
        }
    }
</script>
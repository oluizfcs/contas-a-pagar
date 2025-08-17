fornecedores
<br>
<a href="<?= $_ENV['BASE_URL'] . '/fornecedores/adicionar' ?>">Adicionar</a>

<table>
    <tr>
        <th>Nome</th>
        <th>Telefone</th>
        <th>Opções</th>
    </tr>
    <?php foreach($fornecedores as $fornecedor): ?>
        <tr>
            <td><?= $fornecedor['nome'] ?></td>
            <td><?= $fornecedor['telefone'] ?></td>
            <td><a href="<?= $_ENV['BASE_URL'] . '/fornecedores/detalhar/' . $fornecedor['id'] ?>"><i class="fa-solid fa-magnifying-glass"></i></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php if($this->page > 1): ?>
<a href="<?= $_ENV['BASE_URL'] . '/fornecedores/' . $this->page - 1?>">< </a>
<?php endif; ?>
<?php echo 'Página ' . $this->page . ' de ' . $lastPage; ?>
<?php if($this->page < $lastPage): ?>
<a href="<?= $_ENV['BASE_URL'] . '/fornecedores/' . $this->page + 1?>"> ></a>
<?php endif; ?>
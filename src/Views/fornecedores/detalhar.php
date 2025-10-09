<?php
$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $fornecedor->getData_criacao());
if ($fornecedor->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $fornecedor->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}
?>

<h1>Fornecedor: <?= $fornecedor->getNome() ?></h1>
Telefone: <?= $fornecedor->getTelefone() ?> <br>
<br>
<a class="btn btn-secondary" href="/fornecedores">Voltar</a>
<a class="btn btn-secondary" href="/fornecedores/atualizar/<?= $fornecedor->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>

<form method="POST" action="">
    <input type="hidden" name="fornecedor_id" value="<?= $fornecedor->getId() ?>">
    <?php if($fornecedor->isEnabled()): ?>
        <button class="btn btn-secondary" name="type" value="unable" onclick="return confirm('Realmente deseja inativar este fornecedor?')">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </button>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-open"></i> Ativar</a>
        </a>
    <?php else: ?>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </a>
        <button class="btn btn-secondary" name="type" value="enable" onclick="return confirm('Realmente deseja ativar este fornecedor?')">
            <i class="fa-solid fa-box-open"></i> Ativar</a>
        </button>
    <?php endif; ?>
</form>


<div class="section">
    <h2><i class="fa-solid fa-chart-simple"></i> Estatísticas</h2>
    <img src="/public/images/graph.jpg" alt="example graph" width="525" height="412.5">
</div>

<?php include 'templates/auditoria.php'; ?>
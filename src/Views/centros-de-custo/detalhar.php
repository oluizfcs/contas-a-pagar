<?php
$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $centro_de_custo->getData_criacao());
if ($centro_de_custo->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $centro_de_custo->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}
?>

<h1>Centro de custo: <?= $centro_de_custo->getNome() ?></h1>
<br>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo">Voltar</a>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo/atualizar/<?= $centro_de_custo->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>

<form method="POST" action="">
    <input type="hidden" name="centro_de_custo_id" value="<?= $centro_de_custo->getId() ?>">
    <?php if($centro_de_custo->isEnabled()): ?>
        <button class="btn btn-secondary" name="type" value="unable" onclick="return confirm('Realmente deseja inativar este centro de custo?')">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </button>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-open"></i> Ativar
        </a>
    <?php else: ?>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </a>
        <button class="btn btn-secondary" name="type" value="enable" onclick="return confirm('Realmente deseja ativar este centro de custo?')">
            <i class="fa-solid fa-box-open"></i> Ativar
        </button>
    <?php endif; ?>
</form>


<div class="section">
    <h2><i class="fa-solid fa-chart-simple"></i> Estatísticas</h2>
    <img src="/public/images/graph.jpg" alt="example graph" width="525" height="412.5">
</div>

<?php include 'templates/auditoria.php'; ?>
<?php
$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $centro_de_custo->getData_criacao());
if ($centro_de_custo->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $centro_de_custo->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}

use App\Controllers\Services\Money;
use App\Models\CentroDeCusto;

?>
<?php if ($centro_de_custo->getCategoriaId() != null): ?>
    <h1>Sub-centro de custo: <?= $centro_de_custo->getNome() ?></h1>
    Centro de custo superior: <?= CentroDeCusto::getById($centro_de_custo->getCategoriaId())->getNome() ?>
    <br>
<?php else: ?>
    <h1>Centro de custo: <?= $centro_de_custo->getNome() ?></h1>
<?php endif; ?>
<br>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo">Voltar</a>
<a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/centros-de-custo/atualizar/<?= $centro_de_custo->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>

<form method="POST" action="">
    <input type="hidden" name="centro_de_custo_id" value="<?= $centro_de_custo->getId() ?>">
    <?php if ($centro_de_custo->isEnabled()): ?>
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


<?php if (count($contas) > 0): ?>
    <div class="section">
        <h2><i class="fa-solid fa-receipt"></i> Contas a pagar</h2>
        <div class="table-section">
            <table class="sortable" id="contas-table">
                <tr>
                    <th>Fornecedor</th>
                    <th>Próximo vencimento</th>
                    <th>Parcelas pagas</th>
                    <th>Valor Total (R$)</th>
                </tr>
                <?php foreach ($contas as $conta): ?>
                    <?php

                    $nextInstallment = null;
                    $paidInstallments = 0;
                    $nextInstallmentPrice = null;

                    foreach ($conta->getParcelas() as $parcela) {
                        if (!$parcela->isPaid() && $nextInstallment == null) {
                            $nextInstallment = new DateTime($parcela->getData_vencimento())->format('d/m/Y');
                            $nextInstallmentPrice = Money::centavos_para_reais($parcela->getValor_em_centavos());
                        }

                        if ($parcela->isPaid()) {
                            $paidInstallments += 1;
                        }
                    }
                    ?>
                    <tr onclick="window.open('<?= $_ENV['BASE_URL'] ?>/contas/detalhar/<?= $conta->getId() ?>', '_blank');">
                        <td><?= $conta->fornecedor ?? '-' ?></td>
                        <?php if ($nextInstallment == null): ?>
                            <td>-</td>
                        <?php else: ?>
                            <td><?= "$nextInstallment<br><span style='color: #777; font-size: smaller;'>(R$ $nextInstallmentPrice)</span>" ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= $paidInstallments . '/' . count($conta->getParcelas()) ?>
                        </td>
                        <td><?= Money::centavos_para_reais($conta->getValor_em_centavos()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($subCentros) && count($subCentros) > 0): ?>
    <div class="section">
        <h2><i class="fa-solid fa-receipt"></i> Sub-centros</h2>
        <div class="table-section">
            <table class="sortable" id="centros-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Total em contas a pagar (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subCentros as $centro): ?>
                        <tr onclick="window.location.href='<?= $_ENV['BASE_URL'] ?>/centros-de-custo/detalhar/<?= $centro['id'] ?>';">
                            <td><?= $centro['nome'] ?></td>
                            <td><?= Money::centavos_para_reais($centro['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <button class="btn btn-success" onclick="openModal()">
                <i class="fa-solid fa-plus"></i> Cadastrar Sub-centro
            </button>
        </div>
    </div>
<?php elseif (is_null($centro_de_custo->getCategoriaId())): ?>
    <div class="section">
        <h2><i class="fa-solid fa-receipt"></i> Sub centros de custo</h2>
        <br>
        <p>Nenhum sub-centro cadastrado.</p>
        <br>
        <button class="btn btn-success" onclick="openModal()">
            <i class="fa-solid fa-plus"></i> Cadastrar Sub-centro
        </button>
    </div>
<?php endif; ?>

<!-- Modal -->
<div id="newSubCenterModal" class="modal" style="display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:40%; border-radius: 8px;">
        <span class="close" onclick="closeModal()" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
        <h2>Cadastrar Sub-centro</h2>
        <form id="newSubCenterForm">
            <input type="hidden" name="type" value="create">
            <input type="hidden" name="ajax" value="true">
            <input type="hidden" name="categoria_id" value="<?= $centro_de_custo->getId() ?>">

            <label for="new_nome">Nome:</label>
            <div class="input-wrapper">
                <input type="text" id="new_nome" name="nome" required maxlength="45" autocomplete="off">
            </div>
            <br>
            <button class="btn btn-primary" type="submit">Cadastrar</button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById("newSubCenterModal").style.display = "block";
        document.getElementById("new_nome").focus();
    }

    function closeModal() {
        document.getElementById("newSubCenterModal").style.display = "none";
    }

    document.getElementById('newSubCenterForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= $_ENV['BASE_URL'] ?>/centros-de-custo', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro: ' + (data.message || 'Desconhecido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao processar requisição.');
            });
    });

    window.onclick = function(event) {
        if (event.target == document.getElementById("newSubCenterModal")) {
            closeModal();
        }
    }
</script>

<?php include '../src/templates/auditoria.php'; ?>
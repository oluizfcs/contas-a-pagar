<?php

use App\Controllers\Services\Money;
use App\Models\Usuario;

$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $banco->getData_criacao());
if ($banco->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $banco->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}

$formatter = new NumberFormatter('PT_BR', NumberFormatter::SPELLOUT);
$extenso = $formatter->format($banco->getSaldo_em_centavos() / 100);
?>

<h1>Conta bancária: <?= $banco->getNome() ?></h1>
<p title="<?= $extenso ?>">Saldo: R$ <?= Money::centavos_para_reais($banco->getSaldo_em_centavos()) ?></p>
<br>
<a class="btn" href="/bancos" style="margin-right: 3px;">Voltar</a> <a class="btn" href="/bancos/atualizar/<?= $banco->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Atualizar</a>
<br><br>

<form method="POST" action="">
    <input type="hidden" name="banco_id" value="<?= $banco->getId() ?>">
    <?php if ($banco->isEnabled()): ?>
        <button class="btn" name="type" value="unable" onclick="return confirm('Realmente deseja inativar esta conta bancária?')">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </button>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-open"></i> Ativar</a>
        </a>
    <?php else: ?>
        <a class="btn btn-disabled">
            <i class="fa-solid fa-box-archive"></i> Inativar
        </a>
        <button class="btn" name="type" value="enable" onclick="return confirm('Realmente deseja ativar esta conta bancária?')">
            <i class="fa-solid fa-box-open"></i> Ativar</a>
        </button>
    <?php endif; ?>
</form>

<div class="section">
    <h2><i class="fa-solid fa-up-down"></i> Extrato</h2>
</div>

<div class="section">
    <h2><i class="fa-solid fa-chart-simple"></i> Estatísticas</h2>
    <img src="/public/images/graph.jpg" alt="example graph" width="525" height="412.5">
</div>

<div class="section">
    <h2><i class="fa-solid fa-scroll"></i> Auditoria</h2>
    <?php
    echo "Cadastrado em: " . $data_criacao->format("d/m/Y \à\s H:i");
    if (isset($data_edicao)) {
        echo "<br>Última modificação: $data_edicao";
    }
    ?>

    <table class="auditoria">
        <tr>
            <th>Usuário</th>
            <th>Ação</th>
            <th>Campo</th>
            <th>Antigo</th>
            <th>Novo</th>
            <th>Data e Hora</th>
        </tr>
        <?php foreach ($logs as $log) {
            $usuario = Usuario::getById($log['usuario_id'])->getNome();
            $data = DateTime::createFromFormat('Y-m-d H:i:s', $log['data_log'])->format('d/m/Y \à\s H:i');

            $msg = "<br> $usuario";
            $acao = 'atualizar';
            $campo = $log['campo'];
            $antigo = $log['valor_antigo'];
            $novo = $log['valor_novo'];

            if (in_array($log['campo'], ['create', 'unable', 'enable'])) {
                $campo = '-';
                $antigo = '-';
                $novo = '-';

                switch($log['campo']) {
                    case 'create':
                        $acao = 'cadastrar';
                        break;
                    case 'unable':
                        $acao = 'inativar';
                        break;
                    case 'enable':
                        $acao = 'ativar';
                }
            }

            echo "<tr>";
            echo "<td>$usuario</td>";
            echo "<td>$acao</td>";
            echo "<td>$campo</td>";
            echo "<td>$antigo</td>";
            echo "<td>$novo</td>";
            echo "<td>$data</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>
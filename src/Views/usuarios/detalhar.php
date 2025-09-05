<?php

use App\Models\Usuario;

$data_criacao = DateTime::createFromFormat("Y-m-d H:i:s", $usuario->getData_criacao());
if ($usuario->getData_edicao() != null) {
    $data_edicao = DateTime::createFromFormat("Y-m-d H:i:s", $usuario->getData_edicao());
    $data_edicao = $data_edicao->format("d/m/Y \à\s H:i");
} else {
    $data_edicao = 'nunca';
}

?>

<h1>Usuario: <?= $usuario->getNome() ?></h1>
CPF: <?= $usuario->getMaskedCpf() ?> <br>
<br>
<a class="btn" href="/usuarios" style="margin-right: 3px;">Voltar</a> <?php if ($_SESSION['usuario_id'] == $usuario->getId() || $_SESSION['usuario_id'] == 1): ?> <a class="btn" href="/usuarios/alterar-senha/<?= $usuario->getId() ?>"><i class="fa-solid fa-pen-to-square"></i> Alterar Senha</a> <?php endif; ?>
<br><br>

<?php if ($_SESSION['usuario_id'] == 1): ?>
    <form method="POST" action="">
        <input type="hidden" name="usuario_id" value="<?= $usuario->getId() ?>">
        <?php if ($usuario->isEnabled()): ?>
            <button class="btn" name="type" value="unable" onclick="return confirm('Realmente deseja inativar este usuário?')">
                <i class="fa-solid fa-box-archive"></i> Inativar
            </button>
            <a class="btn btn-disabled">
                <i class="fa-solid fa-box-open"></i> Ativar</a>
            </a>
        <?php else: ?>
            <a class="btn btn-disabled">
                <i class="fa-solid fa-box-archive"></i> Inativar
            </a>
            <button class="btn" name="type" value="enable" onclick="return confirm('Realmente deseja ativar este usuario?')">
                <i class="fa-solid fa-box-open"></i> Ativar</a>
            </button>
        <?php endif; ?>
    </form>
<?php endif; ?>

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

            if ($log['campo'] == 'create') {
                $acao = 'cadastrar';
                $campo = '-';
                $antigo = '-';
                $novo = '-';
            }

            if ($log['campo'] == 'unable') {
                $acao = 'inativar';
                $campo = '-';
                $antigo = '-';
                $novo = '-';
            }

            if ($log['campo'] == 'enable') {
                $acao = 'ativar';
                $campo = '-';
                $antigo = '-';
                $novo = '-';
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
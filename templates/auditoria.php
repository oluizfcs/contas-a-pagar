<?php

use App\Models\Usuario;
?>
<div class="section">
    <h2><i class="fa-solid fa-scroll"></i> Auditoria</h2>
    <?php
    echo "Cadastrado em: " . $data_criacao->format("d/m/Y \à\s H:i");
    if (isset($data_edicao)) {
        echo "<br>Última modificação: $data_edicao";
    }
    ?>

    <div class="table-section">
        <table class="non-clickable-table">
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

                    switch ($log['campo']) {
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
</div>
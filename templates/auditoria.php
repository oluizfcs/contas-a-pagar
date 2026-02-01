<?php

use App\Models\Usuario;
use App\Models\Banco;
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
        <table id="audit-table" class="non-clickable-table">
            <tr>
                <th>Usuário</th>
                <th>Ação</th>
                <th>Campo</th>
                <th>Antigo</th>
                <th>Novo</th>
                <th>Data e Hora</th>
            </tr>
            <?php 
            $query = explode('/', $_SERVER['QUERY_STRING']);
            if ($query[0] == 'url=contas' && $query[1] == 'detalhar') {
                $numeroParcelaBanco = [];
                foreach($logs as $log) {
                    if(str_contains($log['campo'], 'Parcela ')) {
                        $numero = (int) explode(' ', $log['campo'])[1];
                        $parcela = null;
                        foreach($conta->getParcelas() as $p) {
                            if($p->getNumero_parcela() == (int) $numero) {
                                $parcela = $p;
                            }
                        }
                        $numeroParcelaBanco[$numero] = Banco::getById($parcela->getBanco_id())->getNome();
                    }
                }
            }

            foreach ($logs as $log) {
                $usuario = Usuario::getById($log['usuario_id'])->getNome();
                $data = DateTime::createFromFormat('Y-m-d H:i:s', $log['data_log'])->format('d/m/Y \à\s H:i');

                $msg = "<br> $usuario";
                $acao = 'atualizar';
                $campo = $log['campo'];
                $antigo = $log['valor_antigo'];
                $novo = $log['valor_novo'];

                if (in_array($log['campo'], ['create', 'unable', 'enable', 'pay'])) {
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

                if (str_contains($log['campo'], 'Parcela ')) {
                    $acao = 'marcar como pago <br> <span style="font-size: smaller; color: #555">Banco: ' . $numeroParcelaBanco[explode(' ', $log['campo'])[1]] . '</span>';
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

<script>
    [...document.getElementById("audit-table").childNodes[1].childNodes].splice(1).forEach(e => {
        if (e.tagName != "TR") {
            return;
        }

        e.childNodes.forEach(td => {
            if (td.tagName != "TD" || td.textContent.length <= 50) {
                return;
            }

            let savedText = td.textContent;
            td.textContent = td.textContent.slice(0, 50) + "... ";
            const a = document.createElement("a");
            a.textContent = "Ler mais";
            a.classList.add("clickable-text")
            td.appendChild(a);

            a.addEventListener("click", function () {
                td.innerHTML = savedText;
            });
        });
    });
</script>
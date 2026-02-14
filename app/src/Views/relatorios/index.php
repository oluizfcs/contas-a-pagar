<h1>Relatórios</h1>

<div style="display: flex; justify-content: center; width: 100%;">
    <div class="section">
        <form method="POST" style="display: flex; justify-content: center; margin: 0 auto;">
            <div id="form-container">
                <div class="input-group">
                    <div class="input-container">
                        <label for="start_date">Data inicial:</label>
                        <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="input-container">
                        <label for="end_date">Data final:</label>
                        <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="input-container">
                        <label for="status">Situação:</label>
                        <select name="status" id="status">
                            <option value="all">Todas as parcelas</option>
                            <option value="unpaid">Parcelas a pagar</option>
                            <option value="paid">Parcelas pagas</option>
                        </select>
                    </div>
                </div>
                <div>
                    <input type="checkbox" name="descricaoCompleta" id="descricaoCompleta">
                    <label for="descricaoCompleta">Mostrar descrições completas</label>
                </div>
                <hr>
                <div class="input-group">
                    <div class="input-container">
                        <label for="natureza">Natureza:</label>
                        <select name="natureza" id="natureza" class="select2">
                            <option value="all">Todas</option>
                            <?php foreach ($naturezas as $natureza): ?>
                                <option value="<?= $natureza['id'] ?>"><?= $natureza['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-container">
                        <label for="centro">Centro de Custo:</label>
                        <select name="centro" id="centro" class="select2">
                            <option value="all">Todos</option>
                            <?php foreach ($centros as $centro): ?>
                                <option value="<?= $centro['id'] ?>"><?= $centro['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-container">
                        <label for="fornecedor">Fornecedor:</label>
                        <select name="fornecedor" id="fornecedor" class="select2">
                            <option value="all">Todos</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?= $fornecedor['id'] ?>"><?= $fornecedor['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <hr>
                <button type="button" onclick="gerarRelatorio()" class="btn btn-success" style="display: block; margin: 25px auto -15px auto;"><i class="fa-solid fa-plus"></i> Gerar relatório</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="generatingPdfModal" class="modal" style="display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:40%; border-radius: 8px;">
        Gerando PDF <span class="loader"></span>
    </div>
</div>

<style>
    #form-container {
        display: flex;
        flex-direction: column;
    }

    .input-container {
        display: flex;
        flex-direction: column;
    }

    .input-group {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 10px;
        margin: 20px 0;
    }

    .modal-content {
        display: flex;
        justify-content: space-between;
    }

    hr {
        width: 50%;
        border: none;
        border-top: 1px solid rgba(0, 0, 0, 0.14);
    }

    .loader {
        width: 24px;
        height: 24px;
        border: 5px solid;
        border-color: hsl(214, 77%, 14%) transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }

    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    const form = document.forms[0];

    function openModal() {
        document.getElementById("generatingPdfModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("generatingPdfModal").style.display = "none";
    }

    async function gerarRelatorio() {
        openModal();
        const formData = new FormData(form);

        try {
            const response = await fetch('<?= $_ENV['BASE_URL'] . '/relatorios' ?>', {
                method: 'POST',
                body: formData
            });

            // form.submit();
            // return;

            const blob = await response.blob();

            if(blob.type != "application/pdf") {
                alert("Não há registros que satisfaçam os filtros selecionados.");
                window.location.href = '/relatorios';
                return;
            }

            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = "relatorio_" + formData.get("start_date") + "_" + formData.get("end_date");
            link.target = '_blank';

            document.body.append(link);
            link.click();
            link.remove();

            setTimeout(() => URL.revokeObjectURL(link.href), 100);
        } catch (error) {
            alert("Houve um erro ao gerar PDF" + error.message);
        } finally {
            closeModal();
        }
    }
</script>
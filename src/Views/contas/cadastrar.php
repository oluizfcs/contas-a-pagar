<div class="form-section">
    <div class="section">
        <h1>Cadastrar Conta</h1>
        <form action="" method="post">
            <input type="hidden" id="today" value="<?= new DateTime()->format("Y-m-d") ?>">
            <div class="section">
                <label for="fornecedor">Fornecedor:</label>
                <select id="fornecedor" name="fornecedor" class="select2" style="width: 100%;">
                    <option value="-1">-- Nenhum --</option>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= $fornecedor['id'] ?>" <?= ($_SESSION['post_data']['fornecedor'] ?? null) == $fornecedor['id'] ? 'selected' : '' ?>><?= $fornecedor['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <label for="centros">Centro de custo:</label>
                <select id="centros" name="centro" class="select2" style="width: 100%;">
                    <?php foreach ($centros as $centro): ?>
                        <option value="<?= $centro['id'] ?>" <?= ($_SESSION['post_data']['centro'] ?? null) == $centro['id'] ? 'selected' : '' ?>><?= $centro['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <label for="descricao">Descrição/Observações:</label>
                <div class="input-wrapper">
                    <textarea id="descricao" name="descricao" required maxlength="500" autocomplete="off"><?= isset($_SESSION['post_data']['descricao']) ? $_SESSION['post_data']['descricao'] : '' ?></textarea>
                </div>
                <br>
                <div class="input-wrapper">
                    <label for="valor_em_reais">Valor:</label>
                    <input
                        type="text"
                        class="money"
                        id="valor_em_reais"
                        name="valor_em_reais"
                        maxlength="22"
                        autocomplete="off"
                        required
                        value="<?= $_SESSION['post_data']['valor_em_reais'] ?? 'R$ 0,00' ?>"
                        onchange="generateInstallments()">
                    <span class="char-counter"></span>
                </div>
                <div class="input-wrapper">
                    <input type="radio" name="forma-pagamento" value="a vista" onclick="paymentMethod('a vista')" <?= ($_SESSION['post_data']['forma-pagamento'] ?? null) == 'a vista' ? 'checked' : (($_SESSION['post_data']['forma-pagamento'] ?? null) == null ? 'checked' : '') ?>> Á vista
                    <input type="radio" name="forma-pagamento" value="parcelado" onclick="paymentMethod('parcelado')" <?= ($_SESSION['post_data']['forma-pagamento'] ?? null) == 'parcelado' ? 'checked' : '' ?>> Parcelado
                </div>
                <div id="quantidade-parcelas" class="hide">
                    <label for="qtd_parcela">Quantidade de parcelas:</label>
                    <input type="number" id="qtd_parcela" name="qtd_parcela" min="1" max="999" step="1" value="<?= isset($_SESSION['post_data']['qtd_parcela']) ? $_SESSION['post_data']['qtd_parcela'] : '1' ?>" onchange="generateInstallments()">
                </div>
                <div id="input-parcela-a-vista">
                    <label for="dataParcelaAVista">Data de pagamento:</label>
                    <input type="date" name="dataParcelaAVista" id="dataParcelaAVista" onchange="checkIfShouldShowBankAccounts()">
                    <br>
                </div>
                <div id="input-banco" class="hide">
                    <label for="banco">Conta bancária <span style="font-weight: 200; font-size: 13px;">(onde será descontado)<span></label>
                    <br>
                    <select id="bancos" name="banco" class="select2" style="width: 100%;">
                        <option value="-1">-- Perguntar depois --</option>
                        <?php foreach ($bancos as $banco): ?>
                            <option value="<?= $banco['id'] ?>" <?= ($_SESSION['post_data']['banco'] ?? null) == $banco['id'] ? 'selected' : '' ?>><?= $banco['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div id="auto-dates" class="section" style="display: hidden;">
                <h2>Preenchimento automático de datas</h2>
                <label for="initial-date">Data da primeira parcela:</label>
                <input id="initial-date" type="date" onchange="updateButtonSempreDiaX()">
                <br><br>
                <button type="button" id="sempre-dia-x" onclick="dateAutoFill(document.getElementById('initial-date').value.split('-')[2], 'alwaysOnDayX')">sempre no dia __</button>
                <button type="button" onclick="dateAutoFill(30)">a cada 30 dias</button>
                <button type="button" onclick="dateAutoFill(7)">a cada 7 dias</button>
                <br><br>
                <span>a cada </span><input id="a-cada-x" type="number" min="1" max="999" step="1" value="1">
                <select id="unit">
                    <option id="opt-dia" value="dia">dia</option>
                    <option id="opt-mes" value="mes">mês</option>
                    <option id="opt-ano" value="ano">ano</option>
                </select>
                <button type="button" id="customPace">></button>
            </div>
            <div id="secao-parcelas" class="hide">
                <div class="section">
                    <h2>Parcelas</h2>
                    <div class="table-section">
                        <table id="installments" class="non-clickable-table">

                        </table>
                    </div>
                    <br>
                    <span id="centavos-faltando"></span>
                </div>
            </div>
            <br>
            <button class="btn btn-primary" type="submit" name="type" value="create">Cadastrar</button>
            <a class="btn btn-secondary" href="<?= $_ENV['BASE_URL'] ?>/contas">Cancelar</a>
        </form>
    </div>
</div>

<script src="<?= $_ENV['BASE_URL'] ?>/public/js/cadastrarConta.js"></script>
<?php
unset($_SESSION['post_data']);
?>
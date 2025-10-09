<div class="form-section">
    <div class="section">
        <h1>Cadastrar Conta</h1>
        <form action="" method="post">
            <div class="section">
                <label for="centro">Centro de custo:</label>

                <select id="centro" name="centro" autofocus>
                    <?php foreach ($centros as $centro): ?>
                        <option value="<?= $centro['id'] ?>"><?= $centro['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>

                <label for="fornecedor">Fornecedor:</label>
                <select id="fornecedor" name="fornecedor">
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= $fornecedor['id'] ?>"><?= $fornecedor['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>

                <label for="banco">Conta bancária:</label>
                <select id="banco" name="banco">
                    <?php foreach ($bancos as $banco): ?>
                        <option value="<?= $banco['id'] ?>"><?= $banco['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>

                <label for="descricao">Descrição/Observações:</label>
                <div class="input-wrapper">
                    <textarea id="descricao" name="descricao" required maxlength="500" autocomplete="off"></textarea>
                </div>

                <label for="valor_em_centavos">Valor:</label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        class="money"
                        id="valor_em_centavos"
                        name="valor_em_centavos"
                        maxlength="22"
                        autocomplete="off"
                        data-thousands="."
                        data-decimal=","
                        required
                        onchange="generateInstallments()">
                    <span class="char-counter"></span>
                </div>
                <label for="qtd_parcela">Quantidade de parcelas:</label>
                <input type="number" id="qtd_parcela" name="qtd_parcela" min="1" max="999" step="1" required value="1" onchange="generateInstallments()">
                <br><br>
            </div>

            <div id="auto-dates" class="section" style="display: none;">
                <h2>Preenchimento automático de datas</h2>
                <label for="initial-date">Data da primeira parcela:</label>
                <input id="initial-date" type="date" onchange="updateButtonSempreDiaX()">
                <br><br>
                <button type="button" id="sempre-dia-x" onclick="sempreDiaX()">sempre no dia __</button>
                <button type="button" onclick="aCadaXDias(30)">a cada 30 dias</button>
                <button type="button" onclick="aCadaXDias(7)">a cada 7 dias</button>
                <br><br>
                <span>a cada </span><input id="a-cada-x" type="number" min="1" max="999" step="1" value="1" onchange="pluralOrNot()"> <select id="unit">
                    <option id="opt-dia" value="dia">dia</option>
                    <option id="opt-mes" value="mes">mês</option>
                    <option id="opt-ano" value="ano">ano</option>
                </select> <button type="button" onclick="aCadaXX()">></button>
            </div>

            <div class="section">
                <h2>Parcelas</h2>
                <div class="table-section">
                    <table id="installments" class="non-clickable-table">
                        <tr>
                            <th>#</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                        </tr>
                        <tr id="parcela1">
                            <td>1ª</td>
                            <td><input type="date" name="parcela1" id="parcela1_vencimento"></td>
                            <td><input type="text" name="parcela1_valor" class="money" data-thousands="."
                                    data-decimal=",">
                            </td>
                        </tr>
                    </table>
                </div>
                <br>
                <span id="centavos-faltando"></span>
            </div>

            <br>

            <button class="btn btn-primary" type="submit" name="type" value="create">Cadastrar</button>
            <a class="btn btn-secondary" href="/contas">Cancelar</a>
        </form>
    </div>
</div>

<script>
    const aCadaX = document.getElementById("a-cada-x");
    aCadaX.addEventListener("keyup", pluralOrNot);

    function pluralOrNot() {
        if (aCadaX.value > 1) {
            document.getElementById("opt-dia").setAttribute("label", "dias");
            document.getElementById("opt-mes").setAttribute("label", "meses");
            document.getElementById("opt-ano").setAttribute("label", "anos");
        } else {
            document.getElementById("opt-dia").setAttribute("label", "dia");
            document.getElementById("opt-mes").setAttribute("label", "mês");
            document.getElementById("opt-ano").setAttribute("label", "ano");
        }
    }

    function updateButtonSempreDiaX() {
        let input = document.getElementById("initial-date");
        let btn = document.getElementById("sempre-dia-x");

        dateFromInput = input.value.split("-");
        year = dateFromInput[0];
        month = dateFromInput[1];
        day = dateFromInput[2];

        date = new Date(year, month - 1, day).toISOString().split("T")[0];
        btn.innerHTML = "sempre no dia " + day;
    }

    function aCadaXX() {

        const n = parseInt(document.getElementById("a-cada-x").value);
        const unit = document.getElementById("unit").selectedOptions[0].value;

        let dias = 0;
        let meses = 0;
        let anos = 0;

        switch (unit) {
            case "dia":
                dias = n;
                break;
            case "mes":
                meses = n;
                break;
            case "ano":
                anos = n;
                break;
        }

        let parcelas = document.getElementById("installments").children;

        let date = document.getElementById("initial-date").value.split("-");

        for (let i = 0; i < parcelas.length - 1; i++) {
            d = new Date(parseInt(date[0]) + (anos * i), parseInt(date[1]) - 1 + (meses * i), parseInt(date[2]) + (dias * i)).toISOString().split("T")[0];
            document.getElementById("parcela" + (i + 1) + "_vencimento").value = d;
        }
    }

    function sempreDiaX() {
        let parcelas = document.getElementById("installments").children;

        let date = document.getElementById("initial-date").value.split("-");

        for (let i = 0; i < parcelas.length - 1; i++) {

            d = new Date(date[0], date[1] - 1 + i, date[2]).toISOString().split("T")[0];
            document.getElementById("parcela" + (i + 1) + "_vencimento").value = d;
        }
    }

    function aCadaXDias(dias) {
        let parcelas = document.getElementById("installments").children;

        let date = document.getElementById("initial-date").value.split("-");

        for (let i = 0; i < parcelas.length - 1; i++) {
            d = new Date(date[0], date[1] - 1, parseInt(date[2]) + (dias * i)).toISOString().split("T")[0];
            document.getElementById("parcela" + (i + 1) + "_vencimento").value = d;
        }
    }

    $(function() {
        $('.money').maskMoney({
            prefix: 'R$ '
        }).trigger('mask.maskMoney');
    })

    document.getElementById("valor_em_centavos").addEventListener("keyup", generateInstallments);
    document.getElementById("qtd_parcela").addEventListener("keyup", generateInstallments);

    const installments = document.getElementById("installments");
    // installments.style.width = "min-content";
    const qtd = document.getElementById("qtd_parcela");

    function sumInstallments() {

        let sum = 0;

        $('.money').maskMoney({
            prefix: 'R$ '
        }).trigger('mask.maskMoney');

        for (let i = 1; i < installments.children.length; i++) {

            let maskedMoney = document.getElementById("parcela" + i + "_valor").value;

            let unmaskedCents = maskedMoney.replaceAll(/\.|,|R\$ /g, "");

            sum += parseInt(unmaskedCents);
        }

        const valorTotal = document.getElementById("valor_em_centavos").value.replaceAll(/\.|,|R\$ /g, "");
        let diferenca = valorTotal - sum;
        let str = "";
        if (diferenca > 0) {
            str = "&nbsp; &nbsp; (R$ " + new Intl.NumberFormat("pt-BR").format(diferenca / 100) + " faltando)";
        } else if (diferenca < 0) {
            str = "&nbsp; &nbsp; (R$ " + new Intl.NumberFormat("pt-BR").format(Math.abs(diferenca) / 100) + " sobrando)";

        }

        document.getElementById("centavos-faltando").innerHTML = "Soma das parcelas: R$ " + new Intl.NumberFormat("pt-BR").format(sum / 100) + "<span style='color: #f00'>" + str + "</span>";

        $('.money').maskMoney({
            prefix: 'R$ '
        }).trigger('mask.maskMoney');
    }

    function generateInstallments() {

        if (qtd.value > 1) {
            document.getElementById("auto-dates").style.display = "block";
        } else {
            document.getElementById("auto-dates").style.display = "none";
        }

        if (qtd.value > 999) {
            alert("O limite de parcelas é 999");
            qtd.value = 999;
        }

        const novasParcelas = [];

        const valorTotal = document.getElementById("valor_em_centavos").value.replaceAll(/\.|,|R\$ /g, "");
        const valorParcela = ((valorTotal / 100) / qtd.value).toFixed(2);


        let head = document.createElement("tr");
        let parcela = document.createElement("th");
        let vencimento = document.createElement("th");
        let valor = document.createElement("th");

        parcela.textContent = "#";
        vencimento.textContent = "Vencimento";
        valor.textContent = "Valor";

        head.appendChild(parcela);
        head.appendChild(vencimento);
        head.appendChild(valor);

        novasParcelas[0] = head;

        for (let i = 1; i <= qtd.value; i++) {

            let installment = document.createElement("tr");
            let parcela = document.createTextNode(i + "ª");
            let vencimento = document.createElement("input");
            let valor = document.createElement("input");

            let td1 = document.createElement("td");
            let td2 = document.createElement("td");
            let td3 = document.createElement("td");

            td1.appendChild(parcela);
            td2.appendChild(vencimento);
            td3.appendChild(valor);

            installment.appendChild(td1);
            installment.appendChild(td2);
            installment.appendChild(td3);

            installment.id = "parcela" + i;
            vencimento.id = "parcela" + i + "_vencimento";
            valor.id = "parcela" + i + "_valor";

            vencimento.name = "parcela" + i + "_vencimento";
            valor.name = "parcela" + i + "_valor";

            vencimento.type = "date";
            valor.type = "text";

            valor.value = valorParcela;
            valor.classList.add("money");
            valor.setAttribute("data-thousands", ".");
            valor.setAttribute("data-decimal", ",");
            valor.addEventListener("keyup", sumInstallments);

            vencimento.required = true;
            valor.required = true;

            novasParcelas[i] = installment;
        }

        installments.replaceChildren(...novasParcelas);

        sumInstallments();
    }
</script>
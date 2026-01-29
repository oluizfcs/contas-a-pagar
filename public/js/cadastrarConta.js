const installments = document.getElementById("installments");
const qtd = document.getElementById("qtd_parcela");
const aCadaX = document.getElementById("a-cada-x");
const inputValorTotalConta = document.getElementById("valor_em_reais");
const initialDateAutofill = document.getElementById("initial-date");
let somaDasParcelasIgualValorTotal = false;
inputValorTotalConta.addEventListener("keyup", generateInstallments);
document.getElementById("qtd_parcela").addEventListener("keyup", generateInstallments);

function checkIfShouldShowBankAccounts() {
    let today = new Date(document.getElementById("today").value);
    let paymentDay = new Date(document.getElementById("dataParcelaAVista").value);

    if (paymentDay <= today) {
        document.getElementById("input-banco").classList.remove("hide");
        document.getElementById("labelDataParcelaAVista").textContent = "Data de pagamento:";
    } else {
        document.getElementById("input-banco").classList.add("hide");
        document.getElementById("labelDataParcelaAVista").textContent = "Data de vencimento:";
    }
}

function paymentMethod(method) {
    if (method == "a vista") {
        document.getElementById("quantidade-parcelas").classList.add("hide");
        document.getElementById("secao-parcelas").classList.add("hide");
        document.getElementById("input-parcela-a-vista").classList.remove("hide");
        document.getElementById("qtd_parcela").value = 1;
        generateInstallments();
        document.getElementById("dataParcelaAVista").focus();
    }

    if (method == "parcelado") {
        document.getElementById("quantidade-parcelas").classList.remove("hide");
        document.getElementById("secao-parcelas").classList.remove("hide");
        document.getElementById("input-parcela-a-vista").classList.add("hide");
        document.getElementById("input-banco").classList.add("hide");
        generateInstallments();
        qtd.focus();
    }
}

function updateButtonSempreDiaX() {
    let input = initialDateAutofill;
    let btn = document.getElementById("sempre-dia-x");

    dateFromInput = input.value.split("-");
    year = dateFromInput[0];
    month = dateFromInput[1];
    day = dateFromInput[2];

    date = new Date(year, month - 1, day).toISOString().split("T")[0];
    btn.innerHTML = "sempre no dia " + day;
}

function dateAutoFill(n, unit = "dia") {
    if (initialDateAutofill.value == "") {
        alert("É necessário preencher a data da primeira parcela antes de utilizar o preenchedor automático de datas.");
        initialDateAutofill.focus();
        return;
    }

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
        case "alwaysOnDayX":
            meses = 1;
            break;
    }

    let parcelas = document.getElementById("installments").children;

    let date = initialDateAutofill.value.split("-");

    for (let i = 0; i < parcelas.length - 1; i++) {

        let year = parseInt(date[0]) + (anos * i);
        let month = parseInt(date[1]) - 1 + (meses * i);
        let day = parseInt(date[2]) + (dias * i);

        d = new Date(year, month, day).toISOString().split("T")[0];
        document.getElementById("parcela" + (i + 1) + "_vencimento").value = d;
    }
}

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

    const valorTotal = document.getElementById("valor_em_reais").value.replaceAll(/\.|,|R\$ /g, "");
    let diferenca = valorTotal - sum;
    let str = "";
    somaDasParcelasIgualValorTotal = false;
    if (diferenca > 0) {
        str = "&nbsp; &nbsp; (R$ " + new Intl.NumberFormat("pt-BR").format(diferenca / 100) + " faltando)";
    } else if (diferenca < 0) {
        str = "&nbsp; &nbsp; (R$ " + new Intl.NumberFormat("pt-BR").format(Math.abs(diferenca) / 100) + " sobrando)";
    } else {
        somaDasParcelasIgualValorTotal = true;
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

    if (qtd.value > 300) {
        alert("O limite de parcelas é 300");
        qtd.value = 300;
    }

    const novasParcelas = [];

    const valorTotal = document.getElementById("valor_em_reais").value.replaceAll(/\.|,|R\$ /g, "");
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

        installment.innerHTML = `
            <td>${i}ª</td>
            <td>
                <input type="date" id="parcela${i}_vencimento" name="parcela${i}_vencimento">
            </td>
            <td>
                <input
                    type="text"
                    id="parcela${i}_valor"
                    name="parcela${i}_valor"
                    value="${valorParcela}"
                    class="money"
                >
            </td>
        `;

        installment.id = "parcela" + i;
        installment.querySelector('input[type="text"]').addEventListener("keyup", sumInstallments);
        novasParcelas[i] = installment;
    }

    installments.replaceChildren(...novasParcelas);

    sumInstallments();
}

aCadaX.addEventListener("keyup", function () {
    if (this.value > 1) {
        document.getElementById("opt-dia").setAttribute("label", "dias");
        document.getElementById("opt-mes").setAttribute("label", "meses");
        document.getElementById("opt-ano").setAttribute("label", "anos");
    } else if (this.value <= 1) {
        document.getElementById("opt-dia").setAttribute("label", "dia");
        document.getElementById("opt-mes").setAttribute("label", "mês");
        document.getElementById("opt-ano").setAttribute("label", "ano");
    }
});

document.getElementById("customPace").addEventListener("click", () => {
    dateAutoFill(aCadaX.value, document.getElementById("unit").value);
});


document.forms[0].addEventListener("submit", function (e) {
    const formaPagamento = document.querySelector('input[name="forma-pagamento"]:checked').value;

    // validations
    if (parseInt(inputValorTotalConta.value.replaceAll(/\.|,|R\$ /g, "")) <= 0) {
        e.preventDefault();
        alert("O valor da conta deve ser maior que zero");
        inputValorTotalConta.focus();
        inputValorTotalConta.setSelectionRange(0, -1);
        return;
    } else if (document.getElementById("descricao").value.length > 500) {
        e.preventDefault();
        alert("A descrição não pode exceder 500 caracteres.");
        document.getElementById("descricao").focus();
        return;
    } else if (formaPagamento == "parcelado") {

        for (let i = 1; i < installments.children.length; i++) {

            let inputParcelaVencimento = installments.children[i].querySelector(`#parcela${i}_vencimento`);

            if (inputParcelaVencimento.value == "") {
                e.preventDefault();
                alert("Todas as parcelas devem ter seus vencimentos preenchidos. Dica: utilize o preenchedor automático de datas para agilizar este processo.");
                inputParcelaVencimento.focus();
                return;
            }
        }

        if (!somaDasParcelasIgualValorTotal) {
            e.preventDefault();
            alert("A soma das parcelas não coincide com o valor total da conta, verifique o valor sobrando/faltando e distribua-o nas parcelas desejadas.");
            return;
        }
    } else if (formaPagamento == "a vista") {
        if (document.getElementById("dataParcelaAVista").value == "") {
            e.preventDefault();
            alert("Você deve informar a data de pagamento desta conta");
            document.getElementById("dataParcelaAVista").focus();
            return;
        }
    }
});

$(document).ready(function () {
    paymentMethod(document.querySelector('input[name="forma-pagamento"]:checked').value);
});
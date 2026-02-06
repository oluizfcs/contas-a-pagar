<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Controllers\Services\Money;
use App\Models\Banco;
use App\Models\CentroDeCusto;
use App\Models\Database;
use App\Models\Conta;
use App\Models\Fornecedor;
use App\Models\Parcela;
use DateTime;

class Contas
{
    public static bool $needLogin = true;
    public static bool $onlyAdmin = false;
    private array $views = ['index', 'cadastrar', 'detalhar', 'pagar'];
    private int $id;
    private string $rowType = 'contas';
    private string $status = 'a pagar';
    private string $search = '';

    function __construct(string $view = 'index', string $param = '')
    {
        if (strlen($param) > 0) {
            $id = filter_var($param, FILTER_VALIDATE_INT);
            if ($id) {
                $this->id = $id;
            } else {
                $_SESSION['message'] = ['Conta não encontrada', 'fail'];
                header('Location: ' . $_ENV['BASE_URL'] . '/contas');
                exit;
            }
        }

        if (!empty($_POST)) {
            switch ($_POST['type']) {
                case 'create':
                    $this->create();
                    break;
                case 'unable':
                    $this->unable();
                    break;
                case 'enable':
                    $this->enable();
                    break;
                case 'search':
                    $_SESSION['contas_filters'] = [
                        'search' => $_POST['search'] ?? '',
                        'rowType' => $_POST['rowType'] ?? 'contas',
                        'status' => $_POST['status'] ?? 'a pagar',
                        'orderby' => $_POST['orderby'] ?? 'vencimento'
                    ];
                    header('Location: ' . $_ENV['BASE_URL'] . '/contas');
                    exit;
                    break;
                case 'pay':
                    $view = "pagar";
                    break;
                case 'cobrar':
                    $this->cobrar();
                    break;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $banco_id = filter_var($_POST['banco'], FILTER_VALIDATE_INT);
        $centro_id = filter_var($_POST['centro'], FILTER_VALIDATE_INT);
        $fornecedor_id = filter_var($_POST['fornecedor'], FILTER_VALIDATE_INT);
        $valorEmCentavos = Money::reais_para_centavos($_POST['valor_em_reais']);
        $qtd_parcela = filter_var($_POST['qtd_parcela'], FILTER_VALIDATE_INT);
        $descricao = $_POST['descricao'];
        $formaPagamento = $_POST['forma-pagamento'];

        if ($banco_id == -1) { // "Pagar depois"
            $banco_id = null;
        } else {
            Banco::getById($banco_id); // if id does not exists (unlikely), execution stops.
        }

        if ($fornecedor_id == -1) { // "Nenhum"
            $fornecedor_id = null;
        } else {
            Fornecedor::getById($fornecedor_id); // if id does not exists (unlikely), execution stops.
        }

        if ($valorEmCentavos == 0) {
            $_SESSION['message'] = ['O valor da conta não pode ser zero', 'fail'];
            $_SESSION['post_data'] = $_POST;
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
            exit;
        }

        if (trim($descricao) == "") {
            $descricao = "Nenhuma observação foi informada.";
        }

        if (strlen(trim($descricao)) >= 500) {
            $_SESSION['message'] = ['A descrição não pode exceder 500 caracteres', 'fail'];
            $_SESSION['post_data'] = $_POST;
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
            exit;
        }

        if (!$qtd_parcela) {
            $_SESSION['message'] = ['Quantidade de parcelas inválida', 'fail'];
            $_SESSION['post_data'] = $_POST;
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
            exit;
        }

        $parcelas = [];

        switch ($formaPagamento) {
            case 'a vista':
                if ($_POST['dataParcelaAVista'] == '' || strlen($_POST['dataParcelaAVista']) != 10) {
                    $_SESSION['message'] = ['É necessário definir uma data de pagamento', 'fail'];
                    $_SESSION['post_data'] = $_POST;
                    header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
                    exit;
                }
                break;
            case 'parcelado':
                for ($i = 1; $i <= $qtd_parcela; $i++) {

                    $valorParcela = Money::reais_para_centavos($_POST["parcela$i" . "_valor"]);
                    $vencimentoParcela = $_POST["parcela$i" . "_vencimento"];

                    if ($valorParcela == 0) {
                        $_SESSION['message'] = ['O valor das parcelas não está dividido corretamente', 'fail'];
                        $_SESSION['post_data'] = $_POST;
                        header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
                        exit;
                    }

                    if ($vencimentoParcela == '') {
                        $_SESSION['message'] = ['É necessário informar a data de vencimento das parcelas', 'fail'];
                        $_SESSION['post_data'] = $_POST;
                        header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
                        exit;
                    }

                    $parcelas[] = ['valor' => $valorParcela, 'vencimento' => $vencimentoParcela];
                }
                break;
            default:
                $_SESSION['message'] = ['Forma de pagamento não identificada', 'fail'];
                $_SESSION['post_data'] = $_POST;
                header('Location: ' . $_ENV['BASE_URL'] . '/contas/cadastrar');
                exit;
        }

        $c = new Conta(
            0,
            $descricao,
            $valorEmCentavos,
            '',
            null,
            $centro_id,
            $fornecedor_id,
            [],
            true,
            0
        );

        if ($c->save()) {
            $_SESSION['message'] = ['Conta cadastrada com sucesso!', 'success'];

            if (count($parcelas) > 0) {
                //parcelado
                Parcela::bulkInsert($parcelas, $c->lastInsertId);
            } else {
                //a vista
                new Parcela(0, 1, $valorEmCentavos, $_POST['dataParcelaAVista'], null, $c->lastInsertId, $banco_id, false)->save();
            }

            if($formaPagamento == 'a vista' && $banco_id != null) {
                $this->cobrar(1, $banco_id, $c->lastInsertId, $_POST['dataParcelaAVista'], true);
                exit;
            }

            header('Location: ' . $_ENV['BASE_URL'] . '/contas');
            exit;
        }
    }

    private function unable(): void
    {
        if (Conta::hasPaidInstallments($this->id)) {
            $_SESSION['message'] = ['Não é possível inativar uma conta que já possui parcelas pagas.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $this->id);
            exit;
        }

        $c = Conta::getById($this->id);
        $c->setEnabled(0);

        if ($c->save()) {
            Logger::log_unable(Conta::$tableName, $c->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Conta inativada com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $c->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $c = Conta::getById($_POST['conta_id']);
        $c->setEnabled(1);

        if ($c->save()) {
            Logger::log_enable(Conta::$tableName, $c->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Conta ativada com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $c->getId());
            exit;
        }
    }

    private function cobrar($numero_parcela = '', $banco_id = -1, $conta_id = -1, $data = '', $aVista = false): void
    {
        if($numero_parcela == '') {
            $numero_parcela = filter_var($_POST['numero_parcela'], FILTER_VALIDATE_INT);
        }
        
        if($banco_id == -1) {
            $banco_id = filter_var($_POST['banco'], FILTER_VALIDATE_INT);
        }
        
        if($conta_id == -1) {
            $conta_id = filter_var($_POST['conta_id'], FILTER_VALIDATE_INT);
        }
        
        if($data == '') {
            $data = $_POST['data'];
        }

        $conta = Conta::getById($conta_id);

        $parcelas = $conta->getParcelas();
        $parcela_id = -1;
        foreach ($parcelas as $parcela) {
            if ($parcela->getNumero_parcela() == $numero_parcela) {
                $parcela_id = $parcela->getId();
                break;
            }
        }

        $banco = Banco::getById($banco_id);
        if ($parcela_id == -1) {
            $_SESSION['message'] = ['Parcela não encontrada', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $conta_id);
            exit;
        }

        $parcela = Parcela::getById($parcela_id);

        if (!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $data)) {
            $_SESSION['message'] = ['Data inválida', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $conta_id);
            exit;
        }

        $dataPagamentoEscolhida = new DateTime($data);

        if ($dataPagamentoEscolhida > new DateTime("now")) {
            $_SESSION['message'] = ['Não é possível marcar uma parcela como paga em uma data futura', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $conta_id);
            exit;
        }

        $parcela->setPaid(true);
        $parcela->setBanco_id($banco_id);
        $parcela->setData_pagamento($data);
        $banco->setSaldo_em_centavos($banco->getSaldo_em_centavos() - $parcela->getValor_em_centavos());

        if ($parcela->save()) {
            $banco->save();

            if (!Conta::hasUnpaidInstallments($conta_id)) {
                $conta->setPaid(true);
                $conta->save();
            }

            if($aVista) {
                $_SESSION['message'] = ['Conta cadastrada e paga com sucesso!', 'success'];
            } else {
                $_SESSION['message'] = ['Parcela paga com sucesso!', 'success'];
            }
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $conta_id);
            Logger::log(Conta::$tableName, 'Parcela ' . $parcela->getNumero_parcela(), '-', '-', $conta_id, $_SESSION['usuario_id']);
            exit;
        } else {
            $_SESSION['message'] = ['Erro ao pagar parcela', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $conta_id);
            exit;
        }
    }

    private function loadView(string $view): void
    {
        if (!in_array($view, $this->views)) {
            include 'src/404.php';
            exit;
        }

        if (isset($this->id)) {
            $conta = Conta::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(Conta::$tableName, $this->id);
            }
        }

        if ($view == 'index') {

            $filters = $_SESSION['contas_filters'] ?? [
                'search' => '',
                'rowType' => 'contas',
                'status' => 'a pagar'
            ];

            $this->search = $filters['search'];
            $this->rowType = $filters['rowType'];
            $this->status = $filters['status'];

            $contas = [];
            $parcelas = [];

            if ($this->rowType == 'contas') {
                $contas = Conta::getAll($this->search, $this->status);

                usort($contas, function($a, $b) {
                    $infoA = $a->getNextInstallmentInfo();
                    $infoB = $b->getNextInstallmentInfo();
                    
                    $dateA = $infoA['installment'] ? $infoA['installment']->getData_vencimento() : null;
                    $dateB = $infoB['installment'] ? $infoB['installment']->getData_vencimento() : null;

                    if ($dateA === $dateB) {
                        return 0;
                    }

                    // Null dates (no pending installments) go to the end
                    if ($dateA === null) {
                        return 1;
                    }
                    if ($dateB === null) {
                        return -1;
                    }

                    return ($dateA < $dateB) ? -1 : 1;
                });
            } else {
                if ($this->status == 'inativadas') {
                    $this->status = "a pagar";
                }
                $parcelas = Parcela::getAll($this->status);
            }
        }

        if ($view == 'cadastrar') {
            $centros = Database::getOptions(CentroDeCusto::$tableName);
            $fornecedores = Database::getOptions(Fornecedor::$tableName);
            $bancos = Database::getOptions(Banco::$tableName);

            if (empty($centros)) {
                $_SESSION['message'] = ['Não é possível cadastrar uma conta pois não há centros de custo cadastrados', 'warning'];
                header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo/cadastrar');
                exit;
            }

            if (empty($fornecedores)) {
                $_SESSION['message'] = ['Não é possível cadastrar uma conta pois não há fornecedores cadastrados', 'warning'];
                header('Location: ' . $_ENV['BASE_URL'] . '/fornecedores/cadastrar');
                exit;
            }
        }

        if ($view == 'pagar') {
            $bancos = Database::getOptions(Banco::$tableName);

            if (empty($bancos)) {
                $_SESSION['message'] = ['Não é possível pagar essa parcela pois não há nenhuma conta bancária disponível', 'warning'];
                header('Location: ' . $_ENV['BASE_URL'] . '/bancos/cadastrar');
                exit;
            }
        }

        include '../src/templates/header.php';
        include "../src/Views/contas/$view.php";
        include '../src/templates/footer.php';
    }
}

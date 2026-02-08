<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Controllers\Services\Money;
use App\Models\Database;
use App\Models\Banco;
use App\Models\Parcela;
use App\Models\Deposito;

class Bancos
{
    public static bool $needLogin = true;
    public static bool $onlyAdmin = true;
    private array $views = ['index', 'cadastrar', 'detalhar', 'atualizar', 'depositar'];
    private int $id;
    private string $search = '';
    private string $status = 'todos';

    function __construct(string $view = 'index', string $param = '')
    {
        if (strlen($param) > 0) {
            $id = filter_var($param, FILTER_VALIDATE_INT);
            if ($id) {
                $this->id = $id;
            } else {
                $_SESSION['message'] = ['Banco não encontrado', 'fail'];
                header('Location: ' . $_ENV['BASE_URL'] . '/bancos');
                exit;
            }
        }

        if (!empty($_POST)) {
            switch ($_POST['type']) {
                case 'create':
                    $this->create();
                    break;
                case 'update':
                    $this->update();
                    break;
                case 'unable':
                    $this->unable();
                    break;
                case 'enable':
                    $this->enable();
                    break;
                case 'depositar':
                    $this->depositar();
                    break;
                case 'search':
                    $_SESSION['bancos_filters'] = [
                        'search' => $_POST['search'],
                        'status' => $_POST['status']
                    ];
                    header('Location: ' . $_ENV['BASE_URL'] . '/bancos');
                    exit;
                    break;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $saldoDeAbertura = Money::reais_para_centavos($_POST['saldo-abertura']);

        $b = new Banco(0, $_POST['nome'], $saldoDeAbertura, '', null, 0);

        if ($b->save()) {
            $d = new Deposito(0, $_SESSION['usuario_id'], $b->lastInsertId, $saldoDeAbertura, 'Saldo de abertura', '');
            $d->save();
            $_SESSION['message'] = ['Banco cadastrado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos');
            exit;
        }
    }

    private function update(): void
    {
        $b = Banco::getById($_POST['entity_id']);
        $b->setNome($_POST['nome']);

        if ($b->save()) {
            $_SESSION['message'] = ['Banco atualizado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar/' . $_POST['entity_id']);
            exit;
        }
    }

    private function unable(): void
    {
        $b = Banco::getById($_POST['banco_id']);
        $b->setEnabled(0);

        if ($b->save()) {
            Logger::log_unable(Banco::$tableName, $b->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Banco inativado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar/' . $b->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $b = Banco::getById($_POST['banco_id']);
        $b->setEnabled(1);

        if ($b->save()) {
            Logger::log_enable(Banco::$tableName, $b->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Banco ativado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar/' . $b->getId());
            exit;
        }
    }



    private function depositar(): void
    {
        $valor = Money::reais_para_centavos($_POST['valor']);

        if($valor > Deposito::$LIMITE_DEPOSITO) {
            $_SESSION['message'] = ['O limite para depósitos é de R$ ' . Money::centavos_para_reais(Deposito::$LIMITE_DEPOSITO), 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/depositar/' . $_POST['banco_id']);
            exit;
        }

        $banco_id = $_POST['banco_id'];
        $descricao = $_POST['descricao'];
        if (strlen($descricao) > 255) {
            $_SESSION['message'] = ['Descrição muito longa!', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar/' . $banco_id);
            exit;
        }
        $banco = Banco::getById($banco_id);

        $banco->setSaldo_em_centavos($banco->getSaldo_em_centavos() + $valor);
        $banco->save();

        $d = new Deposito(0, $_SESSION['usuario_id'], $banco_id, $valor, $descricao, '');

        if ($d->save()) {
            $_SESSION['message'] = ['Depósito realizado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar/' . $banco_id);
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
            $banco = Banco::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(Banco::$tableName, $this->id);
                $parcelas = Parcela::getByBank($this->id);
                $depositos = Deposito::getByBank($this->id);
            }
        }

        if ($view == 'index') {

            $this->search = $_SESSION['bancos_filters']['search'] ?? $this->search;
            $this->status = $_SESSION['bancos_filters']['status'] ?? $this->status;

            $enabled = $this->status != 'inativadas';
            $paid = $this->status == 'contas pagas';

            if($this->status == 'todos') {
                $paid = 2;
            }

            $bancos = Banco::getAll($enabled, $paid, $this->search);
        }

        include '../src/templates/header.php';
        if ($view == 'cadastrar' || $view == 'atualizar') {
            include "../src/Views/bancos/form.php";
        } else {
            include "../src/Views/bancos/$view.php";
        }
        include '../src/templates/footer.php';
    }
}

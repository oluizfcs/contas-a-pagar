<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Controllers\Services\Money;
use App\Models\Database;
use App\Models\Banco;

class Bancos
{
    public static bool $needLogin = true;
    private array $views = ['index', 'cadastrar', 'detalhar', 'atualizar'];
    private int $id;
    private string $search = '';
    private string $status = 'contas a pagar';

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
        $_POST['saldo_em_centavos'] = Money::reais_para_centavos($_POST['saldo_em_centavos']);

        $b = new Banco(0, $_POST['nome'], $_POST['saldo_em_centavos'], '', null, 0);

        if ($b->save()) {
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
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar' . $_POST['entity_id']);
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
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar' . $b->getId());
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
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos/detalhar' . $b->getId());
            exit;
        }
    }

    private function loadView(string $view): void
    {
        if (!in_array($view, $this->views)) {
            include '404.php';
            exit;
        }

        if (isset($this->id)) {
            $banco = Banco::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(Banco::$tableName, $this->id);
            }
        }

        if ($view == 'index') {

            $this->search = $_SESSION['bancos_filters']['search'] ?? $this->search;
            $this->status = $_SESSION['bancos_filters']['status'] ?? $this->status;

            $enabled = $this->status != 'inativadas';
            $paid = $this->status == 'contas pagas';

            if($this->status == 'contas pagas e não pagas') {
                $paid = 2;
            }

            $bancos = Banco::getAll($enabled, $paid, $this->search);
        }

        include 'templates/header.php';
        if ($view == 'cadastrar' || $view == 'atualizar') {
            include "src/Views/bancos/form.php";
        } else {
            include "src/Views/bancos/$view.php";
        }
        include 'templates/footer.php';
    }
}

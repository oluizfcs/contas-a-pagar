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

class Contas
{
    public static bool $needLogin = true;
    private array $views = ['index', 'cadastrar', 'detalhar', 'atualizar'];
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
                    $_SESSION['contas_filters'] = [
                        'search' => $_POST['search'] ?? '',
                        'rowType' => $_POST['rowType'] ?? 'contas',
                        'status' => $_POST['status'] ?? 'a pagar',
                        'orderby' => $_POST['orderby'] ?? 'vencimento'
                    ];
                    header('Location: ' . $_ENV['BASE_URL'] . '/contas');
                    exit;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $c = new Conta(
            0,
            $_POST['descricao'],
            Money::reais_para_centavos($_POST['valor_em_centavos']),
            '',
            null,
            filter_var($_POST['banco'], FILTER_VALIDATE_INT),
            filter_var($_POST['centro'], FILTER_VALIDATE_INT),
            filter_var($_POST['fornecedor'], FILTER_VALIDATE_INT),
            [],
            true
        );
        
        if ($c->save()) {
            $_SESSION['message'] = ['Conta cadastrada com sucesso!', 'success'];
    
            $qtd_parcela = filter_var($_POST['qtd_parcela'], FILTER_VALIDATE_INT);
    
            // lazy
            for($i = 1; $i <= $qtd_parcela; $i++) {
                $p = new Parcela(0, $i, Money::reais_para_centavos($_POST["parcela$i" . "_valor"]), $_POST["parcela$i" . "_vencimento"], null, $c->lastInsertId, 0);
                $p->save();
            }
            
            header('Location: ' . $_ENV['BASE_URL'] . '/contas');
            exit;
        }
    }

    private function update(): void
    {
        var_dump($_POST); exit;
        // $b = Conta::getById($_POST['entity_id']);
        // $b->setNome($_POST['nome']);

        // if ($b->save()) {
        //     $_SESSION['message'] = ['Conta atualizada com sucesso!', 'success'];
        //     header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $_POST['entity_id']);
        //     exit;
        // }
    }

    private function unable(): void
    {
        $b = Conta::getById($_POST['conta_id']);
        $b->setEnabled(0);

        if ($b->save()) {
            Logger::log_unable(Conta::$tableName, $b->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Conta inativada com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $b->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $b = Conta::getById($_POST['conta_id']);
        $b->setEnabled(1);

        if ($b->save()) {
            Logger::log_enable(Conta::$tableName, $b->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Conta ativada com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas/detalhar/' . $b->getId());
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
            $conta = Conta::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(Conta::$tableName, $this->id);
            }
        }

        if ($view == 'index') {

            $filters = $_SESSION['contas_filters'] ?? [
                'search' => '',
                'rowType' => 'parcelas',
                'status' => 'a pagar'
            ];

            $this->search = $filters['search'];
            $this->rowType = $filters['rowType'];
            $this->status = $filters['status'];

            $contas = [];
            $parcelas = [];
            
            if($this->rowType == 'contas') {
                $contas = Conta::getAll($this->search, $this->status);
            } else {
                if($this->status == 'inativadas') {
                    $this->status = "a pagar";
                }
                $parcelas = Parcela::getAll($this->status);
            }
        }

        if($view == 'cadastrar' || $view == 'atualizar') {
            $centros = Database::getOptions(CentroDeCusto::$tableName);
            $fornecedores = Database::getOptions(Fornecedor::$tableName);
            $bancos = Database::getOptions(Banco::$tableName);

            if(empty($centros)) {
                $_SESSION['message'] = ['Não é possível cadastrar uma conta pois não há centros de custo cadastrados', 'warning'];
                header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo/cadastrar');
                exit;
            }

            if(empty($fornecedores)) {
                $_SESSION['message'] = ['Não é possível cadastrar uma conta pois não há fornecedores cadastrados', 'warning'];
                header('Location: ' . $_ENV['BASE_URL'] . '/fornecedores/cadastrar');
                exit;
            }

            if(empty($bancos)) {
                $_SESSION['message'] = ['Não é possível cadastrar uma conta pois não há bancos cadastrados', 'warning'];
                header('Location: ' . $_ENV['BASE_URL'] . '/bancos/cadastrar');
                exit;
            }
        }

        include 'templates/header.php';
        include "src/Views/contas/$view.php";
        include 'templates/footer.php';
    }
}

<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Models\Database;
use App\Models\Fornecedor;

class Fornecedores
{
    public static bool $needLogin = true;
    private array $views = ['index', 'cadastrar', 'detalhar', 'atualizar'];
    private int $id;
    private string $orderby = 'total';
    private string $mostrar = '';
    private string $search = '';

    function __construct(string $view = 'index', string $param = '')
    {
        if (strlen($param) > 0) {
            $id = filter_var($param, FILTER_VALIDATE_INT);
            if ($id) {
                $this->id = $id;
            } else {
                $_SESSION['message'] = ['Fornecedor não encontrado', 'fail'];
                header("Location: /fornecedores");
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
                    $this->search = $_POST['search'];
                    $this->orderby = $_POST['orderby'];
                    $this->mostrar = $_POST['mostrar'];
                    break;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $f = new Fornecedor(0, $_POST['nome'], $_POST['telefone'], '', null, 0);

        if ($f->save()) {
            $_SESSION['message'] = ['Fornecedor cadastrado com sucesso!', 'success'];
            header("Location: /fornecedores");
            exit;
        }
    }

    private function update(): void
    {
        $f = Fornecedor::getById($_POST['entity_id']);
        $f->setNome($_POST['nome']);
        $f->setTelefone($_POST['telefone']);

        if ($f->save()) {
            $_SESSION['message'] = ['Fornecedor atualizado com sucesso!', 'success'];
            header("Location: /fornecedores/detalhar/" . $_POST['entity_id']);
            exit;
        }
    }

    private function unable(): void
    {
        $f = Fornecedor::getById($_POST['fornecedor_id']);
        $f->setEnabled(0);

        Logger::log_unable(Fornecedor::$tableName, $f->getId(), $_SESSION['usuario_id']);

        if ($f->save()) {
            $_SESSION['message'] = ['Fornecedor inativado com sucesso!', 'success'];
            header("Location: /fornecedores/detalhar/" . $f->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $f = Fornecedor::getById($_POST['fornecedor_id']);
        $f->setEnabled(1);

        Logger::log_enable(Fornecedor::$tableName, $f->getId(), $_SESSION['usuario_id']);

        if ($f->save()) {
            $_SESSION['message'] = ['Fornecedor ativado com sucesso!', 'success'];
            header("Location: /fornecedores/detalhar/" . $f->getId());
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
            $fornecedor = Fornecedor::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(Fornecedor::$tableName, $this->id);
            }
        }

        if ($view == 'index') {

            $enabled = $this->mostrar != 'inativados';
            $paid = $this->mostrar == 'todos';

            $fornecedores = Fornecedor::getAll($enabled, $paid, $this->orderby, $this->search);
        }

        include 'templates/header.php';
        if ($view == 'cadastrar' || $view == 'atualizar') {
            include "src/Views/fornecedores/form.php";
        } else {
            include "src/Views/fornecedores/$view.php";
        }
        include 'templates/footer.php';
    }
}

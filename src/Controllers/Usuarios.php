<?php

namespace App\Controllers;

use App\Controllers\Services\Cpf;
use App\Controllers\Services\Logger;
use App\Models\Database;
use App\Models\Usuario;
use Exception;

class Usuarios
{
    public static bool $needLogin = true;
    private array $views = ['index', 'cadastrar', 'detalhar', 'alterar-senha'];
    private int $id;
    private string $mostrar = 'todos';
    private string $search = '';

    function __construct(string $view = 'index', string $param = '')
    {
        if (strlen($param) > 0) {
            $id = filter_var($param, FILTER_VALIDATE_INT);
            if ($id) {
                $this->id = $id;
            } else {
                $_SESSION['message'] = ['Usuario não encontrado', 'fail'];
                header("Location: /usuarios");
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
                    $_SESSION['usuarios_filters'] = [
                        'search' => $_POST['search'] ?? '',
                        'mostrar' => $_POST['mostrar'] ?? ''
                    ];
                    header("Location: /usuarios");
                    exit;
                    break;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $senha = $_POST['senha'];

        if (strlen($nome) < 3) {
            $_SESSION['message'] = ['Nome muito curto!', 'fail'];
            $this->loadView('cadastrar');
            exit;
        }
        if (strlen($senha) < 3) {
            $_SESSION['message'] = ['Senha muito curta!', 'fail'];
            $this->loadView('cadastrar');
            exit;
        }

        try {
            $usuario = new Usuario(0, Cpf::unmaskCpf($cpf), $nome, password_hash($senha, PASSWORD_DEFAULT), '', null, 0);

            if ($usuario->save()) {
                $_SESSION['message'] = ['Usuário cadastrado com sucesso!', 'success'];
                header("Location: /usuarios");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['message'] = ['CPF inválido!', 'fail'];
            $this->loadView('cadastrar');
            exit;
        }
    }

    private function update(): void
    {
        $usuario = Usuario::getById($_POST['entity_id']);
        $usuario->setSenha(password_hash($_POST['senha'], PASSWORD_DEFAULT));

        if ($usuario->save()) {
            $_SESSION['message'] = ['Senha alterada com sucesso!', 'success'];
            header("Location: /usuarios/detalhar/" . $_POST['entity_id']);
            exit;
        }
    }

    private function unable(): void
    {
        $u = Usuario::getById($_POST['usuario_id']);
        $u->setEnabled(0);

        Logger::log_unable(Usuario::$tableName, $u->getId(), $_SESSION['usuario_id']);

        if ($u->save()) {
            $_SESSION['message'] = ['Usuário inativado com sucesso!', 'success'];
            header("Location: /usuarios/detalhar/" . $u->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $u = Usuario::getById($_POST['usuario_id']);
        $u->setEnabled(1);

        Logger::log_enable(Usuario::$tableName, $u->getId(), $_SESSION['usuario_id']);

        if ($u->save()) {
            $_SESSION['message'] = ['Usuario ativado com sucesso!', 'success'];
            header("Location: /usuarios/detalhar/" . $u->getId());
            exit;
        }
    }

    private function loadView(string $view): void
    {
        $filters = $_SESSION['usuarios_filters'] ?? [
            'search' => '',
            'mostrar' => 'todos'
        ];

        $this->search = $filters['search'];
        $this->mostrar = $filters['mostrar'];

        if (!in_array($view, $this->views)) {
            include '404.php';
            exit;
        }

        if (isset($this->id)) {
            $usuario = Usuario::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(Usuario::$tableName, $this->id);
            }
        }

        if ($view == 'index') {

            $enabled = $this->mostrar != 'inativados';

            $usuarios = Usuario::getAll($enabled, $this->search);
        }

        if ($view == 'cadastrar' && $_SESSION['usuario_id'] != 1) {
            include '404.php';
            exit;
        }

        if ($view == 'alterar-senha' && (
            $_SESSION['usuario_id'] != $usuario->getId() &&
            $_SESSION['usuario_id'] != 1
        )) {
            include '404.php';
            exit;
        }

        include 'templates/header.php';
        include "src/Views/usuarios/$view.php";
        include 'templates/footer.php';
    }
}

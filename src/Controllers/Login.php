<?php

namespace App\Controllers;

use App\Controllers\Services\Cpf;
use App\Models\Usuario;
use Exception;

class Login
{
    public static bool $needLogin = false;

    function __construct(string $p1 = '')
    {
        if (!empty($_POST)) {
            $this->login();
        }

        if ($p1 == 'sair') {
            $this->logout();
        }

        $this->loadView();
    }

    private function login(): void
    {
        try {
            $usuario = Usuario::getByCpf(Cpf::unmaskCpf($_POST['cpf']));

            if (!$usuario || !password_verify($_POST['senha'], $usuario['senha'])) {
                $_SESSION['message'] = ['Credenciais inválidas', 'fail'];
                $this->loadView();
                exit;
            }

            if ($usuario['enabled'] == 0) {
                $_SESSION['message'] = ['Este usuário foi inativado.', 'fail'];
                $this->loadView();
                exit;
            }

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            header('Location: /dashboard');
        } catch (Exception $e) {
            $_SESSION['message'] = ['Credenciais inválidas', 'fail'];
            $this->loadView();
            exit;
        }
    }

    private function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . $_ENV['BASE_URL'] . '/login');
    }

    private function loadView(): void
    {
        include 'src/Views/login.php';
    }
}

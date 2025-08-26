<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Models\Database;
use PDO;
use PDOException;

class Login
{
    public static bool $needLogin = false;

    function __construct(string $p1 = '')
    {
        $this->loadView();

        if (!empty($_POST)) {
            $this->login();
        }

        if ($p1 == 'sair') {
            $this->logout();
        }
    }

    private function login(): void
    {
        try {
            $conn = Database::getConnection();

            $stmt = $conn->prepare("SELECT id, nome, senha FROM usuario WHERE cpf = :cpf");
            $stmt->bindParam(':cpf', $_POST['cpf'], PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // yes, ik it's unsafe
            if (!$usuario || $usuario['senha'] != $_POST['senha']) {
                $_SESSION['message'] = ['Credenciais inválidas', 'fail'];
                exit;
            }

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            header('Location: /dashboard');
        } catch (PDOException $e) {
            Logger::error('Erro de banco de dados', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Houve um erro sério, favor contatar o desenvolvedor do sistema', 'fail'];
            header("Location: /login");
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

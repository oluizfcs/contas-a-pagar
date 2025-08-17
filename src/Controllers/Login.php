<?php

namespace App\Controllers;

use App\Models\Database;
use PDO;
use PDOException;

class Login implements Controller
{
    public bool $needLogin = false;

    function __construct(string $p1 = '')
    {
        $this->loadView();

        if(!empty($_POST)) {
            $this->login();
        }

        if($p1 == 'sair') {
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
            if(!$usuario || $usuario['senha'] != $_POST['senha']) {
                echo 'usuário ou senha incorretos';
                exit;
            }

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
    
            header('Location: ' . $_ENV['BASE_URL'] . '/dashboard');

        } catch(PDOException $e) {
            echo 'Erro no banco de dados: ' . $e->getMessage();
        }
    }

    private function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . $_ENV['BASE_URL'] . '/login');
    }

    private function loadView() : void
    {
        include 'src/Views/login.php';
    }
}
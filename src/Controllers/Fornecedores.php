<?php

namespace App\Controllers;

use App\Models\Database;
use App\Logs\EntityLogger;
use PDO;
use PDOException;
use App\Controllers\Services\Pagination;

class Fornecedores implements Controller
{
    public bool $needLogin = true;
    private array $views = ['index', 'adicionar'];
    public static string $tableName = 'fornecedor';
    private int $page;
    private Pagination $pagination;

    function __construct(string $view = "index")
    {
        $page = filter_var($view, FILTER_VALIDATE_INT);
        if ($page) {
            $this->page = $page;
            $view = 'index';
        }

        $this->loadView($view);

        if (!empty($_POST)) {
            switch ($_POST['type']) {
                case 'create':
                    $this->create();
                    break;
                case 'update':
                    $this->update();
                    break;
                case 'delete':
                    $this->delete();
                    break;
            }
        }
    }

    private function create(): void
    {
        try {
            $conn = Database::getConnection();

            $stmt = $conn->prepare("INSERT INTO " . Fornecedores::$tableName . "(nome, telefone) VALUES (:nome, :telefone)");

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':telefone', $_POST['telefone'], PDO::PARAM_STR);

            $stmt->execute();

            EntityLogger::log_create($conn, Fornecedores::$tableName);

            echo 'fornecedor criado com sucesso!';
        } catch (PDOException $e) {
            echo 'Erro ao criar fornecedor: ' . $e->getMessage();
        }
    }

    private function update(): void
    {
        echo 'chamou a update';
    }

    private function delete(): void
    {
        echo 'chamou a delete';
    }

    private function loadView(string $view): void
    {
        if (!in_array($view, $this->views)) {
            include '404.php';
            exit;
        }

        if (!isset($this->page) && $view == 'index') {
            $this->page = 1;
        }

        if (isset($this->page)) {
            $this->pagination = new Pagination(self::$tableName, 10);
            $fornecedores = $this->pagination->getPage($this->page, 'id, nome, telefone');
            $lastPage = $this->pagination->getLastPage();
        }

        include 'templates/header.php';
        include "src/Views/fornecedores/$view.php";
        include 'templates/footer.php';
    }
}

<?php

namespace App\Models;

use App\Controllers\Services\Logger;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        if (self::$conn == null) {

            try {
                self::$conn = new PDO(
                    "mysql:dbname={$_ENV['DB_NAME']};host={$_ENV['HOST_NAME']};charset=utf8mb4",
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                Logger::error('Erro de banco de dados', ['PDOException' => $e->getMessage()]);
                $_SESSION['message'] = ['Houve um erro sério, favor contatar o desenvolvedor do sistema', 'fail'];
                exit;
            }
        }

        return self::$conn;
    }

    public static function countAll(string $table): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        if ($stmt == false) {
            $_SESSION['message'] = ['Erro inesperado, favor contatar o desenvolvedor do sistema', 'fail'];
            Logger::error("Falha ao contar registros", ["table" => $table]);
            exit;
        }
        return $stmt->fetch()[0];
    }

    public static function countDeleted(string $table, bool $deleted): int
    {
        $deleted = ($deleted) ? 1 : 0;
        $conn = Database::getConnection();
        $stmt = $conn->query("SELECT COUNT(*) FROM $table WHERE deleted = $deleted");
        if ($stmt == false) {
            $_SESSION['message'] = ['Erro inesperado, favor contatar o desenvolvedor do sistema', 'fail'];
            Logger::error("Falha ao contar registros (não-deletados)", ["table" => $table]);
            exit;
        }
        return $stmt->fetch()[0];
    }

    public static function getByPage(
        string $table,
        string $columns,
        int $rowsPerPage,
        int $page,
        bool $deleted
    ): array {
        if ($page == 0) {
            return [];
        }
        $page--;
        $offset = $rowsPerPage * $page;

        try {
            $conn = Database::getConnection();

            $stmt = $conn->prepare("SELECT $columns FROM $table WHERE deleted = :deleted LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':deleted', $deleted, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $rowsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'erro no getByPage' . $e->getMessage();
        }
        return [];
    }

    public static function getById(string $table, int $id): array|false
    {
        $stmt = self::getConnection()->query("SELECT * FROM $table WHERE id = $id");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByColumn(string $table, string $column, string $value): array|false
    {
        $stmt = self::getConnection()->query("SELECT * FROM $table WHERE $column = $value");
        if (!$stmt) {
            return false;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getLog(string $table, int $id): array
    {
        $entity = $table;

        if ($table == 'usuario') {
            $entity = $table . '_editado';
        }

        $sql = "SELECT * FROM log_$table WHERE $entity" . "_id = $id ORDER BY data_log DESC";

        $stmt = self::getConnection()->query($sql);
        if (!$stmt) {
            die("Erro ao buscar log");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

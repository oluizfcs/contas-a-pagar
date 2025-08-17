<?php

namespace App\Models;

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
                die('Database error: ' . $e->getMessage()); // todo: add logging
            }
        }

        return self::$conn;
    }

    public static function countAll($table): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        if($stmt == false) {
            die("A tabela $table não existe.");
        }
        return $stmt->fetch()[0];
    }

    public static function getByPage(
        string $table,
        string $columns,
        int $rowsPerPage,
        int $page
    ): array {
        $page--;
        $offset = $rowsPerPage * $page;
 
        try {
            $conn = Database::getConnection();

            $stmt = $conn->prepare("SELECT $columns FROM $table LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $rowsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'erro no getByPage' . $e->getMessage();
        }
        return [];
    }
}

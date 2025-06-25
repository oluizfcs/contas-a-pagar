<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        if(self::$conn == null) {

            try{
                self::$conn = new PDO(
                    "mysql:dbname={$_ENV['DB_NAME']};host={$_ENV['HOST_NAME']};charset=utf8mb4",
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch(PDOException $e) {
                die('Database error: ' . $e->getMessage()); // todo: add logging
            }
        }

        return self::$conn;
    }
}
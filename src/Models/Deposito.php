<?php

namespace App\Models;

use App\Controllers\Services\Logger;
use App\Models\Database;
use PDO;
use PDOException;

class Deposito
{
    public static string $tableName = 'deposito';
    public static int $LIMITE_DEPOSITO = 100000000;

    public function __construct(
        private int $id,
        private int $usuarioId,
        private int $bancoId,
        private int $valorEmCentavos,
        private string $descricao,
        private string $dataDeposito
    ) {
        $this->descricao = htmlspecialchars($this->descricao, ENT_QUOTES, 'UTF-8', false);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getBancoId(): int
    {
        return $this->bancoId;
    }

    public function getValorEmcentavos(): int
    {
        return $this->valorEmCentavos;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getDataDeposito(): string
    {
        return $this->dataDeposito;
    }

    public function save(): bool
    {
        try {
            $conn = Database::getConnection();

            $stmt = $conn->prepare('SELECT COUNT(*) FROM ' . self::$tableName . ' WHERE id = :id');
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()[0] == 0) {
                // criar
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (usuario_id, banco_id, valor_em_centavos, descricao) VALUES (:usuario_id, :banco_id, :valor_em_centavos, :descricao)');
                $stmt->bindParam(':usuario_id', $this->usuarioId, PDO::PARAM_INT);
                $stmt->bindParam(':banco_id', $this->bancoId, PDO::PARAM_INT);
                $stmt->bindParam(':valor_em_centavos', $this->valorEmCentavos, PDO::PARAM_INT);
                $stmt->bindParam(':descricao', $this->descricao, PDO::PARAM_STR);

                $stmt->execute();

                Logger::log_create($conn, self::$tableName);

                return true;
            }
            return false;
        } catch (PDOException $e) {
            Logger::error('Erro ao realizar depósito', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao realizar depósito', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/bancos');
            exit;
        }
    }

    public static function getByBank(int $banco_id): array
    {
        try {
            $stmt = Database::getConnection()->prepare("
            SELECT 
                id,
                usuario_id,
                banco_id,
                valor_em_centavos,
                descricao,
                data_deposito
            FROM deposito
            WHERE banco_id = :id
            ORDER BY id DESC");
            $stmt->bindParam(':id', $banco_id, PDO::PARAM_INT);
            $stmt->execute();

            $depositos = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $deposito) {
                extract($deposito);
                $depositos[] = new Deposito($id, $usuario_id, $banco_id, $valor_em_centavos, $descricao, $data_deposito);
            }

            return $depositos;
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar depositos por banco', ['id' => $banco_id, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }
}
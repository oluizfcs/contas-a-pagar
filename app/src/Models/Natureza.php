<?php

namespace App\Models;

use App\Controllers\Services\Logger;
use App\Models\Database;
use PDO;
use PDOException;

class Natureza
{
    public static $tableName = 'natureza';

    public function __construct(
        private int $id,
        private string $nome,
        private string $dataCriacao = '',
        private string|null $dataEdicao = null,
        private bool $enabled = true
    ) {
        $this->nome = htmlspecialchars($this->nome, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nome
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return self
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDataCriacao(): string
    {
        return $this->dataCriacao;
    }

    /**
     * Set the value of data_criacao
     *
     * @return self
     */
    public function setDataCriacao($dataCriacao)
    {
        $this->dataCriacao = $dataCriacao;

        return $this;
    }

    /**
     * Get the value of data_edicao
     */
    public function getDataEdicao()
    {
        return $this->dataEdicao;
    }

    /**
     * Set the value of dataEdicao
     *
     * @return self
     */
    public function setDataEdicao($dataEdicao)
    {
        $this->dataEdicao = $dataEdicao;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): bool
    {
        $query = 'SELECT COUNT(*) 
        FROM natureza
        INNER JOIN conta
        ON natureza_id = natureza.id
        WHERE conta.paid = 0
        AND natureza.id = :natureza_id';

        $stmt = Database::getConnection()->prepare($query);
        $stmt->bindParam(":natureza_id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()[0] != 0) {
            return false;
        }

        $this->enabled = $enabled;
        return true;
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
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (nome) VALUES (:nome)');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);

                $stmt->execute();

                Logger::log_create($conn, self::$tableName);

                return true;
            } else {
                // atualizar
                $naturezaAntiga = self::getById($this->id);

                $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET nome = :nome, enabled = :enabled WHERE id = :id');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':enabled', $this->enabled, PDO::PARAM_BOOL);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                if ($naturezaAntiga->getNome() != $this->nome) {
                    Logger::log(self::$tableName, 'nome', $naturezaAntiga->getNome(), $this->nome, $this->id, $_SESSION['usuario_id']);
                }

                return true;
            }
        } catch (PDOException $e) {
            Logger::error('Erro ao cadastrar|atualizar natureza', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar|atualizar natureza', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/naturezas');
            exit;
        }
    }

    public static function getAll(bool $enabled, string $search): array
    {
        $sql = "SELECT id, nome
        FROM natureza
        WHERE natureza.enabled = :enabled";

        if (strlen($search) > 0) {
            $sql = $sql . ' AND nome LIKE :search';
        }

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindValue(':enabled', $enabled, PDO::PARAM_BOOL);

            if (strlen($search) > 0) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao listar naturezas', ['enabled' => $enabled, 'search' => $search, 'PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/dashboard');
            exit;
        }
    }

    public static function getById(int $id): Natureza
    {
        $natureza = Database::getById(self::$tableName, $id);
        if (!$natureza) {
            $_SESSION['message'] = ['Natureza não encontrada', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/naturezas');
            exit;
        }

        extract($natureza);
        return new Natureza($id, $nome, $data_criacao, $data_edicao, $enabled);
    }

    public static function getTotalsByPeriod(string $start, string $end, string $status = 'todas'): array
    {
        $sql = "SELECT 
                    f.nome, 
                    SUM(p.valor_em_centavos) as total 
                FROM natureza f
                JOIN conta c ON f.id = c.natureza_id
                JOIN parcela p ON c.id = p.conta_id
                WHERE p.data_vencimento BETWEEN :start AND :end
                AND f.enabled = 1";

        if ($status === 'a_pagar') {
            $sql .= " AND p.paid = 0";
        } elseif ($status === 'pagas') {
            $sql .= " AND p.paid = 1";
        }

        $sql .= " GROUP BY f.id
                ORDER BY total DESC";

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar top naturezas', ['start' => $start, 'end' => $end, 'status' => $status, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }
}

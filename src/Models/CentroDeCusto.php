<?php

namespace App\Models;

use App\Controllers\Services\Logger;
use App\Models\Database;
use PDO;
use PDOException;

class CentroDeCusto
{
    public static string $tableName = 'centro_de_custo';

    public function __construct(
        private int $id,
        private string $nome,
        private string $data_criacao,
        private string|null $data_edicao,
        private bool $enabled
    ) {
        $this->nome = htmlspecialchars($this->nome, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Get the value of id
     */
    public function getId()
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
    public function getNome()
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

    /**
     * Get the value of data_criacao
     */
    public function getData_criacao()
    {
        return $this->data_criacao;
    }

    /**
     * Set the value of data_criacao
     *
     * @return self
     */
    public function setData_criacao($data_criacao)
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    /**
     * Get the value of data_edicao
     */
    public function getData_edicao()
    {
        return $this->data_edicao;
    }

    /**
     * Set the value of data_edicao
     *
     * @return self
     */
    public function setData_edicao($data_edicao)
    {
        $this->data_edicao = $data_edicao;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): bool
    {
        $query = 'SELECT COUNT(*) 
        FROM centro_de_custo
        INNER JOIN conta
        ON centro_de_custo_id = centro_de_custo.id
        WHERE conta.paid = 0
        AND centro_de_custo.id = :centro_de_custo_id';

        $stmt = Database::getConnection()->prepare($query);
        $stmt->bindParam(":centro_de_custo_id", $this->id, PDO::PARAM_INT);
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
                $bancoAntigo = self::getById($this->id);

                $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET nome = :nome, enabled = :enabled WHERE id = :id');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':enabled', $this->enabled, PDO::PARAM_BOOL);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                if ($bancoAntigo->getNome() != $this->nome) {
                    Logger::log(self::$tableName, 'nome', $bancoAntigo->getNome(), $this->nome, $this->id, $_SESSION['usuario_id']);
                }

                return true;
            }
        } catch (PDOException $e) {
            Logger::error('Erro ao cadastrar|atualizar centro de custo', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar|atualizar centro de custo', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo');
            exit;
        }
    }

    public static function getAll(bool $enabled, int $paid, string $search): array
    {
        $sql = "SELECT 
            centro_de_custo.id,
            nome,
            SUM(valor_em_centavos) as total,
            COUNT(conta.id) as quantidade,
            SUM(valor_em_centavos) / COUNT(conta.id) as media
        FROM centro_de_custo
        LEFT JOIN conta ON centro_de_custo.id = centro_de_custo_id
        WHERE centro_de_custo.enabled = :enabled
        AND (conta.enabled = 1 OR conta.enabled IS NULL)";

        if (($paid == 0) && $enabled) {
            $sql = $sql . ' AND conta.paid = 0';
        }

        if (($paid == 1) && $enabled) {
            $sql = $sql . ' AND conta.paid = 1';
        }

        if (strlen($search) > 0) {
            $sql = $sql . ' AND nome LIKE :search';
        }

        $sql = $sql . ' GROUP BY centro_de_custo.id';

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindValue(':enabled', $enabled ? 1 : 0);

            if (strlen($search) > 0) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao listar centros de custo', ['enabled' => $enabled, 'paid' => $paid, 'search' => $search, 'PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/dashboard');
            exit;
        }
    }

    public static function getById(int $id): CentroDeCusto
    {
        $centroDeCusto = Database::getById(self::$tableName, $id);
        if (!$centroDeCusto) {
            $_SESSION['message'] = ['Centro de custo não encontrado', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo');
            exit;
        }

        extract($centroDeCusto);
        return new CentroDeCusto($id, $nome, $data_criacao, $data_edicao, $enabled);
    }

    public static function getTotalsByPeriod(string $start, string $end, string $status = 'todas'): array
    {
        $sql = "SELECT 
                    cc.nome, 
                    SUM(p.valor_em_centavos) as total 
                FROM centro_de_custo cc
                JOIN conta c ON cc.id = c.centro_de_custo_id
                JOIN parcela p ON c.id = p.conta_id
                WHERE p.data_vencimento BETWEEN :start AND :end
                AND cc.enabled = 1";

        if ($status === 'a_pagar') {
            $sql .= " AND p.paid = 0";
        } elseif ($status === 'pagas') {
            $sql .= " AND p.paid = 1";
        }

        $sql .= " GROUP BY cc.id
                ORDER BY total DESC";

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar totais por centro de custo', ['start' => $start, 'end' => $end, 'status' => $status, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }
}

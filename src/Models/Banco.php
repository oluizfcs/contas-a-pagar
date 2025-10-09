<?php

namespace App\Models;

use App\Controllers\Services\Logger;
use App\Controllers\Services\Money;
use App\Models\Database;
use PDO;
use PDOException;

class Banco
{
    public static string $tableName = 'banco';

    public function __construct(
        private int $id,
        private string $nome,
        private int $saldo_em_centavos,
        private string $data_criacao,
        private string|null $data_edicao,
        private bool $enabled
    ) {}

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
     * Get the value of saldo_em_centavos
     */
    public function getSaldo_em_centavos()
    {
        return $this->saldo_em_centavos;
    }

    /**
     * Set the value of saldo_em_centavos
     *
     * @return self
     */
    public function setSaldo_em_centavos($saldo_em_centavos)
    {
        $this->saldo_em_centavos = $saldo_em_centavos;

        return $this;
    }

    public function getSaldo_em_reais(): string
    {
        return Money::centavos_para_reais($this->saldo_em_centavos);
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

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (nome, saldo_em_centavos) VALUES (:nome, :saldo_em_centavos)');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':saldo_em_centavos', $this->saldo_em_centavos, PDO::PARAM_STR);

                $stmt->execute();

                Logger::log_create($conn, self::$tableName);

                return true;
            } else {
                // atualizar
                $bancoAntigo = self::getById($this->id);

                $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET nome = :nome, saldo_em_centavos = :saldo_em_centavos, enabled = :enabled WHERE id = :id');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':saldo_em_centavos', $this->saldo_em_centavos, PDO::PARAM_STR);
                $stmt->bindParam(':enabled', $this->enabled, PDO::PARAM_BOOL);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                if ($bancoAntigo->getNome() != $this->nome) {
                    Logger::log(self::$tableName, 'nome', $bancoAntigo->getNome(), $this->nome, $this->id, $_SESSION['usuario_id']);
                }

                return true;
            }
        } catch (PDOException $e) {
            Logger::error('Erro ao cadastrar|atualizar banco', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar|atualizar banco', 'fail'];
            header("Location: /bancos");
            exit;
        }
    }

    public static function getAll(bool $enabled, int $paid, string $search): array
    {
        $sql = "SELECT 
            banco.id,
            nome,
            saldo_em_centavos,
            SUM(valor_em_centavos) as total,
            COUNT(conta.id) as quantidade,
            SUM(valor_em_centavos) / COUNT(conta.id) as media
        FROM banco
        LEFT JOIN conta ON banco.id = banco_id
        WHERE banco.enabled = :enabled
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

        $sql = $sql . ' GROUP BY banco.id';

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindValue(':enabled', $enabled ? 1 : 0);

            if (strlen($search) > 0) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao listar bancos', ['enabled' => $enabled, 'paid' => $paid, 'search' => $search, 'PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header("Location: /dashboard");
            exit;
        }
    }

    public static function getById(int $id): Banco
    {
        $banco = Database::getById(self::$tableName, $id);
        if (!$banco) {
            $_SESSION['message'] = ['Banco não encontrado', 'fail'];
            header("Location: /bancos");
            exit;
        }

        extract($banco);
        return new Banco($id, $nome, $saldo_em_centavos, $data_criacao, $data_edicao, $enabled);
    }
}

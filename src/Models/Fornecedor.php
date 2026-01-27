<?php

namespace App\Models;

use App\Controllers\Services\Logger;
use App\Models\Database;
use PDO;
use PDOException;

class Fornecedor
{
    public static $tableName = 'fornecedor';

    public function __construct(
        private int $id,
        private string $nome,
        private string $telefone,
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
     * Get the value of telefone
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set the value of telefone
     *
     * @return self
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

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
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (nome, telefone) VALUES (:nome, :telefone)');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':telefone', $this->telefone, PDO::PARAM_STR);

                $stmt->execute();

                Logger::log_create($conn, self::$tableName);

                return true;
            } else {
                // atualizar
                $fornecedorAntigo = self::getById($this->id);

                $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET nome = :nome, telefone = :telefone, enabled = :enabled WHERE id = :id');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':telefone', $this->telefone, PDO::PARAM_STR);
                $stmt->bindParam(':enabled', $this->enabled, PDO::PARAM_BOOL);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                if ($fornecedorAntigo->getNome() != $this->nome) {
                    Logger::log(self::$tableName, 'nome', $fornecedorAntigo->getNome(), $this->nome, $this->id, $_SESSION['usuario_id']);
                }

                if ($fornecedorAntigo->getTelefone() != $this->telefone) {
                    Logger::log(self::$tableName, 'telefone', $fornecedorAntigo->getTelefone(), $this->telefone, $this->id, $_SESSION['usuario_id']);
                }

                return true;
            }
        } catch (PDOException $e) {
            Logger::error('Erro ao cadastrar|atualizar fornecedor', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar|atualizar fornecedor', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/fornecedores');
            exit;
        }
    }

    public static function getAll(bool $enabled, int $paid, string $search): array
    {
        $sql = "SELECT 
            fornecedor.id,
            nome,
            telefone,
            SUM(valor_em_centavos) as total,
            COUNT(conta.id) as quantidade,
            SUM(valor_em_centavos) / COUNT(conta.id) as media
        FROM fornecedor
        LEFT JOIN conta ON fornecedor.id = fornecedor_id
        WHERE fornecedor.enabled = :enabled
        AND (conta.enabled = 1 OR conta.enabled IS NULL)";

        if (($paid == 0) && $enabled) {
            $sql = $sql . ' AND conta.paid = 0';
        }

        if ($paid == 1) {
            $sql = $sql . ' AND conta.paid = 1';
        }

        if(strlen($search) > 0) {
            $sql = $sql . ' AND nome LIKE :search OR telefone LIKE :search';
        }
        
        $sql = $sql . ' GROUP BY fornecedor.id';
        
        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindValue(':enabled', $enabled ? 1 : 0);
            
            if(strlen($search) > 0) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }
            
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao listar fornecedores', ['enabled' => $enabled, 'paid' => $paid, 'search' => $search, 'PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/dashboard');
            exit;
        }
    }

    public static function getById(int $id): Fornecedor
    {
        $fornecedor = Database::getById(self::$tableName, $id);
        if (!$fornecedor) {
            $_SESSION['message'] = ['Fornecedor não encontrado', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/fornecedores');
            exit;
        }

        extract($fornecedor);
        return new Fornecedor($id, $nome, $telefone, $data_criacao, $data_edicao, $enabled);
    }
}

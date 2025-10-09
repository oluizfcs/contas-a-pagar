<?php

namespace App\Models;

use App\Models\Database;
use PDO;
use PDOException;
use App\Controllers\Services\Logger;

class Conta
{
    public static string $tableName = 'conta';
    public int $lastInsertId;

    public function __construct(
        private int $id,
        private string $descricao,
        private int $valor_em_centavos,
        private string $data_criacao,
        private string|null $data_edicao,
        private int $banco_id,
        private int $centro_de_custo_id,
        private int $fornecedor_id,
        private array $parcelas,
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
     * Get the value of descricao
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     *
     * @return self
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get the value of valor_em_centavos
     */
    public function getValor_em_centavos()
    {
        return $this->valor_em_centavos;
    }

    /**
     * Set the value of valor_em_centavos
     *
     * @return self
     */
    public function setValor_em_centavos($valor_em_centavos)
    {
        $this->valor_em_centavos = $valor_em_centavos;

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

    public function getBanco_id()
    {
        return $this->banco_id;
    }

    public function setBanco_id($banco_id)
    {
        $this->banco_id = $banco_id;

        return $this;
    }

    /**
     * Get the value of centro_de_custo_id
     */
    public function getCentro_de_custo_id()
    {
        return $this->centro_de_custo_id;
    }

    /**
     * Set the value of centro_de_custo_id
     *
     * @return self
     */
    public function setCentro_de_custo_id($centro_de_custo_id)
    {
        $this->centro_de_custo_id = $centro_de_custo_id;

        return $this;
    }

    /**
     * Get the value of fornecedor_id
     */
    public function getFornecedor_id()
    {
        return $this->fornecedor_id;
    }

    /**
     * Set the value of fornecedor_id
     *
     * @return self
     */
    public function setFornecedor_id($fornecedor_id)
    {
        $this->fornecedor_id = $fornecedor_id;

        return $this;
    }

    public function getParcelas(): array
    {
        return $this->parcelas;
    }

    public function setParcelas(): void {}

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
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (descricao, valor_em_centavos, banco_id, centro_de_custo_id, fornecedor_id) VALUES (:descricao, :valor_em_centavos, :banco_id, :centro_de_custo_id, :fornecedor_id)');
                $stmt->bindParam(':descricao', $this->descricao, PDO::PARAM_STR);
                $stmt->bindParam(':valor_em_centavos', $this->valor_em_centavos, PDO::PARAM_STR);
                $stmt->bindParam(':banco_id', $this->banco_id, PDO::PARAM_INT);
                $stmt->bindParam(':centro_de_custo_id', $this->centro_de_custo_id, PDO::PARAM_INT);
                $stmt->bindParam(':fornecedor_id', $this->fornecedor_id, PDO::PARAM_INT);
                
                $stmt->execute();
                $this->lastInsertId = $conn->lastInsertId();

                Logger::log_create($conn, self::$tableName);

                return true;
            } else {
                // // atualizar
                // $bancoAntigo = self::getById($this->id);

                // $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET descricao = :descricao, saldo_em_centavos = :saldo_em_centavos, enabled = :enabled WHERE id = :id');
                // $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                // $stmt->bindParam(':saldo_em_centavos', $this->saldo_em_centavos, PDO::PARAM_STR);
                // $stmt->bindParam(':enabled', $this->enabled, PDO::PARAM_BOOL);
                // $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                // $stmt->execute();

                // if ($bancoAntigo->getNome() != $this->nome) {
                //     Logger::log(self::$tableName, 'nome', $bancoAntigo->getNome(), $this->nome, $this->id, $_SESSION['usuario_id']);
                // }

                return false;
            }
        } catch (PDOException $e) {
            Logger::error('Erro ao cadastrar|atualizar conta', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar|atualizar conta', 'fail'];
            header("Location: /contas");
            exit;
        }
    }

    public static function getById(int $id): Conta
    {
        $conta = Database::getById(self::$tableName, $id);
        if (!$conta) {
            $_SESSION['message'] = ['Conta não encontrada', 'fail'];
            header("Location: /contas");
            exit;
        }

        extract($conta);
        return new Conta($id, $descricao, $valor_em_centavos, $data_criacao, $data_edicao, $banco_id, $centro_de_custo_id, $fornecedor_id, self::findInstallments($id), $enabled);
    }

    public static function getAll(string $search, string $status): array
    {
        $sql = "SELECT 
            conta.id,
            descricao,
            valor_em_centavos,
            c.nome centro
        FROM conta
        INNER JOIN centro_de_custo c ON centro_de_custo_id = c.id
        WHERE 1 = 1";

        switch($status) {
            case "a pagar":
                $sql = $sql . " AND paid = 0 AND conta.enabled = 1";
                break;
            case "pagas":
                $sql = $sql . " AND paid = 1 AND conta.enabled = 1";
                break;
            case "inativadas":
                $sql = $sql . " AND conta.enabled = 0";
                break;
            default: // also "todas"
                $sql = $sql . " AND conta.enabled = 1";
        }

        if(strlen($search) > 0) {
            $sql = $sql . " AND descricao LIKE :descricao";
        }

        try {
            $stmt = Database::getConnection()->prepare($sql);
            if(strlen($search) > 0) {
                $stmt->bindParam(':descricao', $search, PDO::PARAM_STR);
            }
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao listar contas', ['status' => $status, 'search' => $search, 'PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header("Location: /dashboard");
            exit;
        }
    }

    public static function findInstallments(int $id): array
    {
        $sql = "SELECT * FROM parcela WHERE conta_id = :conta_id ORDER BY numero_parcela";

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindParam(':conta_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao encontrar parcelas', ['conta_id' => $id, 'PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header("Location: /dashboard");
            exit;
        }
    }
}

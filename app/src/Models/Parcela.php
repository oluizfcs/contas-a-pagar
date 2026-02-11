<?php

namespace App\Models;

use App\Models\Database;
use PDO;
use PDOException;
use App\Controllers\Services\Logger;

class Parcela
{
    public static string $tableName = 'parcela';
    public string $centro_de_custo;
    public string|null $fornecedor;

    public function __construct(
        private int $id,
        private int $numero_parcela,
        private int $valor_em_centavos,
        private string $data_vencimento,
        private string|null $data_pagamento,
        private int $conta_id,
        private int|null $banco_id,
        private bool $paid
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
     * Get the value of numero_parcela
     */
    public function getNumero_parcela()
    {
        return $this->numero_parcela;
    }

    /**
     * Set the value of numero_parcela
     *
     * @return self
     */
    public function setNumero_parcela($numero_parcela)
    {
        $this->numero_parcela = $numero_parcela;

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
     * Get the value of data_vencimento
     */
    public function getData_vencimento()
    {
        return $this->data_vencimento;
    }

    /**
     * Set the value of data_vencimento
     *
     * @return self
     */
    public function setData_vencimento($data_vencimento)
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }

    /**
     * Get the value of data_pagamento
     */
    public function getData_pagamento(): string|null
    {
        return $this->data_pagamento;
    }

    /**
     * Set the value of data_pagamento
     *
     * @return self
     */
    public function setData_pagamento($data_pagamento)
    {
        $this->data_pagamento = $data_pagamento;

        return $this;
    }

    /**
     * Get the value of conta_id
     */
    public function getConta_id()
    {
        return $this->conta_id;
    }

    /**
     * Set the value of conta_id
     *
     * @return self
     */
    public function setConta_id($conta_id)
    {
        $this->conta_id = $conta_id;

        return $this;
    }

    /**
     * Get the value of paid
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * Set the value of paid
     *
     * @return self
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    public function getBanco_id(): int|null
    {
        return $this->banco_id;
    }

    public function setBanco_id($banco_id)
    {
        $this->banco_id = $banco_id;
    }

    public function save(): bool
    {
        try {
            $conn = Database::getConnection();
            $stmt = $conn->prepare('SELECT * FROM ' . self::$tableName . ' WHERE id = :id');
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            $entry = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($entry == false) {
                // criar
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (numero_parcela, valor_em_centavos, data_vencimento, conta_id, paid) VALUES (:numero_parcela, :valor_em_centavos, :data_vencimento, :conta_id, :paid)');
                $stmt->bindParam(':numero_parcela', $this->numero_parcela, PDO::PARAM_INT);
                $stmt->bindParam(':valor_em_centavos', $this->valor_em_centavos, PDO::PARAM_INT);
                $stmt->bindParam(':data_vencimento', $this->data_vencimento, PDO::PARAM_STR);

                if ($this->conta_id != null) {
                    $stmt->bindParam(':conta_id', $this->conta_id, PDO::PARAM_INT);
                }

                $stmt->bindParam(':paid', $this->paid, PDO::PARAM_BOOL);

                $stmt->execute();

                // Logger::log_create($conn, self::$tableName);

                return true;
            } else {
                if ($entry['paid'] == 1) {
                    return false;
                }

                $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET data_pagamento = :data_pagamento, banco_id = :banco_id, paid = :paid WHERE id = :id');

                $stmt->bindParam(':data_pagamento', $this->data_pagamento);
                $stmt->bindParam(':banco_id', $this->banco_id);
                $stmt->bindParam(':paid', $this->paid, PDO::PARAM_BOOL);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                return true;
            }
        } catch (PDOException $e) {
            Logger::error('Erro ao cadastrar|atualizar conta', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar|atualizar conta', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas');
            exit;
        }
    }

    public static function getAll(
        string $status,
        string $naturezaId = 'all',
        string $centroId = 'all',
        string $fornecedorId = 'all'
    ): array {
        $sql = "SELECT 
            p.id,
            p.conta_id,
            p.numero_parcela,
            p.valor_em_centavos,
            p.data_vencimento,
            p.data_pagamento,
            n.nome natureza,
            cc.nome centro,
            f.nome fornecedor,
            (
                SELECT COUNT(*)
                FROM parcela p2
                WHERE p2.conta_id = p.conta_id
            ) AS total_parcelas
        FROM parcela p
        INNER JOIN conta AS c ON p.conta_id = c.id
        INNER JOIN natureza n ON c.natureza_id = n.id
        INNER JOIN centro_de_custo cc ON c.centro_de_custo_id = cc.id
        LEFT JOIN fornecedor f ON c.fornecedor_id = f.id
        WHERE 1 = 1";

        switch ($status) {
            case "a pagar":
                $sql = $sql . ' AND p.paid = 0';
                break;
            case "pagas":
                $sql = $sql . ' AND p.paid = 1';
                break;
            default: // also "todas"
                // do nothing
        }

        if ($naturezaId != 'all') {
            $sql = $sql . " AND n.id = :natureza_id";
        }

        if ($centroId != 'all') {
            $sql = $sql . " AND cc.id = :centro_id";
        }

        if ($fornecedorId != 'all') {
            $sql = $sql . " AND f.id = :fornecedor_id";
        }

        $sql = $sql . ' ORDER BY data_vencimento';

        try {
            $stmt = Database::getConnection()->prepare($sql);

            if ($naturezaId != 'all') {
                $stmt->bindParam(':natureza_id', $naturezaId, PDO::PARAM_INT);
            }

            if ($centroId != 'all') {
                $stmt->bindParam(':centro_id', $centroId, PDO::PARAM_INT);
            }

            if ($fornecedorId != 'all') {
                $stmt->bindParam(':fornecedor_id', $fornecedorId, PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error(
                'Falha ao listar contas',
                [
                    'status' => $status,
                    'natureza' => $naturezaId,
                    'centro' => $centroId,
                    'fornecedor' => $fornecedorId,
                    'PDOException' => $e->getMessage()
                ]
            );
            $_SESSION['message'] = ['Erro inesperado, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/dashboard');
            exit;
        }
    }

    public static function getById(int $id): Parcela
    {
        $parcela = Database::getById(self::$tableName, $id);
        if (!$parcela) {
            $_SESSION['message'] = ['Parcela não encontrada', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas');
            exit;
        }

        extract($parcela);
        return new Parcela($id, $numero_parcela, $valor_em_centavos, $data_vencimento, $data_pagamento, $conta_id, $banco_id, $paid);
    }

    public static function getDailyTotal(string $start, string $end, string $status = 'todas', string $naturezaId = 'all'): array
    {
        $sql = "SELECT 
                    data_vencimento as date, 
                    SUM(parcela.valor_em_centavos) as total 
                FROM parcela
                INNER JOIN conta ON conta_id = conta.id 
                INNER JOIN natureza ON conta.natureza_id = natureza.id
                WHERE data_vencimento BETWEEN :start AND :end
                AND conta.enabled = 1";

        if ($status === 'a_pagar') {
            $sql .= " AND parcela.paid = 0";
        } elseif ($status === 'pagas') {
            $sql .= " AND parcela.paid = 1";
        }

        if ($naturezaId != 'all') {
            $sql .= " AND conta.natureza_id = :natureza_id";
        }

        $sql .= " GROUP BY data_vencimento 
                ORDER BY data_vencimento";

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);

            if ($naturezaId != 'all') {
                $stmt->bindParam(':natureza_id', $naturezaId, PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar totais diários', ['start' => $start, 'end' => $end, 'status' => $status, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }

    public static function bulkInsert(array $parcelas, int $conta_id): void
    {
        $sql = "INSERT INTO parcela(numero_parcela, valor_em_centavos, data_vencimento, conta_id) VALUES ";

        for ($i = 0; $i < count($parcelas); $i++) {
            $sql = $sql . "(:numero_parcela$i, :valor$i, :vencimento$i, :conta_id)";

            if ($i != count($parcelas) - 1) {
                $sql = $sql . ", ";
            }
        }

        try {
            $stmt = Database::getConnection()->prepare($sql);

            for ($i = 0; $i < count($parcelas); $i++) {
                $stmt->bindValue(":numero_parcela$i", $i + 1);
                $stmt->bindValue(":valor$i", $parcelas[$i]['valor'], PDO::PARAM_INT);
                $stmt->bindValue(":vencimento$i", $parcelas[$i]['vencimento'], PDO::PARAM_STR);
                $stmt->bindValue(':conta_id', $conta_id, PDO::PARAM_INT);
            }

            $stmt->execute();
        } catch (PDOException $e) {
            Logger::error('Falha ao inserir parcelas', ['PDOException' => $e->getMessage()]);
            $_SESSION['message'] = ['Erro ao cadastrar parcelas, entre em contato com o desenvolvedor do sistema.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/contas');
            exit;
        }
    }

    public static function getByBank(int $banco_id): array
    {
        try {
            $stmt = Database::getConnection()->prepare("
            SELECT 
                p.id, 
                p.numero_parcela, 
                p.valor_em_centavos, 
                p.data_pagamento, 
                p.conta_id,
                centro.nome as centro,
                f.nome as fornecedor
            FROM parcela p
            INNER JOIN conta c ON p.conta_id = c.id
            INNER JOIN centro_de_custo centro ON c.centro_de_custo_id = centro.id
            LEFT JOIN fornecedor f ON c.fornecedor_id = f.id
            WHERE banco_id = :id");
            $stmt->bindParam(':id', $banco_id, PDO::PARAM_INT);
            $stmt->execute();

            $parcelas = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $parcela) {
                extract($parcela);
                $p = new Parcela($id, $numero_parcela, $valor_em_centavos, '', $data_pagamento, $conta_id, null, 1);
                $p->centro_de_custo = $centro;
                $p->fornecedor = $fornecedor;
                $parcelas[] = $p;
            }

            return $parcelas;
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar parcelas por banco', ['id' => $id, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }
}

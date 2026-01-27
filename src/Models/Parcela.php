<?php

namespace App\Models;

use App\Models\Database;
use PDO;
use PDOException;
use App\Controllers\Services\Logger;

class Parcela
{
    public static string $tableName = 'parcela';

    public function __construct(
        private int $id,
        private int $numero_parcela,
        private int $valor_em_centavos,
        private string $data_vencimento,
        private string|null $data_pagamento,
        private int $conta_id,
        private int|null $banco_id,
        private bool $paid
    ) {
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

                Logger::log_create($conn, self::$tableName);

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

    public static function getAll(string $status): array
    {
        $sql = "SELECT 
            p.id,
            p.conta_id,
            p.numero_parcela,
            p.valor_em_centavos,
            p.data_vencimento,
            p.data_pagamento,
            (
                SELECT centro_de_custo.nome
                FROM centro_de_custo
                WHERE centro_de_custo.id = c.centro_de_custo_id
            ) AS centro,
            (
                SELECT COUNT(*)
                FROM parcela p2
                WHERE p2.conta_id = p.conta_id
            ) AS total_parcelas
        FROM parcela p
        INNER JOIN conta AS c ON p.conta_id = c.id
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

        $sql = $sql . ' ORDER BY data_vencimento';

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao listar contas', ['status' => $status, 'PDOException' => $e->getMessage()]);
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

    public static function getDailyTotal(string $start, string $end): array
    {
        $sql = "SELECT 
                    data_vencimento as date, 
                    SUM(parcela.valor_em_centavos) as total 
                FROM parcela
                INNER JOIN conta ON conta_id = conta.id 
                WHERE data_vencimento BETWEEN :start AND :end
                AND conta.enabled = 1
                GROUP BY data_vencimento 
                ORDER BY data_vencimento";

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar totais diários', ['start' => $start, 'end' => $end, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }
}

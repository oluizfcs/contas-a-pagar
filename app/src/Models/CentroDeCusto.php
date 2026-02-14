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
        private bool $enabled,
        private int|null $categoriaId = null
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

    public function getCategoriaId()
    {
        return $this->categoriaId;
    }

    public function setCategoriaId($categoriaId)
    {
        $this->categoriaId = $categoriaId;

        return $this;
    }

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
                $stmt = $conn->prepare('INSERT INTO ' . self::$tableName . ' (nome, categoria_id) VALUES (:nome, :categoria_id)');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':categoria_id', $this->categoriaId, PDO::PARAM_INT);

                $stmt->execute();

                Logger::log_create($conn, self::$tableName);
                Logger::log(self::$tableName, 'sub-centros', '-', $this->nome, $this->categoriaId, $_SESSION['usuario_id']);

                return true;
            } else {
                // atualizar
                $centroAntigo = self::getById($this->id);

                $stmt = $conn->prepare('UPDATE ' . self::$tableName . ' SET nome = :nome, categoria_id = :categoria_id, enabled = :enabled WHERE id = :id');
                $stmt->bindParam(':nome', $this->nome, PDO::PARAM_STR);
                $stmt->bindParam(':categoria_id', $this->categoriaId, PDO::PARAM_INT);
                $stmt->bindParam(':enabled', $this->enabled, PDO::PARAM_BOOL);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                if ($centroAntigo->getNome() != $this->nome) {
                    Logger::log(self::$tableName, 'nome', $centroAntigo->getNome(), $this->nome, $this->id, $_SESSION['usuario_id']);
                }

                if ($centroAntigo->getCategoriaId() != $this->getCategoriaId()) {
                    Logger::log(self::$tableName, 'centro de custo superior', self::getById($centroAntigo->getCategoriaId())->getNome(), self::getById($this->categoriaId)->getNome(), $this->id, $_SESSION['usuario_id']);
                    Logger::log(self::$tableName, 'sub-centros', $this->nome . ': foi transferido', '-', $centroAntigo->getCategoriaId(), $_SESSION['usuario_id']);
                    Logger::log(self::$tableName, 'sub-centros', '-', $this->nome . ': foi transferido', $this->categoriaId, $_SESSION['usuario_id']);
                }

                if ($centroAntigo->getCategoriaId() != null && $centroAntigo->isEnabled() != $this->enabled) {
                    Logger::log(self::$tableName, 'sub-centro: ' . $this->nome, $centroAntigo->isEnabled() ? 'ativado' : 'inativado', $this->enabled ? 'ativado' : 'inativado', $centroAntigo->getCategoriaId(), $_SESSION['usuario_id']);
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
            centro_de_custo.categoria_id,
            nome,
            COALESCE(SUM(
                CASE 
                    WHEN (conta.enabled = 1 OR conta.enabled IS NULL) " . ($paid !== 2 ? "AND conta.paid = :paid" : "") . " 
                    THEN valor_em_centavos 
                    ELSE 0 
                END
            ), 0) as total,
            COUNT(
                CASE 
                    WHEN (conta.enabled = 1 OR conta.enabled IS NULL) " . ($paid !== 2 ? "AND conta.paid = :paid" : "") . " 
                    THEN conta.id 
                    ELSE NULL 
                END
            ) as quantidade
        FROM centro_de_custo
        LEFT JOIN conta ON centro_de_custo.id = centro_de_custo_id
        WHERE centro_de_custo.enabled = :enabled";

        if (strlen($search) > 0) {
            $sql = $sql . ' AND nome LIKE :search';
        }

        $sql = $sql . ' GROUP BY centro_de_custo.id';

        try {
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->bindValue(':enabled', $enabled ? 1 : 0);

            if ($paid !== 2) {
                $stmt->bindValue(':paid', $paid, PDO::PARAM_INT);
            }

            if (strlen($search) > 0) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }

            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $map = [];
            foreach ($rows as $i => $row) {
                $rows[$i]['children'] = [];
                $rows[$i]['media'] = $row['quantidade'] > 0 ? $row['total'] / $row['quantidade'] : 0;
                $map[$row['id']] = &$rows[$i];
            }

            $roots = [];
            foreach ($rows as $i => &$row) {
                if ($row['categoria_id'] && isset($map[$row['categoria_id']])) {
                    $map[$row['categoria_id']]['children'][] = &$rows[$i];
                } else {
                    $roots[] = &$rows[$i];
                }
            }

            $prune = function (array &$nodes) use (&$prune, $paid) {
                foreach ($nodes as $key => &$node) {
                    if (!empty($node['children'])) {
                        $prune($node['children']);
                    }

                    if ($paid !== 2) {
                        if ($node['quantidade'] == 0 && empty($node['children'])) {
                            unset($nodes[$key]);
                        }
                    }
                }
            };

            if (!$enabled) {
                return $rows;
            }
            if ($paid !== 2) {
                $prune($roots);
            }

            foreach ($roots as &$root) {
                foreach ($root['children'] as $child) {
                    $root['total'] += $child['total'];
                    $root['quantidade'] += $child['quantidade'];
                }
                $root['media'] = $root['quantidade'] > 0 ? $root['total'] / $root['quantidade'] : 0;
            }

            return $roots;
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
        return new CentroDeCusto($id, $nome, $data_criacao, $data_edicao, $enabled, $categoria_id);
    }

    public static function getTotalsByPeriod(string $start, string $end, string $status = 'todas', string $naturezaId = 'all'): array
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

        if ($naturezaId != 'all') {
            $sql .= " AND c.natureza_id = :natureza_id";
        }

        $sql .= " GROUP BY cc.id
                ORDER BY total DESC";

        try {
            $stmt = Database::getConnection()->prepare($sql);
            if ($naturezaId != 'all') {
                $stmt->bindParam(':natureza_id', $naturezaId, PDO::PARAM_INT);
            }
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Falha ao buscar totais por centro de custo', ['start' => $start, 'end' => $end, 'status' => $status, 'PDOException' => $e->getMessage()]);
            return [];
        }
    }
    public function getSubCentros(): array
    {
        $conn = Database::getConnection();
        $sql = "SELECT 
                    cc.id, 
                    cc.nome, 
                    cc.categoria_id, 
                    COALESCE(SUM(c.valor_em_centavos), 0) as total
                FROM centro_de_custo cc
                LEFT JOIN conta c ON cc.id = c.centro_de_custo_id AND c.paid = 0 AND c.enabled = 1
                WHERE cc.categoria_id = :id AND cc.enabled = 1
                GROUP BY cc.id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOptionsWithHierarchy(): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->query("SELECT id, nome, categoria_id FROM " . self::$tableName . " WHERE enabled = 1 ORDER BY nome");
        $allCenters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = [];
        $keyMap = [];

        foreach ($allCenters as $center) {
            $keyMap[$center['id']] = $center;
        }

        foreach ($allCenters as $center) {
            if (empty($center['categoria_id'])) {
                $options[] = [
                    'id' => $center['id'],
                    'nome' => $center['nome'],
                    'is_group' => true,
                    'children' => []
                ];
            }
        }

        foreach ($allCenters as $center) {
            if (!empty($center['categoria_id'])) {
                foreach ($options as &$opt) {
                    if ($opt['id'] == $center['categoria_id']) {
                        $opt['children'][] = [
                            'id' => $center['id'],
                            'nome' => $center['nome']
                        ];
                        break;
                    }
                }
            }
        }

        return $options;
    }
}

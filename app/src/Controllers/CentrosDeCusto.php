<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Models\Database;
use App\Models\CentroDeCusto;
use App\Models\Conta;

class CentrosDeCusto
{
    public static bool $needLogin = true;
    public static bool $onlyAdmin = false;
    private array $views = ['index', 'cadastrar', 'detalhar', 'atualizar'];
    private int $id;
    private string $search = '';
    private string $status = 'todos';

    function __construct(string $view = 'index', string $param = '')
    {
        if (strlen($param) > 0) {
            $id = filter_var($param, FILTER_VALIDATE_INT);
            if ($id) {
                $this->id = $id;
            } else {
                $_SESSION['message'] = ['Centro de custo não encontrado', 'fail'];
                header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo');
                exit;
            }
        }

        if (!empty($_POST)) {
            switch ($_POST['type']) {
                case 'create':
                    $this->create();
                    break;
                case 'update':
                    $this->update();
                    break;
                case 'unable':
                    $this->unable();
                    break;
                case 'enable':
                    $this->enable();
                    break;
                case 'search':
                    $_SESSION['centrosDeCusto_filters'] = [
                        'search' => $_POST['search'],
                        'status' => $_POST['status']
                    ];
                    header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo');
                    exit;
                    break;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $categoria_id = filter_var($_POST['categoria_id'], FILTER_VALIDATE_INT);
        if (!$categoria_id) $categoria_id = null;

        $cc = new CentroDeCusto(0, $_POST['nome'], '', null, 0, $categoria_id);

        if ($cc->save()) {
            if (isset($_POST['ajax']) && $_POST['ajax'] == 'true') {
                echo json_encode(['success' => true, 'id' => $cc->getId(), 'nome' => $cc->getNome()]);
                exit;
            }

            $_SESSION['message'] = ['Centro de custo cadastrado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo');
            exit;
        } else {
             if (isset($_POST['ajax']) && $_POST['ajax'] == 'true') {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
                exit;
            }
        }
    }

    private function update(): void
    {
        $cc = CentroDeCusto::getById($_POST['entity_id']);
        $cc->setNome($_POST['nome']);
        
        $categoria_id = filter_var($_POST['categoria_id'], FILTER_VALIDATE_INT);
        if (!$categoria_id) $categoria_id = null;
        $cc->setCategoriaId($categoria_id);

        if ($cc->save()) {
            $_SESSION['message'] = ['Centro de custo atualizado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo/detalhar/' . $_POST['entity_id']);
            exit;
        }
    }

    private function unable(): void
    {
        $cc = CentroDeCusto::getById($_POST['centro_de_custo_id']);
        if (!$cc->setEnabled(0)) {
            $_SESSION['message'] = ['Não foi possível inativar o centro de custo pois ele possui contas a pagar.', 'fail'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo/detalhar/' . $cc->getId());
            exit;
        }

        if ($cc->save()) {
            Logger::log_unable(CentroDeCusto::$tableName, $cc->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Centro de custo inativado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo/detalhar/' . $cc->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $cc = CentroDeCusto::getById($_POST['centro_de_custo_id']);
        $cc->setEnabled(1);

        if ($cc->save()) {
            Logger::log_enable(CentroDeCusto::$tableName, $cc->getId(), $_SESSION['usuario_id']);
            $_SESSION['message'] = ['Centro de custo ativado com sucesso!', 'success'];
            header('Location: ' . $_ENV['BASE_URL'] . '/centros-de-custo/detalhar/' . $cc->getId());
            exit;
        }
    }

    private function loadView(string $view): void
    {
        if (!in_array($view, $this->views)) {
            include 'src/404.php';
            exit;
        }

        if (isset($this->id)) {
            $centro_de_custo = CentroDeCusto::getById($this->id);

            if ($view == 'detalhar') {
                $contas = Conta::getByForeignKey(CentroDeCusto::$tableName, $this->id);
                $logs = Database::getLog(CentroDeCusto::$tableName, $this->id);
                $subCentros = $centro_de_custo->getSubCentros();
            }
        }

        if ($view == 'index') {

            $filters = $_SESSION['centrosDeCusto_filters'] ?? [
                'search' => '',
                'orderby' => 'total',
                'mostrar' => ''
            ];

            $this->search = $_SESSION['centrosDeCusto_filters']['search'] ?? $this->search;
            $this->status = $_SESSION['centrosDeCusto_filters']['status'] ?? $this->status;


            $enabled = $this->status != 'inativados';
            $paid = $this->status == 'contas pagas';

            if ($this->status == 'todos') {
                $paid = 2;
            }

            $centrosDeCusto = CentroDeCusto::getAll($enabled, $paid, $this->search);
        }

        include '../src/templates/header.php';
        if ($view == 'cadastrar' || $view == 'atualizar') {
            $categorias = CentroDeCusto::getOptionsWithHierarchy();
            // Filter out children from being parents if we want to enforce 1 level deep, 
            // but for now let's just show options. 
            // Actually getOptionsWithHierarchy returns tree structure. For the parent select, 
            // we probably only want top level items or items that can be parents.
            // For now let's just pass all and in the view we can filter or just show top levels.
            // A better approach for "Parent Category" select is to only show items that are NOT children themselves (if max depth is 1).
            // Let's assume max depth 1 as per "category > sub-center" implication.
            // So we should filter $categorias to only include those with empty categoria_id.
            // But getOptionsWithHierarchy already structures them.
            // Let's just pass them and handle in view or here.
            
            // Re-fetching just top level enabled for simplicity in the Parent Select
            // Using a raw query or a new method would be cleaner, but I can filter the result of getOptionsWithHierarchy if needed, 
            // or just use Database::getOptions but exclude the current ID (cyclic) and ensure no parent.
            
            // Let's use a simple query here for "potential parents" which are just top level centers.
            // And exclude self if updating.
             
            $conn = Database::getConnection();
            $sql = "SELECT id, nome FROM centro_de_custo WHERE enabled = 1 AND categoria_id IS NULL";
            if ($view == 'atualizar') {
                 $sql .= " AND id != " . $this->id;
            }
            $stmt = $conn->query($sql);
            $categorias = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            include "../src/Views/centros-de-custo/form.php";
        } else {
            include "../src/Views/centros-de-custo/$view.php";
        }
        include '../src/templates/footer.php';
    }
}

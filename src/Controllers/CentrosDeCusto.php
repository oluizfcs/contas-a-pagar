<?php

namespace App\Controllers;

use App\Controllers\Services\Logger;
use App\Models\Database;
use App\Models\CentroDeCusto;

class CentrosDeCusto
{
    public static bool $needLogin = true;
    private array $views = ['index', 'cadastrar', 'detalhar', 'atualizar'];
    private int $id;
    private string $orderby = 'total';
    private string $mostrar = '';
    private string $search = '';

    function __construct(string $view = 'index', string $param = '')
    {
        if (strlen($param) > 0) {
            $id = filter_var($param, FILTER_VALIDATE_INT);
            if ($id) {
                $this->id = $id;
            } else {
                $_SESSION['message'] = ['Centro de custo não encontrado', 'fail'];
                header("Location: /centros-de-custo");
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
                        'search' => $_POST['search'] ?? '',
                        'orderby' => $_POST['orderby'] ?? 'total',
                        'mostrar' => $_POST['mostrar'] ?? ''
                    ];
                    header("Location: /centros-de-custo");
                    exit;
                    break;
            }
        }

        $this->loadView($view);
    }

    private function create(): void
    {
        $cc = new CentroDeCusto(0, $_POST['nome'], '', null, 0);

        if ($cc->save()) {
            $_SESSION['message'] = ['Centro de custo cadastrado com sucesso!', 'success'];
            header("Location: /centros-de-custo");
            exit;
        }
    }

    private function update(): void
    {
        $cc = CentroDeCusto::getById($_POST['entity_id']);
        $cc->setNome($_POST['nome']);

        if ($cc->save()) {
            $_SESSION['message'] = ['Centro de custo atualizado com sucesso!', 'success'];
            header("Location: /centros-de-custo/detalhar/" . $_POST['entity_id']);
            exit;
        }
    }

    private function unable(): void
    {
        $cc = CentroDeCusto::getById($_POST['centro_de_custo_id']);
        $cc->setEnabled(0);

        Logger::log_unable(CentroDeCusto::$tableName, $cc->getId(), $_SESSION['usuario_id']);

        if ($cc->save()) {
            $_SESSION['message'] = ['Centro de custo inativado com sucesso!', 'success'];
            header("Location: /centros-de-custo/detalhar/" . $cc->getId());
            exit;
        }
    }

    private function enable(): void
    {
        $cc = CentroDeCusto::getById($_POST['centro_de_custo_id']);
        $cc->setEnabled(1);

        Logger::log_enable(CentroDeCusto::$tableName, $cc->getId(), $_SESSION['usuario_id']);

        if ($cc->save()) {
            $_SESSION['message'] = ['Centro de custo ativado com sucesso!', 'success'];
            header("Location: /centros-de-custo/detalhar/" . $cc->getId());
            exit;
        }
    }

    private function loadView(string $view): void
    {
        if (!in_array($view, $this->views)) {
            include '404.php';
            exit;
        }

        if (isset($this->id)) {
            $centro_de_custo = CentroDeCusto::getById($this->id);

            if ($view == 'detalhar') {
                $logs = Database::getLog(CentroDeCusto::$tableName, $this->id);
            }
        }

        if ($view == 'index') {

            $filters = $_SESSION['centrosDeCusto_filters'] ?? [
                'search' => '',
                'orderby' => 'total',
                'mostrar' => ''
            ];

            $this->search = $filters['search'];
            $this->orderby = $filters['orderby'];
            $this->mostrar = $filters['mostrar'];

            $enabled = $this->mostrar != 'inativados';
            $paid = $this->mostrar == 'todos';

            $centrosDeCusto = CentroDeCusto::getAll($enabled, $paid, $this->orderby, $this->search);
        }

        include 'templates/header.php';
        if ($view == 'cadastrar' || $view == 'atualizar') {
            include "src/Views/centros-de-custo/form.php";
        } else {
            include "src/Views/centros-de-custo/$view.php";
        }
        include 'templates/footer.php';
    }
}

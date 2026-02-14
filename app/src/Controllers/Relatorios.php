<?php

namespace App\Controllers;

use App\Models\CentroDeCusto;
use App\Models\Database;
use App\Models\Fornecedor;
use App\Models\Natureza;
use Mpdf\Mpdf;
use DateTime;

class Relatorios
{
    public static bool $needLogin = true;
    public static bool $onlyAdmin = true;

    function __construct(string $view = 'index', string $parm = '')
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = DateTime::createFromFormat('Y-m-d', $_POST['start_date']);

            if ($startDate == false) {
                $_SESSION['message'] = ['Data inicial inválida', 'fail'];
                header('Location: ' . $_ENV['BASE_URL'] . '/relatorios');
                exit;
            };

            $endDate = DateTime::createFromFormat('Y-m-d', $_POST['end_date']);

            if ($endDate == false) {
                $_SESSION['message'] = ['Data final inválida', 'fail'];
                header('Location: ' . $_ENV['BASE_URL'] . '/relatorios');
                exit;
            };

            $data = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'natureza' => null,
                'centro' => null,
                'fornecedor' => null,
                'descricaoCompleta' => false,
                'status' => $_POST['status']
            ];

            if (isset($_POST['descricaoCompleta'])) {
                $data['descricaoCompleta'] = true;
            }

            if ($_POST['natureza'] != 'all') {
                $natureza = Natureza::getById($_POST['natureza']);
                $data['natureza'] = $natureza->getNome();
            }

            if ($_POST['centro'] != 'all') {
                $centro = CentroDeCusto::getById($_POST['centro']);
                $data['centro'] = $centro->getNome();
            }

            if ($_POST['fornecedor'] != 'all') {
                $fornecedor = Fornecedor::getById($_POST['fornecedor']);
                $data['fornecedor'] = $fornecedor->getNome();
            }

            $rawData = Database::getReport(
                $_POST['start_date'],
                $_POST['end_date'],
                $_POST['natureza'],
                $_POST['centro'],
                $_POST['fornecedor'],
                $_POST['status']
            );

            if (count($rawData) <= 0) {
                $_SESSION['message'] = ['Não há registros que satisfaçam os filtros selecionados', 'fail'];
                header('Location: ' . $_ENV['BASE_URL'] . '/relatorios');
                exit;
            }

            $data['weeks'] = $this->groupDataByWeek($rawData, $_POST['start_date'], $_POST['end_date']);

            $data['total'] = 0;

            foreach($data['weeks'] as $week) {
                $data['total'] += $week['total'];
            }

            $this->renderPdf($data);
            exit;
        }

        $this->loadView($view);
    }

    private function groupDataByWeek($rawData, $startDate, $endDate): array
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        if ($start->format('w') != 6) {
            $start->modify('last saturday');
        }

        if ($end->format('w') != 5) {
            $end->modify('next friday');
        }

        $weeks = [];
        $current = clone $start;

        while ($current <= $end) {
            $weekEnd = clone $current;
            $weekEnd->modify('+6 days');

            $key = $current->format('Y-m-d');
            $weeks[$key] = [
                'start_date' => $current->format('d/m/Y'),
                'end_date' => $weekEnd->format('d/m/Y'),
                'rows' => [],
                'total' => 0
            ];
            $current->modify('+1 week');
        }

        foreach ($rawData as $row) {
            $date = new DateTime($row['data_vencimento']);
            $weekStart = clone $date;

            if ($weekStart->format('w') != 6) {
                $weekStart->modify('last saturday');
            }

            $key = $weekStart->format('Y-m-d');

            if (isset($weeks[$key])) {
                $weeks[$key]['rows'][] = $row;
                if (isset($row['valor_em_centavos'])) {
                    $weeks[$key]['total'] += $row['valor_em_centavos'];
                }
            }
        }

        return array_values($weeks);
    }

    private function renderPdf($data): void
    {
        ob_start();
        include('../src/Views/relatorios/relatorio.php');
        $html = ob_get_contents();
        ob_end_clean();

        $mpdf = new Mpdf();
        // $mpdf->SetFooter('pág.{PAGENO}');
        $mpdf->WriteHTML($html);
        $mpdf->OutputHttpInline();
    }

    private function loadView($view): void
    {
        if ($view == 'index') {
            $naturezas = Database::getOptions(Natureza::$tableName);
            $centros = Database::getOptions(CentroDeCusto::$tableName);
            $fornecedores = Database::getOptions(Fornecedor::$tableName);

            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        include '../src/templates/header.php';
        include "../src/Views/relatorios/$view.php";
        include '../src/templates/footer.php';
    }
}

<?php

namespace App\Controllers;


use App\Models\Parcela;
use App\Models\Fornecedor;
use App\Models\CentroDeCusto;
use App\Models\Database;
use App\Models\Natureza;

class Dashboard
{
    public static bool $needLogin = true;
    public static bool $onlyAdmin = false;

    function __construct()
    {
        $this->loadView();
    }

    private function loadView(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['dashboard_filters'] = [
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => $_POST['status'] ?? 'todas',
                'natureza' => $_POST['natureza'] ?? 'all',
                'show_zeros' => isset($_POST['show_zeros']),
            ];

            header('Location: ' . $_ENV['BASE_URL'] . '/dashboard');
            exit;
        }

        $filters = $_SESSION['dashboard_filters'] ?? [
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-t'),
            'status' => 'todas',
            'natureza' => 'all',
            'show_zeros' => false
        ];

        $startDate = $filters['start_date'] ?? date('Y-m-01');
        $endDate = $filters['end_date'] ?? date('Y-m-t');
        $status = $filters['status'] ?? 'todas';
        $naturezaId = $filters['natureza'] ?? 'all';
        $showZeros = $filters['show_zeros'] ?? false;

        if (trim($startDate) == "" || trim($endDate) == "") {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        $dailyTotals = Parcela::getDailyTotal($startDate, $endDate, $status, $naturezaId);

        if ($showZeros) {
            $dailyTotalsMap = [];
            foreach ($dailyTotals as $day) {
                $dailyTotalsMap[$day['date']] = $day['total'];
            }

            $currentDate = new \DateTime($startDate);
            $endDateTime = new \DateTime($endDate);
            $dailyTotals = [];

            while ($currentDate <= $endDateTime) {
                $dateStr = $currentDate->format('Y-m-d');
                $dailyTotals[] = [
                    'date' => $dateStr,
                    'total' => $dailyTotalsMap[$dateStr] ?? 0
                ];
                $currentDate->modify('+1 day');
            }
        }

        $topSuppliers = Fornecedor::getTopByPeriod($startDate, $endDate, 10, $status, $naturezaId);
        $costCenterTotals = CentroDeCusto::getTotalsByPeriod($startDate, $endDate, $status, $naturezaId);

        $chartData = [
            'daily' => $dailyTotals,
            'suppliers' => $topSuppliers,
            'costCenters' => $costCenterTotals
        ];

        $naturezas = Database::getOptions(Natureza::$tableName);

        include '../src/templates/header.php';
        include '../src/Views/dashboard.php';
        include '../src/templates/footer.php';
    }
}
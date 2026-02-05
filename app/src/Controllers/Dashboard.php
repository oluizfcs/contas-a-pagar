<?php

namespace App\Controllers;


use App\Models\Parcela;
use App\Models\Fornecedor;
use App\Models\CentroDeCusto;

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
                'show_zeros' => isset($_POST['show_zeros']),
            ];
        }

        $filters = $_SESSION['dashboard_filters'] ?? [];

        $startDate = $filters['start_date'] ?? date('Y-m-01');
        $endDate = $filters['end_date'] ?? date('Y-m-t');
        $status = $filters['status'] ?? 'todas';
        $showZeros = $filters['show_zeros'] ?? false;

        if ($startDate == "" || $endDate == "") {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        $dailyTotals = Parcela::getDailyTotal($startDate, $endDate, $status);

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

        $topSuppliers = Fornecedor::getTopByPeriod($startDate, $endDate, 10, $status);
        $costCenterTotals = CentroDeCusto::getTotalsByPeriod($startDate, $endDate, $status);

        $chartData = [
            'daily' => $dailyTotals,
            'suppliers' => $topSuppliers,
            'costCenters' => $costCenterTotals
        ];

        include 'src/templates/header.php';
        include 'src/Views/dashboard.php';
        include 'src/templates/footer.php';
    }
}
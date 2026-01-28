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
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        if ($startDate == "" || $endDate == "") {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        $showZeros = isset($_GET['show_zeros']);

        $dailyTotals = Parcela::getDailyTotal($startDate, $endDate);

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

        $topSuppliers = Fornecedor::getTopByPeriod($startDate, $endDate);
        $costCenterTotals = CentroDeCusto::getTotalsByPeriod($startDate, $endDate);

        $chartData = [
            'daily' => $dailyTotals,
            'suppliers' => $topSuppliers,
            'costCenters' => $costCenterTotals
        ];

        include 'templates/header.php';
        include 'src/Views/dashboard.php';
        include 'templates/footer.php';
    }
}
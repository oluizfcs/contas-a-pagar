<h1>Dashboard</h1>

<form method="POST" action="" class="mb-4" style="display: flex; gap: 1rem; align-items: flex-end;">
    <div class="section" style="width: 100%; display: flex; gap: 20px; flex-wrap: wrap;">
        <div>
            <label for="start_date">Data inicial: </label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
                class="form-control" style="margin-bottom: 0;">
        </div>
        <div>
            <label for="end_date">Data final: </label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
                class="form-control" style="margin-bottom: 0;">
        </div>
        <div>
            <label for="status">Status: </label>
            <select name="status" id="status" class="form-control" style="margin-bottom: 0;">
                <option value="todas" <?= $status === 'todas' ? 'selected' : '' ?>>Todas</option>
                <option value="pagas" <?= $status === 'pagas' ? 'selected' : '' ?>>Pagas</option>
                <option value="a_pagar" <?= $status === 'a_pagar' ? 'selected' : '' ?>>A pagar</option>
            </select>
        </div>
        <div>
            <label for="natureza">Natureza: </label>
            <select name="natureza" id="natureza" class="form-control" style="margin-bottom: 0;">
                <option value="all">Todas</option>
                <?php foreach ($naturezas as $natureza): ?>
                    <?php
                        $selected = $natureza['id'] == $naturezaId ? 'selected' : '';
                    ?>
                    <option <?= $selected ?> value="<?= $natureza['id'] ?>"><?= $natureza['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 0rem;">
            <input type="checkbox" id="show_zeros" name="show_zeros" <?= $showZeros ? 'checked' : '' ?>
                style="margin-right: 0.5rem; margin-bottom: 0;">
            <label for="show_zeros" style="margin-bottom: 0;">Mostrar dias sem contas</label>
        </div>
        <div style="margin-bottom: -0.5rem;">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </div>
</form>

<?php if (count($chartData['daily']) <= 0): ?>
    <br>
    <p>Nenhuma conta encontrada
        para o período selecionado.</p>
    <?php exit; ?>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="card full-width">
        <h3>R$ em contas a pagar por dia</h3>
        <div class="chart-container">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
    <div class="card">
        <h3>Fornecedores com maior custo</h3>
        <div class="chart-container">
            <canvas id="suppliersChart"></canvas>
        </div>
    </div>
    <div class="card">
        <h3>Despesas por centro de custo</h3>
        <div class="chart-container" style="position: relative; height: 300px;">
            <canvas id="costCenterChart"></canvas>
        </div>
    </div>
</div>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin: 1rem 0;
    }

    @media (min-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr 1fr;
        }

        .full-width {
            grid-column: 1 / -1;
        }
    }

    .card {
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .chart-container {
        position: relative;
        height: 60vh;
        min-height: 300px;
        max-height: 400px;
        width: 100%;
    }
</style>

<script src="js/chart.js"></script>
<script>
    const startDate = document.getElementById('start_date')
    const endDate = document.getElementById('end_date')
    const startDateFormated = startDate.value.split('-').reverse().join('/');
    const endDateFormated = endDate.value.split('-').reverse().join('/');

    const chartData = <?= json_encode($chartData) ?>;

    const dailyCtx = document.getElementById('dailyChart');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: chartData.daily.map(item => item.date.split('-').reverse().slice(0, 2).join('/')),
            datasets: [{
                label: 'Total (R$)',
                data: chartData.daily.map(item => item.total / 100),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    const suppliersCtx = document.getElementById('suppliersChart');
    new Chart(suppliersCtx, {
        type: 'bar',
        data: {
            labels: chartData.suppliers.map(item => item.nome),
            datasets: [{
                label: 'Custo Total (R$)',
                data: chartData.suppliers.map(item => item.total / 100),
                backgroundColor: '#28a745',
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toFixed(2);
                        }
                    },
                    title: {
                        display: true,
                        text: `${startDateFormated}  -  ${endDateFormated}`,
                        color: "#0000dd"
                    }
                }
            }
        }
    });

    const costCtx = document.getElementById('costCenterChart');
    new Chart(costCtx, {
        type: 'pie',
        data: {
            labels: chartData.costCenters.map(item => item.nome),
            datasets: [{
                data: chartData.costCenters.map(item => item.total / 100),
                backgroundColor: [
                    '#ffc107', '#17a2b8', '#dc3545', '#6610f2', '#fd7e14', '#20c997'
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                }).format(context.parsed);
                            }
                            return label;
                        }
                    }
                },
                title: {
                    display: true,
                    position: 'bottom',
                    text: `${startDateFormated}  -  ${endDateFormated}`,
                    color: "#0000dd",
                    font: {
                        weight: 'normal'
                    }
                }
            }
        }
    });

    document.forms[0].addEventListener('submit', function(event) {

        if (startDate.value == "") {
            event.preventDefault();
            startDate.focus();
            return;
        }

        if (endDate.value == "") {
            event.preventDefault();
            endDate.focus();
            return;
        }

        if (new Date(startDate.value) > new Date(endDate.value)) {
            let tmp = startDate.value;

            startDate.value = endDate.value;
            endDate.value = tmp;
        }
    });
</script>
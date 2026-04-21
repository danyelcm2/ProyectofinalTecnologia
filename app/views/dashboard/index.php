<?php

declare(strict_types=1);
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Dashboard KPI</h2>
    <p class="text-secondary mb-0">Seguimiento en tiempo real de ventas, categorias y rendimiento comercial.</p>
</section>

<section class="row g-3 mb-4" id="summaryCards">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Total ventas</span>
            <strong class="metric-value" data-metric="totalVentas">-</strong>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Monto total</span>
            <strong class="metric-value" data-metric="montoTotal">-</strong>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Ticket promedio</span>
            <strong class="metric-value" data-metric="ticketPromedio">-</strong>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Total clientes</span>
            <strong class="metric-value" data-metric="totalClientes">-</strong>
        </div>
    </div>
</section>

<section class="row g-3">
    <div class="col-12 col-xl-6">
        <div class="chart-card">
            <h3>Ventas por dia</h3>
            <div class="chart-frame">
                <canvas id="chartVentasDia"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="chart-card">
            <h3>Ventas por cliente</h3>
            <div class="chart-frame">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="chart-card">
            <h3>Top productos</h3>
            <div class="chart-frame">
                <canvas id="chartPostres"></canvas>
            </div>
        </div>
    </div>
</section>

<div class="small text-secondary mt-3" id="kpiMeta"></div>

<script>
window.APP_PAGE = 'dashboard';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

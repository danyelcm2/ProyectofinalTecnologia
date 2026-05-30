<?php

declare(strict_types=1);
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Dashboard</h2>
    <p class="text-secondary mb-0">Vista ejecutiva simplificada para controlar el flujo comercial en tiempo real.</p>
</section>

<section class="dashboard-hero mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-lg-8">
            <h3 class="dashboard-hero-title">Centro de monitoreo comercial</h3>
            <p class="dashboard-hero-copy">El panel fue reorganizado para priorizar lectura rapida: indicadores arriba, comportamiento de ventas abajo y distribucion de categorias en un bloque lateral.</p>
        </div>
        <div class="col-12 col-lg-4">
            <div class="dashboard-hero-actions">
                <a href="index.php?page=ventas" class="dashboard-link"><i class="fa-solid fa-plus"></i> Registrar venta</a>
                <a href="index.php?page=productos" class="dashboard-link"><i class="fa-solid fa-box-open"></i> Ver productos</a>
            </div>
        </div>
    </div>
</section>

<section class="kpi-strip mb-4" id="summaryCards">
    <article class="kpi-tile">
        <span class="kpi-tag">Ventas del dia</span>
        <strong class="kpi-value" data-metric="totalVentas">-</strong>
    </article>
    <article class="kpi-tile">
        <span class="kpi-tag">Monto total</span>
        <strong class="kpi-value" data-metric="montoTotal">-</strong>
    </article>
    <article class="kpi-tile">
        <span class="kpi-tag">Ticket promedio</span>
        <strong class="kpi-value" data-metric="ticketPromedio">-</strong>
    </article>
    <article class="kpi-tile">
        <span class="kpi-tag">Clientes activos</span>
        <strong class="kpi-value" data-metric="totalClientes">-</strong>
    </article>
</section>

<section class="dashboard-grid">
    <article class="chart-card chart-main">
        <h3>Top productos por movimiento</h3>
        <div class="chart-frame">
            <canvas id="chartPostres"></canvas>
        </div>
    </article>

    <article class="chart-card chart-side">
        <div>
            <h3>Distribucion por categoria</h3>
            <div class="chart-frame chart-frame-sm">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
        <div class="meta-box" id="kpiMeta">Sincronizando datos...</div>
    </article>

    <article class="chart-card chart-wide">
        <h3>Tendencia de ventas de los ultimos 7 dias</h3>
        <div class="chart-frame chart-frame-sm">
                <canvas id="chartVentasDia"></canvas>
            </div>
    </article>
</section>

<script>
window.APP_PAGE = 'dashboard';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

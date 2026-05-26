<?php

declare(strict_types=1);
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Dashboard</h2>
    <p class="text-secondary mb-0">Bienvenido Administrador. Vista premium para la gestion de tu reposteria.</p>
</section>

<section class="row g-3 mb-4" id="summaryCards">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card metric-icon-card">
            <span class="metric-icon">💲</span>
            <span class="metric-label">Ventas del dia</span>
            <strong class="metric-value" data-metric="totalVentas">-</strong>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card metric-icon-card">
            <span class="metric-icon">📦</span>
            <span class="metric-label">Pedidos del dia</span>
            <strong class="metric-value" data-metric="montoTotal">-</strong>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card metric-icon-card">
            <span class="metric-icon">🍰</span>
            <span class="metric-label">Postres registrados</span>
            <strong class="metric-value" data-metric="ticketPromedio">-</strong>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="metric-card metric-icon-card">
            <span class="metric-icon">👤</span>
            <span class="metric-label">Total clientes</span>
            <strong class="metric-value" data-metric="totalClientes">-</strong>
        </div>
    </div>
</section>

<section class="row g-3">
    <div class="col-12 col-xl-7">
        <div class="chart-card">
            <h3>Ventas de los ultimos 7 dias</h3>
            <div class="chart-frame">
                <canvas id="chartVentasDia"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="chart-card">
            <h3>Postres mas vendidos</h3>
            <div class="chart-frame chart-frame-sm">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="mb-0">Pedidos recientes</h3>
                <a href="#" class="soft-link">Ver todos</a>
            </div>
            <div class="chart-frame chart-frame-sm">
                <canvas id="chartPostres"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="chart-card reminder-card">
            <h3>Recordatorios</h3>
            <ul class="reminder-list mb-0">
                <li>💗 Revisar stock de ingredientes</li>
                <li>🍓 Confirmar pedidos pendientes</li>
                <li>✨ Actualizar promociones de temporada</li>
                <li>🧁 Reunion de equipo 5:00 PM</li>
            </ul>
        </div>
    </div>
</section>

<div class="small text-secondary mt-3" id="kpiMeta"></div>

<script>
window.APP_PAGE = 'dashboard';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

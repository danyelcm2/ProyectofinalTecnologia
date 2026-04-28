<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Consulta de tablas</h2>
    <p class="text-secondary mb-0">Selecciona una tabla de la base activa para revisar sus registros en pantalla.</p>
</section>

<div class="row g-4">
    <div class="col-12 col-xl-4">
        <div class="chart-card forms-card forms-card--picker">
            <h3 class="mb-3">Tablas disponibles</h3>
            <select id="tablesExplorerSelect" class="form-select">
                <option value="">Selecciona una tabla</option>
            </select>
            <div class="form-text mt-2">Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="chart-card forms-card forms-card--records">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">Datos de la tabla</h3>
                <span id="tablesExplorerMeta" class="form-text mt-0"></span>
            </div>
            <div id="tablesExplorerAlert" class="mb-3"></div>
            <div id="tablesExplorerEmpty" class="form-text">Selecciona una tabla para visualizar sus datos.</div>
            <div id="tablesExplorerWrapper" class="records-table-wrapper d-none">
                <table id="tablesExplorerTable" class="table table-sm align-middle mb-0">
                    <thead id="tablesExplorerHead"></thead>
                    <tbody id="tablesExplorerBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.APP_PAGE = 'tables';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

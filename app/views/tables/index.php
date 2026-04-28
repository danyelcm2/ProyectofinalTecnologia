<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Consulta de tablas</h2>
    <p class="text-secondary mb-0">Selecciona una tabla de la base activa para revisar sus registros y exportarlos.</p>
</section>

<section class="forms-toolbar mb-3">
    <div class="chart-card forms-toolbar-card py-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-6 col-xl-5">
                <label for="tablesExplorerSelect" class="form-label mb-1">Tabla</label>
                <select id="tablesExplorerSelect" class="form-select">
                    <option value="">Selecciona una tabla</option>
                </select>
            </div>
            <div class="col-12 col-lg-6 col-xl-7 text-lg-end">
                <span class="badge text-bg-light border px-3 py-2">Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
</section>

<section class="forms-layout">
    <div class="chart-card forms-card forms-card--records forms-generator-full">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="mb-0">Datos de la tabla</h3>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span id="tablesExplorerMeta" class="form-text mt-0"></span>
                <button type="button" id="tablesCsvBtn" class="btn btn-outline-secondary btn-sm" disabled>Exportar CSV</button>
                <button type="button" id="tablesExcelBtn" class="btn btn-outline-secondary btn-sm" disabled>Exportar Excel</button>
            </div>
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
</section>

<script>
window.APP_PAGE = 'tables';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

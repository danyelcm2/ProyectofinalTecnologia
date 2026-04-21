<?php

declare(strict_types=1);
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Formularios dinamicos</h2>
    <p class="text-secondary mb-0">Elige una tabla para generar automaticamente su formulario segun columnas.</p>
</section>

<div class="row g-4">
    <div class="col-12 col-xl-4">
        <div class="chart-card">
            <h3 class="mb-3">Tablas disponibles</h3>
            <select id="tableSelect" class="form-select">
                <option value="">Selecciona una tabla</option>
            </select>
            <div class="form-text mt-2">Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="form-text">Solo se listan tablas de la base operativa seleccionada. La tabla `usuarios` pertenece a la base de seguridad del login y no se mezcla aqui.</div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="chart-card">
            <h3 class="mb-3">Formulario generado</h3>
            <div id="formAlert" class="mb-3"></div>
            <form id="dynamicForm" class="row g-3">
                <input type="hidden" name="table" id="tableInput">
                <div id="dynamicFields" class="row g-3"></div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Insertar registro</button>
                </div>
            </form>
        </div>

        <div class="chart-card mt-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">Registros actuales</h3>
                <span id="recordsMeta" class="form-text mt-0"></span>
            </div>
            <div id="recordsEmpty" class="form-text">Selecciona una tabla para ver sus registros actuales.</div>
            <div id="recordsWrapper" class="records-table-wrapper d-none">
                <table class="table table-sm align-middle mb-0">
                    <thead id="recordsHead"></thead>
                    <tbody id="recordsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.APP_PAGE = 'forms';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

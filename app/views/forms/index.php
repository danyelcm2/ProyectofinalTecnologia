<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Formularios dinamicos</h2>
    <p class="text-secondary mb-0">Selecciona una tabla y completa el formulario generado para insertar nuevos registros.</p>
</section>

<section class="forms-toolbar mb-3">
    <div class="chart-card forms-toolbar-card py-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-6 col-xl-5">
                <label for="tableSelect" class="form-label mb-1">Tabla</label>
                <select id="tableSelect" class="form-select">
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
    <div class="chart-card forms-card forms-card--generator forms-generator-full">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="mb-0">Formulario generado</h3>
            <span class="form-text mt-0" id="formSelectedMeta"></span>
        </div>
        <div id="formAlert" class="mb-3"></div>
        <form id="dynamicForm" class="row g-3">
            <input type="hidden" name="table" id="tableInput">
            <div id="dynamicFields" class="row g-3"></div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary px-4">Insertar registro</button>
            </div>
        </form>
    </div>
</section>

<script>
window.APP_PAGE = 'forms';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

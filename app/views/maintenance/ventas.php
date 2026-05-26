<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Ventas</h2>
    <p class="text-muted mb-0">Registra ventas por cliente usando select relacionado.</p>
</section>

<div class="dessert-card mb-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="fa-solid fa-database"></i>
            <span>Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <button class="btn btn-dessert" type="button" data-action="create-venta">
            <i class="fa-solid fa-plus"></i> Nuevo
        </button>
    </div>
</div>

<div class="dessert-card table-card">
    <div class="table-responsive">
        <table id="ventasTable" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="ventaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dessert-modal">
            <form id="ventaForm" novalidate>
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="ventaModalTitle">Nueva venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="venta_id">
                    <div class="mb-3">
                        <label for="venta_cliente_id" class="form-label">Cliente</label>
                        <select id="venta_cliente_id" name="cliente_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="venta_fecha" class="form-label">Fecha</label>
                        <input type="date" id="venta_fecha" name="fecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="venta_total" class="form-label">Total</label>
                        <input type="number" id="venta_total" name="total" class="form-control" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dessert">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.APP_PAGE = 'ventas';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

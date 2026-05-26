<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Detalle de ventas</h2>
    <p class="text-muted mb-0">Relaciona venta y producto, con subtotal calculado automaticamente.</p>
</section>

<div class="dessert-card mb-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="fa-solid fa-database"></i>
            <span>Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <button class="btn btn-dessert" type="button" data-action="create-detalle-venta">
            <i class="fa-solid fa-plus"></i> Nuevo detalle
        </button>
    </div>
</div>

<div class="dessert-card table-card">
    <div class="table-responsive">
        <table id="detalleVentasTable" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Venta</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="detalleVentaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dessert-modal">
            <form id="detalleVentaForm" novalidate>
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="detalleVentaModalTitle">Nuevo detalle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="detalle_venta_id">
                    <div class="mb-3">
                        <label for="detalle_venta_id_ref" class="form-label">Venta</label>
                        <select id="detalle_venta_id_ref" name="venta_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="detalle_producto_id" class="form-label">Producto</label>
                        <select id="detalle_producto_id" name="producto_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="detalle_cantidad" class="form-label">Cantidad</label>
                        <input type="number" id="detalle_cantidad" name="cantidad" class="form-control" min="1" step="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="detalle_subtotal" class="form-label">Subtotal</label>
                        <input type="number" id="detalle_subtotal" class="form-control" readonly>
                        <div class="form-text">Se calcula automaticamente en base al producto y cantidad.</div>
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
window.APP_PAGE = 'detalle_ventas';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Clientes</h2>
    <p class="text-muted mb-0">Gestiona clientes con busqueda y paginacion.</p>
</section>

<div class="dessert-card mb-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="fa-solid fa-database"></i>
            <span>Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <button class="btn btn-dessert" type="button" data-action="create-cliente">
            <i class="fa-solid fa-plus"></i> Nuevo cliente
        </button>
    </div>
</div>

<div class="dessert-card table-card">
    <div class="table-responsive">
        <table id="clientesTable" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Creado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dessert-modal">
            <form id="clienteForm" novalidate>
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="clienteModalTitle">Nuevo cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="cliente_id">
                    <div class="mb-3">
                        <label for="cliente_nombre" class="form-label">Nombre</label>
                        <input type="text" id="cliente_nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="cliente_email" class="form-label">Email</label>
                        <input type="email" id="cliente_email" name="email" class="form-control" required>
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
window.APP_PAGE = 'clientes';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

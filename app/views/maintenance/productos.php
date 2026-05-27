<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Mantenimiento de Productos</h2>
    <p class="text-muted mb-0">Administra el catalogo de productos del sistema.</p>
</section>

<section class="row g-3">
    <div class="col-12 col-xl-9">
        <div class="dessert-card mb-3">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="fa-solid fa-database"></i>
                    <span>Conexion activa: <?= htmlspecialchars((string) $viewData['connection']['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <button class="btn btn-dessert" type="button" data-action="create-producto">
                    <i class="fa-solid fa-plus"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <div class="dessert-card table-card">
            <div class="table-responsive">
                <table id="productosTable" class="table table-hover align-middle w-100 postres-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoria</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-3">
        <div class="dessert-card side-form-card">
            <form id="productoForm" novalidate>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="modal-title mb-0" id="productoModalTitle">Nuevo Producto</h5>
                    <span class="sparkle-badge"><i class="fa-solid fa-circle"></i></span>
                </div>

                <input type="hidden" name="id" id="producto_id">

                <div class="mb-3">
                    <label class="form-label">Subir imagen</label>
                    <div class="upload-mock"><i class="fa-solid fa-camera"></i> PNG, JPG hasta 2MB</div>
                </div>

                <div class="mb-3">
                    <label for="producto_nombre" class="form-label">Nombre</label>
                    <input type="text" id="producto_nombre" name="nombre" class="form-control" required placeholder="Ej: Cheesecake de fresa">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="categoria_visual">Categoria</label>
                    <select id="categoria_visual" class="form-select">
                        <option value="">Selecciona una categoria</option>
                        <option>Tortas</option>
                        <option>Cupcakes</option>
                        <option>Cheesecakes</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="producto_precio" class="form-label">Precio (S/)</label>
                    <input type="number" id="producto_precio" name="precio" class="form-control" step="0.01" min="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="descripcion_visual">Descripcion</label>
                    <textarea id="descripcion_visual" class="form-control" rows="4" placeholder="Describe el producto..."></textarea>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-action="reset-producto-form">Cancelar</button>
                    <button type="submit" class="btn btn-dessert w-50">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
window.APP_PAGE = 'productos';
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

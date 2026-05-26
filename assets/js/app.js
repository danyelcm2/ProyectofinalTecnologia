(function () {
    'use strict';

    const charts = {};

    function toast(message, level) {
        const container = document.getElementById('appToastContainer');
        if (!container) {
            return;
        }

        const colorClass = {
            success: 'text-bg-success',
            danger: 'text-bg-danger',
            warning: 'text-bg-warning',
            info: 'text-bg-primary',
        }[level || 'info'];

        const wrapper = document.createElement('div');
        wrapper.className = 'toast align-items-center border-0 ' + colorClass;
        wrapper.role = 'alert';
        wrapper.ariaLive = 'assertive';
        wrapper.ariaAtomic = 'true';
        wrapper.innerHTML = ''
            + '<div class="d-flex">'
            + '<div class="toast-body">' + message + '</div>'
            + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
            + '</div>';

        container.appendChild(wrapper);
        const bsToast = new bootstrap.Toast(wrapper, { delay: 2600 });
        bsToast.show();
        wrapper.addEventListener('hidden.bs.toast', function () {
            wrapper.remove();
        });
    }

    function startLoader(message) {
        if (!window.Swal) {
            return;
        }

        Swal.fire({
            title: message || 'Procesando...',
            allowOutsideClick: false,
            didOpen: function () {
                Swal.showLoading();
            },
            background: '#fff5f8',
            color: '#7e2a7a',
        });
    }

    function stopLoader() {
        if (!window.Swal) {
            return;
        }

        Swal.close();
    }

    function money(value) {
        return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(value);
    }

    async function getJson(url, options) {
        const response = await fetch(url, options || {});

        try {
            return await response.json();
        } catch (error) {
            return {
                ok: false,
                message: 'Respuesta invalida del servidor'
            };
        }
    }

    function mountOrUpdateChart(id, type, labels, values, label) {
        const canvas = document.getElementById(id);
        if (!canvas) {
            return;
        }

        const config = {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    borderWidth: 2,
                    borderRadius: 8,
                    backgroundColor: [
                        'rgba(255, 105, 180, 0.42)',
                        'rgba(157, 78, 221, 0.42)',
                        'rgba(200, 162, 200, 0.45)',
                        'rgba(255, 182, 193, 0.55)',
                        'rgba(214, 122, 198, 0.45)',
                        'rgba(237, 162, 232, 0.4)',
                        'rgba(255, 145, 187, 0.5)',
                    ],
                    borderColor: 'rgba(128, 45, 120, 0.95)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: type !== 'bar' }
                }
            }
        };

        if (charts[id]) {
            charts[id].data.labels = labels;
            charts[id].data.datasets[0].data = values;
            charts[id].update();
            return;
        }

        charts[id] = new Chart(canvas, config);
    }

    async function loadDashboard() {
        const result = await getJson('index.php?page=api_kpi');
        if (!result.ok) {
            const meta = document.getElementById('kpiMeta');
            if (meta) {
                meta.textContent = result.message || 'No se pudieron cargar los KPIs con la base seleccionada.';
            }
            return;
        }

        const summary = result.summary;
        document.querySelector('[data-metric="totalVentas"]').textContent = String(Math.round(summary.totalVentas));
        document.querySelector('[data-metric="montoTotal"]').textContent = money(summary.montoTotal);
        document.querySelector('[data-metric="ticketPromedio"]').textContent = money(summary.ticketPromedio);
        document.querySelector('[data-metric="totalClientes"]').textContent = String(Math.round(summary.totalClientes));

        mountOrUpdateChart('chartVentasDia', 'line', result.charts.ventasPorDia.labels, result.charts.ventasPorDia.data, 'Ventas por dia');
        mountOrUpdateChart('chartCategorias', 'doughnut', result.charts.ventasPorCategoria.labels, result.charts.ventasPorCategoria.data, 'Ventas por cliente');
        mountOrUpdateChart('chartPostres', 'bar', result.charts.topPostres.labels, result.charts.topPostres.data, 'Top productos');

        const meta = document.getElementById('kpiMeta');
        if (meta) {
            meta.textContent = 'Conexion: ' + result.connection + ' | Ultima actualizacion: ' + result.updatedAt;
        }
    }

    function destroyDataTable(selector) {
        if (!window.jQuery || !$.fn || !$.fn.DataTable) {
            return;
        }

        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
        }
    }

    function resolveModal(modalEl) {
        if (!modalEl) {
            return { show: function () {}, hide: function () {} };
        }

        if (window.bootstrap && bootstrap.Modal) {
            return new bootstrap.Modal(modalEl);
        }

        return {
            show: function () {
                modalEl.style.display = 'block';
                modalEl.classList.add('show');
                modalEl.removeAttribute('aria-hidden');
            },
            hide: function () {
                modalEl.style.display = 'none';
                modalEl.classList.remove('show');
                modalEl.setAttribute('aria-hidden', 'true');
            }
        };
    }

    function extractRows(result) {
        if (!result || !result.ok) {
            return [];
        }

        if (Array.isArray(result.rows)) {
            return result.rows;
        }

        if (result.data && Array.isArray(result.data.rows)) {
            return result.data.rows;
        }

        return [];
    }

    function renderSkeleton(table, columns) {
        const tbody = table.querySelector('tbody');
        if (!tbody) {
            return;
        }

        const lines = [];
        for (let i = 0; i < 6; i += 1) {
            const cols = [];
            for (let c = 0; c < columns; c += 1) {
                cols.push('<td><span class="skeleton-line"></span></td>');
            }
            lines.push('<tr>' + cols.join('') + '</tr>');
        }

        tbody.innerHTML = lines.join('');
    }

    function buildActions(id, entity) {
        return ''
            + '<div class="d-flex justify-content-end gap-2">'
            + '<button type="button" class="btn btn-sm btn-outline-primary" data-action="edit-' + entity + '" data-id="' + id + '"><i class="fa-solid fa-pen"></i></button>'
            + '<button type="button" class="btn btn-sm btn-outline-danger" data-action="delete-' + entity + '" data-id="' + id + '"><i class="fa-solid fa-trash"></i></button>'
            + '</div>';
    }

    async function postForm(url, formData) {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
        });
        return response.json();
    }

    function wireDelete(buttonSelector, endpoint, refreshFn, label) {
        document.addEventListener('click', async function (event) {
            const button = event.target.closest(buttonSelector);
            if (!button) {
                return;
            }

            const id = button.getAttribute('data-id');
            if (window.Swal) {
                const result = await Swal.fire({
                    title: 'Confirmar eliminacion',
                    text: 'Se eliminara este registro de ' + label + '.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#9D4EDD',
                    cancelButtonColor: '#6c757d',
                    background: '#fff5f8',
                });

                if (!result.isConfirmed) {
                    return;
                }
            } else if (!window.confirm('Se eliminara este registro de ' + label + '.')) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            startLoader('Eliminando...');
            const payload = await postForm(endpoint, formData);
            stopLoader();

            if (!payload.ok) {
                toast(payload.message || 'No se pudo eliminar.', 'danger');
                return;
            }

            toast(payload.message || 'Registro eliminado.', 'success');
            refreshFn();
        });
    }

    function buildDataTable(selector) {
        if (!window.jQuery || !$.fn || !$.fn.DataTable) {
            return;
        }

        $(selector).DataTable({
            paging: true,
            searching: true,
            info: true,
            lengthMenu: [5, 10, 20, 50],
            order: [[0, 'desc']],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Sin registros disponibles',
                emptyTable: 'No hay datos para mostrar',
                paginate: {
                    first: 'Primera',
                    last: 'Ultima',
                    next: 'Siguiente',
                    previous: 'Anterior',
                },
            },
        });
    }

    function setModalTitle(id, text) {
        const title = document.getElementById(id);
        if (title) {
            title.textContent = text;
        }
    }

    function bindClientes() {
        const table = document.getElementById('clientesTable');
        if (!table) {
            return;
        }

        const modalEl = document.getElementById('clienteModal');
        const form = document.getElementById('clienteForm');
        const modal = resolveModal(modalEl);
        const rowsById = new Map();

        async function reload() {
            renderSkeleton(table, 5);
            startLoader('Cargando clientes...');
            const result = await getJson('index.php?page=api_clientes&perPage=500');
            stopLoader();
            if (!result.ok) {
                toast(result.message || 'Error cargando clientes.', 'danger');
                return;
            }

            rowsById.clear();
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';

            extractRows(result).forEach(function (row) {
                rowsById.set(String(row.id), row);
                tbody.insertAdjacentHTML('beforeend', ''
                    + '<tr>'
                    + '<td>' + row.id + '</td>'
                    + '<td>' + (row.nombre || '') + '</td>'
                    + '<td>' + (row.email || '') + '</td>'
                    + '<td>' + (row.created_at || '') + '</td>'
                    + '<td>' + buildActions(row.id, 'cliente') + '</td>'
                    + '</tr>');
            });

            destroyDataTable('#clientesTable');
            buildDataTable('#clientesTable');
        }

        const createBtn = document.querySelector('[data-action="create-cliente"]');
        if (createBtn) {
            createBtn.addEventListener('click', function () {
                form.reset();
                document.getElementById('cliente_id').value = '';
                setModalTitle('clienteModalTitle', 'Nuevo cliente');
                modal.show();
            });
        }

        document.addEventListener('click', function (event) {
            const edit = event.target.closest('[data-action="edit-cliente"]');
            if (!edit) {
                return;
            }

            const row = rowsById.get(edit.getAttribute('data-id'));
            if (!row) {
                return;
            }

            setModalTitle('clienteModalTitle', 'Editar cliente #' + row.id);
            document.getElementById('cliente_id').value = row.id;
            document.getElementById('cliente_nombre').value = row.nombre || '';
            document.getElementById('cliente_email').value = row.email || '';
            modal.show();
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const id = document.getElementById('cliente_id').value;
            const formData = new FormData(form);
            startLoader('Guardando cliente...');
            const result = await postForm('index.php?page=' + (id ? 'api_clientes_update' : 'api_clientes_create'), formData);
            stopLoader();

            if (!result.ok) {
                toast(result.message || 'No se pudo guardar.', 'danger');
                return;
            }

            modal.hide();
            toast(result.message || 'Cliente guardado.', 'success');
            reload();
        });

        wireDelete('[data-action="delete-cliente"]', 'index.php?page=api_clientes_delete', reload, 'clientes');
        reload();
    }

    function bindProductos() {
        const table = document.getElementById('productosTable');
        if (!table) {
            return;
        }

        const modalEl = document.getElementById('productoModal');
        const form = document.getElementById('productoForm');
        const modal = resolveModal(modalEl);
        const rowsById = new Map();

        async function reload() {
            renderSkeleton(table, 5);
            startLoader('Cargando productos...');
            const result = await getJson('index.php?page=api_productos&perPage=500');
            stopLoader();
            if (!result.ok) {
                toast(result.message || 'Error cargando productos.', 'danger');
                return;
            }

            rowsById.clear();
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';

            extractRows(result).forEach(function (row) {
                rowsById.set(String(row.id), row);
                tbody.insertAdjacentHTML('beforeend', ''
                    + '<tr>'
                    + '<td>' + row.id + '</td>'
                    + '<td>' + (row.nombre || '') + '</td>'
                    + '<td>' + money(Number(row.precio || 0)) + '</td>'
                    + '<td>' + (row.created_at || '') + '</td>'
                    + '<td>' + buildActions(row.id, 'producto') + '</td>'
                    + '</tr>');
            });

            destroyDataTable('#productosTable');
            buildDataTable('#productosTable');
        }

        const createBtn = document.querySelector('[data-action="create-producto"]');
        if (createBtn) {
            createBtn.addEventListener('click', function () {
                form.reset();
                document.getElementById('producto_id').value = '';
                setModalTitle('productoModalTitle', 'Nuevo producto');
                modal.show();
            });
        }

        document.addEventListener('click', function (event) {
            const edit = event.target.closest('[data-action="edit-producto"]');
            if (!edit) {
                return;
            }

            const row = rowsById.get(edit.getAttribute('data-id'));
            if (!row) {
                return;
            }

            setModalTitle('productoModalTitle', 'Editar producto #' + row.id);
            document.getElementById('producto_id').value = row.id;
            document.getElementById('producto_nombre').value = row.nombre || '';
            document.getElementById('producto_precio').value = row.precio || '';
            modal.show();
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('producto_id').value;
            const precio = Number(document.getElementById('producto_precio').value || 0);

            if (precio <= 0) {
                toast('El precio debe ser numerico y mayor a 0.', 'warning');
                return;
            }

            const formData = new FormData(form);
            startLoader('Guardando producto...');
            const result = await postForm('index.php?page=' + (id ? 'api_productos_update' : 'api_productos_create'), formData);
            stopLoader();

            if (!result.ok) {
                toast(result.message || 'No se pudo guardar.', 'danger');
                return;
            }

            modal.hide();
            toast(result.message || 'Producto guardado.', 'success');
            reload();
        });

        wireDelete('[data-action="delete-producto"]', 'index.php?page=api_productos_delete', reload, 'productos');
        reload();
    }

    function bindVentas() {
        const table = document.getElementById('ventasTable');
        if (!table) {
            return;
        }

        const modalEl = document.getElementById('ventaModal');
        const form = document.getElementById('ventaForm');
        const modal = resolveModal(modalEl);
        const clienteSelect = document.getElementById('venta_cliente_id');
        const rowsById = new Map();
        let clientes = [];

        function fillClientes(selectedId) {
            clienteSelect.innerHTML = '<option value="">Selecciona un cliente</option>';
            clientes.forEach(function (item) {
                const selected = String(item.id) === String(selectedId) ? ' selected' : '';
                clienteSelect.insertAdjacentHTML('beforeend', '<option value="' + item.id + '"' + selected + '>' + item.nombre + '</option>');
            });
        }

        async function loadClientesOptions(selectedId) {
            const result = await getJson('index.php?page=api_options_clientes');
            if (result.ok) {
                clientes = result.rows || [];
            }
            fillClientes(selectedId || '');
        }

        async function reload() {
            renderSkeleton(table, 5);
            startLoader('Cargando ventas...');
            const result = await getJson('index.php?page=api_ventas&perPage=500');
            stopLoader();
            if (!result.ok) {
                toast(result.message || 'Error cargando ventas.', 'danger');
                return;
            }

            rowsById.clear();
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';

            extractRows(result).forEach(function (row) {
                rowsById.set(String(row.id), row);
                tbody.insertAdjacentHTML('beforeend', ''
                    + '<tr>'
                    + '<td>' + row.id + '</td>'
                    + '<td>' + (row.cliente_nombre || '') + '</td>'
                    + '<td>' + (row.fecha || '') + '</td>'
                    + '<td>' + money(Number(row.total || 0)) + '</td>'
                    + '<td>' + buildActions(row.id, 'venta') + '</td>'
                    + '</tr>');
            });

            destroyDataTable('#ventasTable');
            buildDataTable('#ventasTable');
        }

        const createBtn = document.querySelector('[data-action="create-venta"]');
        if (createBtn) {
            createBtn.addEventListener('click', async function () {
                form.reset();
                document.getElementById('venta_id').value = '';
                document.getElementById('venta_fecha').valueAsDate = new Date();
                await loadClientesOptions('');
                setModalTitle('ventaModalTitle', 'Nueva venta');
                modal.show();
            });
        }

        document.addEventListener('click', async function (event) {
            const edit = event.target.closest('[data-action="edit-venta"]');
            if (!edit) {
                return;
            }

            const row = rowsById.get(edit.getAttribute('data-id'));
            if (!row) {
                return;
            }

            setModalTitle('ventaModalTitle', 'Editar venta #' + row.id);
            document.getElementById('venta_id').value = row.id;
            await loadClientesOptions(row.cliente_id);
            document.getElementById('venta_fecha').value = String(row.fecha || '').slice(0, 10);
            document.getElementById('venta_total').value = row.total || 0;
            modal.show();
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('venta_id').value;
            const formData = new FormData(form);
            startLoader('Guardando venta...');
            const result = await postForm('index.php?page=' + (id ? 'api_ventas_update' : 'api_ventas_create'), formData);
            stopLoader();

            if (!result.ok) {
                toast(result.message || 'No se pudo guardar.', 'danger');
                return;
            }

            modal.hide();
            toast(result.message || 'Venta guardada.', 'success');
            reload();
        });

        wireDelete('[data-action="delete-venta"]', 'index.php?page=api_ventas_delete', reload, 'ventas');
        reload();
    }

    function bindDetalleVentas() {
        const table = document.getElementById('detalleVentasTable');
        if (!table) {
            return;
        }

        const modalEl = document.getElementById('detalleVentaModal');
        const form = document.getElementById('detalleVentaForm');
        const modal = resolveModal(modalEl);
        const ventaSelect = document.getElementById('detalle_venta_id_ref');
        const productoSelect = document.getElementById('detalle_producto_id');
        const cantidadInput = document.getElementById('detalle_cantidad');
        const subtotalInput = document.getElementById('detalle_subtotal');
        const rowsById = new Map();
        let ventas = [];
        let productos = [];

        function fillVentas(selectedId) {
            ventaSelect.innerHTML = '<option value="">Selecciona una venta</option>';
            ventas.forEach(function (item) {
                const selected = String(item.id) === String(selectedId) ? ' selected' : '';
                const label = '#' + item.id + ' - ' + (item.cliente_nombre || '') + ' (' + String(item.fecha || '').slice(0, 10) + ')';
                ventaSelect.insertAdjacentHTML('beforeend', '<option value="' + item.id + '"' + selected + '>' + label + '</option>');
            });
        }

        function fillProductos(selectedId) {
            productoSelect.innerHTML = '<option value="">Selecciona un producto</option>';
            productos.forEach(function (item) {
                const selected = String(item.id) === String(selectedId) ? ' selected' : '';
                productoSelect.insertAdjacentHTML('beforeend', '<option value="' + item.id + '" data-precio="' + (item.precio || 0) + '"' + selected + '>' + item.nombre + '</option>');
            });
        }

        function updateSubtotal() {
            const selectedOption = productoSelect.options[productoSelect.selectedIndex];
            const precio = selectedOption ? Number(selectedOption.getAttribute('data-precio') || 0) : 0;
            const cantidad = Number(cantidadInput.value || 0);
            subtotalInput.value = (precio * cantidad).toFixed(2);
        }

        async function loadOptions(selectedVenta, selectedProducto) {
            const [ventasResult, productosResult] = await Promise.all([
                getJson('index.php?page=api_options_ventas'),
                getJson('index.php?page=api_options_productos'),
            ]);

            if (ventasResult.ok) {
                ventas = ventasResult.rows || [];
            }
            if (productosResult.ok) {
                productos = productosResult.rows || [];
            }

            fillVentas(selectedVenta || '');
            fillProductos(selectedProducto || '');
            updateSubtotal();
        }

        async function reload() {
            renderSkeleton(table, 6);
            startLoader('Cargando detalle de ventas...');
            const result = await getJson('index.php?page=api_detalle_ventas&perPage=500');
            stopLoader();
            if (!result.ok) {
                toast(result.message || 'Error cargando detalle de ventas.', 'danger');
                return;
            }

            rowsById.clear();
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';

            extractRows(result).forEach(function (row) {
                rowsById.set(String(row.id), row);
                tbody.insertAdjacentHTML('beforeend', ''
                    + '<tr>'
                    + '<td>' + row.id + '</td>'
                    + '<td>#' + row.venta_id + '</td>'
                    + '<td>' + (row.producto_nombre || '') + '</td>'
                    + '<td>' + row.cantidad + '</td>'
                    + '<td>' + money(Number(row.subtotal || 0)) + '</td>'
                    + '<td>' + buildActions(row.id, 'detalle-venta') + '</td>'
                    + '</tr>');
            });

            destroyDataTable('#detalleVentasTable');
            buildDataTable('#detalleVentasTable');
        }

        productoSelect.addEventListener('change', updateSubtotal);
        cantidadInput.addEventListener('input', updateSubtotal);

        const createBtn = document.querySelector('[data-action="create-detalle-venta"]');
        if (createBtn) {
            createBtn.addEventListener('click', async function () {
                form.reset();
                document.getElementById('detalle_venta_id').value = '';
                setModalTitle('detalleVentaModalTitle', 'Nuevo detalle');
                await loadOptions('', '');
                modal.show();
            });
        }

        document.addEventListener('click', async function (event) {
            const edit = event.target.closest('[data-action="edit-detalle-venta"]');
            if (!edit) {
                return;
            }

            const row = rowsById.get(edit.getAttribute('data-id'));
            if (!row) {
                return;
            }

            setModalTitle('detalleVentaModalTitle', 'Editar detalle #' + row.id);
            document.getElementById('detalle_venta_id').value = row.id;
            document.getElementById('detalle_cantidad').value = row.cantidad || 1;
            document.getElementById('detalle_subtotal').value = Number(row.subtotal || 0).toFixed(2);
            await loadOptions(row.venta_id, row.producto_id);
            updateSubtotal();
            modal.show();
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('detalle_venta_id').value;
            const formData = new FormData(form);
            startLoader('Guardando detalle...');
            const result = await postForm('index.php?page=' + (id ? 'api_detalle_ventas_update' : 'api_detalle_ventas_create'), formData);
            stopLoader();

            if (!result.ok) {
                toast(result.message || 'No se pudo guardar.', 'danger');
                return;
            }

            modal.hide();
            toast(result.message || 'Detalle guardado.', 'success');
            reload();
        });

        wireDelete('[data-action="delete-detalle-venta"]', 'index.php?page=api_detalle_ventas_delete', reload, 'detalle de ventas');
        reload();
    }

    function bindConnections() {
        const select = document.getElementById('connection');
        const group = document.getElementById('sqlitePathGroup');
        const input = document.getElementById('sqlite_path');
        if (!select || !group || !input) {
            return;
        }

        function syncVisibility() {
            const isSqlite = select.value === 'sqlite';
            group.style.display = isSqlite ? '' : 'none';
            input.required = isSqlite;
        }

        select.addEventListener('change', syncVisibility);
        syncVisibility();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.APP_PAGE === 'dashboard') {
            loadDashboard();
            setInterval(loadDashboard, 15000);
        }

        if (window.APP_PAGE === 'clientes') bindClientes();
        if (window.APP_PAGE === 'productos') bindProductos();
        if (window.APP_PAGE === 'ventas') bindVentas();
        if (window.APP_PAGE === 'detalle_ventas') bindDetalleVentas();
        if (window.APP_PAGE === 'connections') bindConnections();
    });
})();

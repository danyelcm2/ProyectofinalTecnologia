(function () {
    'use strict';

    const charts = {};

    function money(value) {
        return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(value);
    }

    async function getJson(url, options) {
        const response = await fetch(url, options || {});
        return response.json();
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
                        'rgba(41, 128, 185, 0.45)',
                        'rgba(39, 174, 96, 0.45)',
                        'rgba(243, 156, 18, 0.45)',
                        'rgba(192, 57, 43, 0.45)',
                        'rgba(142, 68, 173, 0.45)',
                        'rgba(44, 62, 80, 0.45)',
                        'rgba(22, 160, 133, 0.45)',
                    ],
                    borderColor: 'rgba(44, 62, 80, 0.9)'
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

    function inferInputType(columnType) {
        const type = columnType.toLowerCase();
        if (type.includes('int') || type.includes('decimal') || type.includes('float') || type.includes('double')) {
            return 'number';
        }
        if (type.includes('timestamp') || type.includes('datetime')) {
            return 'datetime-local';
        }
        if (type === 'date' || (type.includes('date') && !type.includes('time'))) {
            return 'date';
        }
        if (type.includes('time')) {
            return 'time';
        }
        return 'text';
    }

    function renderAlert(message, level) {
        const box = document.getElementById('formAlert');
        if (!box) {
            return;
        }
        box.innerHTML = '<div class="alert alert-' + level + ' py-2 mb-0">' + message + '</div>';
    }

    async function loadTables() {
        const select = document.getElementById('tableSelect');
        if (!select) {
            return;
        }

        const result = await getJson('index.php?page=api_tables');
        if (!result.ok) {
            renderAlert('No se pudieron cargar tablas', 'danger');
            return;
        }

        result.tables.forEach(function (table) {
            const option = document.createElement('option');
            option.value = table;
            option.textContent = table;
            select.appendChild(option);
        });
    }

    async function loadColumns(table) {
        const fieldBox = document.getElementById('dynamicFields');
        const tableInput = document.getElementById('tableInput');
        if (!fieldBox || !tableInput) {
            return;
        }

        fieldBox.innerHTML = '';
        tableInput.value = table;

        if (!table) {
            return;
        }

        const result = await getJson('index.php?page=api_columns&table=' + encodeURIComponent(table));
        if (!result.ok) {
            renderAlert('No se pudieron cargar columnas', 'danger');
            return;
        }

        result.columns.forEach(function (column) {
            if (String(column.extra).toLowerCase().includes('auto_increment')) {
                return;
            }

            const col = document.createElement('div');
            col.className = 'col-12 col-md-6';

            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = column.field;

            const input = document.createElement('input');
            input.className = 'form-control';
            input.name = column.field;
            input.type = inferInputType(column.type);
            input.required = !column.nullable && column.default === null;

            col.appendChild(label);
            col.appendChild(input);
            fieldBox.appendChild(col);
        });
    }

    async function bindDynamicForm() {
        const select = document.getElementById('tableSelect');
        const form = document.getElementById('dynamicForm');
        if (!select || !form) {
            return;
        }

        await loadTables();

        select.addEventListener('change', function () {
            loadColumns(this.value);
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(form);
            const response = await fetch('index.php?page=api_insert', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (!result.ok) {
                renderAlert(result.message || 'Error al insertar', 'danger');
                return;
            }

            renderAlert(result.message, 'success');
            form.reset();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.APP_PAGE === 'dashboard') {
            loadDashboard();
            setInterval(loadDashboard, 15000);
        }

        if (window.APP_PAGE === 'forms') {
            bindDynamicForm();
        }
    });
})();

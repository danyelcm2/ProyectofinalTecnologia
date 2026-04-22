(function () {
    'use strict';

    const charts = {};
    const recordsState = {
        table: '',
        columns: [],
        rows: []
    };

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

    function shouldUseSelect(column) {
        if (!column || !column.field) {
            return false;
        }

        const isIdField = String(column.field).toLowerCase().endsWith('_id');
        const hasOptions = Array.isArray(column.options) && column.options.length > 0;

        return isIdField && hasOptions;
    }

    function buildSelectInput(column) {
        const select = document.createElement('select');
        select.className = 'form-select';
        select.name = column.field;
        select.required = !column.nullable && column.default === null;

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Selecciona una opcion';
        placeholder.selected = true;
        placeholder.disabled = !!select.required;
        select.appendChild(placeholder);

        column.options.forEach(function (optionData) {
            const option = document.createElement('option');
            option.value = String(optionData.value || '');
            option.textContent = String(optionData.label || optionData.value || '');
            select.appendChild(option);
        });

        return select;
    }

    function renderAlert(message, level) {
        const box = document.getElementById('formAlert');
        if (!box) {
            return;
        }
        box.innerHTML = '<div class="alert alert-' + level + ' py-2 mb-0">' + message + '</div>';
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderRecordsTable(payload, table) {
        const wrapper = document.getElementById('recordsWrapper');
        const empty = document.getElementById('recordsEmpty');
        const head = document.getElementById('recordsHead');
        const body = document.getElementById('recordsBody');
        const meta = document.getElementById('recordsMeta');
        const exportBtn = document.getElementById('exportRecordsBtn');
        const printBtn = document.getElementById('printRecordsBtn');

        if (!wrapper || !empty || !head || !body || !meta || !exportBtn || !printBtn) {
            return;
        }

        const columns = Array.isArray(payload.columns) ? payload.columns : [];
        const rows = Array.isArray(payload.rows) ? payload.rows : [];
        recordsState.table = table;
        recordsState.columns = columns;
        recordsState.rows = rows;

        exportBtn.disabled = columns.length === 0;
        printBtn.disabled = columns.length === 0;

        if (!table) {
            wrapper.classList.add('d-none');
            head.innerHTML = '';
            body.innerHTML = '';
            meta.textContent = '';
            empty.textContent = 'Selecciona una tabla para ver sus registros actuales.';
            return;
        }

        if (columns.length === 0) {
            wrapper.classList.add('d-none');
            head.innerHTML = '';
            body.innerHTML = '';
            meta.textContent = '';
            empty.textContent = 'La tabla seleccionada no tiene columnas disponibles para mostrar.';
            return;
        }

        head.innerHTML = '<tr>' + columns.map(function (column) {
            return '<th scope="col">' + escapeHtml(column) + '</th>';
        }).join('') + '</tr>';

        wrapper.classList.remove('d-none');

        if (rows.length === 0) {
            body.innerHTML = '<tr><td colspan="' + columns.length + '" class="text-center text-secondary py-3">No hay registros en esta tabla.</td></tr>';
            meta.textContent = 'Tabla: ' + table;
            empty.textContent = '';
            return;
        }

        body.innerHTML = rows.map(function (row) {
            return '<tr>' + columns.map(function (column) {
                const value = row[column];
                return '<td>' + escapeHtml(value === null ? '' : value) + '</td>';
            }).join('') + '</tr>';
        }).join('');

        meta.textContent = 'Tabla: ' + table + ' | Mostrando ' + rows.length + ' registros';
        empty.textContent = '';
    }

    function exportRecordsAsCsv() {
        if (recordsState.columns.length === 0) {
            return;
        }

        const quote = function (value) {
            const plain = value === null || value === undefined ? '' : String(value);
            return '"' + plain.replace(/"/g, '""') + '"';
        };

        const lines = [];
        lines.push(recordsState.columns.map(quote).join(','));

        recordsState.rows.forEach(function (row) {
            const line = recordsState.columns.map(function (column) {
                return quote(row[column]);
            }).join(',');
            lines.push(line);
        });

        const csvContent = '\uFEFF' + lines.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const filename = (recordsState.table || 'registros') + '_export.csv';

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function printRecordsTable() {
        const table = document.getElementById('recordsTable');
        if (!table || recordsState.columns.length === 0) {
            return;
        }

        const popup = window.open('', '_blank', 'width=1100,height=700');
        if (!popup) {
            return;
        }

        const title = 'Registros - ' + (recordsState.table || 'tabla');
        popup.document.write('<!doctype html><html><head><meta charset="utf-8"><title>' + escapeHtml(title) + '</title>');
        popup.document.write('<style>body{font-family:Segoe UI,Tahoma,sans-serif;padding:24px;color:#1f2d3d}h1{font-size:18px;margin:0 0 12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #d8e0ea;padding:8px;font-size:12px;text-align:left;vertical-align:top}th{background:#f2f6fb}</style>');
        popup.document.write('</head><body>');
        popup.document.write('<h1>' + escapeHtml(title) + '</h1>');
        popup.document.write(table.outerHTML);
        popup.document.write('</body></html>');
        popup.document.close();
        popup.focus();
        popup.print();
    }

    async function loadRecords(table) {
        if (!table) {
            renderRecordsTable({ columns: [], rows: [] }, '');
            return;
        }

        const result = await getJson('index.php?page=api_records&table=' + encodeURIComponent(table) + '&limit=500');
        if (!result.ok) {
            renderRecordsTable({ columns: [], rows: [] }, table);
            renderAlert(result.message || 'No se pudieron cargar los registros', 'danger');
            return;
        }

        renderRecordsTable(result.data || { columns: [], rows: [] }, table);
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
            renderRecordsTable({ columns: [], rows: [] }, '');
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

            let input;
            if (shouldUseSelect(column)) {
                input = buildSelectInput(column);
            } else {
                input = document.createElement('input');
                input.className = 'form-control';
                input.name = column.field;
                input.type = inferInputType(column.type);
                input.required = !column.nullable && column.default === null;
            }

            col.appendChild(label);
            col.appendChild(input);
            fieldBox.appendChild(col);
        });
    }

    async function bindDynamicForm() {
        const select = document.getElementById('tableSelect');
        const form = document.getElementById('dynamicForm');
        const exportBtn = document.getElementById('exportRecordsBtn');
        const printBtn = document.getElementById('printRecordsBtn');
        if (!select || !form || !exportBtn || !printBtn) {
            return;
        }

        await loadTables();

        exportBtn.addEventListener('click', exportRecordsAsCsv);
        printBtn.addEventListener('click', printRecordsTable);

        select.addEventListener('change', function () {
            loadColumns(this.value);
            loadRecords(this.value);
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
            document.getElementById('tableInput').value = select.value;
            loadRecords(select.value);
        });

        if (select.value) {
            loadColumns(select.value);
            loadRecords(select.value);
        }
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

<?php

declare(strict_types=1);

class DessertRepository
{
    private PDO $pdo;
    private string $driver;

    public function __construct()
    {
        $this->pdo = db_connect();
        $this->driver = (string) $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function clientes(string $search, int $page, int $perPage): array
    {
        return $this->listSimple('clientes', ['id', 'nombre', 'email', 'created_at'], ['nombre', 'email'], $search, $page, $perPage);
    }

    public function clienteById(int $id): ?array
    {
        return $this->findById('clientes', $id, ['id', 'nombre', 'email', 'created_at']);
    }

    public function createCliente(string $nombre, string $email): void
    {
        $sql = 'INSERT INTO ' . $this->quoteIdentifier('clientes') . ' ('
            . $this->quoteIdentifier('nombre') . ', '
            . $this->quoteIdentifier('email') . ', '
            . $this->quoteIdentifier('created_at')
            . ') VALUES (:nombre, :email, :created_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function updateCliente(int $id, string $nombre, string $email): void
    {
        $sql = 'UPDATE ' . $this->quoteIdentifier('clientes')
            . ' SET ' . $this->quoteIdentifier('nombre') . ' = :nombre, '
            . $this->quoteIdentifier('email') . ' = :email'
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
    }

    public function deleteCliente(int $id): void
    {
        $this->deleteById('clientes', $id);
    }

    public function productos(string $search, int $page, int $perPage): array
    {
        return $this->listSimple('productos', ['id', 'nombre', 'precio', 'created_at'], ['nombre'], $search, $page, $perPage);
    }

    public function productoById(int $id): ?array
    {
        return $this->findById('productos', $id, ['id', 'nombre', 'precio', 'created_at']);
    }

    public function createProducto(string $nombre, float $precio): void
    {
        $sql = 'INSERT INTO ' . $this->quoteIdentifier('productos') . ' ('
            . $this->quoteIdentifier('nombre') . ', '
            . $this->quoteIdentifier('precio') . ', '
            . $this->quoteIdentifier('created_at')
            . ') VALUES (:nombre, :precio, :created_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':precio', $precio);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function updateProducto(int $id, string $nombre, float $precio): void
    {
        $sql = 'UPDATE ' . $this->quoteIdentifier('productos')
            . ' SET ' . $this->quoteIdentifier('nombre') . ' = :nombre, '
            . $this->quoteIdentifier('precio') . ' = :precio'
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':precio', $precio);
        $stmt->execute();
    }

    public function deleteProducto(int $id): void
    {
        $this->deleteById('productos', $id);
    }

    public function ventas(string $search, int $page, int $perPage): array
    {
        $safePage = max(1, $page);
        $safePerPage = max(1, min($perPage, 200));
        $offset = ($safePage - 1) * $safePerPage;

        $from = ' FROM ' . $this->quoteIdentifier('ventas') . ' v '
            . 'JOIN ' . $this->quoteIdentifier('clientes') . ' c ON c.' . $this->quoteIdentifier('id') . ' = v.' . $this->quoteIdentifier('cliente_id');

        $searchTerm = trim($search);
        $whereSql = '';
        $params = [];

        if ($searchTerm !== '') {
            $whereSql = ' WHERE LOWER(c.' . $this->quoteIdentifier('nombre') . ') LIKE :search';
            $params[':search'] = '%' . strtolower($searchTerm) . '%';
        }

        $countSql = 'SELECT COUNT(*)' . $from . $whereSql;
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $param => $value) {
            $countStmt->bindValue($param, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $selectSql = 'SELECT v.' . $this->quoteIdentifier('id') . ' AS id, '
            . 'v.' . $this->quoteIdentifier('cliente_id') . ' AS cliente_id, '
            . 'c.' . $this->quoteIdentifier('nombre') . ' AS cliente_nombre, '
            . 'v.' . $this->quoteIdentifier('fecha') . ' AS fecha, '
            . 'v.' . $this->quoteIdentifier('total') . ' AS total'
            . $from
            . $whereSql;

        $orderBy = ' ORDER BY v.' . $this->quoteIdentifier('id') . ' DESC';
        $pagedSql = $this->applyPagination($selectSql, $orderBy, $safePerPage, $offset);

        $stmt = $this->pdo->prepare($pagedSql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        if ($this->driver === 'sqlsrv') {
            $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':_limit', $safePerPage, PDO::PARAM_INT);
        }
        $stmt->execute();

        return [
            'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'page' => $safePage,
                'perPage' => $safePerPage,
                'total' => $total,
                'pages' => (int) ceil(max(1, $total) / $safePerPage),
            ],
        ];
    }

    public function ventaById(int $id): ?array
    {
        $sql = 'SELECT ' . $this->quoteIdentifier('id') . ', '
            . $this->quoteIdentifier('cliente_id') . ', '
            . $this->quoteIdentifier('fecha') . ', '
            . $this->quoteIdentifier('total')
            . ' FROM ' . $this->quoteIdentifier('ventas')
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function createVenta(int $clienteId, string $fecha, float $total): void
    {
        $sql = 'INSERT INTO ' . $this->quoteIdentifier('ventas') . ' ('
            . $this->quoteIdentifier('cliente_id') . ', '
            . $this->quoteIdentifier('fecha') . ', '
            . $this->quoteIdentifier('total')
            . ') VALUES (:cliente_id, :fecha, :total)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':total', $total);
        $stmt->execute();
    }

    public function updateVenta(int $id, int $clienteId, string $fecha, float $total): void
    {
        $sql = 'UPDATE ' . $this->quoteIdentifier('ventas')
            . ' SET ' . $this->quoteIdentifier('cliente_id') . ' = :cliente_id, '
            . $this->quoteIdentifier('fecha') . ' = :fecha, '
            . $this->quoteIdentifier('total') . ' = :total'
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':total', $total);
        $stmt->execute();
    }

    public function deleteVenta(int $id): void
    {
        $this->deleteById('ventas', $id);
    }

    public function detalleVentas(string $search, int $page, int $perPage): array
    {
        $safePage = max(1, $page);
        $safePerPage = max(1, min($perPage, 200));
        $offset = ($safePage - 1) * $safePerPage;

        $from = ' FROM ' . $this->quoteIdentifier('detalle_ventas') . ' d '
            . 'JOIN ' . $this->quoteIdentifier('ventas') . ' v ON v.' . $this->quoteIdentifier('id') . ' = d.' . $this->quoteIdentifier('venta_id')
            . ' JOIN ' . $this->quoteIdentifier('productos') . ' p ON p.' . $this->quoteIdentifier('id') . ' = d.' . $this->quoteIdentifier('producto_id');

        $searchTerm = trim($search);
        $whereSql = '';
        $params = [];

        if ($searchTerm !== '') {
            $whereSql = ' WHERE LOWER(p.' . $this->quoteIdentifier('nombre') . ') LIKE :search';
            $params[':search'] = '%' . strtolower($searchTerm) . '%';
        }

        $countSql = 'SELECT COUNT(*)' . $from . $whereSql;
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $param => $value) {
            $countStmt->bindValue($param, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $selectSql = 'SELECT d.' . $this->quoteIdentifier('id') . ' AS id, '
            . 'd.' . $this->quoteIdentifier('venta_id') . ' AS venta_id, '
            . 'd.' . $this->quoteIdentifier('producto_id') . ' AS producto_id, '
            . 'd.' . $this->quoteIdentifier('cantidad') . ' AS cantidad, '
            . 'd.' . $this->quoteIdentifier('subtotal') . ' AS subtotal, '
            . 'p.' . $this->quoteIdentifier('nombre') . ' AS producto_nombre'
            . $from
            . $whereSql;

        $orderBy = ' ORDER BY d.' . $this->quoteIdentifier('id') . ' DESC';
        $pagedSql = $this->applyPagination($selectSql, $orderBy, $safePerPage, $offset);

        $stmt = $this->pdo->prepare($pagedSql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        if ($this->driver === 'sqlsrv') {
            $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':_limit', $safePerPage, PDO::PARAM_INT);
        }
        $stmt->execute();

        return [
            'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'page' => $safePage,
                'perPage' => $safePerPage,
                'total' => $total,
                'pages' => (int) ceil(max(1, $total) / $safePerPage),
            ],
        ];
    }

    public function detalleVentaById(int $id): ?array
    {
        $sql = 'SELECT ' . $this->quoteIdentifier('id') . ', '
            . $this->quoteIdentifier('venta_id') . ', '
            . $this->quoteIdentifier('producto_id') . ', '
            . $this->quoteIdentifier('cantidad') . ', '
            . $this->quoteIdentifier('subtotal')
            . ' FROM ' . $this->quoteIdentifier('detalle_ventas')
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function createDetalleVenta(int $ventaId, int $productoId, int $cantidad): float
    {
        $subtotal = $this->calcularSubtotal($productoId, $cantidad);
        $sql = 'INSERT INTO ' . $this->quoteIdentifier('detalle_ventas') . ' ('
            . $this->quoteIdentifier('venta_id') . ', '
            . $this->quoteIdentifier('producto_id') . ', '
            . $this->quoteIdentifier('cantidad') . ', '
            . $this->quoteIdentifier('subtotal')
            . ') VALUES (:venta_id, :producto_id, :cantidad, :subtotal)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':venta_id', $ventaId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':subtotal', $subtotal);
        $stmt->execute();

        return $subtotal;
    }

    public function updateDetalleVenta(int $id, int $ventaId, int $productoId, int $cantidad): float
    {
        $subtotal = $this->calcularSubtotal($productoId, $cantidad);
        $sql = 'UPDATE ' . $this->quoteIdentifier('detalle_ventas')
            . ' SET ' . $this->quoteIdentifier('venta_id') . ' = :venta_id, '
            . $this->quoteIdentifier('producto_id') . ' = :producto_id, '
            . $this->quoteIdentifier('cantidad') . ' = :cantidad, '
            . $this->quoteIdentifier('subtotal') . ' = :subtotal'
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':venta_id', $ventaId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':subtotal', $subtotal);
        $stmt->execute();

        return $subtotal;
    }

    public function deleteDetalleVenta(int $id): void
    {
        $this->deleteById('detalle_ventas', $id);
    }

    public function clientesOptions(): array
    {
        $sql = 'SELECT ' . $this->quoteIdentifier('id') . ' AS id, '
            . $this->quoteIdentifier('nombre') . ' AS nombre'
            . ' FROM ' . $this->quoteIdentifier('clientes')
            . ' ORDER BY ' . $this->quoteIdentifier('nombre') . ' ASC';

        return $this->limitedQuery($sql, 500);
    }

    public function productosOptions(): array
    {
        $sql = 'SELECT ' . $this->quoteIdentifier('id') . ' AS id, '
            . $this->quoteIdentifier('nombre') . ' AS nombre, '
            . $this->quoteIdentifier('precio') . ' AS precio'
            . ' FROM ' . $this->quoteIdentifier('productos')
            . ' ORDER BY ' . $this->quoteIdentifier('nombre') . ' ASC';

        return $this->limitedQuery($sql, 500);
    }

    public function ventasOptions(): array
    {
        $sql = 'SELECT v.' . $this->quoteIdentifier('id') . ' AS id, '
            . 'v.' . $this->quoteIdentifier('fecha') . ' AS fecha, '
            . 'c.' . $this->quoteIdentifier('nombre') . ' AS cliente_nombre'
            . ' FROM ' . $this->quoteIdentifier('ventas') . ' v '
            . 'JOIN ' . $this->quoteIdentifier('clientes') . ' c ON c.' . $this->quoteIdentifier('id') . ' = v.' . $this->quoteIdentifier('cliente_id')
            . ' ORDER BY v.' . $this->quoteIdentifier('id') . ' DESC';

        return $this->limitedQuery($sql, 500);
    }

    public function productoPrecio(int $productoId): float
    {
        $sql = 'SELECT ' . $this->quoteIdentifier('precio')
            . ' FROM ' . $this->quoteIdentifier('productos')
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $productoId, PDO::PARAM_INT);
        $stmt->execute();

        $precio = $stmt->fetchColumn();
        if ($precio === false) {
            throw new InvalidArgumentException('Producto no encontrado.');
        }

        return (float) $precio;
    }

    private function listSimple(string $table, array $columns, array $searchColumns, string $search, int $page, int $perPage): array
    {
        $safePage = max(1, $page);
        $safePerPage = max(1, min($perPage, 200));
        $offset = ($safePage - 1) * $safePerPage;

        $tableQ = $this->quoteIdentifier($table);
        $from = ' FROM ' . $tableQ;
        $searchTerm = trim($search);

        $whereSql = '';
        $params = [];

        if ($searchTerm !== '' && $searchColumns !== []) {
            $conditions = [];
            foreach ($searchColumns as $index => $column) {
                $param = ':search_' . $index;
                $conditions[] = 'LOWER(' . $this->quoteIdentifier($column) . ') LIKE ' . $param;
                $params[$param] = '%' . strtolower($searchTerm) . '%';
            }
            $whereSql = ' WHERE ' . implode(' OR ', $conditions);
        }

        $countSql = 'SELECT COUNT(*)' . $from . $whereSql;
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $param => $value) {
            $countStmt->bindValue($param, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $columnSql = implode(', ', array_map(fn(string $c): string => $this->quoteIdentifier($c), $columns));
        $selectSql = 'SELECT ' . $columnSql . $from . $whereSql;
        $orderBy = ' ORDER BY ' . $this->quoteIdentifier('id') . ' DESC';
        $pagedSql = $this->applyPagination($selectSql, $orderBy, $safePerPage, $offset);

        $stmt = $this->pdo->prepare($pagedSql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if ($this->driver === 'sqlsrv') {
            $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':_limit', $safePerPage, PDO::PARAM_INT);
        }

        $stmt->execute();
        return [
            'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'page' => $safePage,
                'perPage' => $safePerPage,
                'total' => $total,
                'pages' => (int) ceil(max(1, $total) / $safePerPage),
            ],
        ];
    }

    private function findById(string $table, int $id, array $columns): ?array
    {
        $columnSql = implode(', ', array_map(fn(string $c): string => $this->quoteIdentifier($c), $columns));
        $sql = 'SELECT ' . $columnSql
            . ' FROM ' . $this->quoteIdentifier($table)
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    private function deleteById(string $table, int $id): void
    {
        $sql = 'DELETE FROM ' . $this->quoteIdentifier($table)
            . ' WHERE ' . $this->quoteIdentifier('id') . ' = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function calcularSubtotal(int $productoId, int $cantidad): float
    {
        $precio = $this->productoPrecio($productoId);
        return round($cantidad * $precio, 2);
    }

    private function limitedQuery(string $sql, int $limit): array
    {
        $safeLimit = max(1, min($limit, 1000));

        if ($this->driver === 'sqlsrv') {
            $sql = preg_replace('/^\s*SELECT\s+/i', 'SELECT TOP ' . $safeLimit . ' ', $sql, 1) ?: $sql;
        } else {
            $sql .= ' LIMIT ' . $safeLimit;
        }

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function applyPagination(string $selectSql, string $orderBySql, int $limit, int $offset): string
    {
        if ($this->driver === 'sqlsrv') {
            return $selectSql . $orderBySql . ' OFFSET :_offset ROWS FETCH NEXT :_limit ROWS ONLY';
        }

        return $selectSql . $orderBySql . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    private function quoteIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
            throw new InvalidArgumentException('Identificador invalido.');
        }

        if ($this->driver === 'pgsql') {
            return '"' . $identifier . '"';
        }

        if ($this->driver === 'sqlsrv') {
            return '[' . $identifier . ']';
        }

        return '`' . $identifier . '`';
    }
}

<?php

declare(strict_types=1);

class KpiModel
{
    public function summary(): array
    {
        $pdo = db_connect();

        return [
            'totalVentas' => $this->scalar($pdo, 'SELECT COALESCE(COUNT(*), 0) FROM ventas'),
            'montoTotal' => $this->scalar($pdo, 'SELECT COALESCE(SUM(total), 0) FROM ventas'),
            'ticketPromedio' => $this->scalar($pdo, 'SELECT COALESCE(AVG(total), 0) FROM ventas'),
            'totalClientes' => $this->scalar($pdo, 'SELECT COALESCE(COUNT(*), 0) FROM clientes'),
        ];
    }

    public function charts(): array
    {
        $pdo = db_connect();

        return [
            'ventasPorDia' => $this->dataset(
                $pdo,
                'SELECT CAST(fecha AS DATE) AS etiqueta, COALESCE(SUM(total), 0) AS valor
                 FROM ventas
                 GROUP BY CAST(fecha AS DATE)
                 ORDER BY CAST(fecha AS DATE) DESC
                 LIMIT 14',
                true
            ),
            'ventasPorCategoria' => $this->dataset(
                $pdo,
                'SELECT c.nombre AS etiqueta, COALESCE(SUM(v.total), 0) AS valor
                 FROM ventas v
                 JOIN clientes c ON c.id = v.cliente_id
                 GROUP BY c.nombre
                 ORDER BY valor DESC'
            ),
            'topPostres' => $this->dataset(
                $pdo,
                'SELECT p.nombre AS etiqueta, COALESCE(SUM(d.cantidad), 0) AS valor
                 FROM detalle_ventas d
                 JOIN productos p ON p.id = d.producto_id
                 GROUP BY p.nombre
                 ORDER BY valor DESC
                 LIMIT 8'
            ),
        ];
    }

    private function scalar(PDO $pdo, string $sql): float
    {
        $stmt = $pdo->query($sql);
        return (float) $stmt->fetchColumn();
    }

    private function dataset(PDO $pdo, string $sql, bool $reverse = false): array
    {
        $rows = $pdo->query($sql)->fetchAll();
        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = (string) $row['etiqueta'];
            $values[] = (float) $row['valor'];
        }

        if ($reverse) {
            $labels = array_reverse($labels);
            $values = array_reverse($values);
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }
}

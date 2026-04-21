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
                'SELECT DATE(fecha) AS etiqueta, SUM(total) AS valor FROM ventas GROUP BY DATE(fecha) ORDER BY DATE(fecha) DESC LIMIT 14',
                true
            ),
            'ventasPorCategoria' => $this->dataset(
                $pdo,
                'SELECT c.nombre_categoria AS etiqueta, SUM(d.subtotal) AS valor
                 FROM detalle_venta d
                 JOIN postres p ON d.id_postre = p.id_postre
                 JOIN categorias_postres c ON p.id_categoria = c.id_categoria
                 GROUP BY c.nombre_categoria
                 ORDER BY valor DESC'
            ),
            'topPostres' => $this->dataset(
                $pdo,
                'SELECT p.nombre_postre AS etiqueta, SUM(d.cantidad) AS valor
                 FROM detalle_venta d
                 JOIN postres p ON d.id_postre = p.id_postre
                 GROUP BY p.nombre_postre
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

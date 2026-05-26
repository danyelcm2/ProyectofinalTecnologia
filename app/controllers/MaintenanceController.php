<?php

declare(strict_types=1);

class MaintenanceController
{
    public function clientes(): void
    {
        $this->render('clientes', 'Mantenimiento de clientes');
    }

    public function productos(): void
    {
        $this->render('productos', 'Mantenimiento de productos');
    }

    public function ventas(): void
    {
        $this->render('ventas', 'Mantenimiento de ventas');
    }

    public function detalleVentas(): void
    {
        $this->render('detalle_ventas', 'Mantenimiento de detalle de ventas');
    }

    private function render(string $active, string $title): void
    {
        $viewData = [
            'title' => $title,
            'active' => $active,
            'user' => $_SESSION['user'] ?? ['name' => 'Usuario'],
            'connection' => db_selected_meta(),
        ];

        require __DIR__ . '/../views/maintenance/' . $active . '.php';
    }
}

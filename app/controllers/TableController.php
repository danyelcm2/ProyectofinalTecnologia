<?php

declare(strict_types=1);

class TableController
{
    public function index(): void
    {
        $viewData = [
            'title' => 'Consulta de tablas',
            'active' => 'tables',
            'user' => $_SESSION['user'] ?? ['name' => 'Usuario'],
            'connection' => db_selected_meta(),
        ];

        require __DIR__ . '/../views/tables/index.php';
    }
}

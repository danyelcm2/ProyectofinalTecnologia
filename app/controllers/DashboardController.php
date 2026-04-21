<?php

declare(strict_types=1);

class DashboardController
{
    public function index(): void
    {
        $viewData = [
            'title' => 'Dashboard KPI',
            'active' => 'dashboard',
            'user' => $_SESSION['user'] ?? ['name' => 'Usuario'],
            'connection' => db_selected_meta(),
        ];

        require __DIR__ . '/../views/dashboard/index.php';
    }
}

<?php

declare(strict_types=1);

class FormController
{
    public function index(): void
    {
        $viewData = [
            'title' => 'Formularios dinamicos',
            'active' => 'forms',
            'user' => $_SESSION['user'] ?? ['name' => 'Usuario'],
            'connection' => db_selected_meta(),
        ];

        require __DIR__ . '/../views/forms/index.php';
    }
}

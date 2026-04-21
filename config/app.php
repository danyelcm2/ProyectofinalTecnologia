<?php

declare(strict_types=1);

function app_config(): array
{
    return [
        'app_name' => 'KPI Control Center',
        'base_path' => dirname(__DIR__),
        'session_name' => 'kpi_secure_session',
        'default_connection' => 'mysql',
        'two_fa_issuer' => 'KPI Control Center',
        'auth_connection' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'proyectofinal_seguridad',
            'username' => 'app_user',
            'password' => 'Password123!',
            'charset' => 'utf8mb4',
        ],
        'connections' => [
            'mysql' => [
                'label' => 'MySQL',
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'proyectofinal_seguridad',
                'username' => 'app_user',
                'password' => 'Password123!',
                'charset' => 'utf8mb4',
                'simulated' => false,
            ],
            'sqlserver' => [
                'label' => 'SQL Server',
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => 1433,
                'database' => 'master',
                'username' => 'sa',
                'password' => '',
                'charset' => 'utf8mb4',
                'simulated' => true,
            ],
            'gcloud' => [
                'label' => 'Google Cloud SQL',
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'ventas_postres',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'simulated' => true,
            ],
            'azure' => [
                'label' => 'Azure SQL',
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'ventas_postres',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'simulated' => true,
            ],
        ],
    ];
}

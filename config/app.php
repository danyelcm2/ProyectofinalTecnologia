<?php

function app_config(): array
{
    return [
        'session_name' => 'kpi_session',
        'two_fa_issuer' => 'KPI App',
        'default_connection' => 'mysql',

        'auth_connection' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'proyectofinal_seguridad',
            'username' => 'app_user',
            'password' => getenv('DB_PASSWORD') ?: 'Password123!',
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
                'password' => getenv('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'simulated' => false,
            ],

            'sqlserver' => [
                'label' => 'SQL Server',
                'driver' => 'sqlsrv',
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

            'postgres' => [
                'label' => 'PostgreSQL',
                'driver' => 'pgsql',
                'host' => '127.0.0.1',
                'port' => 5432,
                'database' => 'kpi_app_pg',
                'username' => 'app_pg',
                'password' => getenv('PG_DB_PASSWORD') ?: 'Password123!',
                'charset' => 'UTF8',
                'simulated' => false,
            ],
        ],
    ];
}
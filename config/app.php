<?php

function app_config(): array
{
    return [
        'default_connection' => 'mysql',

        'auth_connection' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'proyectofinal_seguridad',
            'username' => 'app_user',
            'password' => getenv('DB_PASSWORD') ?: 'Password123!',
        ],

        'connections' => [

            'mysql' => [
                'label' => 'MySQL',
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'proyectofinal_seguridad',
                'username' => 'app_user',
                'password' => getenv('DB_PASSWORD'),
                'simulated' => false,
            ],

            'sqlserver' => [
                'label' => 'SQL Server',
                'driver' => 'sqlsrv',
                'host' => 'localhost',
                'database' => 'master',
                'username' => 'sa',
                'password' => '',
                'simulated' => true,
            ],

            'gcloud' => [
                'label' => 'Google Cloud SQL',
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'ventas_postres',
                'username' => 'root',
                'password' => '',
                'simulated' => true,
            ],

            'azure' => [
                'label' => 'Azure SQL',
                'driver' => 'sqlsrv',
                'host' => 'tu-servidor.database.windows.net',
                'database' => 'kpi_db',
                'username' => 'admin',
                'password' => '',
                'simulated' => true,
            ],
        ],
    ];
}
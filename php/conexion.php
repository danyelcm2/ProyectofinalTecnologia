<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

try {
    $conn = db_connect();
} catch (Throwable $error) {
    http_response_code(500);
    die('Error de conexion con la base de datos.');
}
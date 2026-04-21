<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

function db_connection_options(): array
{
    $config = app_config();
    return $config['connections'];
}

function db_connection_meta(string $key): ?array
{
    $connections = db_connection_options();
    return $connections[$key] ?? null;
}

function db_connection_requires_verification(string $key): bool
{
    return $key !== app_config()['default_connection'];
}

function db_connection_runtime_overrides(string $key): array
{
    $overrides = $_SESSION['db_connection_credentials'][$key] ?? [];
    return is_array($overrides) ? $overrides : [];
}

function db_connection_resolved_meta(string $key): ?array
{
    $meta = db_connection_meta($key);
    if ($meta === null) {
        return null;
    }

    $overrides = db_connection_runtime_overrides($key);
    foreach (['host', 'port', 'database', 'username', 'password', 'charset'] as $field) {
        if (array_key_exists($field, $overrides) && $overrides[$field] !== '') {
            $meta[$field] = $overrides[$field];
        }
    }

    return $meta;
}

function db_selected_key(): string
{
    $config = app_config();
    $selected = $_SESSION['db_connection'] ?? $config['default_connection'];
    $connections = db_connection_options();

    return array_key_exists($selected, $connections) ? $selected : $config['default_connection'];
}

function db_selected_meta(): array
{
    return db_connection_resolved_meta(db_selected_key()) ?? db_connection_options()[app_config()['default_connection']];
}

function db_connection_credentials_view(string $key): array
{
    $meta = db_connection_meta($key);
    if ($meta === null) {
        return [];
    }

    $overrides = db_connection_runtime_overrides($key);

    return [
        'host' => (string) ($overrides['host'] ?? $meta['host'] ?? ''),
        'port' => (string) ($overrides['port'] ?? $meta['port'] ?? ''),
        'database' => (string) ($overrides['database'] ?? $meta['database'] ?? ''),
        'username' => (string) ($overrides['username'] ?? ''),
    ];
}

function db_store_connection_credentials(string $key, array $credentials): void
{
    $_SESSION['db_connection_credentials'][$key] = $credentials;
}

function db_supported_drivers(): array
{
    return PDO::getAvailableDrivers();
}

function db_runtime_driver(string $key, array $meta): string
{
    if ($key === 'sqlserver') {
        return 'sqlsrv';
    }

    return (string) $meta['driver'];
}

function db_validate_runtime_driver(string $key, array $meta): void
{
    $driver = db_runtime_driver($key, $meta);
    $available = db_supported_drivers();

    if (!in_array($driver, $available, true)) {
        throw new RuntimeException('El driver PDO requerido no esta disponible: ' . $driver);
    }
}

function db_build_pdo(array $meta): PDO
{
    $dsn = sprintf(
        '%s:host=%s;port=%d;dbname=%s;charset=%s',
        $meta['driver'],
        $meta['host'],
        (int) $meta['port'],
        $meta['database'],
        $meta['charset']
    );

    return new PDO($dsn, $meta['username'], $meta['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
}

function db_build_runtime_pdo(string $key, array $meta): PDO
{
    $driver = db_runtime_driver($key, $meta);

    if ($driver === 'sqlsrv') {
        $dsn = sprintf(
            'sqlsrv:Server=%s,%d;Database=%s;LoginTimeout=5',
            $meta['host'],
            (int) $meta['port'],
            $meta['database']
        );

        return new PDO($dsn, $meta['username'], $meta['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
    }

    return db_build_pdo($meta);
}

function db_connect_with_runtime(string $key): PDO
{
    $meta = db_connection_resolved_meta($key);
    if ($meta === null) {
        throw new InvalidArgumentException('Conexion invalida.');
    }

    return db_build_pdo($meta);
}

function db_connect(): PDO
{
    static $pdoInstances = [];

    $key = db_selected_key();
    $meta = db_selected_meta();
    $cacheKey = $key . ':' . md5(json_encode([
        'host' => $meta['host'],
        'port' => $meta['port'],
        'database' => $meta['database'],
        'username' => $meta['username'],
        'password' => $meta['password'],
        'charset' => $meta['charset'],
    ]));

    if (isset($pdoInstances[$cacheKey])) {
        return $pdoInstances[$cacheKey];
    }

    $pdo = db_build_pdo($meta);

    $pdoInstances[$cacheKey] = $pdo;
    return $pdo;
}

function db_auth_connect(): PDO
{
    static $authPdo = null;

    if ($authPdo instanceof PDO) {
        return $authPdo;
    }

    $meta = app_config()['auth_connection'];
    $authPdo = db_build_pdo($meta);

    return $authPdo;
}

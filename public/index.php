<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ConnectionController.php';
require_once __DIR__ . '/../app/controllers/MaintenanceController.php';
require_once __DIR__ . '/../app/controllers/ApiController.php';
require_once __DIR__ . '/../app/controllers/MaintenanceApiController.php';

$config = app_config();

session_name($config['session_name'] ?? 'kpi_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']) && !empty($_SESSION['is_authenticated']);
}

function has_db_access(): bool
{
    return !empty($_SESSION['db_connection']);
}

function requires_login(string $page): bool
{
    return in_array($page, [
        'connections',
        'select-connection',
        'dashboard',
        'clientes',
        'productos',
        'ventas',
        'detalle_ventas',
        'logout',
        'api_kpi',
        'api_clientes',
        'api_clientes_create',
        'api_clientes_update',
        'api_clientes_delete',
        'api_productos',
        'api_productos_create',
        'api_productos_update',
        'api_productos_delete',
        'api_ventas',
        'api_ventas_create',
        'api_ventas_update',
        'api_ventas_delete',
        'api_detalle_ventas',
        'api_detalle_ventas_create',
        'api_detalle_ventas_update',
        'api_detalle_ventas_delete',
        'api_options_clientes',
        'api_options_productos',
        'api_options_ventas',
        'api_producto_precio',
    ], true);
}

function requires_db_access(string $page): bool
{
    return in_array($page, [
        'dashboard',
        'clientes',
        'productos',
        'ventas',
        'detalle_ventas',
        'api_kpi',
        'api_clientes',
        'api_clientes_create',
        'api_clientes_update',
        'api_clientes_delete',
        'api_productos',
        'api_productos_create',
        'api_productos_update',
        'api_productos_delete',
        'api_ventas',
        'api_ventas_create',
        'api_ventas_update',
        'api_ventas_delete',
        'api_detalle_ventas',
        'api_detalle_ventas_create',
        'api_detalle_ventas_update',
        'api_detalle_ventas_delete',
        'api_options_clientes',
        'api_options_productos',
        'api_options_ventas',
        'api_producto_precio',
    ], true);
}

$page = $_GET['page']
    ?? (!empty($_SESSION['pending_2fa_context'])
        ? 'verify-2fa'
        : (is_logged_in() ? (has_db_access() ? 'dashboard' : 'connections') : 'login'));

if (requires_login($page) && !is_logged_in()) {
    header('Location: index.php?page=login');
    exit;
}

if (requires_db_access($page) && !has_db_access()) {
    header('Location: index.php?page=connections');
    exit;
}

switch ($page) {
    case 'login':
        (new AuthController())->login();
        break;

    case 'verify-2fa':
        (new AuthController())->verifyTwoFactor();
        break;

    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'connections':
        (new ConnectionController())->index();
        break;

    case 'select-connection':
        (new ConnectionController())->select();
        break;

    case 'clientes':
        (new MaintenanceController())->clientes();
        break;

    case 'productos':
        (new MaintenanceController())->productos();
        break;

    case 'ventas':
        (new MaintenanceController())->ventas();
        break;

    case 'detalle_ventas':
        (new MaintenanceController())->detalleVentas();
        break;

    case 'api_kpi':
        (new ApiController())->kpis();
        break;

    case 'api_clientes':
        (new MaintenanceApiController())->clientes();
        break;

    case 'api_clientes_create':
        (new MaintenanceApiController())->createCliente();
        break;

    case 'api_clientes_update':
        (new MaintenanceApiController())->updateCliente();
        break;

    case 'api_clientes_delete':
        (new MaintenanceApiController())->deleteCliente();
        break;

    case 'api_productos':
        (new MaintenanceApiController())->productos();
        break;

    case 'api_productos_create':
        (new MaintenanceApiController())->createProducto();
        break;

    case 'api_productos_update':
        (new MaintenanceApiController())->updateProducto();
        break;

    case 'api_productos_delete':
        (new MaintenanceApiController())->deleteProducto();
        break;

    case 'api_ventas':
        (new MaintenanceApiController())->ventas();
        break;

    case 'api_ventas_create':
        (new MaintenanceApiController())->createVenta();
        break;

    case 'api_ventas_update':
        (new MaintenanceApiController())->updateVenta();
        break;

    case 'api_ventas_delete':
        (new MaintenanceApiController())->deleteVenta();
        break;

    case 'api_detalle_ventas':
        (new MaintenanceApiController())->detalleVentas();
        break;

    case 'api_detalle_ventas_create':
        (new MaintenanceApiController())->createDetalleVenta();
        break;

    case 'api_detalle_ventas_update':
        (new MaintenanceApiController())->updateDetalleVenta();
        break;

    case 'api_detalle_ventas_delete':
        (new MaintenanceApiController())->deleteDetalleVenta();
        break;

    case 'api_options_clientes':
        (new MaintenanceApiController())->optionsClientes();
        break;

    case 'api_options_productos':
        (new MaintenanceApiController())->optionsProductos();
        break;

    case 'api_options_ventas':
        (new MaintenanceApiController())->optionsVentas();
        break;

    case 'api_producto_precio':
        (new MaintenanceApiController())->productoPrecio();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    default:
        http_response_code(404);
        echo 'Ruta no encontrada.';
        break;
}

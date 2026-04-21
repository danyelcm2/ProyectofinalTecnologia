<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ConnectionController.php';
require_once __DIR__ . '/../app/controllers/FormController.php';
require_once __DIR__ . '/../app/controllers/ApiController.php';

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

function requires_auth(string $page): bool
{
    return in_array($page, ['dashboard', 'connections', 'forms', 'logout', 'api_kpi', 'api_tables', 'api_columns', 'api_insert'], true);
}

$page = $_GET['page'] ?? (is_logged_in() ? 'dashboard' : 'login');

if (requires_auth($page) && !is_logged_in()) {
    header('Location: index.php?page=login');
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

    case 'forms':
        (new FormController())->index();
        break;

    case 'api_kpi':
        (new ApiController())->kpis();
        break;

    case 'api_tables':
        (new ApiController())->tables();
        break;

    case 'api_columns':
        (new ApiController())->columns();
        break;

    case 'api_insert':
        (new ApiController())->insert();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    default:
        http_response_code(404);
        echo 'Ruta no encontrada.';
        break;
}

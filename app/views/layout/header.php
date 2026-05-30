<?php

declare(strict_types=1);

$title = $viewData['title'] ?? 'Proyecto Tecnologias';
$active = $viewData['active'] ?? '';
$user = $viewData['user'] ?? ['name' => 'Usuario'];
$connectionLabel = db_selected_meta()['label'];
$assetFile = __DIR__ . '/../../../assets/css/app.css';
$assetVersion = is_file($assetFile)
    ? (string) md5_file($assetFile)
    : (string) time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('css/app.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
<?php if (!empty($_SESSION['is_authenticated'])): ?>
    <div class="dashboard-shell">
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <span class="brand-mark">SR</span>
                <div>
                    <h1 class="brand-title">Proyecto Tecnologias</h1>
                    <p class="brand-subtitle">Sala de control</p>
                </div>
            </div>
            <p class="sidebar-menu-title">Navegacion</p>
            <nav class="nav flex-column gap-2 mt-4 sidebar-nav">
                <a class="sidebar-link <?= $active === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-house"></i></span>
                    <span>Dashboard</span>
                </a>
                <a class="sidebar-link <?= $active === 'productos' ? 'active' : ''; ?>" href="index.php?page=productos">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-box-open"></i></span>
                    <span>Productos</span>
                </a>
                <a class="sidebar-link <?= $active === 'ventas' ? 'active' : ''; ?>" href="index.php?page=ventas">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-credit-card"></i></span>
                    <span>Ventas</span>
                </a>
                <a class="sidebar-link <?= $active === 'clientes' ? 'active' : ''; ?>" href="index.php?page=clientes">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-user"></i></span>
                    <span>Clientes</span>
                </a>
                <a class="sidebar-link <?= $active === 'detalle_ventas' ? 'active' : ''; ?>" href="index.php?page=detalle_ventas">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-box"></i></span>
                    <span>Pedidos</span>
                </a>
            </nav>
            <p class="sidebar-menu-title mt-4">Sistema</p>
            <nav class="nav flex-column gap-2 sidebar-nav-short">
                <a class="sidebar-link" href="#">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-user-circle"></i></span>
                    <span>Usuarios</span>
                </a>
                <a class="sidebar-link <?= $active === 'connections' ? 'active' : ''; ?>" href="index.php?page=connections">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-gear"></i></span>
                    <span>Configuracion</span>
                </a>
                <a class="sidebar-link" href="index.php?page=logout">
                    <span class="sidebar-link-icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
                    <span>Salir</span>
                </a>
            </nav>

            <div class="sidebar-decoration">
                <p class="sidebar-decoration-label mb-1">Estado</p>
                <strong class="sidebar-decoration-value">Operativo</strong>
            </div>
        </aside>
        <main class="app-main">
            <nav class="navbar app-navbar mb-4">
                <div class="container-fluid px-0">
                    <div class="topbar-grid">
                        <div>
                            <p class="top-caption mb-1">Vista en linea</p>
                            <h2 class="top-title mb-0"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                        </div>

                        <div class="top-search-wrap">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search" class="top-search" placeholder="Buscar cliente, venta o producto">
                        </div>

                        <div class="top-user">
                            <button class="top-icon-btn" type="button" aria-label="Notificaciones"><i class="fa-solid fa-bell"></i></button>
                            <span class="top-avatar"><i class="fa-solid fa-user"></i></span>
                            <span class="top-user-name"><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class="top-meta-row mt-3">
                        <span class="badge connection-badge"><i class="fa-solid fa-database me-1"></i><?= htmlspecialchars($connectionLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="top-meta-chip"><i class="fa-solid fa-wave-square"></i> Flujo monitoreado</span>
                    </div>
                </div>
            </nav>
<?php endif; ?>

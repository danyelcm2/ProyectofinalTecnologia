<?php

declare(strict_types=1);

$title = $viewData['title'] ?? 'KPI Control Center';
$active = $viewData['active'] ?? '';
$user = $viewData['user'] ?? ['name' => 'Usuario'];
$connectionLabel = db_selected_meta()['label'];
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
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
<?php if (!empty($_SESSION['is_authenticated'])): ?>
    <div class="dashboard-shell">
        <aside class="app-sidebar">
            <div>
                <h1 class="brand-title">Sweet Desk</h1>
                <p class="brand-subtitle">Gestion de postres</p>
            </div>
            <nav class="nav flex-column gap-2 mt-4">
                <a class="sidebar-link <?= $active === 'connections' ? 'active' : ''; ?>" href="index.php?page=connections">
                    <i class="fa-solid fa-database"></i> Conexiones
                </a>
                <a class="sidebar-link <?= $active === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    <i class="fa-solid fa-chart-line"></i> Dashboard KPI
                </a>
                <a class="sidebar-link <?= $active === 'clientes' ? 'active' : ''; ?>" href="index.php?page=clientes">
                    <i class="fa-solid fa-users"></i> Clientes
                </a>
                <a class="sidebar-link <?= $active === 'productos' ? 'active' : ''; ?>" href="index.php?page=productos">
                    <i class="fa-solid fa-ice-cream"></i> Productos
                </a>
                <a class="sidebar-link <?= $active === 'ventas' ? 'active' : ''; ?>" href="index.php?page=ventas">
                    <i class="fa-solid fa-receipt"></i> Ventas
                </a>
                <a class="sidebar-link <?= $active === 'detalle_ventas' ? 'active' : ''; ?>" href="index.php?page=detalle_ventas">
                    <i class="fa-solid fa-list-check"></i> Detalle ventas
                </a>
                <a class="sidebar-link" href="index.php?page=logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesion
                </a>
            </nav>
        </aside>
        <main class="app-main">
            <nav class="navbar app-navbar mb-4">
                <div class="container-fluid px-0">
                    <span class="badge connection-badge"><?= htmlspecialchars($connectionLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="text-white small"><i class="fa-solid fa-cake-candles me-1"></i>Hola, <?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </nav>
<?php endif; ?>

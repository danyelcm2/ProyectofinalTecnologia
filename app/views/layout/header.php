<?php

declare(strict_types=1);

$title = $viewData['title'] ?? 'DulceMomento';
$active = $viewData['active'] ?? '';
$user = $viewData['user'] ?? ['name' => 'Usuario'];
$connectionLabel = db_selected_meta()['label'];
$assetVersion = (string) @filemtime(__DIR__ . '/../../../assets/css/app.css');
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
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('css/app.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
<?php if (!empty($_SESSION['is_authenticated'])): ?>
    <div class="dashboard-shell">
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <h1 class="brand-title">🧁 DulceMomento</h1>
                <p class="brand-subtitle">Administracion pastel premium</p>
            </div>
            <nav class="nav flex-column gap-2 mt-4">
                <a class="sidebar-link <?= $active === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <a class="sidebar-link <?= $active === 'productos' ? 'active' : ''; ?>" href="index.php?page=productos">
                    <i class="fa-solid fa-cake-candles"></i> Postres
                </a>
                <a class="sidebar-link" href="#">
                    <i class="fa-solid fa-folder-open"></i> Categorias
                </a>
                <a class="sidebar-link" href="#">
                    <i class="fa-solid fa-glass-water"></i> Ingredientes
                </a>
                <a class="sidebar-link <?= $active === 'ventas' ? 'active' : ''; ?>" href="index.php?page=ventas">
                    <i class="fa-solid fa-credit-card"></i> Ventas
                </a>
                <a class="sidebar-link <?= $active === 'clientes' ? 'active' : ''; ?>" href="index.php?page=clientes">
                    <i class="fa-solid fa-user"></i> Clientes
                </a>
                <a class="sidebar-link <?= $active === 'detalle_ventas' ? 'active' : ''; ?>" href="index.php?page=detalle_ventas">
                    <i class="fa-solid fa-box"></i> Pedidos
                </a>
                <a class="sidebar-link" href="#">
                    <i class="fa-solid fa-chart-bar"></i> Reportes
                </a>
                <a class="sidebar-link" href="#">
                    <i class="fa-solid fa-user-circle"></i> Usuarios
                </a>
                <a class="sidebar-link <?= $active === 'connections' ? 'active' : ''; ?>" href="index.php?page=connections">
                    <i class="fa-solid fa-gear"></i> Configuracion
                </a>
                <a class="sidebar-link" href="index.php?page=logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Salir
                </a>
            </nav>

            <div class="sidebar-decoration">🍰 ✨ 💗</div>
        </aside>
        <main class="app-main">
            <nav class="navbar app-navbar mb-4">
                <div class="container-fluid px-0 topbar-grid">
                    <span class="badge connection-badge"><i class="fa-solid fa-database me-1"></i><?= htmlspecialchars($connectionLabel, ENT_QUOTES, 'UTF-8'); ?></span>

                    <div class="top-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" class="top-search" placeholder="Buscar...">
                    </div>

                    <div class="top-user">
                        <button class="top-icon-btn" type="button" aria-label="Notificaciones"><i class="fa-solid fa-bell"></i></button>
                        <span class="top-avatar">👩🏻‍🍳</span>
                        <span class="top-user-name"><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </nav>
<?php endif; ?>

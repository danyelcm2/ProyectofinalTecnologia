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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
<?php if (!empty($_SESSION['is_authenticated'])): ?>
    <div class="dashboard-shell">
        <aside class="app-sidebar">
            <div>
                <h1 class="brand-title">KPI Control</h1>
                <p class="brand-subtitle">Analitica operativa</p>
            </div>
            <nav class="nav flex-column gap-2 mt-4">
                <a class="sidebar-link <?= $active === 'connections' ? 'active' : ''; ?>" href="index.php?page=connections">
                    <i class="bi bi-diagram-3"></i> Conexiones
                </a>
                <a class="sidebar-link <?= $active === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard KPI
                </a>
                <a class="sidebar-link <?= $active === 'forms' ? 'active' : ''; ?>" href="index.php?page=forms">
                    <i class="bi bi-ui-checks-grid"></i> Formularios dinamicos
                </a>
                <a class="sidebar-link" href="index.php?page=logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </nav>
        </aside>
        <main class="app-main">
            <nav class="navbar app-navbar mb-4">
                <div class="container-fluid px-0">
                    <span class="badge connection-badge"><?= htmlspecialchars($connectionLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="text-white small">Hola, <?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </nav>
<?php endif; ?>

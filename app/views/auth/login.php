<?php

declare(strict_types=1);

$viewData = $viewData ?? [];
$assetFile = realpath(__DIR__ . '/../../../assets/css/app.css') ?: (__DIR__ . '/../../../assets/css/app.css');
$assetVersion = is_file($assetFile)
    ? (string) md5_file($assetFile)
    : (string) time();
$cssHref = asset_url('css/app.css');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) $viewData['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8'); ?>?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="login-clean-body">
    <main class="login-clean-shell">
        <section class="login-clean-panel" aria-label="Acceso al sistema">
            <header class="login-clean-header">
                <p class="login-clean-kicker">Acceso Seguro</p>
                <h1 class="login-clean-title">SistemaMultiBD</h1>
                <p class="login-clean-subtitle">Plataforma de gestion interna multi base de datos.</p>
            </header>

            <?php if (!empty($viewData['error'])): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars((string) $viewData['error'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login" novalidate class="login-clean-form">
                <div class="mb-3">
                    <label for="email" class="form-label login-clean-label">Usuario</label>
                    <input
                        type="email"
                        class="form-control form-control-lg"
                        id="email"
                        name="email"
                        required
                        value="<?= htmlspecialchars((string) ($viewData['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="correo@empresa.com"
                        autocomplete="username"
                    >
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label login-clean-label">Contrasena</label>
                    <input
                        type="password"
                        class="form-control form-control-lg"
                        id="password"
                        name="password"
                        minlength="8"
                        required
                        placeholder="Ingresa tu contrasena"
                        autocomplete="current-password"
                    >
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4 login-clean-meta">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Mantener sesion</label>
                    </div>
                    <a class="auth-link" href="#">Recuperar acceso</a>
                </div>

                <button type="submit" class="btn btn-dessert btn-lg w-100">Iniciar sesion</button>
            </form>

            <footer class="login-clean-footer">
                <span>Version visual 2026</span>
                <span class="login-clean-dot"></span>
                <span>Interfaz minimalista</span>
            </footer>
        </section>
    </main>
    </div>
</body>
</html>

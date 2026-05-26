<?php

declare(strict_types=1);

$viewData = $viewData ?? [];
$assetVersion = (string) @filemtime(__DIR__ . '/../../../assets/css/app.css');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) $viewData['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('css/app.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="auth-body">
    <div class="auth-layout">
        <section class="auth-visual">
            <div class="auth-badge">DulceMomento</div>
            <h1 class="auth-brand">DulceMomento</h1>
            <p class="auth-subtitle">Sistema de Gestion de Postres</p>
            <div class="auth-floaters" aria-hidden="true">
                <span>🧁</span>
                <span>💗</span>
                <span>✨</span>
                <span>🍓</span>
                <span>🍩</span>
                <span>🌸</span>
            </div>
            <div class="auth-cake-panel" aria-hidden="true">
                <div class="cake-icon">🎂</div>
            </div>
        </section>

        <section class="auth-form-zone">
            <div class="card auth-card border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="login-brand">
                        <div class="login-brand-icon"><i class="fa-solid fa-cake-candles"></i></div>
                        <div class="login-brand-name">DulceMomento</div>
                    </div>
                    <div class="login-art" aria-hidden="true">
                        <i class="fa-solid fa-ice-cream"></i>
                        <i class="fa-solid fa-cookie-bite"></i>
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <h2 class="auth-title">Bienvenido(a)</h2>
                    <p class="auth-message">Inicia sesion para continuar</p>

                    <?php if (!empty($viewData['error'])): ?>
                        <div class="alert alert-danger py-2"><?= htmlspecialchars((string) $viewData['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=login" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label auth-label">Usuario</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" required value="<?= htmlspecialchars((string) ($viewData['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ingresa tu usuario">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label auth-label">Contrasena</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" minlength="8" required placeholder="Ingresa tu contrasena">
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3 auth-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Recordarme</label>
                            </div>
                            <a class="auth-link" href="#">¿Olvidaste tu contrasena?</a>
                        </div>

                        <button type="submit" class="btn btn-dessert btn-lg w-100 mt-2"><i class="fa-solid fa-right-to-bracket me-2"></i>Iniciar sesion</button>
                    </form>

                    <p class="auth-copyright">© 2026 DulceMomento. Todos los derechos reservados.</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>

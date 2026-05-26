<?php

declare(strict_types=1);

$viewData = $viewData ?? [];
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
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="card auth-card shadow-lg border-0">
            <div class="card-body p-4 p-md-5">
                <div class="login-brand">
                    <div class="login-brand-icon"><i class="fa-solid fa-cake-candles"></i></div>
                    <div class="login-brand-name">Sweet Desk</div>
                </div>
                <div class="login-art" aria-hidden="true">
                    <i class="fa-solid fa-ice-cream"></i>
                    <i class="fa-solid fa-cookie-bite"></i>
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <h1 class="h3 mb-2 text-white">Acceso seguro</h1>
                <p class="text-white-50 mb-4">Inicia sesion para administrar KPI y mantenimientos de postres.</p>

                <?php if (!empty($viewData['error'])): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars((string) $viewData['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label text-white"><i class="fa-solid fa-envelope me-1"></i>Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required value="<?= htmlspecialchars((string) ($viewData['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="usuario@correo.com">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-white"><i class="fa-solid fa-lock me-1"></i>Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" minlength="8" required placeholder="Ingresa tu clave">
                    </div>
                    <button type="submit" class="btn btn-dessert btn-lg w-100 mt-2"><i class="fa-solid fa-right-to-bracket me-2"></i>Entrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

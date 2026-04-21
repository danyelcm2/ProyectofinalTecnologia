<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) $viewData['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="card auth-card shadow-lg border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 mb-2 text-white">Acceso seguro</h1>
                <p class="text-white-50 mb-4">Inicia sesion para administrar KPI y formularios dinamicos.</p>

                <?php if (!empty($viewData['error'])): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars((string) $viewData['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label text-white">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required value="<?= htmlspecialchars((string) ($viewData['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-white">Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Continuar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

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
                <h1 class="h3 mb-2 text-white">Verificacion 2FA</h1>
                <p class="text-white-50 mb-3">Ingresa el codigo de 6 digitos generado por Google Authenticator.</p>

                <?php if (!empty($viewData['error'])): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars((string) $viewData['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="alert alert-info py-2">
                    <strong><?= !empty($viewData['requiresSetup']) ? 'Configura Google Authenticator para continuar.' : 'Usa tu app Google Authenticator para generar el codigo.'; ?></strong>
                </div>

                <?php if (!empty($viewData['requiresSetup']) && !empty($viewData['manualKey'])): ?>
                    <div class="alert alert-secondary py-2 small">
                        Cuenta: <strong><?= htmlspecialchars((string) $viewData['email'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                        Emisor: <strong><?= htmlspecialchars((string) $viewData['issuer'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                        Clave manual: <strong><?= htmlspecialchars((string) $viewData['manualKey'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>

                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-12 col-md-5 text-center">
                            <?php if (!empty($viewData['qrCodeUrl'])): ?>
                                <img
                                    src="<?= htmlspecialchars((string) $viewData['qrCodeUrl'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="QR para Google Authenticator"
                                    class="img-fluid rounded bg-white p-2"
                                    style="max-width: 220px;"
                                >
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-md-7">
                            <label for="otpauthUri" class="form-label text-white">Enlace de configuracion OTP</label>
                            <textarea class="form-control" id="otpauthUri" rows="4" readonly><?= htmlspecialchars((string) $viewData['otpauthUri'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <div class="form-text text-white-50">Escanea el QR o agrega la cuenta manualmente usando la clave secreta.</div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=verify-2fa" novalidate>
                    <div class="mb-3">
                        <label for="code" class="form-label text-white">Codigo</label>
                        <input type="text" class="form-control form-control-lg" id="code" name="code" pattern="[0-9]{6}" maxlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Validar codigo</button>
                </form>
                <a href="index.php?page=login" class="btn btn-link text-white-50 w-100 mt-3">Volver al login</a>
            </div>
        </div>
    </div>
</body>
</html>

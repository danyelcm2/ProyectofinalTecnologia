<?php

declare(strict_types=1);
$viewData = $viewData ?? [];
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Seleccionar base de datos</h2>
    <p class="text-secondary mb-0">Elige la base que deseas consultar. El sistema usara las credenciales internas ya configuradas y luego pedira tu codigo 2FA para autorizar el acceso.</p>
</section>

<?php if (!empty($viewData['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars((string) $viewData['flash']['level'], ENT_QUOTES, 'UTF-8'); ?> py-2">
        <?= htmlspecialchars((string) $viewData['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="chart-card connection-picker-card">
    <form method="POST" action="index.php?page=select-connection" class="row g-3 align-items-end">
        <div class="col-12 col-md-8 col-xl-6">
            <label for="connection" class="form-label">Base de datos disponible</label>
            <select class="form-select" id="connection" name="connection" required>
                <option value="">Selecciona una opcion</option>
                <?php foreach ($viewData['connections'] as $key => $connection): ?>
                    <?php $isSelected = ($viewData['pendingSelection'] ?: $viewData['selected']) === $key; ?>
                    <option value="<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" <?= $isSelected ? 'selected' : ''; ?>>
                        <?= htmlspecialchars((string) $connection['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text mt-2">Despues de seleccionar la base se solicitara tu codigo 2FA para ingresar al menu principal.</div>
        </div>
        <div class="col-12 col-md-4 col-xl-3">
            <button type="submit" class="btn btn-primary w-100">Continuar con 2FA</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>

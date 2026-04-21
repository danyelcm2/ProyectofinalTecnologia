<?php

declare(strict_types=1);
require __DIR__ . '/../layout/header.php';
?>
<section class="page-intro mb-4">
    <h2 class="mb-2">Conexiones multi-base de datos</h2>
    <p class="text-secondary mb-0">MySQL principal puede cambiar de base de datos directamente. Las conexiones secundarias requieren tu codigo TOTP y credenciales propias para poder activarlas.</p>
</section>

<?php if (!empty($viewData['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars((string) $viewData['flash']['level'], ENT_QUOTES, 'UTF-8'); ?> py-2">
        <?= htmlspecialchars((string) $viewData['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="row g-3">
    <?php foreach ($viewData['connections'] as $key => $connection): ?>
        <div class="col-12 col-md-6 col-xl-3">
            <form method="POST" action="index.php?page=select-connection" class="h-100">
                <?php $isSelected = $viewData['selected'] === $key; ?>
                <?php $canResubmitSelected = $key === 'mysql'; ?>
                <?php $saved = $viewData['credentialState'][$key] ?? []; ?>
                <input type="hidden" name="connection" value="<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="connection-card h-100 <?= $isSelected ? 'selected' : ''; ?>">
                    <h3><?= htmlspecialchars((string) $connection['label'], ENT_QUOTES, 'UTF-8'); ?></h3>

                    <?php if ($key === 'mysql'): ?>
                        <div class="secondary-connection-form mt-3">
                            <label class="form-label small mb-1" for="database_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Base de datos</label>
                            <input type="text" class="form-control form-control-sm mb-2" id="database_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="database" value="<?= htmlspecialchars((string) ($saved['database'] ?? $connection['database']), ENT_QUOTES, 'UTF-8'); ?>" required>
                            <div class="form-text mt-2">Puedes cambiar la base activa sin volver a editar la configuracion general.</div>
                        </div>
                    <?php else: ?>
                        <div class="secondary-connection-form mt-3">
                            <label class="form-label small mb-1" for="totp_code_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Codigo Authenticator</label>
                            <input type="text" class="form-control form-control-sm mb-2" id="totp_code_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="totp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>

                            <label class="form-label small mb-1" for="host_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Host</label>
                            <input type="text" class="form-control form-control-sm mb-2" id="host_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="host" value="<?= htmlspecialchars((string) ($saved['host'] ?? $connection['host']), ENT_QUOTES, 'UTF-8'); ?>" required>

                            <label class="form-label small mb-1" for="port_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Puerto</label>
                            <input type="number" class="form-control form-control-sm mb-2" id="port_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="port" value="<?= htmlspecialchars((string) ($saved['port'] ?? $connection['port']), ENT_QUOTES, 'UTF-8'); ?>" required>

                            <label class="form-label small mb-1" for="database_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Base de datos</label>
                            <input type="text" class="form-control form-control-sm mb-2" id="database_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="database" value="<?= htmlspecialchars((string) ($saved['database'] ?? $connection['database']), ENT_QUOTES, 'UTF-8'); ?>" required>

                            <label class="form-label small mb-1" for="username_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Usuario BD</label>
                            <input type="text" class="form-control form-control-sm mb-2" id="username_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="username" value="<?= htmlspecialchars((string) ($saved['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>

                            <label class="form-label small mb-1" for="password_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>">Contrasena BD</label>
                            <input type="password" class="form-control form-control-sm" id="password_<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" name="password" required>
                            <div class="form-text mt-2">Puedes cambiar host, puerto y base para apuntar a un servidor local o en la nube.</div>
                        </div>
                    <?php endif; ?>

                    <button
                        type="submit"
                        class="btn w-100 mt-3 <?= $isSelected ? 'btn-active-connection' : 'btn-outline-primary'; ?>"
                        <?= ($isSelected && !$canResubmitSelected) ? 'disabled aria-disabled="true"' : ''; ?>
                    >
                        <?=
                            $isSelected && !$canResubmitSelected
                                ? 'Conexion activa'
                                : ($key === 'mysql' ? 'Conectar MySQL' : 'Verificar y conectar');
                        ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>

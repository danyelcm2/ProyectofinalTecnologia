<?php

declare(strict_types=1);

class ConnectionController
{
    public function index(): void
    {
        $connections = db_connection_options();
        $selected = db_selected_key();
        $flash = $_SESSION['connection_flash'] ?? null;
        unset($_SESSION['connection_flash']);

        $viewData = [
            'title' => 'Seleccionar base de datos',
            'active' => 'connections',
            'user' => $_SESSION['user'] ?? ['name' => 'Usuario'],
            'connections' => $connections,
            'selected' => $selected,
            'pendingSelection' => $_SESSION['pending_db_connection'] ?? '',
            'flash' => $flash,
        ];

        require __DIR__ . '/../views/connections/index.php';
    }

    public function select(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=connections');
            exit;
        }

        $selected = (string) ($_POST['connection'] ?? '');
        $connections = db_connection_options();

        if (!array_key_exists($selected, $connections)) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'La conexion solicitada no existe.'];
            header('Location: index.php?page=connections');
            exit;
        }

        try {
            $meta = db_connection_meta($selected);
            if ($meta === null) {
                throw new InvalidArgumentException('Conexion invalida.');
            }

            db_validate_runtime_driver($selected, $meta);
            db_build_runtime_pdo($selected, $meta);

            $_SESSION['pending_db_connection'] = $selected;
            $_SESSION['db_2fa_verified'] = false;

            header('Location: index.php?page=verify-2fa');
            exit;
        } catch (RuntimeException $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'Esta conexion requiere un driver PDO que no esta instalado en PHP.'];
        } catch (Throwable $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'No fue posible validar la base seleccionada con la configuracion actual del sistema.'];
        }

        header('Location: index.php?page=connections');
        exit;
    }
}

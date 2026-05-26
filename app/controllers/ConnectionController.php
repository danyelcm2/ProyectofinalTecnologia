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
            'sqlitePath' => (string) (db_connection_credentials_view('sqlite')['database'] ?? ''),
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

            $runtimeMeta = $meta;
            if (($meta['driver'] ?? '') === 'sqlite') {
                $sqlitePath = trim((string) ($_POST['sqlite_path'] ?? ''));
                if ($sqlitePath === '') {
                    throw new InvalidArgumentException('Debes indicar la ruta del archivo SQLite (.db).');
                }

                $runtimeMeta['database'] = $sqlitePath;
                db_store_connection_credentials($selected, ['database' => $sqlitePath]);
            }

            db_validate_runtime_driver($selected, $runtimeMeta);
            db_build_runtime_pdo($selected, $runtimeMeta);

            $_SESSION['db_connection'] = $selected;
            $_SESSION['connection_flash'] = ['level' => 'success', 'message' => 'Conexión actualizada correctamente'];

            header('Location: index.php?page=dashboard');
            exit;
        } catch (InvalidArgumentException $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => $error->getMessage()];
        } catch (RuntimeException $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'Esta conexion requiere un driver PDO que no esta instalado en PHP.'];
        } catch (Throwable $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'No fue posible validar la base seleccionada con la configuracion actual del sistema.'];
        }

        header('Location: index.php?page=connections');
        exit;
    }
}

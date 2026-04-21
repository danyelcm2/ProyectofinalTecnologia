<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../support/Totp.php';

class ConnectionController
{
    public function index(): void
    {
        $connections = db_connection_options();
        $selected = db_selected_key();
        $flash = $_SESSION['connection_flash'] ?? null;
        unset($_SESSION['connection_flash']);

        $credentialState = [];
        foreach ($connections as $key => $_connection) {
            $credentialState[$key] = db_connection_credentials_view($key);
        }

        $viewData = [
            'title' => 'Conexiones',
            'active' => 'connections',
            'user' => $_SESSION['user'] ?? ['name' => 'Usuario'],
            'connections' => $connections,
            'selected' => $selected,
            'credentialState' => $credentialState,
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

        if (!db_connection_requires_verification($selected)) {
            $_SESSION['db_connection'] = $selected;
            $_SESSION['connection_flash'] = ['level' => 'success', 'message' => 'Conexion principal activada correctamente.'];
            header('Location: index.php?page=connections');
            exit;
        }

        $totpCode = trim((string) ($_POST['totp_code'] ?? ''));
        $host = trim((string) ($_POST['host'] ?? ''));
        $port = trim((string) ($_POST['port'] ?? ''));
        $database = trim((string) ($_POST['database'] ?? ''));
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!preg_match('/^[0-9]{6}$/', $totpCode)) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'Debes ingresar un codigo valido de Google Authenticator.'];
            header('Location: index.php?page=connections');
            exit;
        }

        if ($host === '' || $port === '' || $database === '' || $username === '' || $password === '') {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'Completa host, puerto, base de datos, usuario y contrasena para la conexion secundaria.'];
            header('Location: index.php?page=connections');
            exit;
        }

        $userModel = new UserModel();
        $sessionUser = $_SESSION['user'] ?? [];
        $userEmail = (string) ($sessionUser['email'] ?? '');
        $user = $userEmail !== '' ? $userModel->findByEmail($userEmail) : null;

        if (!$user || empty($user['two_factor_secret']) || !Totp::verify($user['two_factor_secret'], $totpCode)) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'El codigo de autenticacion no coincide con tu usuario actual.'];
            header('Location: index.php?page=connections');
            exit;
        }

        $runtimeMeta = $connections[$selected];
        $runtimeMeta['host'] = $host;
        $runtimeMeta['port'] = (int) $port;
        $runtimeMeta['database'] = $database;
        $runtimeMeta['username'] = $username;
        $runtimeMeta['password'] = $password;

        try {
            db_validate_runtime_driver($selected, $runtimeMeta);
            db_build_runtime_pdo($selected, $runtimeMeta);

            db_store_connection_credentials($selected, [
                'host' => $host,
                'port' => (int) $port,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'charset' => $runtimeMeta['charset'],
            ]);

            $_SESSION['db_connection'] = $selected;
            $_SESSION['connection_flash'] = ['level' => 'success', 'message' => 'Conexion verificada y activada correctamente.'];
        } catch (RuntimeException $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'Esta conexion requiere un driver PDO que no esta instalado en PHP.'];
        } catch (Throwable $error) {
            $_SESSION['connection_flash'] = ['level' => 'danger', 'message' => 'No fue posible conectar con esas credenciales. Revisa el codigo, host, usuario y contrasena.'];
        }

        header('Location: index.php?page=connections');
        exit;
    }
}

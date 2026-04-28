<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../support/Totp.php';

class AuthController
{
    public function login(): void
    {
        if (!empty($_SESSION['is_authenticated'])) {
            header('Location: index.php?page=connections');
            exit;
        }

        $error = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '';
            $password = (string) ($_POST['password'] ?? '');

            if (!$email || strlen($password) < 8) {
                $error = 'Credenciales invalidas. Verifica email y password.';
            } else {
                $userModel = new UserModel();
                $user = $userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $error = 'Usuario o clave incorrectos.';
                } else {
                    $secret = $user['two_factor_secret'];
                    $requiresSetup = $secret === '';

                    if ($requiresSetup) {
                        $secret = Totp::generateSecret();
                        $userModel->storeTwoFactorSecret($user['id'], $secret);
                    }

                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                    ];
                    $_SESSION['is_authenticated'] = true;
                    $_SESSION['two_factor_secret'] = $secret;
                    $_SESSION['requires_2fa_setup'] = $requiresSetup;
                    $_SESSION['db_2fa_verified'] = false;

                    unset($_SESSION['pending_db_connection'], $_SESSION['db_connection']);

                    header('Location: index.php?page=connections');
                    exit;
                }
            }
        }

        $viewData = [
            'title' => 'Login seguro',
            'error' => $error,
            'email' => $email,
        ];

        require __DIR__ . '/../views/auth/login.php';
    }

    public function verifyTwoFactor(): void
    {
        if (empty($_SESSION['is_authenticated'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if (empty($_SESSION['pending_db_connection'])) {
            header('Location: index.php?page=connections');
            exit;
        }

        $error = null;
        $sessionUser = $_SESSION['user'] ?? [];
        $secret = (string) ($_SESSION['two_factor_secret'] ?? '');

        if ($secret === '' && !empty($sessionUser['email'])) {
            $userModel = new UserModel();
            $user = $userModel->findByEmail((string) $sessionUser['email']);
            $secret = (string) ($user['two_factor_secret'] ?? '');
            $_SESSION['two_factor_secret'] = $secret;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim((string) ($_POST['code'] ?? ''));

            if ($secret === '') {
                $error = 'No se encontro la configuracion de autenticacion para este usuario.';
            } elseif (!preg_match('/^[0-9]{6}$/', $code)) {
                $error = 'Ingresa un codigo de 6 digitos.';
            } elseif (!Totp::verify($secret, $code)) {
                $error = 'Codigo incorrecto.';
            } else {
                $_SESSION['db_connection'] = (string) $_SESSION['pending_db_connection'];
                $_SESSION['db_2fa_verified'] = true;
                $_SESSION['requires_2fa_setup'] = false;
                unset($_SESSION['pending_db_connection']);

                header('Location: index.php?page=dashboard');
                exit;
            }
        }

        $issuer = app_config()['two_fa_issuer'];
        $requiresSetup = !empty($_SESSION['requires_2fa_setup']);
        $otpauthUri = $requiresSetup && $secret !== '' ? Totp::provisioningUri($issuer, (string) ($sessionUser['email'] ?? ''), $secret) : '';
        $qrCodeUrl = $otpauthUri !== '' ? Totp::qrImageUrl($otpauthUri) : '';
        $connection = db_connection_meta((string) $_SESSION['pending_db_connection']);

        $viewData = [
            'title' => 'Confirmar acceso a base de datos',
            'error' => $error,
            'manualKey' => $requiresSetup ? $secret : '',
            'otpauthUri' => $otpauthUri,
            'qrCodeUrl' => $qrCodeUrl,
            'issuer' => $issuer,
            'email' => (string) ($sessionUser['email'] ?? ''),
            'requiresSetup' => $requiresSetup,
            'connectionLabel' => (string) ($connection['label'] ?? ''),
        ];

        require __DIR__ . '/../views/auth/twofa.php';
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}

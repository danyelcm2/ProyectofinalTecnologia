<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../support/Totp.php';

class AuthController
{
    public function login(): void
    {
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

                    $_SESSION['pending_user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'two_factor_secret' => $secret,
                        'requires_2fa_setup' => $requiresSetup,
                    ];

                    header('Location: index.php?page=verify-2fa');
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
        if (empty($_SESSION['pending_user'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $error = null;
        $pendingUser = $_SESSION['pending_user'];
        $secret = (string) ($pendingUser['two_factor_secret'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim((string) ($_POST['code'] ?? ''));

            if ($secret === '') {
                $error = 'No se encontro la configuracion de autenticacion para este usuario.';
            } elseif (!preg_match('/^[0-9]{6}$/', $code)) {
                $error = 'Ingresa un codigo de 6 digitos.';
            } elseif (!Totp::verify($secret, $code)) {
                $error = 'Codigo incorrecto.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user'] = $_SESSION['pending_user'];
                $_SESSION['is_authenticated'] = true;
                $_SESSION['db_connection'] = $_SESSION['db_connection'] ?? app_config()['default_connection'];

                unset($_SESSION['pending_user']);

                header('Location: index.php?page=dashboard');
                exit;
            }
        }

        $issuer = app_config()['two_fa_issuer'];
        $requiresSetup = !empty($pendingUser['requires_2fa_setup']);
        $otpauthUri = $requiresSetup && $secret !== '' ? Totp::provisioningUri($issuer, (string) $pendingUser['email'], $secret) : '';
        $qrCodeUrl = $otpauthUri !== '' ? Totp::qrImageUrl($otpauthUri) : '';

        $viewData = [
            'title' => 'Verificacion 2FA',
            'error' => $error,
            'manualKey' => $requiresSetup ? $secret : '',
            'otpauthUri' => $otpauthUri,
            'qrCodeUrl' => $qrCodeUrl,
            'issuer' => $issuer,
            'email' => (string) $pendingUser['email'],
            'requiresSetup' => $requiresSetup,
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

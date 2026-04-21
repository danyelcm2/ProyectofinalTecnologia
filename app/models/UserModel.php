<?php

declare(strict_types=1);

class UserModel
{
    public function findByEmail(string $email): ?array
    {
        $pdo = db_auth_connect();
        $stmt = $pdo->prepare('SELECT id, nombre, email, password, two_factor_secret, activo FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || (isset($user['activo']) && (int) $user['activo'] !== 1)) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['nombre'],
            'email' => $user['email'],
            'password_hash' => $user['password'],
            'two_factor_secret' => (string) ($user['two_factor_secret'] ?? ''),
        ];
    }

    public function storeTwoFactorSecret(int $userId, string $secret): void
    {
        $pdo = db_auth_connect();
        $stmt = $pdo->prepare('UPDATE usuarios SET two_factor_secret = :secret WHERE id = :id LIMIT 1');
        $stmt->execute([
            'secret' => $secret,
            'id' => $userId,
        ]);
    }
}

<?php

declare(strict_types=1);

final class Totp
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function generateSecret(int $length = 32): string
    {
        $secret = '';

        while (strlen($secret) < $length) {
            $random = random_bytes($length);

            for ($i = 0, $max = strlen($random); $i < $max && strlen($secret) < $length; $i++) {
                $secret .= self::BASE32_ALPHABET[ord($random[$i]) % 32];
            }
        }

        return $secret;
    }

    public static function provisioningUri(string $issuer, string $accountName, string $secret): string
    {
        $label = rawurlencode($issuer . ':' . $accountName);
        $issuerParam = rawurlencode($issuer);
        $secretParam = rawurlencode($secret);

        return "otpauth://totp/{$label}?secret={$secretParam}&issuer={$issuerParam}&algorithm=SHA1&digits=6&period=30";
    }

    public static function qrImageUrl(string $otpauthUri, int $size = 220): string
    {
        $safeSize = max(120, min($size, 400));
        $encoded = rawurlencode($otpauthUri);

        return "https://api.qrserver.com/v1/create-qr-code/?size={$safeSize}x{$safeSize}&data={$encoded}";
    }

    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        if (!preg_match('/^[0-9]{6}$/', $code)) {
            return false;
        }

        $timestamp = (int) floor(time() / 30);
        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals(self::codeForCounter($secret, $timestamp + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    private static function codeForCounter(string $secret, int $counter): string
    {
        $secretKey = self::base32Decode($secret);
        $binaryCounter = pack('N2', 0, $counter);
        $hash = hash_hmac('sha1', $binaryCounter, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $chunk = substr($hash, $offset, 4);
        $value = unpack('N', $chunk)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $value): string
    {
        $value = strtoupper(preg_replace('/[^A-Z2-7]/', '', $value) ?? '');
        $binary = '';
        $bits = '';

        for ($i = 0, $max = strlen($value); $i < $max; $i++) {
            $position = strpos(self::BASE32_ALPHABET, $value[$i]);
            if ($position === false) {
                continue;
            }

            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        for ($i = 0, $max = strlen($bits); $i + 8 <= $max; $i += 8) {
            $binary .= chr(bindec(substr($bits, $i, 8)));
        }

        return $binary;
    }
}

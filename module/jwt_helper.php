<?php
require_once '../vendor/autoload.php';
use \Firebase\JWT\JWT;

class JWTHelper {
    private static $key = "your-secret-key"; // Ersetze "your-secret-key" durch deinen tatsächlichen Schlüssel
    private static $algorithm = 'HS256';

    // Token erstellen
    public static function createToken($userId) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token ist 1 Stunde gültig
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'userId' => $userId,
        ];

        return JWT::encode($payload, self::$key, self::$algorithm);
    }

    // Token validieren
    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key(self::$key, self::$algorithm));
            return $decoded; // Gültiges Token zurückgeben
        } catch (Exception $e) {
            return null; // Ungültiges Token
        }
    }
}

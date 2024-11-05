<?php
session_start();
require_once 'jwt_helper.php'; // JWT-Hilfsdatei einbinden

use \Firebase\JWT\JWT;

$key = "your-secret-key";

if (!isset($_SESSION['jwt'])) {
    die("Nicht autorisiert");
}

try {
    $decoded = JWTHelper::validateToken($_SESSION['jwt']); // Token validieren
    if (!$decoded) {
        die("Token ungültig oder abgelaufen");
    }
} catch (Exception $e) {
    die("Token ungültig: " . $e->getMessage());
}
?>

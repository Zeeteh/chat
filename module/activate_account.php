<?php
session_start();
require_once '../db_connection.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo "Ungültiges Token.";
    exit;
}

// Überprüfen, ob der Token existiert und gültig ist
$stmt = $conn->prepare("SELECT user_id, expiry FROM account_verifications WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ungültiger oder abgelaufener Token.";
    exit;
}

$data = $result->fetch_assoc();
$userId = $data['user_id'];
$expiry = $data['expiry'];

if (time() > $expiry) {
    echo "Der Aktivierungslink ist abgelaufen.";
    exit;
}

// Konto als aktiviert markieren
$stmt = $conn->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

// Token entfernen
$stmt = $conn->prepare("DELETE FROM account_verifications WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

echo "Ihr Konto wurde erfolgreich aktiviert! Sie können sich jetzt einloggen.";
?>

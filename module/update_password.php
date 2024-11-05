<?php
session_start();
require_once '../db_connection.php';

$userId = $_POST['user_id'] ?? '';
$token = $_POST['token'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Überprüfen, ob alle Felder ausgefüllt sind
if (empty($userId) || empty($token) || empty($newPassword) || empty($confirmPassword)) {
    echo "Alle Felder müssen ausgefüllt sein.";
    exit;
}

// Überprüfen, ob das Passwort und das Bestätigungspasswort übereinstimmen
if ($newPassword !== $confirmPassword) {
    echo "Die Passwörter stimmen nicht überein.";
    exit;
}

// Überprüfen, ob das Token noch gültig ist, bevor es entfernt wird
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND user_id = ? AND expiry > ?");
$current_time = time();
$stmt->bind_param("sii", $token, $userId, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ungültiges oder abgelaufenes Token.";
    exit;
}

// Neues Passwort hashen und in der Datenbank speichern
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashedPassword, $userId);
$stmt->execute();

// Löschen des Tokens, nachdem es verwendet wurde
$stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

echo "Passwort erfolgreich zurückgesetzt. Sie können sich jetzt <a href='../login.html'>einloggen</a>.";
?>

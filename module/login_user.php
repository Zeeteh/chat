<?php
session_start();
require_once '../db_connection.php';
require_once 'jwt_helper.php';

// hCaptcha-Konfiguration
$captchaRequired = false;
$hcaptcha_secret = 'fb9ebab4-23a0-4c8a-801a-6df4a1a0f9e0';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Überprüfung, ob alle Pflichtfelder ausgefüllt sind
if (empty($username) || empty($password)) {
    echo json_encode(["message" => "Bitte alle Felder ausfüllen"]);
    exit;
}

// Überprüfung des Benutzers in der Datenbank
$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $jwt = JWTHelper::createToken($user['id']);
        $_SESSION['jwt'] = $jwt;
        echo json_encode(["message" => "Login erfolgreich"]);
    } else {
        echo json_encode(["message" => "Falsches Passwort"]);
    }
} else {
    echo json_encode(["message" => "Benutzername nicht gefunden"]);
}

// Statement und Verbindung schließen
$stmt->close();
$conn->close();
?>

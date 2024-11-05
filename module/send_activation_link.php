<?php
session_start();
require_once '../db_connection.php';
$config = include('../config.php');

// Erfasste Registrierungsinformationen
$email = $_POST['email'] ?? '';

// Überprüfen, ob E-Mail in der Datenbank existiert
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["message" => "E-Mail-Adresse nicht gefunden."]);
    exit;
}

$user = $result->fetch_assoc();
$userId = $user['id'];

// Überprüfen, ob die E-Mail-Bestätigung aktiviert ist
if ($config['email_verification_enabled']) {
    // Generiere Aktivierungslink und Token
    $token = bin2hex(random_bytes(32));
    $expiry = time() + 86400; // 24 Stunden gültig

    // Token speichern
    $stmt = $conn->prepare("INSERT INTO account_verifications (user_id, token, expiry) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $userId, $token, $expiry);
    $stmt->execute();

    // Aktivierungslink generieren
    $activationLink = "https://deineseite.de/module/activate_account.php?token=$token";
    
    // E-Mail senden (wenn aktiv)
    mail($email, "Konto aktivieren", "Klicken Sie auf den folgenden Link, um Ihr Konto zu aktivieren: $activationLink");

    echo json_encode(["message" => "Ein Aktivierungslink wurde an Ihre E-Mail-Adresse gesendet."]);
} else {
    // E-Mail-Bestätigung simulieren
    echo json_encode(["message" => "Registrierung erfolgreich! Aktivierung nicht erforderlich."]);
}
?>

<?php
session_start();
require_once '../db_connection.php';

$email = $_POST['email'] ?? '';

if (empty($email)) {
    $message = "Bitte geben Sie Ihre E-Mail-Adresse ein.";
    $status = "error";
} else {
    // Überprüfen, ob die E-Mail in der Datenbank existiert
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "E-Mail-Adresse nicht gefunden.";
        $status = "error";
    } else {
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Erstellen eines einzigartigen Token
        $token = bin2hex(random_bytes(32));
        $expiry = time() + 86400; // Token ist für 24 Stunden gültig

        // Token und Ablaufzeit in der Datenbank speichern
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $userId, $token, $expiry);
        $stmt->execute();

        // Link zum Zurücksetzen des Passworts erstellen
        $resetLink = "https://deineseite.de/module/reset_password.php?token=$token";

        // E-Mail senden
        mail($email, "Passwort zurücksetzen", "Klicken Sie auf den folgenden Link, um Ihr Passwort zurückzusetzen: $resetLink");

        $message = "Ein Link zum Zurücksetzen des Passworts wurde an Ihre E-Mail-Adresse gesendet.";
        $status = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort Zurücksetzen - Nachricht</title>
    <link rel="stylesheet" href="../css/reset_password.css">
</head>
<body>
    <main>
        <section class="reset-password-section">
            <h2>Passwort Zurücksetzen</h2>
            <div class="message <?php echo $status; ?>">
                <?php echo $message; ?>
            </div>
            <a href="../login.html" class="button">Zurück zum Login</a>
        </section>
    </main>
</body>
</html>

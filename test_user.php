<?php
// Einbindung der Datenbankverbindung
require_once 'db_connection.php';

// Überprüfung, ob das Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Beispiel-Daten für die Registrierung
    $username = 'TestUser';
    $email = 'testuser@example.com';
    $password = password_hash('testpassword', PASSWORD_DEFAULT); // Passwort-Hashing
    $name = 'Max Mustermann';
    $city = 'Berlin';
    $country = 'Deutschland';
    $age = 30;
    $hcaptcha_response = 'test_captcha_response';

    // Vorbereitung und Ausführung des SQL-Statements
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, name, city, country, age, hcaptcha_response) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $username, $email, $password, $name, $city, $country, $age, $hcaptcha_response);

    // Prüfung, ob die Einfügung erfolgreich war
    if ($stmt->execute()) {
        $message = "Die Beispiel-Registrierung wurde erfolgreich in die Datenbank eingefügt!";
    } else {
        $message = "Fehler beim Einfügen: " . $stmt->error;
    }

    // Schließen des Statements
    $stmt->close();
}

// Schließen der Datenbankverbindung
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test-Insert in Datenbank</title>
</head>
<body>
    <h1>Test-Insert in Datenbank</h1>
    
    <!-- Meldung anzeigen -->
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

    <!-- Formular mit Button für das Test-Insert -->
    <form method="post" action="">
        <button type="submit">Beispiel-Registrierung in die Datenbank einfügen</button>
    </form>
</body>
</html>

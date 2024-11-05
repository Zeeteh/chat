<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Startet die Session, um Sitzungsdaten wie das CSRF-Token oder JWT-Token zu speichern

require_once '../db_connection.php'; // Stellt die Datenbankverbindung her
require_once 'jwt_helper.php'; // Hilfsdatei für JWT-Funktionen einbinden, um Tokens zu erstellen und zu verifizieren

// Einstellung für die hCaptcha-Verifizierung
$captchaRequired = false; // Auf "false" setzen, um die Captcha-Überprüfung zu deaktivieren

// hCaptcha-Secret-Key (dieser Schlüssel wird zur Verifizierung von hCaptcha-Antworten verwendet)
$hcaptcha_secret = 'DEIN_SECRET_KEY'; // Ersetze diesen Wert mit deinem tatsächlichen hCaptcha-Secret-Key

// Erfassung der Benutzereingaben aus dem Formular, die über den POST-Request übermittelt wurden
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$hcaptcha_response = $_POST['h-captcha-response'] ?? '';
$name = $_POST['name'] ?? '';
$city = $_POST['city'] ?? '';
$country = $_POST['country'] ?? '';
$age = $_POST['age'] ?? '';

// Überprüfen, ob alle Pflichtfelder ausgefüllt wurden
if (empty($username) || empty($email) || empty($password) || empty($name) || empty($city) || empty($country) || empty($age)) {
    echo json_encode(["message" => "Bitte alle Felder ausfüllen"]);
    exit;
}

// hCaptcha-Überprüfung (nur wenn aktiviert)
if ($captchaRequired) {
    $hcaptcha_verify_url = "https://hcaptcha.com/siteverify";
    $hcaptcha_data = [
        'secret' => $hcaptcha_secret,
        'response' => $hcaptcha_response
    ];
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($hcaptcha_data)
        ]
    ];
    $context = stream_context_create($options);
    $hcaptcha_verify_response = file_get_contents($hcaptcha_verify_url, false, $context);
    $hcaptcha_result = json_decode($hcaptcha_verify_response);

    // Überprüfen, ob die hCaptcha-Verifizierung erfolgreich war
    if (!$hcaptcha_result->success) {
        echo json_encode(["message" => "hCaptcha-Überprüfung fehlgeschlagen"]);
        exit;
    }
}

// Passwort-Hashing für die sichere Speicherung des Passworts
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL-Statement vorbereiten und Eingabewerte binden
$stmt = $conn->prepare("INSERT INTO users (username, email, password, name, city, country, age, hcaptcha_response) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssis", $username, $email, $hashed_password, $name, $city, $country, $age, $hcaptcha_response);

// Ausführen des Statements und Fehlerprüfung
if ($stmt->execute()) {
    $userId = $conn->insert_id; // Neu erstellte Benutzer-ID abrufen
    
    // JWT-Token für den Benutzer erstellen
    $jwt = JWTHelper::createToken($userId);
    
    // JWT-Token in der Session speichern
    $_SESSION['jwt'] = $jwt;

    // Weiterleitung zur Aktivierungsseite
    header("Location: ../activation.html");
    exit();
} else {
    echo json_encode(["message" => "Registrierung fehlgeschlagen", "error" => $stmt->error]);
}

// Prepared Statement schließen und Ressourcen freigeben
$stmt->close();
$conn->close(); // Datenbankverbindung schließen
?>

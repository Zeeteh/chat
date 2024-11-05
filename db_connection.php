<?php
// Konfigurationsdatei laden
$config = require_once __DIR__ . '/config.php';

// Verbindung zur MySQL-Datenbank herstellen
$conn = new mysqli(
    $config['host'], 
    $config['username'], 
    $config['password'], 
    $config['database']
);

// Überprüfen, ob die Verbindung erfolgreich hergestellt wurde
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}
?>

<?php
require_once '../db_connection.php';

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
} else {
    echo "Verbindung zur Datenbank erfolgreich hergestellt!";
    // Datenbanknamen ausgeben zur Überprüfung
    $result = $conn->query("SELECT DATABASE()");
    $row = $result->fetch_row();
    echo "Verbundene Datenbank: " . $row[0];
}

// Überprüfen, ob alle erforderlichen Variablen definiert sind
function checkDatabaseVariables() {
    $requiredVariables = ['DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'];
    $missingVariables = [];
    
    foreach ($requiredVariables as $var) {
        if (!defined($var)) {
            $missingVariables[] = $var;
        }
    }
    
    if (!empty($missingVariables)) {
        return "Fehlende Variablen: " . implode(", ", $missingVariables);
    } else {
        return "Alle notwendigen Variablen sind definiert!";
    }
}

echo "<h2>Datenbank-Verbindungstest</h2>";
echo "<p>" . testDatabaseConnection($conn) . "</p>";
echo "<p>" . checkDatabaseVariables() . "</p>";
?>

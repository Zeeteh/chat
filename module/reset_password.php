<?php
session_start();
require_once '../db_connection.php';

$token = $_GET['token'] ?? '';

// Überprüfen, ob der Token gültig ist und nicht abgelaufen ist
$stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expiry > ?");
$current_time = time();
$stmt->bind_param("si", $token, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Der Link zum Zurücksetzen des Passworts ist ungültig oder abgelaufen.";
    exit;
}

$user = $result->fetch_assoc();
$userId = $user['user_id'];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort zurücksetzen</title>
    <link rel="stylesheet" href="../css/reset_password.css">
</head>
<body>
    <main>
        <section class="reset-password-section">
            <h2>Neues Passwort festlegen</h2>
            <form action="update_password.php" method="post">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label for="new_password">Neues Passwort:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Passwort bestätigen:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Passwort aktualisieren</button>
            </form>
        </section>
    </main>
</body>
</html>

<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$winner_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['draw_winner'])) {
    $stmt = $conn->prepare("SELECT id, email, favorite_number FROM users ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $winner = $result->fetch_assoc();
        $stmt_update = $conn->prepare("UPDATE users SET is_winner = 1 WHERE id = ?");
        $stmt_update->bind_param("i", $winner['id']);
        if ($stmt_update->execute()) {
            $winner_message = "Zwycięzca: " . htmlspecialchars($winner['email']) . " (Ulubiona liczba: " . $winner['favorite_number'] . ")";
        }
    } else {
        $winner_message = "Brak uczestników w loterii.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administratora</title>
</head>
<body>
    <h1>Panel administratora</h1>
    <?php if (!empty($winner_message)) echo "<p style='color:green;'>$winner_message</p>"; ?>
    <form method="post" action="admin_panel.php">
        <button type="submit" name="draw_winner">Wylosuj zwycięzcę</button>
    </form>
    <a href="logout.php">Wyloguj się</a>
</body>
<button style="margin-top: 50px;"onclick="window.location.href='index.php'">Powrót na główną</button>
</html>

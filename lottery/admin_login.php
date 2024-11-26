<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (hash('sha256', $password) === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin_panel.php');
            exit();
        }
    }

    $error_message = "Nieprawidłowy login lub hasło.";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie administratora</title>
</head>
<body>
    <?php if (!empty($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
    <form method="post" action="admin_login.php">
        <label for="username">user:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Log In</button>
    </form>
    <button style="margin-top: 50px;"onclick="window.location.href='index.php'">Powrót na główną</button>

</body>
</html>

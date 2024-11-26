<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$winner_message = null;
$clear_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['draw_winner'])) {
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

    if (isset($_POST['clear_table'])) {
        $stmt = $conn->prepare("DELETE FROM users");
        if ($stmt->execute()) {
            $clear_message = "Tabela uczestników została wyczyszczona.";
        } else {
            $clear_message = "Wystąpił błąd podczas czyszczenia tabeli.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT email, favorite_number, ip_address, is_winner FROM users ORDER BY is_winner DESC, id ASC");
$stmt->execute();
$participants = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administratora</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
        }
        .winner-row {
            background-color: #d4edda;
        }
        .message {
            color: green;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Panel administratora</h1>
    <?php if (!empty($winner_message)) echo "<p class='message'>$winner_message</p>"; ?>
    <?php if (!empty($clear_message)) echo "<p class='error-message'>$clear_message</p>"; ?>

    <form method="post" action="admin_panel.php">
        <button type="submit" name="draw_winner">Wylosuj zwycięzcę</button>
    </form>
    <form method="post" action="admin_panel.php" onsubmit="return confirm('Czy na pewno chcesz wyczyścić tabelę uczestników?')">
        <button type="submit" name="clear_table" style="background-color: red; color: white;">Wyczyść tabelę uczestników</button>
    </form>
    <a href="logout.php">Wyloguj się</a>

    <h2>Lista uczestników</h2>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Ulubiona liczba</th>
                <th>Adres IP</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $participants->fetch_assoc()): ?>
                <tr class="<?= $row['is_winner'] ? 'winner-row' : '' ?>">
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['favorite_number']) ?></td>
                    <td><?= htmlspecialchars($row['ip_address']) ?></td>
                    <td><?= $row['is_winner'] ? 'Zwycięzca' : 'Uczestnik' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button style="margin-top: 50px;" onclick="window.location.href='index.php'">Powrót na główną</button>
</body>
</html>

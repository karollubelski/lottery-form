<?php
include 'lottery.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

$captcha_num1 = rand(1, 50);
$captcha_num2 = rand(1, 50);
$_SESSION['captcha_sum'] = $captcha_num1 + $captcha_num2;

$winners = [];
$stmt = $conn->prepare("SELECT email, favorite_number FROM users WHERE is_winner = 1");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $winners[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loteria Online</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        .winners-box {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            font-size: 14px;
        }
        .winners-box h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        .winner {
            margin-bottom: 8px;
        }
        .winner span {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="winners-box">
        <h2>Zwycięzcy:</h2>
        <?php if (!empty($winners)): ?>
            <?php foreach ($winners as $winner): ?>
                <div class="winner">
                    <span>Email:</span> <?= htmlspecialchars($winner['email']); ?><br>
                    <span>Ulubiona liczba:</span> <?= htmlspecialchars($winner['favorite_number']); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Brak zwycięzców.</p>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>Weź udział w loterii!</h1>
        <?php if (isset($success_message)) : ?>
            <div class="message"><?= htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)) : ?>
            <div class="message error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <label for="email">Twój email:</label>
            <input type="email" id="email" name="email" required>        
            <label for="favorite_number">Twoja ulubiona liczba:</label>
            <input type="text" id="favorite_number" name="favorite_number" required>
            
            <div class="captcha-row">
                <label for="captcha">Podaj sumę:</label>
                <span class="captcha-text"><?= $captcha_num1; ?> + <?= $captcha_num2; ?></span>
                <input type="number" id="captcha" name="captcha" required>
            </div>

            <button type="submit" name="submit">Zarejestruj się</button>
        </form>
    </div>

    <button style="position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;" onclick="window.location.href='admin_login.php'">Panel administratora</button>
</body>
</html>

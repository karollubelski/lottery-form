<?php
include 'lottery.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
$captcha_num1 = rand(1, 50);
$captcha_num2 = rand(1, 50);
$_SESSION['captcha_sum'] = $captcha_num1 + $captcha_num2;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loteria Online</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
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

<button style="margin-left: 50px;"
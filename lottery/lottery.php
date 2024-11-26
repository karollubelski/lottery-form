<?php
require 'config.php';
session_start();
$success_message = null;
$error_message = null;

function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$allowed_domains = [
    'gmail.com', 'googlemail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'live.com', 'icloud.com', 'protonmail.com', 'zoho.com', 
    'aol.com', 'yandex.com', 'mail.ru', 'wp.pl', 'interia.pl', 'o2.pl', 'onet.pl', 'tlen.pl', 'gazeta.pl', 'poczta.onet.pl', 'autograf.pl', 
    'buziaczek.pl', 'gmx.de', 'web.de', 'freenet.de', 't-online.de', 'orange.fr', 'sfr.fr', 'free.fr', 'laposte.net', 'btinternet.com', 
    'sky.com', 'talktalk.net', 'virginmedia.com', 'seznam.cz', 'post.cz', 'centrum.cz', 'zoznam.sk', 'bk.ru', 'inbox.ru', 'list.ru', 
    'hotmail.se', 'spray.se', 'live.dk', 'mail.dk', 'europe.com', 'posteo.de', 'hushmail.com'
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['favorite_number']) && isset($_POST['captcha'])) {
        $email = sanitizeInput($_POST['email']);
        $favorite_number = sanitizeInput($_POST['favorite_number']);
        $captcha = sanitizeInput($_POST['captcha']);
        $userIP = getUserIP();

        if (!isset($_SESSION['captcha_sum']) || $captcha != $_SESSION['captcha_sum']) {
            $error_message = "Nieprawidłowy wynik CAPTCHA.";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Nieprawidłowy format emaila.";
            } else {
                $domain = substr(strrchr($email, "@"), 1);
                if (!in_array($domain, $allowed_domains)) {
                    $error_message = "Email z domeny '$domain' nie jest akceptowany.";
                } elseif (!is_numeric($favorite_number) || $favorite_number < 1 || $favorite_number > 100) {
                    $error_message = "Ulubiona liczba musi być liczbą w zakresie 1-100.";
                } else {
                    if ($domain === 'gmail.com' || $domain === 'googlemail.com') {
                        $local_part = substr($email, 0, strpos($email, '@'));
                        $local_part = str_replace('.', '', $local_part);
                        $email_sanitized = $local_part . '@' . $domain;
                    } else {
                        $email_sanitized = $email;
                    }

                    $ip_check_stmt = $conn->prepare("SELECT id FROM users WHERE ip_address = ?");
                    $ip_check_stmt->bind_param("s", $userIP);
                    $ip_check_stmt->execute();
                    $ip_check_stmt->store_result();

                    if ($ip_check_stmt->num_rows > 0) {
                        $error_message = "Z tego adresu IP można zarejestrować tylko jeden e-mail.";
                    } else {
                        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->bind_param("s", $email_sanitized);
                        $stmt->execute();
                        $stmt->store_result();

                        if ($stmt->num_rows > 0) {
                            $error_message = "Email jest już zarejestrowany w loterii.";
                        } else {
                            $stmt = $conn->prepare("INSERT INTO users (email, favorite_number, ip_address) VALUES (?, ?, ?)");
                            $stmt->bind_param("sis", $email_sanitized, $favorite_number, $userIP);

                            if ($stmt->execute()) {
                                $success_message = "Pomyślnie zarejestrowano w loterii!";
                            } else {
                                $error_message = "Błąd podczas rejestracji.";
                            }
                        }
                        $stmt->close();
                    }
                    $ip_check_stmt->close();
                }
            }
        }
    } else {
        $error_message = "Wszystkie pola są wymagane.";
    }
}

// $conn->close();

?>

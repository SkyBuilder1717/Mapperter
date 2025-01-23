<?php
    require_once 'vendor/connect.php';
    require_once 'vendor/antinull.php';
    session_start();

    $token = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '';
    $token = mysqli_real_escape_string($connect, $token);

    $resultCode = mysqli_query($connect, "SELECT * FROM Recovery WHERE code='$token' AND expiration > NOW()");

    if (mysqli_num_rows($resultCode) == 0) {
        header("Location: /network/login");
        $_SESSION['message'] = 'Токен не найден или истёк.';
        exit();
    }
    $tokenInfo = mysqli_fetch_assoc($resultCode);
    $username = $tokenInfo['username'];
    $resultUser = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username'");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $password = md5($_POST['password']);
        mysqli_query($connect, "DELETE FROM Recovery WHERE code='$token'");
        mysqli_query($connect, "UPDATE Accounts SET password='$password' WHERE username='$username'");
        $_SESSION['message'] = 'Пароль был изменён!';
        header("Location: /network/login");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Сброс пароля</title>
        <style>
            body, html {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                background-image: url('/img/backgrounds/default.png');
                background-size: cover;
                background-repeat: repeat;
                background-position: center;
                font-family: Arial, sans-serif;
                color: white;
                text-shadow: 2px 4px 4px rgba(0, 0, 0, 0.85);
            }
            .header {
                color: white;
                text-align: left;
                padding: 15px;
                width: 100%;
                font-size: 24px;
                font-weight: bold;
            }
            form {
                padding: 100px 0;
                width: 100%;
                max-height: auto;
                text-align: center;
                font-size: 30px;
            }
            form input[type="text"] {
                width: 300px;
                height: 30px;
                font-size: 17px;
                border-radius: 5px;
                border: none;
            }
            form button {
                background-color: #0047ab;
                cursor: pointer;
                color: white;
                border-radius: 10px;
                font-size: 18px;
                border: none;
                width: 300px;
                height: 30px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Сброс пароля</h1>
        </div>
        <form action="" method="post">
            <label for="password">Новый пароль:</label><br>
            <input type="text" id="password" name="password" placeholder="Ваш новый пароль" minlength="8" required><br>
            <button type="submit">Изменить</button>
        </form>
    </body>
</html>
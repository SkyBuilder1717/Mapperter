<?php
    require_once 'vendor/connect.php';
    require_once 'vendor/antinull.php';
    session_start();

    $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
    $username = mysqli_real_escape_string($connect, $username);

    $result = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username'");
    $resultCode = mysqli_query($connect, "SELECT * FROM Codes WHERE username='$username'");
    if (mysqli_num_rows($resultCode) == 0) {
        header("Location: /network/login");
        exit();
    }
    $user = mysqli_fetch_assoc($result);
    if (!$user["state"] == "not_verified") {
        header("Location: /network/login");
        exit();
    }

    if (isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $enterCode = $_POST['code'];
        $code = mysqli_fetch_assoc($resultCode)["code"];
        if ($enterCode == $code) {
            mysqli_query($connect, "UPDATE Accounts SET state='user' WHERE username='$username'");
            $_SESSION['message'] = 'Аккаунт подтверждён! Можете входить!';
            header("Location: /network/login");
            exit();
        } else {
            $_SESSION['message'] = 'Неверный код.';
            header("Location: /network/login");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Подтверждение аккаунта</title>
        <style>
            body, html {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                background-image: url('/img/bg.png');
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
            p {
                text-align: center;                
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Подтверждение аккаунта</h1>
        </div>
        <form action="" method="post">
            <label for="code">Код:</label><br>
            <input type="text" id="code" name="code" placeholder="Код, отправленный на почту" minlength="4" maxlength="4" required><br>
            <button type="submit">Подтвердить</button>
        </form>
        <p>Не забудьте проверить вкладку "Входящее" и "Спам"!</p>
    </body>
</html>
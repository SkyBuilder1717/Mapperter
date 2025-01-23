<?php
    require_once 'vendor/connect.php';
    require_once 'vendor/antinull.php';
    require_once 'vendor/mailclass.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $email = mysqli_real_escape_string($connect, $email);
        $resultEmail = mysqli_query($connect, "SELECT * FROM Accounts WHERE email='$email'");

        if (mysqli_num_rows($resultEmail) > 0) {
            $user = mysqli_fetch_assoc($resultEmail);
            $username = $user["username"];
            $resultRec = mysqli_query($connect, "SELECT * FROM Recovery WHERE username='$username'");
            if (mysqli_num_rows($resultRec) > 0) {
                $_SESSION['message'] = 'Вы уже пытались сбросить пароль.';
                header("Location: /network/forgot.php");
                exit();
            } else {
                $_SESSION['message'] = 'Письмо было отправлено на вашу почту!';
                $code = generateString(50);
                $expiration = date("Y-m-d H:i:s", strtotime('+5 minutes'));
                $query = "INSERT INTO Recovery (username, code, expiration) VALUES ('$username', '$code', '$expiration')";

                $link = "http://mappercoder.yzz.me/network/password.php?code=" . $code;

                $mail = new SendMail('mappercoder@yandex.com', 'efkfcibrrtirlzlx', 'ssl://smtp.yandex.ru', 465, "UTF-8");
                $from = array("mappercoder", "mappercoder@yandex.com");
                $result =  $mail->send($email, 'Сброс пароля', "Ваша ссылка для сброса пароля на аккаунте @$username: $link<br><em>Ссылка будет действовать 5 минут!</em><br><strong>Никому не отправляйте, это может привести к последствиям!</strong>", $from);

                $recovery = mysqli_query($connect, $query);
            }
        } else {
            $_SESSION['message'] = 'Не удалось найти аккаунт с такой почтой.';
            header("Location: /network/forgot.php");
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
        <title>Забыли пароль?</title>
        <style>
            body {
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
            form input[type="email"] {
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
            <h1>Сбрасывание пароля</h1>
        </div>
        <form action="" method="post">
            <label for="email">Введите почту:</label><br>
            <input type="email" id="email" name="email" placeholder="Почта аккаунта" required><br>
            <button type="submit">Сбросить пароль</button>
            <?php session_start(); ?>
            <p><?php echo $_SESSION["message"]; ?></p>
            <?php $_SESSION["message"] = null; ?>
        </form>
    </body>
</html>
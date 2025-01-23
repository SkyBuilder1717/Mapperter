<?php
    require_once "connect.php";
    require_once 'mailclass.php';

    session_start();

    function isValid($value) {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    function get_ip()
    {
        $value = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $value = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $value = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $value = $_SERVER['REMOTE_ADDR'];
        }
    
        return $value;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = limitString($_POST['username'], 16);
        $email = $_POST['email'];
        if (!isValid($username)) {
            $_SESSION["message"] = "Логин может содержать только латинские буквы.";
            header("Location: /network/register");
            exit();
        }
        $name = base64_encode(limitString(removeEmojis($_POST['name'], 32)));
        $username = mysqli_real_escape_string($connect, $username);
        $password = md5($_POST['password']);

        $usernameResult = mysqli_query($connect, "SELECT * FROM Accounts WHERE username = '$username'");
        $emailResult = mysqli_query($connect, "SELECT * FROM Accounts WHERE email = '$email'");
        $email = mysqli_real_escape_string($connect, $email);

        if (mysqli_num_rows($usernameResult) > 0) {
            $_SESSION["message"] = "Логин уже занят.";
            header("Location: /network/register");
            exit();
        } elseif (mysqli_num_rows($emailResult) > 0) {
            $_SESSION["message"] = "Почта уже занята.";
            header("Location: /network/register");
            exit();
        } else {
            $code = rand(1000, 9999);
            $insertQuery = "INSERT INTO Accounts (name, username, email, state, description, password, ip) VALUES ('$name', '$username', '$email', 'not_verified', NULL, '$password', '" . get_ip() . "')";
            $codeQuery = "INSERT INTO Codes (username, code) VALUES ('$username', '$code')";
            mysqli_query($connect, $codeQuery);
            
            $mail = new SendMail('mappercoder@yandex.com', 'efkfcibrrtirlzlx', 'ssl://smtp.yandex.ru', 465, "UTF-8");
            $from = array("mappercoder", "mappercoder@yandex.com");
            $result = $mail->send($email, 'Код подтверждения', "Ваш код подтверждения аккаунта @$username: <em>$code</em><br><strong>Никому не говорить!!</strong>", $from);
            
            if (mysqli_query($connect, $insertQuery)) {
                header("Location: /network/verification.php?username=$username");
                exit();
            } else {
                $_SESSION["message"] = "Ошибка: " . mysqli_error($connect);
                header("Location: /network/register");
                exit();
            }
        }
    } else {
        header("Location: /network/main");
        exit();
    }
?>
<?php
    require_once 'connect.php';
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $resultEmail = mysqli_query($connect, "SELECT * FROM Accounts WHERE email='$username'");
        if (mysqli_num_rows($resultEmail) > 0) {
            $row = mysqli_fetch_assoc($resultEmail);
            $username = $row["username"];
        }

        $result = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username'");
        $resultPassword = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username' AND password='$password'");

        if (mysqli_num_rows($result) == 0) {
            $_SESSION["message"] = "Такого аккаунта не существует";
            header("Location: /network/login");
            exit();
        }
        if (mysqli_num_rows($resultPassword) > 0) {
            $row = mysqli_fetch_assoc($result);

            if ($row['state'] == "banned") {
                $_SESSION["message"] = "Ваш аккаунт был забанен.";
                header("Location: /network/login");
                exit();
            } elseif ($row['state'] == "not_verified") {
                $_SESSION["message"] = "Сначала подтвердите ваш аккаунт!";
                header("Location: /network/login");
                exit();
            }

            $_SESSION['user'] = array(
                "id" => $row['id'],
                "username" => $row['username'],
                "name" => $row['name'],
                "state" => $row['state'],
                "email" => $row['email'],
                "coins" => $row['coins']
            );

            header("Location: /network/main");
            exit();
        } else {
            $_SESSION["message"] = "Неправильный пароль";
            header("Location: /network/login");
            exit();
        }
    } else {
        header("Location: /network/login");
        exit();
    }
?>
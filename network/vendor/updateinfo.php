<?php
    require_once 'connect.php';
    session_start();

    if (!isset($_SESSION['user'])) {
        exit();
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

    $username = $_SESSION['user']['username'];
    $ip = get_ip();

    $result = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username'");
    $resultip = mysqli_query($connect, "SELECT * FROM IPs WHERE ip='$ip'");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['state'] == "banned") {
            $_SESSION["message"] = "Ваш аккаунт был забанен.";
            unset($_SESSION['user']);
            header("Location: /network/login");
            exit();
        }

        $_SESSION['user'] = array(
            "id" => $row['id'],
            "username" => $row['username'],
            "avatar" => $row['avatar'],
            "name" => $row['name'],
            "state" => $row['state'],
            "email" => $row['email'],
            "coins" => $row['coins']
        );
        if (mysqli_num_rows($resultip) > 0) {
            $_SESSION["message"] = "Вы не можете пользоваться платформой.";
            unset($_SESSION['user']);
            header("Location: /network/login");
            exit();
        }
    } else {
        $_SESSION["message"] = "Ваш аккаунт был удалён.";
        unset($_SESSION['user']);
        header("Location: /network/login");
        exit();
    }
?>
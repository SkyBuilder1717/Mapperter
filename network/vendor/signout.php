<?php
    require_once 'connect.php';
    require_once "updateinfo.php";
    session_start();

    unset($_SESSION['user']);

    $_SESSION["message"] = "Успешный выход из аккаунта.";
    header("Location: /network/login");
?>
<?php
    require_once "connect_to.php";
    require_once "get_ip.php";
    session_start();

    if (isset($_SESSION['user']))
    {
    $user = $_SESSION['user'];

    if (($user['state'] == "admin") or ($user['state'] == "developer")) {
        http_response_code(202);
    } else {
        http_response_code(403);
        exit();
    }
    } else {
        http_response_code(401);
    }
?>
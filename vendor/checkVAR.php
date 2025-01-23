<?php
    require_once "connect_to.php";
    require_once "get_ip.php";
    session_start();

    $is_admin = false;

    if (isset($_SESSION['user'])) {
        if (($_SESSION['user']['state'] == "admin") or ($_SESSION['user']['state'] == "developer")) {
            $is_admin = true;
        }
    }
?>
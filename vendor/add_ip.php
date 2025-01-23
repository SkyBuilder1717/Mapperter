<?php
    require_once "check_admin.php";
    require_once "connect_to.php";

    $ip = $_POST['ip'];

    function is_base64($str) {
        if (base64_encode(base64_decode($str, true)) === $str) {
            return true;
        } else {
            return false;
        }
    }

    if (is_base64($ip)){
        $ip = base64_decode($ip);
    } else {
        die("Incorrect base64.");
    }

    mysqli_query($connect, "INSERT INTO `IPs` (`id`, `ip`) VALUES (NULL, '$ip')");

    echo "IP has been added!";
?>
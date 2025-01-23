<?php
    // DataBase connection
    $connect = mysqli_connect('sql211.yzz.me', 'yzzme_37068083', 'Glitch201101711', 'yzzme_37068083_IPs');
    
    // Error, if we got nothing
    if (!$connect) {
        http_response_code(500);
        die('Error connect to DataBase');
    }
?>
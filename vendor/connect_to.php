<?php
    // DataBase connection
    $connect = mysqli_connect('', '', '', '');
    
    // Error, if we got nothing
    if (!$connect) {
        http_response_code(500);
        die('Error connect to DataBase');
    }
?>

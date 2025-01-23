<?php
    // DataBase connection
    $connect = mysqli_connect('', '', '', ''); // MySQL, username, password, database
    
    // Error, if we got nothing
    if (!$connect) {
        die('Error connect to DataBase');
    } else {
        //echo 'Connected successfully!';
    }
    
    // Automatic helpful functions insect

    function removeEmojis($text) {
        $emojiPattern = '/[\x{1F600}-\x{1F64F}|\x{1F300}-\x{1F5FF}|\x{1F680}-\x{1F6FF}|\x{1F700}-\x{1F77F}|\x{1F780}-\x{1F7FF}|\x{1F800}-\x{1F8FF}|\x{1F900}-\x{1F9FF}|\x{2600}-\x{26FF}|\x{2700}-\x{27BF}|\x{FE0F}|\x{1F1E6}-\x{1F1FF}]/u';
        $cleanText = preg_replace($emojiPattern, '', $text);
        return $cleanText;
    }

    function generateString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $stringResult = '';
        for ($i = 0; $i < $length; $i++) {
            $stringResult .= $characters[rand(0, $charactersLength - 1)];
        }
        return $stringResult;
    }

    function limitString($string, $length) {
        if (mb_strlen($string) > $length) {
            return mb_substr($string, 0, $length);
        }
        return $string;
    }
?>
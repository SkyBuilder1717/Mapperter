<?php
    session_start();
    
    if (!isset($_SESSION["message"])) {
        $_SESSION["message"] = null;
    }
?>
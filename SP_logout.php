<?php
    session_start();
    include "includes/function.php";
    requireLogin();

    $_SESSION = [];

    session_destroy();

    header("Location: SP_login.php");
    exit();
?>
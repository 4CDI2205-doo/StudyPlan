<?php
    $dsn = 'mysql:dbname=studyplan;host=localhost;charset=utf8mb4';
    $user = 'root';
    $password = '';

    $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>
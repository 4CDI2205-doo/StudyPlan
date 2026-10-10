<?php
    require_once "includes/SP_table_pdo.php";

    $sql = "CREATE TABLE IF NOT EXISTS SP_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(64) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME
    );";

    $pdo->query($sql);
    
    echo "SP_usersを作成しました";
?>
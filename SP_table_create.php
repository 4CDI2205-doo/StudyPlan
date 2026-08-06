<?php
    require_once "SP_table_pdo.php";

    $sql = "CREATE TABLE IF NOT EXISTS SP_study_logs(
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(32),
        SP_subject VARCHAR(64),
        study_time INT NOT NULL,
        progress text,
        memo text,
        created_at DATETIME
    );";

    $pdo->query($sql);
?>
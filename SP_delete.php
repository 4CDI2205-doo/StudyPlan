<?php
    session_start();
    include "SP_table_pdo.php";
    include "function.php";
    requireLogin();

    $user_name = $_SESSION["user_name"];
    $user_id = $_SESSION["user_id"];

    if (isset($_POST["delete_id"]) && !empty($_POST["delete_id"])){
        //$study_id = $_POST["study_id"];
        $delete_id = (int)$_POST["delete_id"];
        $sql = "DELETE FROM SP_study_logs WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":id",$delete_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id",$user_id, PDO::PARAM_INT);
        if ($stmt->execute()){
            header("Location: SP_history.php");
            exit();
        }else{
            echo "削除できませんでした";
        }
    }   
?>
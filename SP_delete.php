<?php
    session_start();
    include "includes/SP_table_pdo.php";
    include "includes/function.php";
    requireLogin();

    $user_name = $_SESSION["user_name"];
    $user_id = $_SESSION["user_id"];
    $csrf_token = generate_csrf_token();

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        if (!isset($_POST["csrf_token"]) || !verify_csrf_token($_POST["csrf_token"])){
            die("不正なアクセスです");
        }
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
    }

?>
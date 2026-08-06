<?php
    // HTML特殊文字変換
    function h($str){
        return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
    }

    function requireLogin(){
        if (!isset($_SESSION["user_id"])){
            header("Location: SP_login.php");
            exit();
        }
    }
?>
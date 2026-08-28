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

    function generate_csrf_token(){
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if (isset($_SESSION["csrf_token"])){
            return $_SESSION["csrf_token"];
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION["csrf_token"] = $token;
        return $token;
    }

    function verify_csrf_token($token){
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if (!isset($_SESSION["csrf_token"])){
            return false;
        }

        return hash_equals($_SESSION["csrf_token"],$token);
    }
?>
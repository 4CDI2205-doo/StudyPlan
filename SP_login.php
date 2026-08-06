<?php
    session_start();
    include "SP_table_pdo.php";

    if (isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["password"])){
        $email = $_POST["email"];
        $password = $_POST["password"];

        $sql = "SELECT id, name, email,password FROM SP_users where email = :email";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":email",$email, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result){
            if ($password == $result["password"]){
                $_SESSION["user_id"] = $result["id"];
                $_SESSION["user_name"] = $result["name"];
                
                header("Location: SP_home.php");
                exit();
            }else{
                echo "パスワードが異なります";
            };
        }else{
            echo "E-mailが存在しません";
        }
    }else if (isset($_POST["email"]) && !empty($_POST["email"])){
        echo "パスワードを入力してください";
    }else if (isset($_POST["password"]) && !empty($_POST["password"])){
        echo "E-mailを入力してください";
    };
?>

<?php include "header-normal.php" ?>
<h2>ログイン</h2>
<form action="" method="post" class="login-form">
    <div class="form-group">
        <label for="login_email">E-mail:</label>
        <input type="text" id="login_email" name="email" placeholder="登録したE-mailを入力してください">
    </div>
    <div class="form-group">
        <label for="login_password">パスワード：</label>
        <input type="password" id="login_password" name="password" placeholder="登録したパスワードを入力してください">
    </div>
    <button type="submit" name="login_submit" class="btn btn-history">ログイン</button>
</form>
<hr>
<a href="SP_register.php">新規登録はこちら</a>

<?php include "footer.php" ?>
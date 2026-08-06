<?php
    include "SP_table_pdo.php";

    if (isset($_POST["name"]) && !empty($_POST["name"]) && isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["password"])){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $date = date("Y/m/d h:i:s");

        $sql = "SELECT * FROM SP_users WHERE email = :email";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":email",$email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result){
            echo "同じメールアドレスが登録されています";
        }else{
            $sql = "INSERT INTO SP_users (name,email,password,created_at) VALUES (:name,:email,:password,:created_at)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":name",$name, PDO::PARAM_STR);
            $stmt->bindParam(":email",$email, PDO::PARAM_STR);
            $stmt->bindParam(":password",$password, PDO::PARAM_STR);
            $stmt->bindParam(":created_at",$date, PDO::PARAM_STR);

            $result = $stmt->execute();
            if ($result){
                header("Location: SP_login.php");
                exit();
            }else{
                echo "登録に失敗しました";
            }
        }
    }
?>
<?php include "header-normal.php" ?>
<h2>初期登録</h2>
<form action="" method="post" class="register-form">
    <div class="form-group">
        <label for="user_name">名前：</label>
        <input type="text" id="user_name" name="name" placeholder="名前を入力してください">
    </div>
    <div class="form-group">
        <label for="user_email">E-mail：</label>
        <input type="text" id="user_email" name="email" placeholder="E-mailを入力してください">
    </div>
    <div class="form-group">
        <label for="user_password">パスワード：</label>
        <input type="password" id="user_password" name="password" placeholder="パスワードを入力してください">
    </div>
        <button type="submit" name="user_submit" class="btn btn-history">登録</button>
</form>
<hr>
<p><a href="SP_login.php">ログイン画面に戻る</a></p>

<?php include "footer.php";?>
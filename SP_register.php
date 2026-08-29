<?php
    session_start();
    
    include "includes/SP_table_pdo.php";
    include "includes/function.php";

    $csrf_token = generate_csrf_token();

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        if (!isset($_POST["csrf_token"]) || !verify_csrf_token($_POST["csrf_token"])){
            die("不正なアクセスです");
        }
        if (isset($_POST["name"]) && !empty($_POST["name"]) && isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["password"])){
            $name = $_POST["name"];
            $email = trim($_POST["email"]);
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $error_message = "正しいメールアドレスを入力してください";
            }else{
                $password = $_POST["password"];
                if (strlen($password) < 8){
                    $error_message = "パスワードは8文字以上にしてください";
                }else{
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $date = date("Y/m/d H:i:s");
    
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
                        $stmt->bindParam(":password",$hashed_password, PDO::PARAM_STR);
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
            }
        }
    }
?>
<?php include "includes/header-normal.php" ?>

<?php if (isset($error_message)): ?>
    <p><?= htmlspecialchars($error_message, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>

<h2>初期登録</h2>
<form action="" method="post" class="register-form">
    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
    <div class="form-group">
        <label for="user_name">名前：</label>
        <input type="text" id="user_name" name="name" placeholder="名前を入力してください" required>
    </div>
    <div class="form-group">
        <label for="user_email">E-mail：</label>
        <input type="email" id="user_email" name="email" placeholder="E-mailを入力してください" required>
    </div>
    <div class="form-group">
        <label for="user_password">パスワード：</label>
        <input type="password" id="user_password" name="password" minlength="8" required>
    </div>
        <button type="submit" name="user_submit" class="btn btn-history">登録</button>
</form>
<hr>
<p><a href="SP_login.php">ログイン画面に戻る</a></p>
<?php include "includes/footer.php";?>
<?php
    session_start();
    include "includes/SP_table_pdo.php";
    include "includes/function.php";
    requireLogin();

    $user_id = $_SESSION["user_id"];
    $user_name = $_SESSION["user_name"];

    if (isset($_POST["upload_subject"]) && !empty($_POST["upload_subject"]) && isset($_POST["time"]) && !empty($_POST["time"]) && isset($_POST["progress"]) && !empty($_POST["progress"])){
        $subject = $_POST["upload_subject"];
        $time = $_POST["time"];
        $progress = $_POST["progress"];
        $memo = $_POST["memo"] ?? "";
        $date = date("Y/m/d H:i:s");

        $sql = "INSERT INTO SP_study_logs (user_id, SP_subject, study_time, progress, memo, created_at) VALUES (:user_id, :SP_subject, :study_time, :progress, :memo, :created_at)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(":user_id",$user_id, PDO::PARAM_INT);
        $stmt->bindParam(":SP_subject",$subject, PDO::PARAM_STR);
        $stmt->bindParam(":study_time",$time, PDO::PARAM_STR);
        $stmt->bindParam(":progress",$progress, PDO::PARAM_STR);
        $stmt->bindParam(":memo",$memo, PDO::PARAM_STR);
        $stmt->bindParam(":created_at",$date, PDO::PARAM_STR);

        if ($stmt->execute()){
            header("Location: SP_history.php");
            exit();
        } else {
            echo "データの入力に失敗しました";
        }
    }
?>

<?php include "includes/header.php" ?>
<form action="" method="post" class="upload-form">
    <h2>学習履歴の追加</h2>
    <div class="form-group">
        <label for="upload_sub">科目：</label>
        <input type="text" id="upload_sub" name="upload_subject" placeholder="例：Pythonなど">
    </div>
    <div class="form-group">
        <label for="upload_time">勉強時間(分)：</label>
        <input type="number" id="upload_time" name="time" min="1" step="1"  placeholder="例：120" required>
    </div>
    <div class="form-group">
        <label for="upload_progress">進捗：</label>
        <input type="text" id="upload_progress" name="progress" placeholder="例：P.162まで">
    </div>
    <div class="form-group">
        <label for="upload_memo">学習内容：</label>
        <textarea id="upload_memo" name="memo" placeholder="学習内容やメモをお書きください（未記入でも可）"></textarea>
    </div>
    <button type="submit" name="upload_submit" class="btn btn-history">送信する</button>
</form>
<?php include "includes/footer.php" ?>
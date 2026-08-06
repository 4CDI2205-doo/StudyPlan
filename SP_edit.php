<?php
    session_start();
    include "SP_table_create.php";
    include "function.php";

    requireLogin();

    $user_name = $_SESSION["user_name"];
    $user_id = $_SESSION["user_id"];

    $edit_data = null;
    $error_message = "";
    
    if (isset($_GET["id"]) && !empty($_GET["id"])){
        $edit_id = (int)$_GET["id"];

        $sql = "SELECT * FROM SP_study_logs WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":id",$edit_id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id",$user_id,PDO::PARAM_INT);

        $stmt->execute();

        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$edit_data){
            $error_message = "編集する学習記録が見つかりません";
        }
    }

    if (isset($_POST["update_id"]) && !empty($_POST["update_id"]) && isset($_POST["update_subject"]) && !empty($_POST["update_subject"]) && isset($_POST["update_time"]) && !empty($_POST["update_time"]) && isset($_POST["update_progress"]) && !empty($_POST["update_progress"])){
        $update_id = $_POST["update_id"];    
        $subject = $_POST["update_subject"];
        $time = $_POST["update_time"];
        $progress = $_POST["update_progress"];
        $memo = $_POST["update_memo"] ?? "";
        $created_at = date("Y/m/d H:i:s");

        $sql = "UPDATE SP_study_logs SET SP_subject = :SP_subject, study_time = :study_time, progress = :progress, memo = :memo, created_at = :created_at WHERE id = :id AND user_id = :user_id";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(":id",$update_id,PDO::PARAM_INT);
        $stmt->bindParam(":user_id",$user_id,PDO::PARAM_INT);
        $stmt->bindParam(":SP_subject",$subject, PDO::PARAM_STR);
        $stmt->bindParam(":study_time",$time,PDO::PARAM_STR);
        $stmt->bindParam(":progress",$progress,PDO::PARAM_STR);
        $stmt->bindParam(":memo",$memo,PDO::PARAM_STR);
        $stmt->bindParam(":created_at",$created_at,PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            header("Location: SP_history.php");
            exit();
        }else{
            echo "データがありません";
        }
    }

?>

<?php include "header.php" ?>
<h2>学習履歴の編集</h2>
<?php if ($error_message !== ""): ?>
    <p><?= h($error_message) ?></p>
<?php endif; ?>

<?php if ($edit_data): ?>
    <form action="" method="post" class="edit-form">
        <input type="hidden" name="update_id" value="<?= h($edit_data["id"]) ?>">
        <div class="form-group">
            <label for="updatesubject">科目名：</label>
            <input type="text" id="updatesubject" name="update_subject" value="<?= h($edit_data["SP_subject"]) ?>">
        </div>
        <div class="form-group">
            <label for="updatetime">学習時間：</label>
            <input type="text" id="updatetime" name="update_time" value="<?= h($edit_data["study_time"]) ?>">
        </div>
        <div class="form-group">
            <label for="updateprogress">学習進捗：</label>
            <input type="text" id="updateprogress" name="update_progress" value="<?= h($edit_data["progress"]) ?>">
        </div>
        <div class="form-group">
            <label for="updatememo">学習内容：</label>
            <textarea id="updatememo" name="update_memo"><?= h($edit_data["memo"]) ?></textarea>
        </div>
        <button type="submit" name="update_submit" class="btn btn-history">変更する</button>
    </form>
<?php endif; ?>
<?php include "footer.php" ?>
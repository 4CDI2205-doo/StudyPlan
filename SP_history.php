<?php
    session_start();
    include "SP_table_pdo.php";
    include "function.php";
    requireLogin();

    $user_id = $_SESSION["user_id"];
    $user_name = $_SESSION["user_name"];
    
    $sql = "SELECT * FROM SP_study_logs WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id",$user_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "header.php" ?>
<h2>学習履歴</h2>

<?php if (empty($results)): ?>
    <p>学習履歴はまだありません</p>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>学習ID</th>
                <th>科目</th>
                <th>勉強時間</th>
                <th>進捗</th>
                <th>学習内容</th>
                <th>登録日時</th>
                <th>操作</th>
            </tr>
            <?php foreach ($results as $result): ?>
            <tr>
                <td><?= h($result["id"]) ?></td>
                <td><?= h($result["SP_subject"]) ?></td>
                <td><?= h($result["study_time"]) ?></td>
                <td><?= h($result["progress"]) ?></td>
                <td><?= nl2br(h($result["memo"])) ?></td>
                <td><?= h($result["created_at"]) ?></td>
                <td class="operation-cell"><a href="SP_edit.php?id=<?= h($result["id"]) ?>" class = "btn btn-history">編集</a>
                    <form action="SP_delete.php" method="post" class="inline-form">
                        <input type="hidden" name="delete_id" value="<?= h($result["id"]) ?>">
                        <button type="submit" class="btn btn-delete" onclick="return confirm('この学習記録を削除しますか？');">削除</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
<?php include "footer.php"; ?>
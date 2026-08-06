<?php 
    session_start();

    date_default_timezone_set("Asia/Tokyo");

    include "SP_table_pdo.php";
    include "function.php";
    requireLogin();
    
    //今日の勉強時間関連
    $user_id = $_SESSION["user_id"];
    $user_name = $_SESSION["user_name"];
    //今日の日付
    $today = date("Y-m-d");
    $month = date("Y-m");
    //今日の学習時間の合計
    $sql_today = "SELECT COALESCE(SUM(study_time),0) AS today_total FROM SP_study_logs WHERE user_id = :user_id AND created_at LIKE :today";

    $stmt_today = $pdo->prepare($sql_today);

    $today_search = $today . "%";

    $stmt_today->bindParam(":user_id",$user_id, PDO::PARAM_INT);
    $stmt_today->bindParam(":today",$today_search,PDO::PARAM_STR);
    
    $stmt_today->execute();

    $today_result = $stmt_today->fetch(PDO::FETCH_ASSOC);

    $today_total = (int)$today_result["today_total"];
    $today_hours = intdiv($today_total,60);
    $today_minutes = $today_total % 60;
 
?>

<?php
    //今までの勉強時間関連
    $sql = "SELECT SP_subject, SUM(study_time) AS total_time FROM SP_study_logs WHERE user_id = :user_id GROUP BY SP_subject ORDER BY total_time DESC";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":user_id",$user_id,PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $study_times = [];
    $all_total_time = 0;

    foreach ($results as $result){
        $labels[] = $result["SP_subject"];
        $study_times[] = (int)$result["total_time"];
        $all_total_time += (int)$result["total_time"];
    }
    $total_hours = intdiv($all_total_time, 60);
    $total_minutes = $all_total_time % 60;
?>

<?php
    //総投稿数
    $sql_count = "SELECT COUNT(*) AS total_count FROM SP_study_logs WHERE user_id = :user_id";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->bindParam(":user_id",$user_id,PDO::PARAM_INT);
    $stmt_count->execute();

    $count_result = $stmt_count->Fetch(PDO::FETCH_ASSOC);
    $total_count = (int)$count_result["total_count"];
?>

<?php
    //ホームでの新しい5件のみ表示するためのセレクト文
    $sql_recent = "SELECT * FROM SP_study_logs WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5";
    
    $stmt_recent = $pdo->prepare($sql_recent);
    
    $stmt_recent->bindParam(":user_id",$user_id,PDO::PARAM_INT);
    $stmt_recent->execute();

    $recent_results  = $stmt_recent->FetchAll(PDO::FETCH_ASSOC);
?>

<?php
    //今月の勉強時間の割合を円グラフにする
    $month_search = $month . "%";
    $sql_month = "SELECT SP_subject, COALESCE(SUM(study_time),0) AS month_total_time FROM SP_study_logs WHERE user_id = :user_id AND created_at LIKE :month GROUP BY SP_subject ORDER BY month_total_time DESC";
    $stmt_month = $pdo->prepare($sql_month);

    $stmt_month->bindParam(":user_id",$user_id,PDO::PARAM_INT);
    $stmt_month->bindParam(":month",$month_search,PDO::PARAM_STR);
    $stmt_month->execute();

    $month_results = $stmt_month->FetchAll(PDO::FETCH_ASSOC);

    $month_labels = [];
    $month_study_times = [];
    $month_total_time = 0;

    foreach ($month_results as $month_result){
        $month_labels[] = $month_result["SP_subject"];
        $month_study_times[] = (int)$month_result["month_total_time"];
        $month_total_time += (int)$month_result["month_total_time"];
    }

    include "header.php";
?>    
<section class="card">
    <h2>今日の勉強時間</h2>
    
    <p class="study-time">
        <?= h($today_hours) ?>時間
        <?= h($today_minutes) ?>分
    </p>
    <?php if (empty($month_results)): ?>
        <p>グラフに表示できる学習記録が在りません</p>
    <?php else: ?>
        <div class="chart-container">
            <canvas id="monthstudyChart"></canvas>
        </div>
        <script>
            const monthlabels = <?= json_encode(
                $month_labels,
                JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG |
                JSON_HEX_AMP |
                JSON_HEX_APOS |
                JSON_HEX_QUOT
            ) ?>;

            const monthstudyTimes = <?= json_encode($month_study_times) ?>;

            const monthchartElement = document.getElementById("monthstudyChart");

            new Chart(monthchartElement, {
                type: "pie",

                data: {
                    labels: monthlabels,

                    datasets: [{
                        label: "今月の学習時間（分）",
                        data: monthstudyTimes
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        title: {
                            display: true,
                            text: "今月の科目別学習時間"
                        },
                        legend: {
                            position: "bottom"
                        },

                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const values = 
                                        context.dataset.data;

                                    const total = values.reduce(
                                        function(sum,value){
                                            return sum + value;
                                        },
                                        0
                                    );

                                    const value = context.raw;

                                    const percentage = total === 0 ? 0 :(
                                        value / total * 100
                                    ).toFixed(1);
                                    return (
                                        context.label + ":" + value + "分(" + percentage + "%) "
                                    );

                                }
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
    <?php if ($today_total === 0): ?>
        <p>今日はまだ学習がありません。</p>
        <a href="SP_upload.php">学習を記録する</a>
    <?php else: ?>
        <p>今日も学習お疲れ様です！</p>
    <?php endif; ?>
</section>
<section class="card">
    <h2>最近の学習履歴</h2>
    <?php if (empty($recent_results)): ?>
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
                <?php foreach ($recent_results as $result): ?>
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
</section>
<section class="card">
    <h2>今までのあなたの勉強成果！！</h2>

    <p>総学習時間：</p>
    <p class="study-time">
        
        <?= h($total_hours) ?>時間
        <?= h($total_minutes) ?>分<br />
        総投稿数：<?= h($total_count) ?>件
    </p>

    <?php if (empty($results)): ?>
        <p>グラフに表示できる学習記録が在りません</p>
    <?php else: ?>
        <div class="chart-container">
            <canvas id="studyChart"></canvas>
        </div>
        <script>
            const labels = <?= json_encode(
                $labels,
                JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG |
                JSON_HEX_AMP |
                JSON_HEX_APOS |
                JSON_HEX_QUOT
            ) ?>;

            const studyTimes = <?= json_encode($study_times) ?>;

            const chartElement = document.getElementById("studyChart");

            new Chart(chartElement, {
                type: "bar",

                data: {
                    labels: labels,

                    datasets: [{
                        label: "合計学習時間（分）",
                        data: studyTimes
                    }]
                },

                options: {
                    responsive: true,

                    plugins: {
                        title: {
                            display: true,
                            text: "科目別の合計学習時間"
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
</section>
<?php include "footer.php" ?>
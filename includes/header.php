<!doctype html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>StudyLog</title>
        <link rel="stylesheet" href="css/style.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
        <div class="container">
            <header>
                <h1>StudyLog</h1>
                <p>こんにちは <emsp> </emsp> <?= h($user_name) ?>さん   <a href="SP_logout.php" class="btn btn-history">ログアウト</a></p>
            </header>
            <hr>
            <a href="SP_home.php" class="btn btn-home">ホーム</a>
            <a href="SP_history.php" class="btn btn-history">学習履歴を表示</a>
            <a href="SP_upload.php" class="btn btn-upload">学習履歴を追加</a>
            <br />
            <hr>
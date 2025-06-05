<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그아웃</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>로그아웃 되었습니다.</h2>
        <p>다시 로그인하려면 아래 버튼을 눌러주세요.</p>
        <a href="login.php"><button class="btn">로그인 페이지로</button></a>
    </div>
</body>
</html>


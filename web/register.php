<?php
session_start();
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $nickname = trim($_POST['nickname']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $pdo = getDbConnection();

    // 이메일 중복 확인
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $error = "이미 존재하는 계정입니다.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (email, nickname, password) VALUES (?, ?, ?)");
        $stmt->execute([$email, $nickname, $password]);
        header("Location: register_success.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>회원가입</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="이메일(ID로 사용)" required>
            <input type="text" name="nickname" placeholder="닉네임" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <button class="btn" type="submit">가입하기</button>
        </form>
        <p style="margin-top:10px;">이미 계정이 있나요? <a href="login.php">로그인</a></p>
    </div>
</body>
</html>


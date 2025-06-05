<?php
session_start();
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => '로그인이 필요합니다.']);
    exit;
}

$pdo = getDbConnection();
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT emotion, COUNT(*) as cnt FROM chat_history WHERE user_id = ? AND emotion IS NOT NULL GROUP BY emotion");
$stmt->execute([$userId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [
    '분노' => 0,
    '슬픔' => 0,
    '외로움' => 0,
    '불안' => 0,
    '걱정' => 0,
    '사랑' => 0,
    '기쁨' => 0
];

foreach ($data as $row) {
    $emotion = $row['emotion'];
    if (array_key_exists($emotion, $result)) {
        $result[$emotion] = (int)$row['cnt'];
    }
}

echo json_encode($result);
?>

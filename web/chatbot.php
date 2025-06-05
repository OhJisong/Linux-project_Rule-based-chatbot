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
$userMessage = trim($_POST['message'] ?? '');

if ($userMessage === '') {
    echo json_encode(['reply' => '무슨 말인지 모르겠어.']);
    exit;
}

// 현재 대화 상태 확인 (가장 최근 입력한 chat_history를 기준으로 판단)
$stmt = $pdo->prepare("SELECT event, emotion FROM chat_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$userId]);
$last = $stmt->fetch(PDO::FETCH_ASSOC);

// 단계 판단
$isAwaitingEmotion = $last && $last['event'] && !$last['emotion'];

if ($isAwaitingEmotion) {
    // 감정 입력 처리
    $emotion = mapEmotion($userMessage);
    $youtube = generateMusicLink($emotion);

    $update = $pdo->prepare("UPDATE chat_history SET emotion = ?, youtube_link = ? WHERE user_id = ? AND emotion IS NULL ORDER BY created_at DESC LIMIT 1");
    $update->execute([$emotion, $youtube, $userId]);

    echo json_encode([
        'reply' => "아 그런 감정이구나. 너의 기분에 맞는 노래를 들려줄게!",
        'done' => true,
        'music' => $youtube
    ]);
    exit;
} else {
    // 사건(event) 입력 처리
    $insert = $pdo->prepare("INSERT INTO chat_history (user_id, event) VALUES (?, ?)");
    $insert->execute([$userId, $userMessage]);

    echo json_encode([
        'reply' => "아 그런 일이 있었구나. 그럼 너의 지금 감정은 어때?",
        'done' => false
    ]);
    exit;
}

function mapEmotion($text) {
    $text = strtolower($text);

    $patterns = [
        '분노' => ['화나', '짜증', '열받', '빡쳐', '성질', '분노', '불쾌', '거슬려', '억울', '분개', '홧김', '격분'],
        '슬픔' => ['슬프', '우울', '눈물', '상실', '비참', '절망', '눈시울', '가슴 아파', '비애', '허무', '서글퍼', '낙담'],
        '외로움' => ['외로', '혼자', '쓸쓸', '고독', '허전', '친구가 없어', '소외', '적막', '공허'],
        '불안' => ['불안', '초조', '긴장', '걱정돼', '두려', '겁나', '망설여', '혼란스러', '불편한 느낌'],
        '걱정' => ['걱정', '고민', '신경', '불확실', '근심', '마음이 무거워', '속상해', '염려'],
        '사랑' => ['좋아해', '사랑', '설레', '그리워', '연인', '애정', '보고싶어', '연애', '두근', '좋아하는 사람'],
        '기쁨' => ['행복', '기뻐', '좋아', '신나', '즐거', '기쁨', '만족', '좋은 일', '웃음', '감격', '들떠', '행운']
    ];

    foreach ($patterns as $emotion => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return $emotion;
            }
        }
    }
    return '기쁨'; // 기본값
}

function generateMusicLink($emotion) {
    $query = [
        '분노' => '화났을 때 듣는 노래 플레이리스트',
        '슬픔' => '우울할 때 듣는 노래 플레이리스트',
        '외로움' => '외로울 때 듣는 노래 플레이리스트',
        '불안' => '불안할 때 듣는 노래 플레이리스트',
        '걱정' => '걱정이 많을 때 듣는 노래 플레이리스트',
        '사랑' => '사랑에 빠졌을 때 듣는 노래 플레이리스트',
        '기쁨' => '신날 때 듣는 노래 플레이리스트'
    ];

    $search = isset($query[$emotion]) ? $query[$emotion] : '기분에 맞는 노래 플레이리스트';
    return 'https://www.youtube.com/results?search_query=' . urlencode($search);
}
?>

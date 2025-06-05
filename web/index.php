<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getDbConnection();
$userId = $_SESSION['user_id'];
$nickname = $_SESSION['nickname'] ?? '친구';

// 최근 대화 기록 1개 (가장 마지막 event + emotion)
$stmt = $pdo->prepare("SELECT event, emotion FROM chat_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$userId]);
$lastRecord = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>감정 챗봇 🎵</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .emotion-button {
            background-color: #d6f5d6;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            margin-left: 10px;
            font-weight: bold;
        }

        .emotion-button:hover {
            background-color: #b5e7b5;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div style="text-align: right; margin-bottom: 10px;">
            <a href="logout.php"><button class="btn">로그아웃</button></a>
        </div>

        <h2><?= htmlspecialchars($nickname) ?>의 감정 챗봇 🎵</h2>

        <div id="chat-box">
            <?php if ($lastRecord): ?>
                <div class="bot-message">
                    <?= htmlspecialchars($nickname) ?> 안녕 또 만났네, 저번에 너는 "<?= htmlspecialchars($lastRecord['event']) ?>" 했다고 했어.
                    그때 기분은 "<?= htmlspecialchars($lastRecord['emotion']) ?>" 였어. 오늘은 무슨 일 없어?
                </div>
            <?php else: ?>
                <div class="bot-message">
                    <?= htmlspecialchars($nickname) ?>, 안녕? 오늘은 무슨 일이 있었니?
                </div>
            <?php endif; ?>
        </div>

        <form id="chat-form" style="display: flex; margin-top: 10px; align-items: center; gap: 10px;">
            <input type="text" id="user-input" name="message" class="chat-input" placeholder="내용을 입력하세요" required>
            <button class="submit-btn" type="submit">전송</button>
            <button type="button" onclick="showStats()" class="emotion-button">🧠 감정 통계 보기</button>
        </form>

        <canvas id="emotionChart" width="400" height="400" style="display:none; margin-top:20px;"></canvas>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        const input = document.getElementById('user-input');
        const form = document.getElementById('chat-form');

        async function submitChat() {
            const message = input.value.trim();
            if (!message) return;
            input.value = '';

            const userDiv = document.createElement('div');
            userDiv.classList.add('user-message');
            userDiv.textContent = message;
            chatBox.appendChild(userDiv);

            const res = await fetch('chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message)
            });

            const data = await res.json();

            const botDiv = document.createElement('div');
            botDiv.classList.add('bot-message');
            botDiv.textContent = data.reply;
            chatBox.appendChild(botDiv);

            if (data.done && data.music) {
                const musicDiv = document.createElement('div');
                musicDiv.classList.add('bot-message');
                musicDiv.innerHTML = `<strong>🎵 추천 음악: <a href="${data.music}" target="_blank">${data.music}</a></strong>`;
                chatBox.appendChild(musicDiv);
            }

            chatBox.scrollTop = chatBox.scrollHeight;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitChat();
        });

        input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                submitChat();
            }
        });

        async function showStats() {
            const res = await fetch('emotion_stats.php');
            const data = await res.json();
            const labels = Object.keys(data);
            const values = Object.values(data);

            const ctx = document.getElementById('emotionChart').getContext('2d');
            document.getElementById('emotionChart').style.display = 'block';

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '감정 비율',
                        data: values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(201, 203, 207, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }
    </script>
</body>
</html>


<?php
function getDbConnection() {
    $host = 'db';
    $db   = 'chatbot';
    $user = 'chatbot_user';
    $pass = 'chatbot_pw';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        echo "DB 연결 오류: " . $e->getMessage();
        exit;
    }
}


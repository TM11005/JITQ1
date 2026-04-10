<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $user_id = $_SESSION['user_id'];
    $score = $_POST['score'];

    $stmt = $conn->prepare("INSERT INTO attempts (quiz_id, user_id, score, attended, submitted_at) VALUES (?, ?, ?, 1, NOW())");
    $stmt->bind_param("iii", $quiz_id, $user_id, $score);
    $stmt->execute();
    echo "Your score: " . $score;
}
?>

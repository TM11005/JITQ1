<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];
    $time_limit = $_POST['time_limit'];

    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option, time_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $quiz_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $time_limit);
    $stmt->execute();
}
?>

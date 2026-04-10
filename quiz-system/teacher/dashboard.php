<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../auth/login.php");
}

echo "<h1>Teacher Dashboard</h1>";
// Code to display quizzes and create new ones
echo "
<div class='dashboard-container'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .btn-create {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .btn-create:hover {
            background-color: #218838;
        }
        .quiz-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .quiz-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .quiz-card h3 {
            margin-top: 0;
            color: #007bff;
        }
    </style>
    <a href='create_quiz.php' style='text-decoration: none;'><button class='btn-create'>+ Create New Quiz</button></a>
    <div class='quiz-list'>
        <p>No quizzes created yet.</p>
    </div>
</div>
";
?>

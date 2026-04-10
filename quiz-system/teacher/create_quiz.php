<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action']) && $input['action'] == 'create_quiz') {
        if (empty($input['title'])) {
            echo json_encode(['success' => false, 'error' => 'Title is required']);
            exit;
        }
        $title = htmlspecialchars($input['title']);
        $created_by = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO quizzes (title, created_by, is_active, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("si", $title, $created_by);
        if ($stmt->execute()) {
            $quiz_id = $conn->insert_id;
            echo json_encode(['success' => true, 'quiz_id' => $quiz_id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error creating quiz']);
        }
        exit;
    }
    
    if (isset($input['action']) && $input['action'] == 'add_question') {
        if (empty($input['quiz_id']) || empty($input['question_text']) || empty($input['option1']) || empty($input['option2']) || empty($input['option3']) || empty($input['option4']) || empty($input['correct_answer'])) {
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit;
        }
        $quiz_id = (int)$input['quiz_id'];
        $question_text = htmlspecialchars($input['question_text']);
        $option1 = htmlspecialchars($input['option1']);
        $option2 = htmlspecialchars($input['option2']);
        $option3 = htmlspecialchars($input['option3']);
        $option4 = htmlspecialchars($input['option4']);
        $correct_answer = htmlspecialchars($input['correct_answer']);
        
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option1, option2, option3, option4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $quiz_id, $question_text, $option1, $option2, $option3, $option4, $correct_answer);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error adding question']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .question-section {
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
            background-color: #fafafa;
        }
        .option {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Quiz</h1>
        <form method="POST">
            <div class="form-group">
                <label>Quiz Title</label>
                <input type="text" name="title" required>
            </div>
            <button type="submit">Create Quiz</button>
        </form>

        <div class="question-section">
            <h2>Add Questions</h2>
            <form id="questionForm">
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea name="question_text" required></textarea>
                </div>
                <div class="form-group">
                    <label>Option 1</label>
                    <input type="text" name="option1" required>
                </div>
                <div class="form-group">
                    <label>Option 2</label>
                    <input type="text" name="option2" required>
                </div>
                <div class="form-group">
                    <label>Option 3</label>
                    <input type="text" name="option3" required>
                </div>
                <div class="form-group">
                    <label>Option 4</label>
                    <input type="text" name="option4" required>
                </div>
                <div class="form-group">
                    <label>Correct Answer</label>
                    <select name="correct_answer" required>
                        <option value="">Select correct answer</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>
                <button type="submit">Add Question</button>
            </form>
        </div>
    </div>
    <script>
        let currentQuizId = null;
        
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const title = document.querySelector('input[name="title"]').value;
            
            fetch('create_quiz.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'create_quiz', title: title})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentQuizId = data.quiz_id;
                    document.querySelector('input[name="title"]').value = '';
                    alert('Quiz created successfully!');
                }
            });
        });
        
        document.getElementById('questionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentQuizId) {
                alert('Please create a quiz first!');
                return;
            }
            
            const formData = {
                action: 'add_question',
                quiz_id: currentQuizId,
                question_text: document.querySelector('textarea[name="question_text"]').value,
                option1: document.querySelector('input[name="option1"]').value,
                option2: document.querySelector('input[name="option2"]').value,
                option3: document.querySelector('input[name="option3"]').value,
                option4: document.querySelector('input[name="option4"]').value,
                correct_answer: document.querySelector('select[name="correct_answer"]').value
            };
            
            fetch('create_quiz.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('questionForm').reset();
                    alert('Question added successfully!');
                }
            });
        });
    </script>
</body>
</html>
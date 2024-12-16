<?php
require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_text = filter_input(INPUT_POST, 'feedback_text', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5],
    ]);

    if ($feedback_text && $rating) {
        $stmt = $conn->prepare("INSERT INTO feedback (feedback, rating) VALUES (?, ?)");
        $stmt->bind_param("si", $feedback_text, $rating);
        
        if ($stmt->execute()) {
          echo json_encode([
            'status' => 'success',
            'message' => 'Feedback submitted successfully!'
        ]);
        http_response_code(200);
        } else {
            http_response_code(500);
            echo "Error saving feedback.";
        }
    } else {
        http_response_code(400);
        echo "Invalid input.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
?>

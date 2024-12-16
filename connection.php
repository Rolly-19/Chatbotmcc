<?php
$servername = "localhost";
$username = "u510162695_chatbot_db";
$password = "1Chatbot_db";
$dbname = "u510162695_chatbot_db";

// Establish a MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection status
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create feedback table
$createFeedbackTable = "CREATE TABLE IF NOT EXISTS feedback (
    id int(11) NOT NULL AUTO_INCREMENT,
    feedback text NOT NULL,
    rating int(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
    date_submitted datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute create table query
if ($conn->query($createFeedbackTable) === TRUE) {
    echo "Feedback table created successfully!";
} else {
    echo "Error creating feedback table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
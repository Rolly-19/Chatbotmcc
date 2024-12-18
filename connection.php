<?php
$servername = "localhost";
$username = "u510162695_chatbot_db";
$password = "1Chatbot_db";
$dbname = "u510162695_chatbot_db";

// Establish a MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the data to be inserted
$question = "What is the weather like today?"; // Example question
$no_asks = 3; // Example number of asks
$ask_date = '2024-12-16'; // Date in MySQL format (YYYY-MM-DD)

// Insert query with date field
$insertQuery = "INSERT INTO unanswered (question, no_asks, ask_date) VALUES (?, ?, ?)";

// Prepare and bind statement
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sis", $question, $no_asks, $ask_date); 
// "sis" means string for question, integer for no_asks, and string for ask_date

// Execute the statement
if ($stmt->execute()) {
    echo "New record inserted successfully.";
} else {
    echo "Error inserting record: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
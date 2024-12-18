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

// Insert a value into the unanswered table
$insertUnanswered = "INSERT INTO unanswered (id, question, no_asks, date)
                     VALUES (1, 'What is PHP?', 5, '2024-12-17 00:00:00')";

// Execute the INSERT query
if ($conn->query($insertUnanswered) === TRUE) {
    echo "New record inserted successfully.<br>";
} else {
    echo "Error inserting record: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
?>

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

// Delete all rows from the unanswered table
$deleteQuery = "DELETE FROM unanswered";

// Execute the DELETE query
if ($conn->query($deleteQuery) === TRUE) {
    echo "All data has been deleted from the unanswered table.<br>";
} else {
    echo "Error deleting data: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
?>

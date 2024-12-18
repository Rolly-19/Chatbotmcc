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

// Alter unanswered table to add a datetime column
$alterUnansweredTable = "ALTER TABLE unanswered ADD COLUMN date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";

// Execute alter table query
if ($conn->query($alterUnansweredTable) === TRUE) {
    echo "Unanswered table altered successfully!<br>";
} else {
    echo "Error altering unanswered table: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
?>

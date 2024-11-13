<?php
$servername = "localhost";
$username = "u510162695_chatbot_db";
$password = "1Chatbot_db";
$dbname = "u510162695_chatbot_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to delete all records from the unanswered table
$sql = "DELETE FROM unanswered";

if ($conn->query($sql) === TRUE) {
  echo "Records deleted successfully";
} else {
  echo "Error deleting records: " . $conn->error;
}

$conn->close();
?>
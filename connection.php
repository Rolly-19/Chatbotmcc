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

// SQL to create the `user_logins` table
$tableSQL = "
CREATE TABLE IF NOT EXISTS `user_logins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `time_in` DATETIME NOT NULL,
  `time_out` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the SQL
if ($conn->query($tableSQL) === TRUE) {
    echo "Table `user_logins` created successfully (if it didn't already exist).";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>

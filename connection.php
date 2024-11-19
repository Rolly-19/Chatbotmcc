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

// SQL query to add the new columns
$addColumnsSql = "
    ALTER TABLE `users`
    ADD COLUMN `phone` VARCHAR(15) DEFAULT NULL,
    ADD COLUMN `SMSOTP` VARCHAR(6) DEFAULT NULL,
    ADD COLUMN `OTP_TIMESTAMP` DATETIME DEFAULT NULL,
    ADD COLUMN `failed_attempts` INT(11) DEFAULT 0,
    ADD COLUMN `last_failed_attempt` TIMESTAMP NULL DEFAULT NULL;
";

// Execute the query to add the columns
if ($conn->query($addColumnsSql) === TRUE) {
    echo "Columns added successfully!<br>";
} else {
    echo "Error: " . $addColumnsSql . "<br>" . $conn->error;
}

// Now, let's update the values for the existing rows.
$updateValuesSql = "
    UPDATE `users`
    SET 
        `phone` = '09631064348', 
        `SMSOTP` = NULL, 
        `OTP_TIMESTAMP` = NULL, 
        `failed_attempts` = 3, 
        `last_failed_attempt` = '2024-11-19 04:18:33'
    WHERE `id` = 1;
";

// Execute the update query
if ($conn->query($updateValuesSql) === TRUE) {
    echo "Values updated successfully!<br>";
} else {
    echo "Error: " . $updateValuesSql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>

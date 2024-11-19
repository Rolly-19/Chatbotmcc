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

// Function to check and alter the table
function check_and_alter_table_for_login_attempts($conn) {
    // Check if the login_attempts column already exists
    $check_columns_query = "SHOW COLUMNS FROM users LIKE 'login_attempts';";
    $result = $conn->query($check_columns_query);

    if ($result->num_rows == 0) {
        // Columns do not exist, alter the table to add them
        $alter_query = "
            ALTER TABLE users
            ADD COLUMN login_attempts INT DEFAULT 0,
            ADD COLUMN last_attempt DATETIME NULL;
        ";

        if ($conn->query($alter_query) === TRUE) {
            echo "Table altered successfully.<br>";
        } else {
            echo "Error altering table: " . $conn->error . "<br>";
        }
    } else {
        echo "Columns already exist.<br>";
    }
}

// Call the function to check and alter the table
check_and_alter_table_for_login_attempts($conn);

// Close the connection
$conn->close();
?>

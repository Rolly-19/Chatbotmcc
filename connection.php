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

// Fetch data from the unanswered table
$selectUnanswered = "SELECT * FROM unanswered";

// Execute the SELECT query
$result = $conn->query($selectUnanswered);

if ($result->num_rows > 0) {
    // Output the table headers
    echo "<table border='1'>
            <tr>";
    // Fetch field names for table headers dynamically
    while ($field = $result->fetch_field()) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";

    // Output each row of the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No data found in the unanswered table.<br>";
}

// Close the connection
$conn->close();
?>

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

// Describe the unanswered table
$describeUnansweredTable = "DESCRIBE unanswered";

// Execute describe table query
$result = $conn->query($describeUnansweredTable);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['Field'] . "</td>
                <td>" . $row['Type'] . "</td>
                <td>" . $row['Null'] . "</td>
                <td>" . $row['Key'] . "</td>
                <td>" . $row['Default'] . "</td>
                <td>" . $row['Extra'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No table structure found or an error occurred.<br>";
}

// Close the connection
$conn->close();
?>

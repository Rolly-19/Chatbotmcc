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

// Get all table names
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        echo "<h2>Table: $tableName</h2>";
        
        // Describe the table
        $describeSql = "DESCRIBE `$tableName`";
        $describeResult = $conn->query($describeSql);

        if ($describeResult->num_rows > 0) {
            echo "<table border='1' cellpadding='10' cellspacing='0'>";
            echo "<tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                    <th>Default</th>
                    <th>Extra</th>
                  </tr>";

            // Fetch and display each row
            while ($descRow = $describeResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($descRow['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($descRow['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($descRow['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($descRow['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($descRow['Default']) . "</td>";
                echo "<td>" . htmlspecialchars($descRow['Extra']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Failed to retrieve the structure of the table.</p>";
        }
    }
} else {
    echo "No tables found in the database.";
}

// Close the connection
$conn->close();
?>
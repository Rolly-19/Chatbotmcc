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

// Query to retrieve all table names
$showTablesQuery = "SHOW TABLES";
$result = $conn->query($showTablesQuery);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        echo "<h2>Table: $tableName</h2>";

        // Query to retrieve table contents
        $selectQuery = "SELECT * FROM $tableName";
        $tableResult = $conn->query($selectQuery);

        if ($tableResult && $tableResult->num_rows > 0) {
            // Display table headers dynamically
            echo "<table border='1' cellspacing='0' cellpadding='10'>";
            echo "<tr>";
            while ($field = $tableResult->fetch_field()) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";

            // Display table rows
            while ($row = $tableResult->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if (strpos($key, 'avatar') !== false || strpos($key, 'logo') !== false || strpos($key, 'IMAGE') !== false) {
                        // Check for image columns
                        if ($value) {
                            echo "<td><img src='$value' alt='$key' style='max-width: 50px; max-height: 50px;'></td>";
                        } else {
                            echo "<td>N/A</td>";
                        }
                    } else {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found in table '$tableName'.</p>";
        }
    }
} else {
    echo "<p>No tables found in the database.</p>";
}

// Close the connection
$conn->close();
?>
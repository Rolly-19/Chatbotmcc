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

// Get all tables in the database
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each table
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        echo "<h2>Table: $tableName</h2>";
        
        // Fetch data from the table
        $tableDataSql = "SELECT * FROM `$tableName`";
        $tableDataResult = $conn->query($tableDataSql);

        if ($tableDataResult->num_rows > 0) {
            // Display table data
            echo "<table border='1' cellpadding='10' cellspacing='0'>";
            
            // Fetch and display column headers
            $fields = $tableDataResult->fetch_fields();
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            // Fetch and display rows
            while ($dataRow = $tableDataResult->fetch_assoc()) {
                echo "<tr>";
                foreach ($dataRow as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data found in this table.</p>";
        }
    }
} else {
    echo "No tables found in the database.";
}

// Close the connection
$conn->close();
?>
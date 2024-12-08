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

// Query to fetch all rows from the unanswered table
$sql = "SELECT * FROM unanswered";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Unanswered Table</h2>";
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>Question</th>
            <th>No. of Asks</th>
          </tr>";

    // Output each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['question']) . "</td>";
        echo "<td>" . htmlspecialchars($row['no_asks']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No data found in the unanswered table.";
}

// Close the connection
$conn->close();
?>
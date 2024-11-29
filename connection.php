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

// SQL to fetch data from the `user_logins` table
$sql = "SELECT ul.id, ul.user_id, u.firstname, u.lastname, ul.time_in, ul.time_out
        FROM user_logins ul
        INNER JOIN users u ON ul.user_id = u.id
        ORDER BY ul.time_in DESC";
$result = $conn->query($sql);

// Display the table
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Time In</th>
            <th>Time Out</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['firstname']}</td>
                <td>{$row['lastname']}</td>
                <td>{$row['time_in']}</td>
                <td>" . ($row['time_out'] ?? "N/A") . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No records found in the `user_logins` table.";
}

// Close the connection
$conn->close();
?>

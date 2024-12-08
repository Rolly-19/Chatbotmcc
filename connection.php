<?php
$servername = "localhost";
$username = "your_username"; // Replace with your DB username
$password = "your_password"; // Replace with your DB password
$dbname = "chatbot_db"; // Replace with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all data from the `users` table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Display the table
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Password</th>
            <th>Avatar</th>
            <th>Last Login</th>
            <th>Date Added</th>
            <th>Date Updated</th>
            <th>OTP</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['firstname']}</td>
                <td>{$row['lastname']}</td>
                <td>{$row['username']}</td>
                <td>{$row['password']}</td>
                <td>{$row['avatar']}</td>
                <td>{$row['last_login']}</td>
                <td>{$row['date_added']}</td>
                <td>{$row['date_updated']}</td>
                <td>{$row['OTP']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No users found.";
}

// Close the connection
$conn->close();
?>

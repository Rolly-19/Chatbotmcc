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

// Define the new user values
$firstname = "Rolly";
$lastname = "Recabar";
$username = "recabarrolly@gmail.com";
$password = password_hash("rollyrecabar", PASSWORD_BCRYPT); // Securely hash the password
$otp = "";
$phone = "09631064348";
$avatar = NULL; // Optional: Set to NULL if no avatar
$dateAdded = date("Y-m-d H:i:s");

// Insert the user into the database
$sql = "INSERT INTO users (firstname, lastname, username, password, OTP, phone, avatar, date_added) 
        VALUES ('$firstname', '$lastname', '$username', '$password', '$otp', '$phone', '$avatar', '$dateAdded')";

if ($conn->query($sql) === TRUE) {
    echo "New user added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>

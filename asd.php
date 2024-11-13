<?php

require_once('classes/DBConnection.php');
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Assuming you have a $conn connection object to your database
    $stmt = $conn->prepare("DELETE FROM unanswered WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        echo 1;  // Return success
    } else {
        echo 0;  // Return failure
    }

    $stmt->close();
}
?>

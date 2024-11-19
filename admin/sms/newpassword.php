<?php
include '../conn.php';
session_start();

// Ensure user has verified OTP before they can reset the password
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: verify-otp.php");
    exit();
}

$success = '';
$error = '';

// Check for verification success message
if (isset($_SESSION['verification_success'])) {
    $success = $_SESSION['verification_success'];
    unset($_SESSION['verification_success']); // Clear the message after displaying
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the incoming user input (new password and confirm password)
    $password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];
    $phone = $_SESSION['reset_phone'];  // Assuming phone number is stored in session after OTP verification

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the new password securely using password_hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // More secure hashing

        // Prepare the database query to update the password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE phone = ?");
        $stmt->bind_param("ss", $hashed_password, $phone);  // Bind parameters securely

        if ($stmt->execute()) {
            // Password updated successfully, clear OTP-related session data
            unset($_SESSION['otp_verified']);
            unset($_SESSION['reset_phone']);

            $_SESSION['success_message'] = "Your password has been updated successfully!";
    
            // Redirect to login page
            header("Location: ../login.php");
            exit();
        } else {
            // Error occurred during the password update
            $error = "Password update failed. Please try again.";
        }

        $stmt->close();  // Close the prepared statement
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }
        input[type="password"] {
            padding: 10px;
            width: 100%;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #fd2323;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .instructions {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }

        .success {
            color: green;
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Reset Your Password</h2>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Display error message if passwords don't match or update fails -->
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validatePassword()">
            <label for="new-password">New Password:</label>
            <input type="password" id="new-password" name="new-password" required placeholder="Enter new password" minlength="8">

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required placeholder="Confirm your password" minlength="8">

            <button type="submit">Reset Password</button>
        </form>

        <div class="instructions">
            <p>Password must be at least 8 characters long.</p>
        </div>
    </div>

    <script>
        function validatePassword() {
            // Get the password values
            var newPassword = document.getElementById("new-password").value;
            var confirmPassword = document.getElementById("confirm-password").value;

            // Check if passwords match
            if (newPassword !== confirmPassword) {
                // Show error message and prevent form submission
                alert("Passwords do not match!");
                return false;
            }

            // If passwords match, allow form submission
            return true;
        }
    </script>

</body>
</html>

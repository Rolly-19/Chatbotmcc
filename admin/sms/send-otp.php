<?php
require_once '../conn.php';

session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to standardize phone number format for comparison
function standardizePhoneNumber($phone) {
    // Remove any non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // If it starts with 63, remove it
    if (substr($phone, 0, 2) === '63') {
        $phone = '0' . substr($phone, 2);
    }
    
    return $phone;
}

// Function to format phone for SMS sending
function formatPhoneForSMS($phone) {
    // Remove any non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // If it starts with 0, replace with +63
    if (substr($phone, 0, 1) === '0') {
        return '+63' . substr($phone, 1);
    }
    
    // If it starts with 63, add +
    if (substr($phone, 0, 2) === '63') {
        return '+' . $phone;
    }
    
    // If no prefix, assume it needs 63
    return '+63' . $phone;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $phone = $_POST['phone'];
        
        // Standardize phone number for database lookup
        $standardizedPhone = standardizePhoneNumber($phone);
        
        // Debug log
        error_log("Looking up standardized phone: " . $standardizedPhone);
        
        // Check if phone exists in database
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        $stmt->bind_param("s", $standardizedPhone);
        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generate OTP
            $otp = sprintf("%06d", mt_rand(0, 999999));
            $_SESSION['reset_phone'] = $standardizedPhone;
            $_SESSION['OTP_TIMESTAMP'] = time();
            
            // Update OTP in database
            $update_stmt = $conn->prepare("UPDATE users SET SMSOTP = ?, OTP_TIMESTAMP = CURRENT_TIMESTAMP WHERE phone = ?");
            if (!$update_stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }
            
            $update_stmt->bind_param("ss", $otp, $standardizedPhone);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update OTP: " . $update_stmt->error);
            }
            
            // Format phone number for SMS sending
            $smsPhone = formatPhoneForSMS($standardizedPhone);
            
            // Send OTP via SMS
            $message = "Your OTP for password reset is: " . $otp;
            
            // Log before sending SMS
            error_log("Attempting to send SMS to: " . $smsPhone);
            error_log("Message content: " . $message);
            
            if (!sendSMS($smsPhone, $message)) {
                throw new Exception("Failed to send SMS. Please try again later.");
            }
            
            // If we got here, everything worked
            $_SESSION['success_message'] = "OTP sent successfully! Please check your phone.";
            header("Location: verify-otp.php");
            exit();
            
        } else {
            throw new Exception("Phone number not found in our records");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Forgot Password Error: " . $error);
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
        input[type="tel"] {
            padding: 10px;
            width: 85%;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color:  #fd2323;
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
        .instructions {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }

        .login {
            font-size: 14px;
            color: black;
            margin-top: 10px;
            font-weight:bold;
            cursor:pointer;
        }

        .login a{
            text-decoration:none;
            color: grey;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Enter Your Phone Number</h2>
        <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="tel" id="phone" name="phone" pattern="[0-9]*" required placeholder="Enter your phone number (e.g., 09123456789)"  maxlength="11">
            <button type="submit">Send OTP</button>
        </form>
        <div class="instructions">
            <p>Please enter your 11-digit phone number. An OTP will be sent to this number.</p>
        </div>

        <div class="login">
            <a href="../choose-forgotpass.php"> Back to Login</a>
        </div>
    </div>
    <script>
    document.getElementById('phone').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    });
</script>
</body>
</html>

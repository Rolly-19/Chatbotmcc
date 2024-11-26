<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Security Options</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body {
            height: 100%;
            background-image: url('wave.png'); /* Background image from the second example */
            background-size: cover;
            background-position: center;
            font-family: 'Roboto', sans-serif;
        }
        .utility-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 30px;
        }
        .utility-panel {
            max-width: 400px;
            width: 90%;
            background-color: rgba(255, 255, 255, 0.9); /* Optional: Add some transparency */
            border-radius: 10px; /* Border radius matching second design */
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .utility-btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 20px 0;
            background-color: #fd2323; /* Button color from second design */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        .utility-btn:hover {
            background-color: #f71d1d; /* Hover effect matching the second design */
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .page-title {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="utility-container">
        <div class="utility-panel">
            <div class="start-end">
                <img src="logo.png" width="80" height="80" alt="Logo"> <!-- Logo added similar to second design -->
            </div>
            <h1 class="page-title">Account Security</h1>
            <p class="text-center mb-4">Choose an option below to secure your account.</p>
            <a href="forgot-password.php" class="utility-btn">
                Reset Password
            </a>
            <a href="sms/send-otp.php" class="utility-btn">
                Generate OTP
            </a>
        </div>
    </div>
</body>
</html>

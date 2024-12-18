<?php 
require_once "server.php"; 
$email = $_SESSION['email'];
if($email == false){
  header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            background-image: url('wave.png'); /* Update with your image path */
            background-size: cover; /* Ensure the image covers the entire background */
            background-position: center; /* Center the image */
        }
        .verification-container {
            max-width: 400px;
            width: 90%;
            background-color: rgba(255, 255, 255, 0.9); /* Optional: Add some transparency */
            border-radius: 10px; /* Optional: Add border radius */
        }
        .btn-danger {
            background-color: #fd2323;
        }
        .btn-danger:hover {
            background-color: #f71d1d;
        }
        .start-end {
            text-align: center; /* Center align the logo */
            margin-bottom: 20px; /* Add some space below the logo */
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="verification-container p-4 rounded shadow">
        <div class="start-end">
            <img src="logo.png" width="80" height="80" alt="Logo">
        </div>
        <h2 class="text-center mb-3">Enter OTP</h2>
        <p class="text-center mb-4">Enter the OTP sent to your email.</p>
        <?php 
        if(isset($_SESSION['info'])){
            ?>
            <div class="alert alert-success text-center" style="padding: 0.4rem 0.4rem">
                <?php echo $_SESSION['info']; ?>
            </div>
            <?php
        }
        ?>
        <?php
        if(count($errors) > 0){
            ?>
            <div class="alert alert-danger text-center">
                <?php
                foreach($errors as $showerror){
                    echo $showerror;
                }
                ?>
            </div>
            <?php
        }
        ?>
        <form action="reset-code.php" method="POST" autocomplete="off">
            <div class="mb-3">
                <input type="text" name="otp" class="form-control" placeholder="Enter OTP code" required>
            </div>
            <button type="submit" name="check-reset-otp" class="btn btn-danger w-100 mb-3">Submit</button>
            <p class="text-center mb-0">Didn’t receive a code? <a href="forgot-password.php" class="text-danger">Resend Code</a></p>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

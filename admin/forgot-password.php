<?php require_once "server.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    html, body {
        height: 100%;
        background-image: url('wave.png'); /* Update with your image path */
        background-size: cover;
        background-position: center;
    }
    .forgot-password-container {
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
    <div class="forgot-password-container bg-white p-4 rounded shadow">
    <div class="start-end"> 
    <img src="logo.png" width="80" height="80" alt="Logo"></div>
        <h2 class="text-center mb-3">Forgot Password?</h2>
        <p class="text-center mb-4">Enter your email address and we'll send you instructions to reset your password.</p>
        <?php
                        if(count($errors) > 0){
                            ?>
                    <div class="alert alert-danger text-center">
                        <?php 
                                    foreach($errors as $error){
                                        echo $error;
                                    }
                                ?>
                    </div>
                    <?php
                        }
                    ?>
        <form action="forgot-password.php" method="POST" autocomplete="">
            <div class="mb-3">
                <input type="email" name="email" class="form-control"  value="<?php echo $email ?>" placeholder="Enter your email" required>
            </div>
            <button type="submit" name="check-email" value="Continue" class="btn btn-danger w-100 mb-3">Send Reset Link</button>
            <p class="text-center mb-0">Remember your password? <a href="index.php" class="text-danger">Sign In</a></p>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($emailSent): ?>
            Swal.fire({
                icon: 'success',
                title: 'Email Sent',
                text: 'Please check your email for the password reset link.',
                confirmButtonColor: '#fd2323'
            });
        <?php endif; ?>
    </script>
</body>
</html>

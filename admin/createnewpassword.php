<?php require_once "server.php"; ?>
<?php 
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
    <title>Create New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            background-color: #faf9f6;
        }
        .new-password-container {
            max-width: 400px;
            width: 90%;
        }
        .btn-danger {
            background-color: #fd2323;
        }
        .btn-danger:hover {
            background-color: #f71d1d;
        }
        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .start-end {
            text-align: right;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="new-password-container bg-white p-4 rounded shadow">
        <div class="start-end"> <img src="logo.png" width="80" height="80"></div>
        <form action="createnewpassword.php" method="POST" autocomplete="off">
        <h2 class="text-center mb-3">Create New Password</h2>
        <p class="text-center mb-4">Please enter your new password and confirm it.</p>
        <?php 
                    if(isset($_SESSION['info'])){
                        ?>
                        <div class="alert alert-success text-center">
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
       
            <div class="mb-3 password-container">
                <input type="password" name="password" class="form-control" placeholder="New Password" required>
                <span class="password-toggle" onclick="togglePassword('new-password', 'toggleNewPasswordIcon')">
                    <i class="fas fa-eye" id="toggleNewPasswordIcon"></i>
                </span>
            </div>
            <div class="mb-3 password-container">
                <input type="password" name="cpassword" class="form-control" placeholder="Confirm Password" required>
                <span class="password-toggle" onclick="togglePassword('confirm-password', 'toggleConfirmPasswordIcon')">
                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                </span>
            </div>
            <button type="submit" name="change-password" value="" class="btn btn-danger w-100 mb-3">Reset Password</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'cpassword';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

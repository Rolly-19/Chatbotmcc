<?php require_once "server.php"; ?>
<?php
if($_SESSION['info'] == false){
    header('Location: index.php');  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Now</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            background-image: url('wave.png'); /* Update with your image path */
            background-size: cover; /* Ensure the image covers the entire background */
            background-position: center; /* Center the image */
        }
        .login-now-container {
            max-width: 300px;
            width: 90%;
            background-color: rgba(255, 255, 255, 0.9); /* Add some transparency */
            border-radius: 10px; /* Optional: Add border radius */
            padding: 20px; /* Optional: Add padding */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Optional: Add shadow */
        }
        .btn-danger {
            background-color: #fd2323;
        }
        .btn-danger:hover {
            background-color: #f71d1d;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="login-now-container text-center">
        <?php 
        if(isset($_SESSION['info'])){
            ?>
            <div class="alert alert-success text-center">
                <?php echo $_SESSION['info']; ?>
            </div>
            <?php
        }
        ?>
        <h2 class="mb-4">Welcome!</h2> <!-- Optional: Add a welcome message -->
        <a href="login.php" class="btn btn-danger w-100">Login Now</a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js

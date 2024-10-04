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
            background-color: #faf9f6;
        }
        .login-now-container {
            max-width: 300px;
            width: 90%;
        }
        .btn-danger {
            background-color: #fd2323;
        }
        .btn-danger:hover {
            background-color: #f71d1d;
        }
        .start-end {
            text-align: right;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="login-now-container bg-white p-4 rounded shadow text-center">
    <?php 
            if(isset($_SESSION['info'])){
                ?>
                <div class="alert alert-success text-center">
                    <?php echo $_SESSION['info']; ?>
                </div>
                <?php
            }
            ?>
        <a href="login.php" class="btn btn-danger w-100">Login Now</a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

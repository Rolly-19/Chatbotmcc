

<?php ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php require_once('../config.php')
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<body class="hold-transition login-page" style="background-image: url('wave.png'); background-size: cover; background-position: center;">
  <script>
    start_loader()
  </script>
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <img src="logo.png" alt="Logo" class="login-logo">
      <a href="./" class="h1"><b>Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="username" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" id="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span id="toggle-password" class="fas fa-eye"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <a href="3ways.php" class="text-left">Forgot Password?</a>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <a href="<?php echo base_url ?>" class="btn btn-link">Go to Website</a>
          </div>
          <div class="col-6 text-right">
            <button type="submit" class="btn btn-primary">Sign In</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
    
    $('#toggle-password').click(function(){
      var passwordField = $('#password');
      var passwordFieldType = passwordField.attr('type');
      if (passwordFieldType === 'password') {
        passwordField.attr('type', 'text');
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        passwordField.attr('type', 'password');
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
      }
    });
  });
</script>

<style>
  .login-logo {
    width: 100px; /* Adjust the width as needed */
    margin-bottom: 5px; /* Adjust the spacing as needed */
    margin-top: 5px;
  }
  #toggle-password {
    font-size: .8rem; /* Adjust the size as needed */
    cursor: pointer; /* Change cursor to pointer for better UX */
  }
  .btn-link {
    color: #007bff; /* Bootstrap primary link color */
    text-decoration: none;
    padding: 0; /* Remove padding for a cleaner look */
  }
  .btn-link:hover {
    text-decoration: underline; /* Underline on hover for better UX */
  }
</style>

</body>
</html>

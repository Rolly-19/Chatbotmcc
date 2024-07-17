<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page" style="background-image: url('mcc.png'); background-size: cover; background-position: center;">
  <script>
    start_loader()
  </script>
<div class="login-box" style="position: absolute; right: 20%; top: 50%; transform: translateY(-50%);">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="./" class="h1"><b>Sign Up</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Create a new account</p>

      <form id="signup-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="new_username" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <a href="<?php echo base_url ?>">Go to Website</a>
          </div>
          <div class="col-6 text-right">
            <button type="submit" class="btn btn-primary">Sign Up</button>
          </div>
        </div>
      </form>

      <hr>
      <p class="login-box-msg">Already have an account? <a href="login.php">Sign in here!</a></p>

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
  });
</script>
</body>
</html>

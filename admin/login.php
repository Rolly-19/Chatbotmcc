<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<body class="hold-transition login-page" style="background-image: url('wave.png'); background-size: cover; background-position: center;">
  <script>
    start_loader();
  </script>
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <img src="logo.png" alt="Logo" class="login-logo">
      <a href="./" class="h1"><b>Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <!-- Form with hidden input for reCAPTCHA -->
      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="username" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span id="toggle-password" class="fas fa-eye"></span>
            </div>
          </div>
        </div>
        <input type="hidden" id="recaptchaToken" name="recaptchaToken">
        <div class="row">
          <div class="col-12">
            <a href="<?php echo base_url ?>admin/3ways" class="text-left">Forgot Password?</a>
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

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js?render=6LcT_pIqAAAAAIVwkx4EyPagkk-w-c0RhI-P-FLW"></script>
<script>
    const siteKey = '6LcT_pIqAAAAAIVwkx4EyPagkk-w-c0RhI-P-FLW'; // Replace with your site key from Google
    const loginForm = document.getElementById('login-frm');

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent form submission
        grecaptcha.ready(() => {
            grecaptcha.execute(siteKey, { action: 'login' })
                .then((token) => {
                    document.getElementById('recaptchaToken').value = token;
                    loginForm.submit(); // Submit the form after setting token
                })
                .catch((err) => {
                    alert("Failed to generate reCAPTCHA token. Please try again.");
                    console.error("reCAPTCHA Error: ", err);
                });
        });
    });
</script>
<script>
  $(document).ready(function(){
    end_loader();
    
    $('#toggle-password').click(function(){
      const passwordField = $('#password');
      const passwordFieldType = passwordField.attr('type');
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
    width: 100px; 
    margin-bottom: 5px;
    margin-top: 5px;
  }
  #toggle-password {
    font-size: .8rem; 
    cursor: pointer;
  }
  .btn-link {
    color: #007bff;
    text-decoration: none;
    padding: 0;
  }
  .btn-link:hover {
    text-decoration: underline;
  }
</style>
</body>
</html>

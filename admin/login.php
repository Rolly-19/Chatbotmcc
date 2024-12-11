<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
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
  <input type="hidden" id="recaptchaToken" name="recaptchaToken">
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
  <div class="form-check mb-3">
          <input type="checkbox" class="form-check-input" id="terms_conditions" disabled>
          <label for="terms_conditions" class="form-check-label">
            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
          </label>
        </div>
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
<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Welcome to the Madridejos Community College (MCC) Chat Bot System. By using this system, you agree to the following terms and conditions:</p>
        <ul>
          <li><strong>Eligibility:</strong> The system is only for use by students of Madridejos Community College. Access is restricted to registered students with valid credentials.</li>
          <li><strong>Account Security:</strong> You are responsible for maintaining the confidentiality of your account details, including your email and password. Any unauthorized access to your account is your responsibility.</li>
          <li><strong>Usage Policy:</strong> The system should be used only for legitimate academic or administrative purposes. Any misuse, including but not limited to, tampering with system functions or data, may result in the suspension or termination of your access.</li>
          <li><strong>Data Privacy:</strong> Personal information collected through the system is used for registration and scheduling purposes only. We commit to protecting your data according to applicable data privacy laws and regulations.</li>
          <li><strong>Prohibited Activities:</strong> You are prohibited from engaging in activities that could harm the system's integrity or security, including but not limited to, hacking attempts, unauthorized data collection, or distributing malicious software.</li>
          <li><strong>System Availability:</strong> While we strive to ensure the system is always available, we do not guarantee 100% uptime. The system may be temporarily unavailable for maintenance or unforeseen technical issues.</li>
          <li><strong>Changes to Terms:</strong> We reserve the right to modify these terms and conditions at any time. Any changes will be posted on this page, and it is your responsibility to review them regularly.</li>
          <li><strong>Liability:</strong> Madridejos Community College is not liable for any direct, indirect, incidental, or consequential damages arising from your use of the system, including data loss or disruption of service.</li>
          <li><strong>Acceptance:</strong> By using this system, you agree to comply with these terms. If you do not agree with any of these terms, you must not use the system.</li>
        </ul>
        <p>If you have any questions or concerns about these terms, please contact the MCC support team at <strong>MCC Chat Bot System</strong>.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Decline</button>
        <button type="button" id="acceptTerms" class="btn btn-success" data-bs-dismiss="modal">Accept</button>
      </div>
    </div>
  </div>
</div>
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle (includes Popper and JavaScript) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<script src="https://www.google.com/recaptcha/api.js?render=6LcT_pIqAAAAAIVwkx4EyPagkk-w-c0RhI-P-FLW"></script>
<script>
  grecaptcha.ready(function() {
    grecaptcha.execute('6LcT_pIqAAAAAIVwkx4EyPagkk-w-c0RhI-P-FLW', { action: 'login' }).then(function(token) {
      document.getElementById('recaptchaToken').value = token;
    });
  });

  // document.getElementById("login-frm").addEventListener("submit", function(event) {
  //   const recaptchaTokenField = document.getElementById('recaptchaToken');

  //   if (!recaptchaTokenField.value) {
  //       event.preventDefault();
  //       grecaptcha.execute('6LcT_pIqAAAAAIVwkx4EyPagkk-w-c0RhI-P-FLW', { action: 'login' }).then(function(token) {
  //           recaptchaTokenField.value = token;
  //           document.getElementById("login-frm").submit();
  //       });
  //   }
  // });
</script>
<script>
  $(document).ready(function(){
    // Show/hide password functionality
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

    // Enable checkbox and Sign-In button after accepting terms
    $('#acceptTerms').click(function () {
      // Enable and check the checkbox
      $('#terms_conditions').prop('disabled', false).prop('checked', true);
      // Enable the login button
      $('#login-btn').prop('disabled', false);
    });

    // Ensure checkbox styles update properly when toggled
    $('#terms_conditions').on('change', function () {
      if (this.checked) {
        this.style.accentColor = 'dodgerblue';
      } else {
        this.style.accentColor = 'gray';
      }
    });

    // Debugging tip: Ensure the loader ends properly
    end_loader();
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

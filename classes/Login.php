<?php
require_once '../config.php';
class Login extends DBConnection {
    private $settings;
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;

        parent::__construct();
        ini_set('display_error', 1);
    }
    public function __destruct(){
        parent::__destruct();
    }
    public function index(){
        echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
    }
    public function login() {
        extract($_POST);
    
        // Initialize session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Verify reCAPTCHA response
        $recaptchaResponse = $_POST['g-recaptcha-response'];
        $secretKey = '6LeZaY0qAAAAAOwWKPywmBavlc05hwcAGFV_RCpf'; // Replace with your secret key
        $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
    
        $response = file_get_contents($verifyUrl . "?secret=" . $secretKey . "&response=" . $recaptchaResponse);
        $responseKeys = json_decode($response, true);
    
        if (!$responseKeys['success']) {
            return json_encode(array(
                'status' => 'recaptcha_failed',
                'message' => 'reCAPTCHA verification failed. Please try again.'
            ));
        }
    
        // Existing login logic...
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $qry = $stmt->get_result();

        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();

            // If password is correct, allow login regardless of attempts
            if (password_verify($password, $user['password'])) {
                // Reset attempts on successful login
                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['login_blocked_until']); // Remove any existing block

                // Update last_login timestamp in the database
                $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();

                // Insert a new time-in record in user_logins
                $stmt = $this->conn->prepare("INSERT INTO user_logins (user_id, time_in) VALUES (?, NOW())");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();

                // Store the user_logins ID in the session for later time-out tracking
                $_SESSION['user_login_id'] = $this->conn->insert_id;

                // Set user session data
                foreach ($user as $k => $v) {
                    if (!is_numeric($k) && $k != 'password') {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 1);

                return json_encode(array('status' => 'success'));
            }
        }

        // If we reach here, the login attempt failed
        // Check if user is currently blocked
        if (isset($_SESSION['login_blocked_until'])) {
            if (time() < $_SESSION['login_blocked_until']) {
                $wait_minutes = ceil(($_SESSION['login_blocked_until'] - time()) / 60);
                return json_encode(array(
                    'status' => 'blocked',
                    'message' => "Please wait {$wait_minutes} minutes before trying again."
                ));
            } else {
                // Reset if block time has passed
                unset($_SESSION['login_blocked_until']);
                $_SESSION['login_attempts'] = 0;
            }
        }

        // Increment failed attempts
        $_SESSION['login_attempts']++;

        // Block user after 3 attempts
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['login_blocked_until'] = time() + (5 * 60); // 5 minutes
            return json_encode(array(
                'status' => 'blocked',
                'message' => 'Too many failed attempts. Please try again in 5 minutes.'
            ));
        }

        $remaining = 3 - $_SESSION['login_attempts'];
        return json_encode(array(
            'status' => 'incorrect',
            'message' => "Incorrect email or password. {$remaining} attempts remaining."
        ));
    }
    public function logout() {
        // Initialize session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get the current user ID from session
        $user_id = $this->settings->userdata('id');
        $user_login_id = isset($_SESSION['user_login_id']) ? $_SESSION['user_login_id'] : null;

        // Update the time_out for the current session in user_logins
        if ($user_login_id) {
            $stmt = $this->conn->prepare("UPDATE user_logins SET time_out = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user_login_id);
            $stmt->execute();
        }

        // Destroy session and redirect
        if ($this->settings->sess_des()) {
            redirect('admin/login.php');
        }
    }
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
    case 'login':
        echo $auth->login();
        break;
    case 'logout':
        echo $auth->logout();
        break;
    default:
        echo $auth->index();
        break;
}

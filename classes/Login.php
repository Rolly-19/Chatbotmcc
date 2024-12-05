<?php
require_once '../config.php';

class Login extends DBConnection {
    private $settings;

    public function __construct() {
        global $_settings;
        $this->settings = $_settings;

        parent::__construct();
        ini_set('display_error', 1);
    }

    public function __destruct() {
        parent::__destruct();
    }

    public function index() {
        echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
    }

    public function login() {
        extract($_POST);
    
        // Verify reCAPTCHA token
        $recaptchaSecret = '6LcT_pIqAAAAAMkQSZYz_LmgCfhsKKm1RT0YabnL'; // Your secret key
        $recaptchaResponse = $_POST['recaptchaToken'] ?? null;
    
        if (!$recaptchaResponse) {
            return json_encode([
                'status' => 'error',
                'message' => 'reCAPTCHA validation failed: Token is missing.'
            ]);
        }
    
        $recaptchaURL = 'https://www.google.com/recaptcha/api/siteverify';
        $response = @file_get_contents($recaptchaURL . "?secret={$recaptchaSecret}&response={$recaptchaResponse}");
        if (!$response) {
            return json_encode([
                'status' => 'error',
                'message' => 'Unable to validate reCAPTCHA. Please try again later.'
            ]);
        }
    
        $responseKeys = json_decode($response, true);
    
        if (!$responseKeys["success"] || $responseKeys["score"] < 0.5 || $responseKeys["action"] !== "login") {
            return json_encode([
                'status' => 'recaptcha_failed',
                'message' => 'reCAPTCHA validation failed. Please try again.'
            ]);
        }
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Login attempts logic
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
    
        if (isset($_SESSION['login_blocked_until']) && time() < $_SESSION['login_blocked_until']) {
            $wait_minutes = ceil(($_SESSION['login_blocked_until'] - time()) / 60);
            return json_encode([
                'status' => 'blocked',
                'message' => "Please wait {$wait_minutes} minutes before trying again."
            ]);
        }
    
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        if (!$stmt) {
            return json_encode(['status' => 'error', 'message' => 'Database error: ' . $this->conn->error]);
        }
    
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $qry = $stmt->get_result();
    
        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Reset failed attempts
                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['login_blocked_until']);
    
                // Update last login
                $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
    
                // Log successful login
                return json_encode([
                    'status' => 'success',
                    'message' => 'Login successful!'
                ]);
            }
        }
    
        // Failed login logic
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['login_blocked_until'] = time() + (5 * 60);
            return json_encode([
                'status' => 'blocked',
                'message' => 'Too many failed attempts. Please try again in 5 minutes.'
            ]);
        }
    
        $remaining = 3 - $_SESSION['login_attempts'];
        return json_encode([
            'status' => 'incorrect',
            'message' => "Incorrect email or password. {$remaining} attempts remaining."
        ]);
    }
}

    public function logout() {
        // Initialize session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Get the current user ID from session
        $user_id = $this->settings->userdata('id');
        $user_login_id = $_SESSION['user_login_id'] ?? null;
    
        // Update the time_out for the current session in user_logins
        if ($user_login_id) {
            $stmt = $this->conn->prepare("UPDATE user_logins SET time_out = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user_login_id);
            $stmt->execute();
        }
    
        // Destroy session and return a JSON response
        if ($this->settings->sess_des()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Logout successful!',
                'redirect' => base_url . 'admin/login'
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Logout failed. Please try again.'
            ]);
            exit;
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

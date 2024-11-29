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
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
    
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $qry = $stmt->get_result();
    
        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['login_blocked_until']);
    
                foreach ($user as $k => $v) {
                    if (!is_numeric($k) && $k != 'password') {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 1);
    
                return json_encode(array('status' => 'success'));
            }
        }
    
        if (isset($_SESSION['login_blocked_until']) && time() < $_SESSION['login_blocked_until']) {
            $waitMinutes = ceil(($_SESSION['login_blocked_until'] - time()) / 60);
            return json_encode(array(
                'status' => 'blocked',
                'message' => "Please wait {$waitMinutes} minutes before trying again."
            ));
        }
    
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['login_blocked_until'] = time() + (5 * 60);
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
    public function logout(){
        if($this->settings->sess_des()){
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

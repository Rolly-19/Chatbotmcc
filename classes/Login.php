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
        
        // Initialize login attempts if not set
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        
        // Check credentials first - if correct, allow login regardless of attempts
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

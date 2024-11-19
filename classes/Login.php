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
        
        // Check attempts from session
        session_start();
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        
        // Check if user is blocked
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
        
        // Check user credentials
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $qry = $stmt->get_result();
        
        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Reset attempts on successful login
                $_SESSION['login_attempts'] = 0;
                
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
        
        // Failed login attempt
        $_SESSION['login_attempts']++;
        
        // Block user after 3 attempts
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['login_blocked_until'] = time() + (30 * 60); // 30 minutes
            return json_encode(array(
                'status' => 'blocked',
                'message' => 'Too many failed attempts. Please try again in 30 minutes.'
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

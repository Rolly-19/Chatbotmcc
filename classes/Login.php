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
    
        // Using prepared statements to prevent SQL injection
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $qry = $stmt->get_result();
    
        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
    
            // Check if the user is locked out due to too many failed attempts
            $attempt_time = strtotime($user['last_attempt']);
            $current_time = time();
            $max_attempts = 3;
            $lockout_time = 60; // 1 minute lockout period
    
            // If attempts are more than allowed and lockout period is not passed
            if ($user['login_attempts'] >= $max_attempts && ($current_time - $attempt_time) < $lockout_time) {
                return json_encode(array('status' => 'locked_out', 'message' => 'Too many failed login attempts. Please try again after 1 minute.'));
            }
    
            // If the provided password matches the hashed password in the database
            if (password_verify($password, $user['password'])) {
                // Reset login attempts on successful login
                $this->reset_login_attempts($user['id']);
    
                // Set user data in session or some other storage
                foreach ($user as $k => $v) {
                    if (!is_numeric($k) && $k != 'password') {
                        $this->settings->set_userdata($k, $v);
                    }
                }
    
                // Store the login type (1 could mean logged in)
                $this->settings->set_userdata('login_type', 1);
    
                // Return success message as JSON
                return json_encode(array('status' => 'success'));
            } else {
                // Increment login attempts and record the last attempt time
                $this->increment_login_attempts($user['id']);
                return json_encode(array('status' => 'incorrect'));
            }
        } else {
            // User not found
            return json_encode(array('status' => 'incorrect'));
        }
    }
    
    private function increment_login_attempts($user_id) {
        $stmt = $this->conn->prepare("UPDATE users SET login_attempts = login_attempts + 1, last_attempt = NOW() WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    
    private function reset_login_attempts($user_id) {
        $stmt = $this->conn->prepare("UPDATE users SET login_attempts = 0, last_attempt = NULL WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
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

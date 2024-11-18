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

    // Index function for access denied
    public function index(){
        echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
    }

    // Function to handle login
    public function login(){
        session_start();

        // Check if user is locked out
        if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) {
            $remaining_time = $_SESSION['lockout_time'] - time();
            return json_encode(array('status' => 'locked', 'remaining_time' => $remaining_time));
        }

        // Check for username and password from POST
        extract($_POST);

        // Using prepared statements to prevent SQL injection
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND password = md5(?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $qry = $stmt->get_result();

        // If login is successful
        if($qry->num_rows > 0){
            // Reset login attempts after successful login
            $_SESSION['login_attempts'] = 0;

            // Set user data in session
            foreach($qry->fetch_assoc() as $k => $v){
                if(!is_numeric($k) && $k != 'password'){
                    $this->settings->set_userdata($k,$v);
                }
            }
            $this->settings->set_userdata('login_type',1);
            return json_encode(array('status'=>'success'));
        } else {
            // Increment login attempts on failure
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            $_SESSION['login_attempts']++;

            // Check if the number of attempts has reached the limit (3 attempts)
            if ($_SESSION['login_attempts'] >= 3) {
                // Lock user out for 15 minutes
                $_SESSION['lockout_time'] = time() + 15 * 60;
                return json_encode(array('status'=>'locked', 'remaining_time' => 15 * 60));
            }

            // If login fails but under 3 attempts, show incorrect credentials
            return json_encode(array('status'=>'incorrect'));
        }
    }

    // Logout function
    public function logout(){
        if($this->settings->sess_des()){
            session_start();
            // Reset the login attempts and lockout time on logout
            unset($_SESSION['login_attempts']);
            unset($_SESSION['lockout_time']);
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

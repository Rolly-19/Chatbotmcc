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
    public function login(){
        extract($_POST);
    
        // First, check if the user exists and retrieve failed attempts, last failed attempt timestamp, and password
        $stmt = $this->conn->prepare("SELECT failed_attempts, last_failed_attempt, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $qry = $stmt->get_result();
    
        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
    
            // If the account is locked (failed 3 times), check the lockout time
            if ($user['failed_attempts'] >= 3) {
                $last_failed = strtotime($user['last_failed_attempt']);
                $current_time = time();
    
                // Lockout period (1 minute)
                if ($current_time - $last_failed < 60) { // 60 seconds = 1 minute
                    $remaining_time = 60 - ($current_time - $last_failed); // Remaining time in seconds
                    $remaining_minutes = ceil($remaining_time / 60); // Convert to minutes
    
                    return json_encode(array(
                        'status' => 'locked',
                        'message' => 'Your account is locked. Please try again after ' . $remaining_minutes . ' minute(s).',
                        'remaining_time' => $remaining_time // Send the remaining time in seconds
                    ));
                } else {
                    // Reset the failed attempts after lockout period has passed
                    $this->resetFailedAttempts($username);
                }
            }
    
            // Now check the username and password using password_verify()
            if (password_verify($password, $user['password'])) {
                // Reset failed attempts on successful login
                $this->resetFailedAttempts($username);
    
                // Only set the session data for the logged-in user, without altering any existing session structure
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $qry = $stmt->get_result();
    
                if ($qry->num_rows > 0) {
                    // Set session data for the logged-in user, ensuring we don't overwrite existing session structure
                    $user_data = $qry->fetch_assoc();
                    foreach ($user_data as $k => $v) {
                        if (!is_numeric($k) && $k != 'password') {
                            $this->settings->set_userdata($k, $v);
                        }
                    }
                    $this->settings->set_userdata('login_type', 1);
    
                    // Successful login, no need to send remaining_time
                    return json_encode(array('status' => 'success', 'remaining_time' => null));
                } else {
                    return json_encode(array('status' => 'error', 'message' => 'User data retrieval failed.', 'remaining_time' => null));
                }
            } else {
                // Increment failed attempts if login fails
                $this->incrementFailedAttempts($username);
    
                // Incorrect login attempt, no need to send remaining_time
                return json_encode(array('status' => 'incorrect', 'message' => 'Incorrect username or password.', 'remaining_time' => null));
            }
        } else {
            return json_encode(array('status' => 'incorrect', 'message' => 'User does not exist.', 'remaining_time' => null));
        }
    }
    

    private function incrementFailedAttempts($username) {
        // Increment failed login attempts and set timestamp of the last failed attempt
        $stmt = $this->conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_attempt = NOW() WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }

    private function resetFailedAttempts($username) {
        // Reset failed attempts after successful login or when lockout period has passed
        $stmt = $this->conn->prepare("UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }

    public function logout(){
        if ($this->settings->sess_des()) {
            redirect('admin/login.php');
        }
    }
}

// Check if there's an action to perform (like login or logout)
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();

switch ($action) {
    case 'login':
        echo $auth->login(); // Call the login function
        break;
    case 'logout':
        echo $auth->logout(); // Call the logout function
        break;
    default:
        echo $auth->index(); // Show access denied if no valid action
        break;
}
?>

<?php
require_once('../config.php');

class Adduser extends DBConnection {
    private $settings;
    
    public function __construct() {
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }

    public function save() {
        try {
            // Validate required fields
            $required_fields = ['firstname', 'lastname', 'username', 'phone'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    return 3; // Return error code for missing fields
                }
            }
    
            // Sanitize input
            $firstname = $this->conn->real_escape_string($_POST['firstname']);
            $lastname = $this->conn->real_escape_string($_POST['lastname']);
            $username = $this->conn->real_escape_string($_POST['username']);
            $phone = $this->conn->real_escape_string($_POST['phone']);
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
            // Check if username already exists
            $check = $this->conn->query("SELECT * FROM users WHERE username = '{$username}' " . ($id > 0 ? " AND id != {$id} " : ""));
            if ($check->num_rows > 0) {
                return 4; // Return error code for duplicate username
            }
    
            // Prepare base data
            $data = "firstname = '{$firstname}', lastname = '{$lastname}', username = '{$username}', phone = '{$phone}'";
    
            // Handle password
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $data .= ", password = '{$password}'";
            }
    
            // Handle avatar upload with file validation
            if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileMimeType = mime_content_type($_FILES['img']['tmp_name']);
                if (!in_array($fileMimeType, $allowedMimeTypes)) {
                    return 5; // Invalid file type
                }
    
                // Move the uploaded file
                $upload_path = '../uploads/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
    
                // Use a unique name for the avatar
                $fname = 'uploads/' . md5(time()) . '_' . $_FILES['img']['name'];
                $move = move_uploaded_file($_FILES['img']['tmp_name'], '../' . $fname);
    
                if ($move) {
                    $data .= ", avatar = '{$fname}'";
                    
                    // Delete old avatar if exists
                    if ($id > 0) {
                        $old_avatar = $this->conn->query("SELECT avatar FROM users WHERE id = {$id}")->fetch_assoc()['avatar'] ?? '';
                        if (!empty($old_avatar) && is_file('../' . $old_avatar)) {
                            unlink('../' . $old_avatar);
                        }
                    }
                }
            }
    
            // Generate a random ID if it's a new user
            if (empty($id)) {
                do {
                    $random_id = random_int(100000, 999999); // Generate a random 6-digit ID
                    $check_id = $this->conn->query("SELECT id FROM users WHERE id = {$random_id}");
                } while ($check_id->num_rows > 0); // Ensure ID is unique
    
                $data = "id = {$random_id}, " . $data; // Include the random ID in the data
                $sql = "INSERT INTO users SET {$data}"; // Insert new user
                $save = $this->conn->query($sql);
            } else {
                // Update existing user
                $sql = "UPDATE users SET {$data} WHERE id = {$id}";
                $save = $this->conn->query($sql);
            }
    
            if ($save) {
                return 1; // Success
            } else {
                return 2; // Database error
            }
    
        } catch (Exception $e) {
            error_log($e->getMessage());
            return 2; // Return general error code
        }
    }
    

    public function fetch() {
        try {
            // Fetch users
            $sql = "SELECT id, firstname, lastname, username, phone, avatar, date_added FROM users ORDER BY date_added DESC";
            $result = $this->conn->query($sql);
    
            // Check if users exist
            if ($result->num_rows > 0) {
                $users = [];
                $index = 1;
                while ($row = $result->fetch_assoc()) {
                    $users[] = [
                        'index' => $index++,
                        'id' => $row['id'],
                        'name' => $row['firstname'] . ' ' . $row['lastname'],
                        'username' => $row['username'],
                        'phone' => $row['phone'],
                        'avatar' => $row['avatar'],
                        'date_added' => $row['date_added']
                    ];
                }
                return json_encode(['status' => 'success', 'data' => $users]);
            } else {
                return json_encode(['status' => 'error', 'message' => 'No users found']);
            }
    
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(['status' => 'error', 'message' => 'An error occurred while fetching users']);
        }
    }

    public function delete($id) {
        try {
            // Sanitize the input ID
            $id = intval($id);
            
            // Check if user exists
            $result = $this->conn->query("SELECT avatar FROM users WHERE id = {$id}");
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // If user has an avatar, delete the file
                if (!empty($user['avatar']) && is_file('../' . $user['avatar'])) {
                    unlink('../' . $user['avatar']);
                }
                
                // Delete the user
                $delete = $this->conn->query("DELETE FROM users WHERE id = {$id}");
                if ($delete) {
                    return 1; // Success
                } else {
                    return 2; // Error occurred while deleting
                }
            } else {
                return 3; // User not found
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return 2; // General error
        }
    }

    public function fetch_single($id) {
        try {
            $id = intval($id); // Ensure the ID is an integer
            $sql = "SELECT id, firstname, lastname, username, phone, avatar FROM users WHERE id = {$id}";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                return json_encode(['status' => 'success', 'data' => $user]); // Return user data
            } else {
                return json_encode(['status' => 'error', 'message' => 'User not found']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(['status' => 'error', 'message' => 'An error occurred while fetching user']);
        }
    }

    public function get_user($id) {
        try {
            $id = intval($id); // Ensure id is an integer
            if (!$id) {
                return json_encode(['status' => 'error', 'message' => 'Invalid ID provided']);
            }
    
            $sql = "SELECT id, firstname, lastname, username, phone, avatar FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                return json_encode(['status' => 'success', 'data' => $user]);
            } else {
                return json_encode(['status' => 'error', 'message' => 'User not found']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return json_encode(['status' => 'error', 'message' => 'An error occurred while fetching user']);
        }
    }
    
    
    
    
    
    public function update($id, $fullName, $username, $phone, $avatar = null, $password = null) {
        try {
            // Split full name into first and last name
            $nameParts = explode(' ', $fullName, 2);
            $firstname = $nameParts[0];
            $lastname = isset($nameParts[1]) ? $nameParts[1] : '';
    
            // Initialize avatar_name to null
            $avatar_name = null;
    
            // Rest of the update method remains the same
            $id = intval($id);
            $firstname = $this->conn->real_escape_string($firstname);
            $lastname = $this->conn->real_escape_string($lastname);
            $username = $this->conn->real_escape_string($username);
            $phone = $this->conn->real_escape_string($phone);
    
            // Check if the user exists
            $check_user = $this->conn->query("SELECT id, avatar FROM users WHERE id = {$id}");
            if ($check_user->num_rows === 0) {
                return 3; // User not found
            }
    
            // Check if username already exists (excluding the current user)
            $check_username = $this->conn->query("SELECT * FROM users WHERE username = '{$username}' AND id != {$id}");
            if ($check_username->num_rows > 0) {
                return 4; // Username already exists
            }
    
            // Prepare data to update
            $data = "firstname = '{$firstname}', lastname = '{$lastname}', username = '{$username}', phone = '{$phone}'";
    
            // Handle password update (if provided)
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $data .= ", password = '{$hashed_password}'";
            }
    
            // Handle avatar update (if provided)
            if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileMimeType = mime_content_type($_FILES['img']['tmp_name']);
                if (!in_array($fileMimeType, $allowedMimeTypes)) {
                    return 5; // Invalid file type
                }
    
                // Move the uploaded avatar file
                $upload_path = '../uploads/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
    
                // Use a unique name for the avatar
                $avatar_name = 'uploads/' . md5(time()) . '_' . $_FILES['img']['name'];
                $move = move_uploaded_file($_FILES['img']['tmp_name'], '../' . $avatar_name);
                if ($move) {
                    // Delete the old avatar if it exists
                    $old_avatar = $check_user->fetch_assoc()['avatar'];
                    if (!empty($old_avatar) && is_file('../' . $old_avatar)) {
                        unlink('../' . $old_avatar); // Delete old avatar
                    }
    
                    // Add the new avatar to the data
                    $data .= ", avatar = '{$avatar_name}'";
                }
            }
    
            // Update user data
            $sql = "UPDATE users SET {$data} WHERE id = {$id}";
            $update = $this->conn->query($sql);
    
            if ($update) {
                // Update session data if the updated user is the logged-in user
                if ($id == $_SESSION['userdata']['id']) {
                    // Update session variables
                    $_SESSION['userdata']['firstname'] = $firstname;
                    $_SESSION['userdata']['lastname'] = $lastname;
                    $_SESSION['userdata']['username'] = $username;
                    $_SESSION['userdata']['phone'] = $phone;
                    
                    // Update avatar if a new one was uploaded
                    if ($avatar_name !== null) {
                        $_SESSION['userdata']['avatar'] = $avatar_name;
                    }
                }
                
                return 1; // Success
            } else {
                return 2; // Database error
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return 2; // General error
        }
    }
}
    
    
// Handle the request
if (isset($_GET['f'])) {
    $adduser = new Adduser();
    $action = strtolower($_GET['f']);
    
    switch ($action) {
        case 'save':
            echo $adduser->save();
            break;
            case 'fetch':
                echo $adduser->fetch();
                break;
                case 'get_user':
                    if (isset($_POST['id'])) {
                        echo $adduser->get_user($_POST['id']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
                    }
                    break;                
                case 'delete':
                    if (isset($_GET['id'])) {
                        echo $adduser->delete($_GET['id']);
                    } else {
                        echo 2; // Error if no ID is provided
                    }
                    break;
                    case 'update':
                        if (isset($_POST['id'], $_POST['name'], $_POST['username'], $_POST['phone'])) {
                            $avatar = isset($_FILES['img']) ? $_FILES['img'] : null;
                            $password = isset($_POST['password']) ? $_POST['password'] : null;
                            echo $adduser->update(
                                $_POST['id'], 
                                $_POST['name'], 
                                $_POST['username'], 
                                $_POST['phone'], 
                                $avatar, 
                                $password
                            );
                        } else {
                            echo 2; // Error if necessary fields are missing
                        }
                        break;
                    default:
                        echo 2; // Error code for invalid action
                        break;
                }
            } else {
                echo 2; // Error code for no action specified
            }
?>
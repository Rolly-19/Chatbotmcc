<?php
session_start();
require_once('../../conn.php'); // Adjust this to your database connection file

class UserManager {
    private $conn;
    private $upload_path = 'uploads/';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function handleRequest() {
        $response = array('status' => 'failed', 'msg' => '');
        
        try {
            // Validate CSRF token here if implemented
            
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $firstname = $this->cleanInput($_POST['firstname']);
            $lastname = $this->cleanInput($_POST['lastname']);
            $username = $this->cleanInput($_POST['username']);
            $phone = $this->cleanInput($_POST['phone']);
            
            // Validate inputs
            if (!$this->validateInputs($firstname, $lastname, $username, $phone)) {
                throw new Exception("Please fill all required fields with valid data");
            }
            
            // Check username uniqueness
            if (!$this->isUsernameUnique($username, $id)) {
                throw new Exception("Username already exists");
            }
            
            // Handle file upload
            $avatar_path = $this->handleFileUpload();
            
            if ($id) {
                // Update existing user
                $this->updateUser($id, $firstname, $lastname, $username, $phone, $_POST['password'], $avatar_path);
                $response['msg'] = "User information updated successfully";
            } else {
                // Create new user
                if (empty($_POST['password'])) {
                    throw new Exception("Password is required for new users");
                }
                $this->createUser($firstname, $lastname, $username, $phone, $_POST['password'], $avatar_path);
                $response['msg'] = "User added successfully";
            }
            
            $response['status'] = 'success';
            
        } catch (Exception $e) {
            $response['msg'] = $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    private function cleanInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    private function validateInputs($firstname, $lastname, $username, $phone) {
        if (empty($firstname) || empty($lastname) || empty($username)) {
            return false;
        }
        
        // Validate phone number format (11 digits)
        if (!empty($phone) && !preg_match('/^\d{11}$/', $phone)) {
            return false;
        }
        
        return true;
    }
    
    private function isUsernameUnique($username, $exclude_id = '') {
        $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $username, $exclude_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows === 0;
    }
    
    private function handleFileUpload() {
        if (!isset($_FILES['img']) || $_FILES['img']['error'] == 4) {
            return null;
        }
        
        $file = $_FILES['img'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (!in_array(strtolower($ext), $allowed)) {
            throw new Exception("Invalid file type");
        }
        
        $filename = time() . '_' . str_replace(' ', '_', $file['name']);
        $destination = $this->upload_path . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to upload file");
        }
        
        return $destination;
    }
    
    private function updateUser($id, $firstname, $lastname, $username, $phone, $password, $avatar) {
        $sql = "UPDATE users SET firstname=?, lastname=?, username=?, phone=?";
        $params = array($firstname, $lastname, $username, $phone);
        $types = "ssss";
        
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password=?";
            $params[] = $hashed_password;
            $types .= "s";
        }
        
        if ($avatar) {
            $sql .= ", avatar=?";
            $params[] = $avatar;
            $types .= "s";
        }
        
        $sql .= ", date_updated=CURRENT_TIMESTAMP WHERE id=?";
        $params[] = $id;
        $types .= "i";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update user");
        }
    }
    
    private function createUser($firstname, $lastname, $username, $phone, $password, $avatar) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (firstname, lastname, username, password, phone, avatar) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $firstname, $lastname, $username, $hashed_password, $phone, $avatar);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create user");
        }
    }
}

// Create connection
// $conn = new mysqli("localhost", "username", "password", "chatbot_db"); // Update with your credentials

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$userManager = new UserManager($conn);
$userManager->handleRequest();
?>
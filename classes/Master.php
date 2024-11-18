<?php
require_once('../config.php');

class Master extends DBConnection {
    private $settings;

    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    public function __destruct(){
        parent::__destruct();
    }

    // Helper function to sanitize input for SQL and XSS
    private function sanitizeInput($input) {
        // Remove unwanted tags and encode special characters for safety
        return htmlspecialchars(trim($this->conn->real_escape_string($input)), ENT_QUOTES, 'UTF-8');
    }

    public function save_response(){
        if (empty($_POST['response_message']) || empty($_POST['question'])) {
            return 2; // Invalid input
        }

        // Extract sanitized input
        $response_message = $this->sanitizeInput($_POST['response_message']);
        $questions = array_map([$this, 'sanitizeInput'], $_POST['question']); // Sanitize each question
        
        // Optional: Validate that the input message is valid (e.g., a string)
        if (!$response_message || empty($questions)) {
            return 2; // Invalid or empty response/question
        }

        // Deleting existing response if updating
        if (!empty($_POST['id'])) {
            $id = $this->conn->real_escape_string($_POST['id']);
            $del = $this->conn->query("DELETE FROM `questions` WHERE id = '{$id}' ");
            if (!$del) {
                return 2; // Failed to delete
            }
        }

        // Insert response message into database
        $ins_resp = $this->conn->query("INSERT INTO `responses` SET response_message = '{$response_message}'");
        if (!$ins_resp) {
            return 2; // Failed to insert response
        }
        $resp_id = $this->conn->insert_id;

        // Insert each question associated with the response
        foreach ($questions as $question) {
            $data = "response_id = {$resp_id}, `question` = '{$question}'";
            $ins[] = $this->conn->query("INSERT INTO `questions` SET $data");
        }

        // Remove from unanswered questions
        foreach ($questions as $question) {
            $this->conn->query("DELETE FROM `unanswered` WHERE question = '{$question}'");
        }

        // Check if all insertions were successful
        if (isset($ins) && count($ins) == count($questions)) {
            $this->settings->set_flashdata("success", "Data successfully saved");
            return 1;
        } else {
            return 2; // Failed to insert question data
        }
    }

    public function delete_response(){
        if (empty($_POST['id'])) {
            return 2; // Invalid ID
        }

        $id = $this->sanitizeInput($_POST['id']);
        $del = $this->conn->query("DELETE FROM `questions` WHERE id = '{$id}' ");
        if ($del) {
            $this->settings->set_flashdata("success", "Data successfully deleted");
            return 1;
        } else {
            return 2; // Failed to delete
        }
    }

    public function get_response(){
        if (empty($_POST['message'])) {
            return json_encode(['status' => 'error', 'message' => 'Message is required']);
        }

        $message = $this->sanitizeInput($_POST['message']);
        $not_question = ["what", "what is", "who", "who is", "where"];

        if (in_array(strtolower($message), $not_question)) {
            $resp['status'] = "success";
            $resp['message'] = $this->settings->info('no_result');
            return json_encode($resp);
        }

        // Perform SQL query to get the response based on the message
        $sql = "SELECT r.response_message, q.id FROM `questions` q INNER JOIN `responses` r ON q.response_id = r.id WHERE q.question LIKE '%{$message}%'";
        $qry = $this->conn->query($sql);

        if ($qry->num_rows > 0) {
            $result = $qry->fetch_array();
            $resp['status'] = "success";
            $resp['message'] = $result['response_message'];
            $this->conn->query("INSERT INTO `frequent_asks` SET question_id = '{$result['id']}' ");
            return json_encode($resp);
        } else {
            $resp['status'] = "success";
            $resp['message'] = $this->settings->info('no_result');
            // Check and insert into unanswered questions
            $chk = $this->conn->query("SELECT * FROM `unanswered` WHERE `question` = '{$message}' ");
            if ($chk->num_rows > 0) {
                $this->conn->query("UPDATE `unanswered` SET no_asks = no_asks + 1 WHERE question = '{$message}' ");
            } else {
                $this->conn->query("INSERT INTO `unanswered` SET question = '{$message}', no_asks = 1 ");
            }
            return json_encode($resp);
        }
    }

    public function delete_unanswer(){
        if (empty($_POST['id'])) {
            return 2; // Invalid ID
        }

        $id = $this->sanitizeInput($_POST['id']);
        $del = $this->conn->query("DELETE FROM `unanswered` WHERE id = '{$id}' ");
        if ($del) {
            $this->settings->set_flashdata("success", "Data successfully deleted");
            return 1;
        } else {
            return 2; // Failed to delete unanswered entry
        }
    }
}

$Master = new Master();
$action = isset($_GET['f']) ? strtolower($_GET['f']) : 'none';
$sysset = new SystemSettings();

switch ($action) {
    case 'save_response':
        echo $Master->save_response();
        break;
    case 'delete_response':
        echo $Master->delete_response();
        break;
    case 'get_response':
        echo $Master->get_response();
        break;
    case 'delete_unanswer':
        echo $Master->delete_unanswer();
        break;
    default:
        // Default action, if any
        break;
}
?>

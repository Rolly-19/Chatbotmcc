<?php
require_once('../config.php');

Class Master extends DBConnection {
    private $settings;

    public function __construct() {
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }
    
    // Sanitize and validate response input
    public function sanitize_input($input) {
        // Strip HTML tags and escape special characters to prevent XSS & SQL injection
        return $this->conn->real_escape_string(strip_tags($input));
    }

    // Save Response
    public function save_response() {
        // Sanitize the inputs
        extract($_POST);

        if (!empty($id)) {
            // Delete existing question safely
            $del = $this->conn->prepare("DELETE FROM `questions` WHERE id = ?");
            $del->bind_param('i', $id);
            if (!$del->execute()) {
                return 2;
            }
        }

        $response_message = $this->sanitize_input($response_message); // Sanitize response_message

        // Insert new response safely
        $ins_resp = $this->conn->prepare("INSERT INTO `responses` (response_message) VALUES (?)");
        $ins_resp->bind_param('s', $response_message);
        if (!$ins_resp->execute()) {
            return 2;
        }

        $resp_id = $this->conn->insert_id;

        // Sanitize and insert questions
        foreach ($question as $k => $v) {
            $question[$k] = $this->sanitize_input($v); // Sanitize each question
            $ins[] = $this->conn->prepare("INSERT INTO `questions` (response_id, question) VALUES (?, ?)");
            $ins[$k]->bind_param('is', $resp_id, $question[$k]);
            $ins[$k]->execute();
        }

        // Remove questions from unanswered
        foreach ($question as $k => $v) {
            $del_unanswered = $this->conn->prepare("DELETE FROM `unanswered` WHERE question = ?");
            $del_unanswered->bind_param('s', $question[$k]);
            $del_unanswered->execute();
        }

        if (isset($ins) && count($ins) == count($question)) {
            $this->settings->set_flashdata("success", "Data successfully saved");
            return 1;
        } else {
            return 2;
        }
    }

    // Delete response
    public function delete_response() {
        extract($_POST);
        $id = $this->conn->real_escape_string($id);

        $del = $this->conn->prepare("DELETE FROM `questions` WHERE id = ?");
        $del->bind_param('i', $id);
        if ($del->execute()) {
            $this->settings->set_flashdata("success", "Data successfully deleted");
            return 1;
        } else {
            return 2;
        }
    }

    // Get response
    public function get_response() {
        extract($_POST);
        $message = str_replace(array("?"), '', $message);
        $message = $this->sanitize_input($message); // Sanitize message

        $not_question = array("what", "what is", "who", "who is", "where");
        if (in_array($message, $not_question)) {
            $resp['status'] = "success";
            $resp['message'] = $this->settings->info('no_result');
            return json_encode($resp);
        }

        $sql = "SELECT r.response_message, q.id FROM `questions` q 
                INNER JOIN `responses` r ON q.response_id = r.id 
                WHERE q.question LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $search_term = "%{$message}%"; // Safely bind the LIKE parameter
        $stmt->bind_param('s', $search_term);
        $stmt->execute();
        $qry = $stmt->get_result();

        if ($qry->num_rows > 0) {
            $result = $qry->fetch_array();
            $resp['status'] = "success";
            $resp['message'] = $result['response_message'];
            $this->conn->query("INSERT INTO `frequent_asks` (question_id) VALUES ('{$result['id']}')");
            return json_encode($resp);
        } else {
            $resp['status'] = "success";
            $resp['message'] = $this->settings->info('no_result');
            $chk = $this->conn->prepare("SELECT * FROM `unanswered` WHERE `question` = ?");
            $chk->bind_param('s', $message);
            $chk->execute();
            $result = $chk->get_result();

            if ($result->num_rows > 0) {
                $this->conn->query("UPDATE `unanswered` SET no_asks = no_asks + 1 WHERE question = '{$message}'");
            } else {
                $this->conn->query("INSERT INTO `unanswered` (question, no_asks) VALUES ('{$message}', 1)");
            }
            return json_encode($resp);
        }
    }

    // Delete unanswered question
    public function delete_unanswer() {
        extract($_POST);
        $id = $this->conn->real_escape_string($id);

        $del = $this->conn->prepare("DELETE FROM `unanswered` WHERE id = ?");
        $del->bind_param('i', $id);
        if ($del->execute()) {
            $this->settings->set_flashdata("success", "Data successfully deleted");
            return 1;
        } else {
            return 2;
        }
    }
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
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
        // echo $sysset->index();
        break;
}
?>

<?php
require_once('../config.php');

Class Master extends DBConnection {
    private $settings;

    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    public function __destruct(){
        parent::__destruct();
    }

    public function generate_response_with_openai($prompt) {
        $api_key = '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';  // Replace with your OpenAI API key
        $url = 'https://api.openai.com/v1/chat/completions';  // Correct endpoint for chat model
        
        $data = [
            'model' => 'gpt-3.5-turbo', // Model name
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => 150,
            'temperature' => 0.7,
            'n' => 1,
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            return json_encode(['status' => 'error', 'message' => curl_error($ch)]);
        }
        curl_close($ch);

        $response_data = json_decode($response, true);
        if(isset($response_data['choices'][0]['message']['content'])){
            return $response_data['choices'][0]['message']['content'];
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to get response']);
        }
    }

    public function get_response(){
        extract($_POST);
        $message = str_replace(array("?"), '', $message);
        $message = $this->conn->real_escape_string($message);
        $not_question = array("what", "what is","who","who is", "where");

        if(in_array($message, $not_question)){
            $resp['status'] = "success";
            $resp['message'] = $this->settings->info('no_result');
            return json_encode($resp);
        }

        $sql = "SELECT r.response_message, q.id FROM `questions` q 
                INNER JOIN `responses` r ON q.response_id = r.id 
                WHERE q.question LIKE '%{$message}%'";
        $qry = $this->conn->query($sql);

        if($qry->num_rows > 0){
            $result = $qry->fetch_array();
            $resp['status'] = "success";
            $resp['message'] = $result['response_message'];
            $resp['sql'] = $sql;
            $this->conn->query("INSERT INTO `frequent_asks` SET question_id = '{$result['id']}'");
            return json_encode($resp);
        } else {
            $openai_response = $this->generate_response_with_openai($message);
            $resp['status'] = "success";
            $resp['message'] = $openai_response;
            $chk = $this->conn->query("SELECT * FROM `unanswered` WHERE `question` = '{$message}'");

            if($chk->num_rows > 0){
                $this->conn->query("UPDATE `unanswered` SET no_asks = no_asks + 1 WHERE question = '{$message}'");
            } else {
                $this->conn->query("INSERT INTO `unanswered` SET question = '{$message}', no_asks = 1");
            }
            return json_encode($resp);
        }
    }

    // Other methods remain unchanged
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

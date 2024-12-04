<?php
require_once('../config.php');

class FilterLogs {
    private $conn;

    public function __construct($conn) {
        if (!$conn) {
            throw new Exception("Database connection error.");
        }
        $this->conn = $conn;
    }

    public function getUserLoginLogs($dateFrom = null, $dateTo = null) {
        $sql = "SELECT ul.id, ul.user_id, u.firstname, u.lastname, ul.time_in, ul.time_out 
                FROM user_logins ul 
                INNER JOIN users u ON ul.user_id = u.id";

        if ($dateFrom && $dateTo) {
            $sql .= " WHERE DATE(ul.time_in) BETWEEN ? AND ?";
        }

        $sql .= " ORDER BY ul.time_in DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        if ($dateFrom && $dateTo) {
            $stmt->bind_param("ss", $dateFrom, $dateTo);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $row['time_out'] = $row['time_out'] ?? "N/A";
            $logs[] = $row;
        }

        $stmt->close();
        return $logs;
    }

    public function renderLoginLogs($dateFrom = null, $dateTo = null) {
        try {
            $logs = $this->getUserLoginLogs($dateFrom, $dateTo);

            if (empty($logs)) {
                return "<p>No records found for the selected date range.</p>";
            }

            $html = '';
            foreach ($logs as $log) {
                $html .= sprintf(
                    "<li>User: %s %s, Time In: %s, Time Out: %s</li>",
                    htmlspecialchars($log['firstname']),
                    htmlspecialchars($log['lastname']),
                    htmlspecialchars($log['time_in']),
                    htmlspecialchars($log['time_out'])
                );
            }

            return $html;
        } catch (Exception $e) {
            return "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

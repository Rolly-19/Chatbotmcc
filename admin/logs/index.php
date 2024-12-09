<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    background-color: #f4f4f4;
                }
                .form-container {
                    margin-bottom: 20px;
                }
                .form-container input[type="date"] {
                    padding: 5px;
                    margin-right: 10px;
                }
                .progress-tracker {
                    list-style: none;
                    padding: 0;
                    border-left: 2px solid #ccc;
                    margin-top: 20px;
                }
                .progress-tracker li {
                    position: relative;
                    padding: 10px 20px;
                    margin: 10px 0;
                    background-color: #fff;
                    border-radius: 5px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    transition: all 0.3s ease;
                }
                .progress-tracker li:hover {
                    transform: translateX(10px);
                    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
                }
                .progress-tracker li::before {
                    content: '';
                    position: absolute;
                    top: 10px;
                    left: -6px;
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                }
                .progress-tracker li span {
                    font-weight: bold;
                }
                /* Color variations for different steps */
                .progress-tracker li:nth-child(1)::before { background-color: #3498db; }      /* Blue */
                .progress-tracker li:nth-child(2)::before { background-color: #2ecc71; }      /* Green */
                .progress-tracker li:nth-child(3)::before { background-color: #e74c3c; }      /* Red */
                .progress-tracker li:nth-child(4)::before { background-color: #f39c12; }      /* Orange */
                .progress-tracker li:nth-child(5)::before { background-color: #9b59b6; }      /* Purple */
                /* Alternate colors for more entries */
                .progress-tracker li:nth-child(6n+1)::before { background-color: #1abc9c; }  /* Turquoise */
                .progress-tracker li:nth-child(6n+2)::before { background-color: #34495e; }  /* Dark Blue */
                .progress-tracker li:nth-child(6n+3)::before { background-color: #d35400; }  /* Dark Orange */
                .progress-tracker li:nth-child(6n+4)::before { background-color: #27ae60; }  /* Emerald Green */
                .progress-tracker li:nth-child(6n+5)::before { background-color: #8e44ad; }  /* Dark Purple */
            </style>
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const dateFromInput = document.getElementById("date_from");
                    const dateToInput = document.getElementById("date_to");
                    const form = document.querySelector("form");

                    // Automatically submit the form when both dates are set
                    const submitFormOnDateChange = () => {
                        if (dateFromInput.value && dateToInput.value) {
                            form.submit();
                        }
                    };

                    dateFromInput.addEventListener("change", submitFormOnDateChange);
                    dateToInput.addEventListener("change", submitFormOnDateChange);
                });
            </script>

            <div class="form-container">
                <form method="POST">
                    <label for="date_from">From: </label>
                    <input type="date" id="date_from" name="date_from" required>
                    <label for="date_to">To: </label>
                    <input type="date" id="date_to" name="date_to" required>
                </form>
            </div>

                <ul class="progress-tracker">
                <?php
                    $servername = "localhost";
                    $username = "u510162695_chatbot_db";
                    $password = "1Chatbot_db";
                    $dbname = "u510162695_chatbot_db";

                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Set timezone to Philippine Time for MySQL queries
                    $conn->query("SET time_zone = '+08:00'");

                    // Get the date filter values from POST request
                    $dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : null;
                    $dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : null;

                    // Validate date input
                    if (!$dateFrom || !$dateTo) {
                        echo "<li>Please provide both start and end dates to filter records.</li>";
                        exit;
                    }

                    // Prepare SQL query with timezone conversion and date filtering
                    $stmt = $conn->prepare("SELECT ul.id, ul.user_id, u.firstname, u.lastname, 
                                                CONVERT_TZ(ul.time_in, '+00:00', '+08:00') AS time_in_pht, 
                                                CONVERT_TZ(ul.time_out, '+00:00', '+08:00') AS time_out_pht
                                            FROM user_logins ul
                                            INNER JOIN users u ON ul.user_id = u.id
                                            WHERE DATE(ul.time_in) BETWEEN ? AND ?
                                            ORDER BY ul.time_in DESC");
                    $stmt->bind_param('ss', $dateFrom, $dateTo);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Display the results as vertical progress steps
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li>
                                    <span>User:</span> {$row['firstname']} {$row['lastname']}<br>
                                    <span>Time In:</span> {$row['time_in_pht']}<br>
                                    <span>Time Out:</span> " . ($row['time_out_pht'] ?? "N/A") . "
                                </li>";
                        }
                    } else {
                        echo "<li>No records found for the selected date range.</li>";
                    }

                    // Close the connection
                    $stmt->close();
                    $conn->close();
                    ?>
            </ul>
        </div>
    </div>
</div>

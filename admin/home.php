<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>
<!-- BAR CHART -->
<div class="card card-info">
  <div class="card-header">
    <h3 class="card-title">Top 10 Frequent Questions</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
      <button type="button" class="btn btn-tool" data-card-widget="remove">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  <div class="card-body">
    <!-- First, fetch and populate the questions data -->
    <?php
    // Fetch all questions that are in the frequent_asks table
    $questions = $conn->query("SELECT * FROM `questions` WHERE id IN (SELECT question_id FROM frequent_asks) ");
    $list = array();
    while ($row = $questions->fetch_assoc()) {
        // Count the number of times each question appears in the frequent_asks table
        $count = $conn->query("SELECT * FROM frequent_asks WHERE question_id = {$row['id']} ")->num_rows;
        $list[] = array("count" => $count, "question" => $row['question']);
    }

    // Sort questions by count in descending order
    usort($list, function ($a, $b) {
        return $b['count'] - $a['count'];  // Sorting in descending order of frequency
    });

    // Top 10 questions
    $label = array();
    $data = array();
    $i = 10; // Limit to top 10
    foreach ($list as $k => $v) {
        $i--;
        $label[] = $v['question'];
        $data[] = $v['count'];
        if ($i == 0)
            break;
    }

    // For displaying all questions (if needed)
    $all_label = array();
    $all_data = array();
    foreach ($list as $v) {
        $all_label[] = $v['question'];
        $all_data[] = $v['count'];
    }
    ?>

    <!-- Display the total number of questions above the chart -->
    <div style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">
      Total Questions: <?php echo count($list); ?>
    </div>
    <div class="chart">
      <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
  $(function() {
   
    // Bar Chart Options
    var barChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      },
      plugins: {
        datalabels: {
          display: false,
        }
      },
      layout: {
        padding: {
          bottom: 20,
        }
      },
      beforeDraw: function(chart) {
        var ctx = chart.ctx;
        var img = new Image();
        img.src = 'path/to/your/image.png';  // Replace with your image path
        img.onload = function() {
          ctx.drawImage(img, 0, 0, chart.width, chart.height);
        };
      }
    };

    // Initialize the Bar Chart
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    });
  })
</script>

<!-- Feedback Dashboard -->
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Feedback Overview</h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <div class="card-body">
    <?php
    // Fetch recent feedback
    $feedbacks = $conn->query("SELECT feedback, rating, date_submitted FROM `feedback` ORDER BY date_submitted DESC LIMIT 10");
    $ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]; // Rating distribution
    $recent_feedback = [];
    while ($row = $feedbacks->fetch_assoc()) {
        $ratings[$row['rating']]++;
        $recent_feedback[] = $row;
    }
    ?>
    
    <!-- Feedback Ratings Chart -->
    <div class="chart">
      <canvas id="feedbackPieChart"></canvas>
    </div>

    <!-- Feedback Table -->
    <table class="table table-striped mt-4">
      <thead>
        <tr>
          <th>Date</th>
          <th>Rating</th>
          <th>Feedback</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent_feedback as $feedback): ?>
        <tr>
          <td><?php echo $feedback['date_submitted']; ?></td>
          <td>
            <?php
            switch ($feedback['rating']) {
                case 5: echo '😍'; break;
                case 4: echo '😊'; break;
                case 3: echo '😐'; break;
                case 2: echo '🙁'; break;
                case 1: echo '😢'; break;
            }
            ?>
          </td>
          <td><?php echo htmlspecialchars($feedback['feedback']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  $(function() {
    // Bar Chart for Questions
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    var barChartData = {
      labels: ['<?php echo implode("','", $label); ?>'],
      datasets: [{
        label: 'Frequent Asks',
        backgroundColor: 'rgba(60,141,188,0.9)',
        borderColor: 'rgba(60,141,188,1)',
        data: [<?php echo implode(',', $data); ?>]
      }]
    };
    var barChartOptions = {
      responsive: true,
      maintainAspectRatio: false
    };
    new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    });

    // Pie Chart for Feedback
    var pieChartCanvas = $('#feedbackPieChart').get(0).getContext('2d');
    var pieData = {
      labels: ['😢1', '🙁2', '😐3', '😊4', '😍5'],
      datasets: [{
        data: [<?php echo implode(',', $ratings); ?>],
        backgroundColor: ['#ff6b6b', '#feca57', '#ff9ff3', '#1dd1a1', '#54a0ff']
      }]
    };
    var pieOptions = {
      responsive: true,
      maintainAspectRatio: false
    };
    new Chart(pieChartCanvas, {
      type: 'pie',
      data: pieData,
      options: pieOptions
    });
  });
</script>
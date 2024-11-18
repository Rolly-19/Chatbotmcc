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

<!-- All Questions List (Display below the chart) -->
<h3>All Questions</h3>
<ul>
  <?php foreach($list as $question): ?>
    <li><?php echo $question['question'] . " - " . $question['count']; ?></li>
  <?php endforeach; ?>
</ul>

<script>
  $(function() {
    // Bar Chart Data (Top 10 Frequent Questions)
    var barChartData = {
      labels  : ['<?php echo implode('\',\'',$label) ?>'],
      datasets: [{
        label               : 'Frequent Asks',
        backgroundColor     : [
          'rgba(60,141,188,0.9)',
          'rgba(210, 214, 222, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 205, 86, 1)',
          'rgba(231, 233, 237, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(201, 203, 207, 1)'
        ],
        borderColor         : [
          'rgba(60,141,188,1)',
          'rgba(210, 214, 222, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 205, 86, 1)',
          'rgba(231, 233, 237, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(201, 203, 207, 1)'
        ],
        data                : [<?php echo implode(',',$data) ?>]
      }]
    };

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

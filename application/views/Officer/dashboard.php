<!-- this section is the for the right side panel which content is be seen -->
<div class="card bg-100 shadow-none border mb-3">
  <div class="row gx-0 flex-between-center">
    <div class="col-sm-auto d-flex align-items-center"><img class="ms-n2" src="<?php echo base_url('assets/img/illustrations/crm-bar-chart.png'); ?>" alt="" width="90" />
      <div>
        <h6 class="text-primary fs-10 mb-0">Welcome <?php echo $users['first_name']; ?> to </h6>
        <h4 class="text-primary fw-bold mb-0">OFFICER <span class="text-info fw-medium">Account</span></h4>
      </div><img class="ms-n4 d-md-none d-lg-block" src="<?php echo site_url('assets/img/illustrations/crm-line-chart.png'); ?>" alt="" width="150" />
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-xxl-3">
    <div class="card h-md-100 ecommerce-card-min-width">
      <div class="card-header pb-0">
        <h6 class="mb-0 mt-2 d-flex align-items-center">Completed Activities <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Completed activities in this semester"><span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span></h6>
      </div>
      <div class="card-body d-flex flex-column justify-content-end">
        <div class="row">
          <div class="col">
            <p class="font-sans-serif lh-1 mb-1 fs-5">
              <?= $current_semester['completed_count'] ?>
            </p>
            <?php
            // Calculate percentage change
            $percentage_change = 0;

            // Ensure we don't divide by zero or have an unreasonable increase
            if ($previous_semester['completed_count'] > 0) {
              // Apply the formula: (current count - previous count) / previous count * 100
              $percentage_change = (($current_semester['completed_count'] - $previous_semester['completed_count']) / $previous_semester['completed_count']) * 10;
            } elseif ($previous_semester['completed_count'] == 0 && $current_semester['completed_count'] > 0) {
              // If the previous semester has no activities and the current semester has activities
              $percentage_change = 100; // 10% increase
            } else {
              // If both semesters have no completed activities
              $percentage_change = 0; // No change
            }

            // Check if percentage change is too high (for small numbers like 2 -> 5, cap the percentage)
            if ($percentage_change > 1000) {
              $percentage_change = 1000;  // Cap percentage change if it exceeds 1000%
            }
            ?>

            <span class="badge <?= $percentage_change >= 0 ? 'badge-subtle-success' : 'badge-subtle-danger' ?> rounded-pill fs-11">
              <?= ($percentage_change >= 0 ? '+' : '') . round($percentage_change, 2) ?>%
            </span>

          </div>

          <?php
          $months = [];
          $counts = [];

          foreach ($monthly_breakdown as $row) {
            // Ensure the value is an integer
            $monthNum = (int)$row->month;

            // Convert to full month name using mktime
            $monthName = date("F", mktime(0, 0, 0, $monthNum, 1));

            $months[] = $monthName;
            $counts[] = (int)$row->count;
          }

          $months_json = json_encode($months);
          $counts_json = json_encode($counts);
          ?>

          <div class="col-auto ps-0">
            <div class="echart-bar-monthly-activities h-100"
              data-echarts='{
       "tooltip": {
         "trigger": "axis",
         "formatter": "{b0} : {c0}"
       },
       "xAxis": {
         "data": <?= $months_json ?>
       },
       "yAxis": {},
       "series": [
         {
           "type": "bar",
           "data": <?= $counts_json ?>,
           "barWidth": "40%"
         }
       ],
       "grid": {
         "bottom": "2%",
         "top": "2%",
         "right": "10px",
         "left": "10px"
       }
     }'
              data-echart-responsive="true">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xxl-3">
    <div class="card h-md-100">
      <div class="card-header pb-0">
        <h6 class="mb-0 mt-2">Total Fines</h6>
      </div>
      <div class="card-body d-flex flex-column justify-content-end">
        <div class="row justify-content-between">
          <div class="col-auto align-self-end">
            <div class="fs-5 fw-normal font-sans-serif text-700 lh-1 mb-1"><?= $fines->total_fines ?></div><span class="badge rounded-pill fs-11 bg-200 text-primary">
              <!-- <span class="fas fa-caret-up me-1"></span>13.6%</span> -->
          </div>
          <div class="col-auto ps-0 mt-n4">
            <div class="echart-default-total-order" data-echarts='{"tooltip":{"trigger":"axis","formatter":"{b0} : {c0}"},"xAxis":{"data":["Week 4","Week 5","Week 6","Week 7"]},"series":[{"type":"line","data":[20,40,100,120],"smooth":true,"lineStyle":{"width":3}}],"grid":{"bottom":"2%","top":"2%","right":"10px","left":"10px"}}' data-echart-responsive="true"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-12 col-xxl-3">
    <div class="card h-md-100">
      <div class="card-body">
        <div class="row h-100 justify-content-between g-0">
          <?php
          // Ensure color palette
          $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
          $total_students = 0;

          foreach ($student_counts as $row) {
            $total_students += (int)$row['student_count'];
          }
          ?>

          <!-- Left: Department legends -->
          <div class="col-5 col-sm-6 col-xxl pe-2">
            <h6 class="mt-1">Department Share</h6>
            <div class="fs-11 mt-3">
              <?php foreach ($student_counts as $index => $row): ?>
                <div class="d-flex flex-between-center mb-1">
                  <div class="d-flex align-items-center">
                    <span class="dot <?php echo $colors[$index % count($colors)]; ?>"></span>
                    <span class="fw-semi-bold"><?php echo htmlspecialchars($row['dept_name']); ?></span>
                  </div>
                  <div class="d-xxl-none">
                    <?php
                    $percentage = ($row['student_count'] / $total_students) * 100;
                    echo round($percentage, 2) . "%";
                    ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Right: Progress Bar -->
          <div class="col-7 col-sm-6 col-xxl ps-2 align-self-center">
            <div class="card shadow-sm p-3">
              <div class="progress" style="height: 30px;">
                <?php foreach ($student_counts as $index => $row):
                  $percentage = $total_students > 0 ? ($row['student_count'] / $total_students) * 100 : 0;
                  $color = $colors[$index % count($colors)];
                ?>
                  <div class="progress-bar <?php echo $color; ?>"
                    role="progressbar"
                    style="width: <?php echo $percentage; ?>%;"
                    aria-valuenow="<?php echo $percentage; ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    data-bs-toggle="tooltip"
                    title="<?php echo htmlspecialchars($row['dept_name']) . ': ' . $row['student_count'] . ' students (' . round($percentage, 1) . '%)'; ?>">
                    <span class="d-none d-md-inline"><?php echo $row['student_count']; ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="text-center mt-3">
                <strong>Total Students: <?php echo $total_students; ?></strong>
              </div>
            </div>
          </div>

          <script>
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
              new bootstrap.Tooltip(el);
            });
          </script>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-0">
  <div class="col-md-6 col-xxl-3 pe-md-2 mb-3 mb-xxl-0">
    <div class="card">
      <div class="card-header d-flex flex-between-center bg-body-tertiary py-2">
        <h6 class="mb-0">Officers</h6>
      </div>
      <div class="card-body py-2">
        <div class="d-flex align-items-center position-relative mb-3">
          <div class="avatar avatar-2xl status-online">
            <img class="rounded-circle" src="../assets/img/team/1.jpg" alt="" />
          </div>
          <div class="flex-1 ms-3">
            <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">Emma Watson</a></h6>
            <p class="text-500 fs-11 mb-0">Admin</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xxl-3 ps-md-2 order-xxl-1 mb-3 mb-xxl-0">
    <div class="card h-100">
      <div class="card-header bg-body-tertiary d-flex flex-between-center py-2">
        <h6 class="mb-0">Overall Attendance Rate </h6>
      </div>
      <div class="card-body d-flex flex-center flex-column"><!-- Find the JS file for the following chart at: src/js/charts/echarts/bandwidth-saved.js--><!-- If you are not using gulp based workflow, you can find the transpiled code at: public/assets/js/theme.js-->
        <div class="echart-bandwidth-saved" data-echart-responsive="true"></div>
        <div class="text-center mt-3">
          <h6 class="fs-9 mb-1"><span class="fas fa-check text-success me-1" data-fa-transform="shrink-2"></span>12 students attended</h6>
          <p class="fs-10 mb-0"><?php echo $activity_count['completed_count']; ?> Activities</p>
        </div>
      </div>
    </div>
  </div>
</div>
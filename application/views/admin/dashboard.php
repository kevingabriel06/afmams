<!-- this section is the for the right side panel which content is be seen -->
<div class="row g-3 mb-3">
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
          <div class="col-auto ps-0">
            <div class="echart-bar-weekly-sales h-100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="col-md-6 col-xxl-3">
    <div class="card h-md-100">
      <div class="card-header pb-0">
        <h6 class="mb-0 mt-2">Total Order</h6>
      </div>
      <div class="card-body d-flex flex-column justify-content-end">
        <div class="row justify-content-between">
          <div class="col-auto align-self-end">
            <div class="fs-5 fw-normal font-sans-serif text-700 lh-1 mb-1">58.4K</div><span class="badge rounded-pill fs-11 bg-200 text-primary"><span class="fas fa-caret-up me-1"></span>13.6%</span>
          </div>
          <div class="col-auto ps-0 mt-n4">
            <div class="echart-default-total-order" data-echarts='{"tooltip":{"trigger":"axis","formatter":"{b0} : {c0}"},"xAxis":{"data":["Week 4","Week 5","Week 6","Week 7"]},"series":[{"type":"line","data":[20,40,100,120],"smooth":true,"lineStyle":{"width":3}}],"grid":{"bottom":"2%","top":"2%","right":"10px","left":"10px"}}' data-echart-responsive="true"></div>
          </div>
        </div>
      </div>
    </div>
  </div> -->

  <div class="col-md-12 col-xxl-3">
    <div class="card h-md-100">
      <div class="card-body">
        <div class="row h-100 justify-content-between g-0">
          <div class="col-5 col-sm-6 col-xxl pe-2">
            <h6 class="mt-1">Department Share</h6>
            <div class="fs-11 mt-3">
              <?php
              // Define color array
              $colors = ['bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-danger', 'bg-secondary'];
              $index = 0;
              ?>

              <?php foreach ($student_counts as $row): ?>
                <div class="d-flex flex-between-center mb-1">
                  <div class="d-flex align-items-center">
                    <!-- Dynamically assign color using modulo operator to cycle through colors -->
                    <span class="dot <?php echo $colors[$index % count($colors)]; ?>"></span>
                    <span class="fw-semi-bold"><?php echo htmlspecialchars($row['dept_name']); ?></span>
                  </div>
                  <div class="d-xxl-none">
                    <?php
                    // Calculate percentage
                    $percentage = ($row['student_count'] / $total_students) * 100;
                    echo round($percentage, 2) . "%"; // Round percentage to 2 decimal places
                    ?>
                  </div>
                </div>
                <?php
                // Increment index for the next department
                $index++;
                ?>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="col-auto">
            <!-- Echart Container -->
            <!-- <div class="echart-market-share" style="height: 200px;"></div> -->
            <!-- Position the label in the center of the chart -->
            <div class="position-absolute top-50 start-20 translate-middle text-1100 fs-6">
              <?php echo $total_students; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-0">
  <!-- <div class="col-md-6 col-xxl-3 pe-md-2 mb-3 mb-xxl-0">
    <div class="card">
      <div class="card-header d-flex flex-between-center bg-body-tertiary py-2">
        <h6 class="mb-0">Active Users</h6>
        <div class="dropdown font-sans-serif btn-reveal-trigger"><button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-active-user" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-11"></span></button>
          <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-active-user"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
            <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
          </div>
        </div>
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
        <div class="d-flex align-items-center position-relative mb-3">
          <div class="avatar avatar-2xl status-online">
            <img class="rounded-circle" src="../assets/img/team/2.jpg" alt="" />
          </div>
          <div class="flex-1 ms-3">
            <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">Antony Hopkins</a></h6>
            <p class="text-500 fs-11 mb-0">Moderator</p>
          </div>
        </div>
        <div class="d-flex align-items-center position-relative mb-3">
          <div class="avatar avatar-2xl status-away">
            <img class="rounded-circle" src="../assets/img/team/3.jpg" alt="" />
          </div>
          <div class="flex-1 ms-3">
            <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">Anna Karinina</a></h6>
            <p class="text-500 fs-11 mb-0">Editor</p>
          </div>
        </div>
        <div class="d-flex align-items-center position-relative mb-3">
          <div class="avatar avatar-2xl status-offline">
            <img class="rounded-circle" src="../assets/img/team/4.jpg" alt="" />
          </div>
          <div class="flex-1 ms-3">
            <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">John Lee</a></h6>
            <p class="text-500 fs-11 mb-0">Admin</p>
          </div>
        </div>
        <div class="d-flex align-items-center position-relative false">
          <div class="avatar avatar-2xl status-offline">
            <img class="rounded-circle" src="../assets/img/team/5.jpg" alt="" />
          </div>
          <div class="flex-1 ms-3">
            <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">Rowen Atkinson</a></h6>
            <p class="text-500 fs-11 mb-0">Editor</p>
          </div>
        </div>
      </div>
      <div class="card-footer bg-body-tertiary p-0"><a class="btn btn-sm btn-link d-block w-100 py-2" href="../app/social/followers.html">All active users<span class="fas fa-chevron-right ms-1 fs-11"></span></a></div>
    </div>
  </div> -->
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
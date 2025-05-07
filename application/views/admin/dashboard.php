 <!-- ECharts Library -->
 <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>


 <!-- this section is the for the right side panel which content is be seen -->
 <div class="card bg-100 shadow-none border mb-3">
   <div class="row gx-0 flex-between-center">
     <div class="col-sm-auto d-flex align-items-center"><img class="ms-n2" src="<?php echo base_url('assets/img/illustrations/crm-bar-chart.png'); ?>" alt="" width="90" />
       <div>
         <h6 class="text-primary fs-10 mb-0">Welcome <?php echo $users['first_name']; ?> to </h6>
         <h4 class="text-primary fw-bold mb-0">ADMIN <span class="text-info fw-medium">Account</span></h4>
       </div><img class="ms-n4 d-md-none d-lg-block" src="<?php echo site_url('assets/img/illustrations/crm-line-chart.png'); ?>" alt="" width="150" />
     </div>
   </div>
 </div>

 <div class="row g-3 mb-4">
   <div class="col-md-6 col-xxl-4">
     <div class="card h-md-100">
       <div class="card-header pb-0">
         <h6 class="mb-0 mt-2 d-flex align-items-center">
           Completed Activities
           <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Completed activities in this semester">
             <span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
           </span>
         </h6>
       </div>
       <div class="card-body">
         <?php
          $completedCount = $current_semester['completed_count'];

          // Prepare data: month => count (fill zero for missing months)
          $monthData = [];
          foreach ($monthly_breakdown as $row) {
            $monthNum = (int)$row->month;
            $monthName = date("F", mktime(0, 0, 0, $monthNum, 1));
            $monthData[$monthNum] = [
              'name' => $monthName,
              'count' => (int)$row->count
            ];
          }

          // Sort months numerically
          ksort($monthData);

          // Final arrays for chart
          $months = [];
          $counts = [];

          foreach ($monthData as $data) {
            $months[] = $data['name'];
            $counts[] = $data['count'];
          }

          // Percentage calculation (fixed)
          $percentage_change = 0;
          if ($previous_semester['completed_count'] > 0) {
            $percentage_change = (($completedCount - $previous_semester['completed_count']) / $previous_semester['completed_count']) * 100;
          } elseif ($previous_semester['completed_count'] == 0 && $completedCount > 0) {
            $percentage_change = 100;
          }

          if ($percentage_change > 1000) {
            $percentage_change = 1000;
          }
          ?>

         <!-- Completed count -->
         <div class="mb-3">
           <div class="d-flex align-items-end gap-1">
             <h1 class="text-primary mb-0"><?= $completedCount ?></h1>
             <span class="badge <?= $percentage_change >= 0 ? 'badge-subtle-success' : 'badge-subtle-danger' ?> rounded-pill fs-11 mb-1">
               <?= ($percentage_change >= 0 ? '+' : '') . round($percentage_change, 2) ?>%
             </span>
           </div>
         </div>

         <!-- Bar Chart -->
         <div id="completedActivitiesChart" style="height: 250px;"></div>

         <!-- Chart Initialization -->
         <script>
           var chartDom = document.getElementById('completedActivitiesChart');
           var myChart = echarts.init(chartDom);
           var option = {
             tooltip: {
               trigger: 'axis',
               formatter: '{b0} : {c0}'
             },
             xAxis: {
               type: 'category',
               data: <?= json_encode($months) ?>
             },
             yAxis: {
               type: 'value'
             },
             series: [{
               data: <?= json_encode($counts) ?>,
               type: 'bar',
               barWidth: '50%',
               itemStyle: {
                 color: '#2196f3' // Blue color
               }
             }],
             grid: {
               top: 10,
               bottom: 40,
               left: 40,
               right: 10
             }
           };
           myChart.setOption(option);
           window.addEventListener('resize', function() {
             myChart.resize();
           });
         </script>
       </div>
     </div>
   </div>

   <!-- Total Fines per Activity -->
   <div class="col-md-6 col-xxl-4">
     <div class="card h-md-100">
       <div class="card-header pb-0">
         <h6 class="mb-0 mt-2 d-flex align-items-center">
           Total Fines per Activity
           <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Fines accumulated for each activity this semester">
             <span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
           </span>
         </h6>
       </div>
       <div class="card-body">
         <?php
          // Prepare the data for fines per activity
          $activityTitles = array_column($fines_per_activity, 'activity_title');
          $totalFines = array_column($fines_per_activity, 'total_fines');

          // Calculate total fines for all activities
          $totalFinesForAll = array_sum($totalFines);
          ?>

         <!-- Total fines display -->
         <div class="mb-3">
           <div class="d-flex align-items-end gap-1">
             <h1 class="text-primary mb-0">Php <?= number_format($totalFinesForAll, 2) ?></h1>
           </div>
         </div>

         <!-- Line Chart -->
         <div id="finesPerActivityChart" style="height: 250px;"></div>

         <script>
           var chartDom = document.getElementById('finesPerActivityChart');
           var myChart = echarts.init(chartDom);

           var activityTitles = <?= json_encode($activityTitles) ?>;
           var totalFines = <?= json_encode($totalFines) ?>;

           var option = {
             tooltip: {
               trigger: 'axis',
               formatter: '{b0}: {c0}'
             },
             xAxis: {
               type: 'category',
               data: activityTitles,
               axisLabel: {
                 show: false // hides the bottom labels
               },
               axisTick: {
                 show: false
               }
             },
             yAxis: {
               type: 'value'
             },
             series: [{
               data: totalFines,
               type: 'line',
               smooth: true,
               lineStyle: {
                 width: 3
               },
               itemStyle: {
                 color: '#2196f3'
               },
               connectNulls: true
             }],
             grid: {
               top: 10,
               bottom: 40,
               left: 40,
               right: 10
             }
           };

           myChart.setOption(option);
           window.addEventListener('resize', function() {
             myChart.resize();
           });
         </script>

       </div>
     </div>
   </div>

   <!-- Department Share -->
   <div class="col-md-12 col-xxl-3">
     <div class="card h-md-100">
       <div class="card-body">
         <div class="row h-100 justify-content-between g-0">
           <?php
            $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
            $total_students = 0;
            foreach ($student_counts as $row) {
              $total_students += (int)$row['student_count'];
            }
            ?>

           <div class="col-5 col-sm-6 col-xxl pe-2">
             <h6 class="mt-1">Department Share</h6>
             <div class="fs-11 mt-3">
               <?php foreach ($student_counts as $index => $row): ?>
                 <div class="d-flex flex-between-center mb-1">
                   <div class="d-flex align-items-center">
                     <span class="dot <?= $colors[$index % count($colors)] ?>"></span>
                     <span class="fw-semi-bold"><?= htmlspecialchars($row['dept_name']) ?></span>
                   </div>
                   <div class="d-xxl-none">
                     <?= round(($row['student_count'] / $total_students) * 100, 2) ?>%
                   </div>
                 </div>
               <?php endforeach; ?>
             </div>
           </div>

           <div class="col-7 col-sm-6 col-xxl ps-2 align-self-center">
             <div class="card shadow-sm p-3">
               <div class="progress" style="height: 30px;">
                 <?php foreach ($student_counts as $index => $row):
                    $percentage = $total_students > 0 ? ($row['student_count'] / $total_students) * 100 : 0;
                  ?>
                   <div class="progress-bar <?= $colors[$index % count($colors)] ?>"
                     role="progressbar"
                     style="width: <?= $percentage ?>%;"
                     aria-valuenow="<?= $percentage ?>"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     data-bs-toggle="tooltip"
                     title="<?= htmlspecialchars($row['dept_name']) . ': ' . $row['student_count'] . ' students (' . round($percentage, 1) . '%)' ?>">
                     <span class="d-none d-md-inline"><?= $row['student_count'] ?></span>
                   </div>
                 <?php endforeach; ?>
               </div>

               <div class="text-center mt-3">
                 <strong>Total Students: <?= $total_students ?></strong>
               </div>
             </div>
           </div>
         </div>
       </div>
     </div>
   </div>

   <!-- Tooltip Initialization Script -->
   <script>
     document.addEventListener('DOMContentLoaded', function() {
       document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
         new bootstrap.Tooltip(el);
       });
     });
   </script>
 </div>


 <div class="row g-0">
   <div class="col-md-12 col-xxl-6 pe-md-2 mb-3 mb-xxl-0">
     <div class="card h-md-100">
       <div class="card-header pb-0">
         <h6 class="mb-0 mt-2 d-flex align-items-center">
           Attendance per Activity
           <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Comparison of expected vs actual attendance this semester">
             <span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
           </span>
         </h6>
       </div>
       <div class="card-body">
         <?php
          // Assume $attendance_data is the result from fetch_attendance_data()
          $activityTitles = array_column($attendance_data, 'activity_title');
          $expectedAttendees = array_column($attendance_data, 'expected_attendees');
          $actualAttendees = array_column($attendance_data, 'actual_attendees');

          // Calculate total actual attendees
          $totalActual = array_sum($actualAttendees);
          ?>

         <!-- Total attendees display -->
         <div class="mb-3">
           <div class="d-flex align-items-end gap-1">
             <h1 class="text-success mb-0"><?= $totalActual ?> Attended</h1>
           </div>
         </div>

         <!-- Line Chart -->
         <div id="attendancePerActivityChart" style="height: 250px;"></div>

         <script>
           var chartDom = document.getElementById('attendancePerActivityChart');
           var myChart = echarts.init(chartDom);

           var activityTitles = <?= json_encode($activityTitles) ?>;
           var expectedAttendees = <?= json_encode($expectedAttendees) ?>;
           var actualAttendees = <?= json_encode($actualAttendees) ?>;

           var option = {
             tooltip: {
               trigger: 'axis'
             },
             legend: {
               data: ['Expected', 'Actual']
             },
             xAxis: {
               type: 'category',
               data: activityTitles,
               axisLabel: {
                 show: false
               },
               axisTick: {
                 show: false
               }
             },
             yAxis: {
               type: 'value'
             },
             series: [{
                 name: 'Expected',
                 type: 'line',
                 data: expectedAttendees,
                 smooth: false,
                 symbol: 'circle',
                 symbolSize: 8,
                 lineStyle: {
                   width: 3,
                   color: '#64b5f6' // Blue line
                 },
                 itemStyle: {
                   color: '#64b5f6', // Dot color matching the line
                   borderColor: '#64b5f6' // Dot border matching the line
                 },
                 connectNulls: true
               },
               {
                 name: 'Actual',
                 type: 'line',
                 data: actualAttendees,
                 smooth: false,
                 symbol: 'circle',
                 symbolSize: 8,
                 lineStyle: {
                   width: 3,
                   color: '#1976d2' // Dark blue line
                 },
                 itemStyle: {
                   color: '#1976d2', // Dot color matching the line
                   borderColor: '#1976d2' // Dot border matching the line
                 },
                 connectNulls: true
               }
             ],
             grid: {
               top: 10,
               bottom: 40,
               left: 40,
               right: 10
             }
           };
           myChart.setOption(option);
           window.addEventListener('resize', () => {
             myChart.resize();
           });
         </script>
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

   <!-- Expected vs Actual Attendance -->
   <div class="col-md-6 col-xxl-6">
     <div class="card h-md-100">
       <div class="card-header pb-0">
         <h6 class="mb-0 mt-2 d-flex align-items-center">
           Attendance per Activity
           <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Comparison of expected vs actual attendance this semester">
             <span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
           </span>
         </h6>
       </div>
       <div class="card-body">
         <?php
          // Assume $attendance_data is the result from fetch_attendance_data()
          $activityTitles = array_column($attendance_data, 'activity_title');
          $expectedAttendees = array_column($attendance_data, 'expected_attendees');
          $actualAttendees = array_column($attendance_data, 'actual_attendees');

          // Calculate total actual attendees
          $totalActual = array_sum($actualAttendees);
          ?>

         <!-- Total attendees display -->
         <div class="mb-3">
           <div class="d-flex align-items-end gap-1">
             <h1 class="text-success mb-0"><?= $totalActual ?> Attended</h1>
           </div>
         </div>

         <!-- Line Chart -->
         <div id="attendancePerActivityChart" style="height: 250px;"></div>

         <script>
           var chartDom = document.getElementById('attendancePerActivityChart');
           var myChart = echarts.init(chartDom);

           var activityTitles = <?= json_encode($activityTitles) ?>;
           var expectedAttendees = <?= json_encode($expectedAttendees) ?>;
           var actualAttendees = <?= json_encode($actualAttendees) ?>;

           var option = {
             tooltip: {
               trigger: 'axis'
             },
             legend: {
               data: ['Expected', 'Actual']
             },
             xAxis: {
               type: 'category',
               data: activityTitles,
               axisLabel: {
                 show: false
               },
               axisTick: {
                 show: false
               }
             },
             yAxis: {
               type: 'value'
             },
             series: [{
                 name: 'Expected',
                 data: expectedAttendees,
                 type: 'line',
                 smooth: true,
                 lineStyle: {
                   width: 3
                 },
                 itemStyle: {
                   color: '#ff9800'
                 },
                 connectNulls: true
               },
               {
                 name: 'Actual',
                 data: actualAttendees,
                 type: 'line',
                 smooth: true,
                 lineStyle: {
                   width: 3
                 },
                 itemStyle: {
                   color: '#4caf50'
                 },
                 connectNulls: true
               }
             ],
             grid: {
               top: 10,
               bottom: 40,
               left: 40,
               right: 10
             }
           };

           myChart.setOption(option);
           window.addEventListener('resize', function() {
             myChart.resize();
           });
         </script>

       </div>
     </div>
   </div>

 </div>
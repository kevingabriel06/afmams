 <!-- ECharts Library -->
 <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

 <!-- this section is the for the right side panel which content is be seen -->
 <div class="card bg-100 shadow-none border mb-3">
   <div class="row gx-0 flex-between-center">
     <div class="col-sm-auto d-flex align-items-center"><img class="ms-n2" src="<?php echo base_url('assets/img/illustrations/crm-bar-chart.png'); ?>" alt="" width="90" />
       <div>
         <h6 class="text-primary fs-10 mb-0">Welcome<?php echo ' ' . $users['first_name'] . ' to '; ?></h6>
         <h4 class="text-primary fw-bold mb-0"><?php echo $this->session->userdata('dept_name') . ' ' ?: $this->session->userdata('org_name') . ' '; ?><span class="text-info fw-medium">Account</span></h4>
       </div><img class="ms-n4 d-md-none d-lg-block" src="<?php echo site_url('assets/img/illustrations/crm-line-chart.png'); ?>" alt="" width="150" />
     </div>
   </div>
 </div>

 <div class="card mb-3">
   <div class="card-body px-xxl-0 pt-4">
     <div class="row g-0">
       <!-- Students Card -->
       <div class="col-xxl-3 col-md-6 px-3 text-center border-end-md pb-3 p-xxl-0 ps-md-0">
         <div class="icon-circle icon-circle-primary">
           <span class="fs-7 fas fa-user-graduate text-primary"></span>
         </div>
         <h4 class="mb-1 font-sans-serif">
           <span class="text-700 mx-2 countup" data-countup='{"endValue": <?= $student_count ?>}'>0</span>
           <span class="fw-normal text-600">Students</span>
         </h4>
       </div>

       <script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.8/dist/countUp.umd.js"></script>
       <script>
         window.addEventListener('DOMContentLoaded', () => {
           document.querySelectorAll('.countup').forEach(el => {
             const endValue = JSON.parse(el.getAttribute('data-countup')).endValue;
             const countUp = new countUp.CountUp(el, endValue);
             if (!countUp.error) {
               countUp.start();
             } else {
               console.error(countUp.error);
             }
           });
         });
       </script>

       <!-- Officers Card -->
       <div class="col-xxl-3 col-md-6 px-3 text-center border-end-xxl pb-3 pt-4 pt-md-0 pe-md-0 p-xxl-0">
         <div class="icon-circle icon-circle-info">
           <span class="fs-7 fas fa-chalkboard-teacher text-info"></span>
         </div>
         <h4 class="mb-1 font-sans-serif">
           <span class="text-700 mx-2" data-countup='{"endValue": <?= $officer_count ?>}'>0</span>
           <span class="fw-normal text-600">Officers</span>
         </h4>
       </div>
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
          // Prepare data
          $activityTitles = array_column($attendance_data, 'activity_title');
          $expectedAttendees = array_column($attendance_data, 'expected_attendees');
          $actualAttendees = array_column($attendance_data, 'actual_attendees');

          // Total actual attendees
          $totalActual = array_sum($actualAttendees);
          ?>
         <!-- Total attendees display -->
         <div class="mb-3">
           <div class="d-flex align-items-center gap-3">
             <h1 class="text-primary mb-0"><?= $attendance_rate ?>%</h1>
             <span class="badge bg-primary fs-16">
               Attendance Rate
             </span>
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
               data: ['Expected', 'Actual'],
               orient: 'horizontal',
               bottom: 10 // Place legend at the bottom
             },
             xAxis: {
               type: 'category',
               data: activityTitles,
               axisLabel: {
                 rotate: 30,
                 interval: 0
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
                 smooth: false,
                 symbol: 'circle',
                 symbolSize: 8,
                 data: expectedAttendees,
                 lineStyle: {
                   width: 3,
                   color: '#64b5f6'
                 },
                 itemStyle: {
                   color: '#64b5f6',
                   borderColor: '#64b5f6'
                 },
                 connectNulls: true
               },
               {
                 name: 'Actual',
                 type: 'line',
                 smooth: false,
                 symbol: 'circle',
                 symbolSize: 8,
                 data: actualAttendees,
                 lineStyle: {
                   width: 3,
                   color: '#1976d2'
                 },
                 itemStyle: {
                   color: '#1976d2',
                   borderColor: '#1976d2'
                 },
                 connectNulls: true
               }
             ],
             grid: {
               top: 20,
               bottom: 150, // Increase to avoid overlap with legend
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
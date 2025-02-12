<!-- admin section -->
<div class="card mb-3">
  <div class="card-body px-xxl-0 pt-4">
    <div class="row g-0">

      <?php
      // Query to count all students
      $totalStudents = $this->db->count_all('users');
      ?>

      <!-- Student Counter -->
      <div class="col-xxl-3 col-md-6 px-3 text-center border-end-md border-bottom border-bottom-xxl-0 pb-3 p-xxl-0 ps-md-0">
        <div class="icon-circle icon-circle-primary">
          <span class="fs-7 fas fa-user-graduate text-primary"></span>
        </div>
        <h4 class="mb-1 font-sans-serif">
          <span class="text-700 mx-2" id="totalStudents">
            <?= $totalStudents; ?>
          </span>
          <span class="fw-normal text-600">Students</span>
        </h4>
      </div>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          function animateCounter(id, endValue) {
            var counter = new CountUp(id, endValue, {
              duration: 2,
              separator: ","
            });
            if (!counter.error) {
              counter.start();
            } else {
              console.error(counter.error);
            }
          }

          animateCounter("totalStudents", <?= $totalStudents; ?>);
          animateCounter("studentsLastMonth", <?= $studentsLastMonth; ?>);
        });
      </script>

      <?php
      // Query to count all admins (assuming `is_admin = 'Yes'`)
      $totalAdmins = $this->db->where('is_admin', 'Yes')->count_all_results('users');

      ?>

      <!-- Admin Counter -->
      <div class="col-xxl-3 col-md-6 px-3 text-center border-end-xxl border-bottom border-bottom-xxl-0 pb-3 pt-4 pt-md-0 pe-md-0 p-xxl-0">
        <div class="icon-circle icon-circle-info">
          <span class="fs-7 fas fa-chalkboard-teacher text-info"></span>
        </div>
        <h4 class="mb-1 font-sans-serif">
          <span class="text-700 mx-2" id="totalAdmins">
            <?= $totalAdmins; ?>
          </span>
          <span class="fw-normal text-600">Admins</span>
        </h4>
      </div>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          function animateCounter(id, endValue) {
            var counter = new CountUp(id, endValue, {
              duration: 2,
              separator: ","
            });
            if (!counter.error) {
              counter.start();
            } else {
              console.error(counter.error);
            }
          }

          animateCounter("totalAdmins", <?= $totalAdmins; ?>);
          animateCounter("adminsLastMonth", <?= $adminsLastMonth; ?>);
        });
      </script>



      <!-- activities part-->
      <?php
      // Get CI instance
      $CI = &get_instance();

      // Load database (only if not autoloaded)
      $CI->load->database();

      // Get the current month and last month
      $thisMonth = date('Y-m');
      $lastMonth = date('Y-m', strtotime('-1 month'));

      // Query for this month
      $queryThisMonth = $CI->db->query("SELECT COUNT(*) AS count FROM activity WHERE DATE_FORMAT(start_date, '%Y-%m') = '$thisMonth'");
      $activityCountThisMonth = $queryThisMonth->row()->count;

      // Query for last month
      $queryLastMonth = $CI->db->query("SELECT COUNT(*) AS count FROM activity WHERE DATE_FORMAT(start_date, '%Y-%m') = '$lastMonth'");
      $activityCountLastMonth = $queryLastMonth->row()->count;
      ?>
      <!-- Activities Counter -->
      <div class="col-xxl-3 col-md-6 px-3 text-center border-end-md border-bottom border-bottom-md-0 pb-3 pt-4 p-xxl-0 pb-md-0 ps-md-0">
        <div class="icon-circle icon-circle-success">
          <span class="fs-7 fas fa-book-open text-success"></span>
        </div>
        <h4 class="mb-1 font-sans-serif">
          <span class="text-700 mx-2" id="activityCountThisMonth">
            <?= $activityCountThisMonth; ?>
          </span>
          <span class="fw-normal text-600">Activity</span>
        </h4>
        <p class="fs-10 fw-semi-bold mb-0">
          <span class="text-600 fw-normal">
            <span id="activityCountLastMonth"><?= $activityCountLastMonth; ?> last month</span>
          </span>
        </p>
      </div>

      <!-- CountUp.js Animation -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.min.js"></script>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          function animateCounter(id, endValue) {
            var counter = new CountUp(id, endValue, {
              duration: 2,
              separator: ","
            });
            if (!counter.error) {
              counter.start();
            } else {
              console.error(counter.error);
            }
          }

          animateCounter("activityThisMonth", <?= $activityCountThisMonth; ?>);
          animateCounter("activityLastMonth", <?= $activityCountLastMonth; ?>);
        });
      </script>

      <?php
      // Query to sum all fines
      $totalFinesRow = $this->db->select_sum('total_amount')->get('fines')->row();
      $totalFines = (int) ($totalFinesRow->total_amount ?? 0);

      ?>

      <!-- Fines Counter -->
      <div class="col-xxl-3 col-md-6 px-3 text-center pt-4 p-xxl-0 pb-0 pe-md-0">
        <div class="icon-circle icon-circle-warning">
          <span class="fs-7 fas fa-dollar-sign text-warning"></span>
        </div>
        <h4 class="mb-1 font-sans-serif">
          <span class="text-700 mx-2" id="totalFines">
            <?= number_format($totalFines); ?>
          </span>
          <span class="fw-normal text-600">Fines</span>
        </h4>
      </div>
    </div>
  </div>
</div>
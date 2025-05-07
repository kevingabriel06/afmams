<div class="card mb-3 mb-lg-0">
  <div class="card-header bg-body-tertiary d-flex justify-content-between">
    <h5 class="mb-0">
      <?php echo $forms->title; ?> - Evaluation Responses </h5>
  </div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

<div class="row gx-3">
  <div class="col-xxl-10 col-xl-12">
    <div class="card" id="responsesTable"
      data-list='{"valueNames":["id", "name", "department"],"page":11,"pagination":true,"fallback":"responses-table-fallback"}'>

      <div class="card-header border-bottom border-200 px-0">
        <div class="d-lg-flex justify-content-between">
          <div class="row flex-between-center gy-2 px-x1">
            <div class="col-auto pe-0">

            </div>
          </div>

          <!-- Search Input -->
          <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
            <div class="d-flex align-items-center" id="table-ticket-replace-element">
              <div class="col-auto">
                <form>
                  <div class="input-group input-search-width">
                    <input id="searchInput" class="form-control form-control-sm shadow-none search"
                      type="search" placeholder="Search" aria-label="search" />
                    <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                      <span class="fa fa-search fs-10"></span>
                    </button>
                  </div>
                </form>
              </div>
              <button class="btn btn-sm btn-falcon-default ms-2" type="button">
                <span class="fas fa-download"></span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive scrollbar">
          <table class="table table-hover table-striped overflow-hidden">
            <thead class="bg-200">
              <tr>
                <th scope="col" class="text-nowrap">Student ID</th>
                <th scope="col" class=" text-nowrap">Name</th>
                <th scope="col" class=" text-nowrap">Department</th>
                <?php foreach ($questions as $q): ?>
                  <th scope="col" class=" text-nowrap"><?php echo $q; ?></th>
                <?php endforeach; ?>
                <th scope="col" class=" text-nowrap">Date Evaluated</th>
              </tr>
            </thead>
            <tbody class="list" id="table-response-body">
              <?php foreach ($responses as $response): ?>
                <tr class="responses-row">
                  <td class="text-nowrap id"><?php echo $response['student_id']; ?></td>
                  <td class="text-nowrap name"><?php echo $response['name']; ?></td>
                  <td class="text-nowrap department"><?php echo $response['dept_name']; ?></td>
                  <?php foreach ($questions as $q): ?>
                    <td class="text-nowrap">
                      <?php if (isset($response['answers'][$q])): ?>
                        <?php
                        $answer = trim($response['answers'][$q]); // Trim any whitespace or line breaks
                        $rating = is_numeric($answer) ? (int)$answer : 0;
                        ?>

                        <?php if (isset($response['type'][$q]) && $response['type'][$q] === 'rating' && $rating >= 1 && $rating <= 4): ?>
                          <?php for ($i = 1; $i <= 4; $i++): ?>
                            <?= $i <= $rating ? '⭐' : '☆'; ?>
                          <?php endfor; ?>
                        <?php else: ?>
                          <?= htmlspecialchars($answer); ?>
                        <?php endif; ?>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                  <?php endforeach; ?>


                  <td class=" text-nowrap">
                    <?php echo date('F j, Y | g:i a', strtotime($response['submitted_at'])); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
          </table>
          <div class="text-center d-none" id="responses-table-fallback">
            <span class="fa fa-user-slash fa-2x text-muted"></span>
            <p class="fw-bold fs-8 mt-3">No Student Found</p>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="d-flex justify-content-center">
          <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev">
            <span class="fas fa-chevron-left"></span>
          </button>
          <ul class="pagination mb-0"></ul>
          <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next">
            <span class="fas fa-chevron-right"></span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Space Between Sections -->
  <div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

  <div class="d-flex justify-content-end mb-3">
    <a href="<?php echo site_url('admin/evaluation-statistic/' . $forms->form_id); ?>" class="btn btn-primary">
      <i class="fas fa-chart-bar"></i> View Evaluation Statistic
    </a>
  </div>
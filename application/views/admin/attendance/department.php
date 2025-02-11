<div class="card mb-3 mb-lg-0">
  <div class="card-header bg-body-tertiary d-flex justify-content-between">
    <h5 class="mb-0">List of Department</h5>
  </div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

<div class="card mb-3 mb-lg-0">
  <div class="card-body fs-10">
    <div class="row g-3"> <!-- Single row for alignment -->

    <?php foreach($department as $dept): ?>
        <div class="col-md-6 col-lg-4"> 
            <div class="card fixed-card">
            <img class="card-img-top fixed-image" src="<?php echo base_url('assets/imageDept/') . (!empty($dept->logo) ? $dept->logo : 'default.png'); ?>" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title"><?php echo $dept->dept_name ?></h5>
                <a class="btn btn-primary btn-sm" href="<?php echo site_url('admin/list-attendees/'.$activity_id.'/'.$dept->dept_id ); ?>">View Attendance List</a>
            </div>
            </div>
        </div>
    <?php endforeach; ?>


    </div> <!-- End row -->
  </div>
</div>


<style>
  .fixed-card {
  width: 100%; /* Ensures responsiveness */
  height: 320px; /* Set a fixed height for uniformity */
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
}

.fixed-image {
  width: 100%; /* Makes the image responsive */
  height: 150px; /* Fixed height for uniformity */
  object-fit: contain; /* Ensures the entire image fits without distortion */
  padding: 10px; /* Optional padding */
}

.card-body {
  text-align: center; /* Centers text and button */
  flex-grow: 1; /* Ensures the content adjusts properly */
}

</style>
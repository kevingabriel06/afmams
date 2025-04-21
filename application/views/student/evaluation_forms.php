<div class="container mb-3">
    <div class="row" style="display: flex; flex-wrap: wrap;">
        <!-- STATIC CARD EXAMPLE -->
        <?php foreach ($evaluation_forms as $forms): ?>
            <div class="col-md-6" style="display: flex; justify-content: stretch; margin-bottom: 1rem;">
                <div class="card" style="display: flex; flex-direction: column; flex-grow: 1; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                    <div class="card-header">
                        <div class="row flex-between-center">
                            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                                <h5 class="fs-7 mb-0 text-nowrap py-2 py-xl-0">Evaluation Form</h5>
                            </div>
                        </div>
                    </div>
                    <div class="border"></div>

                    <div class="card-body p-4" style="flex-grow: 1; overflow-y: auto; max-height: 350px;">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check me-2"></i>
                            <strong class="text-muted">Form Title:</strong>
                            <span><?php echo $forms->title; ?></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-users me-2"></i>
                            <strong class="text-muted">From:</strong>
                            <span><?php echo $forms->organizer; ?></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong class="text-muted">Description:</strong>
                            <span><?php echo $forms->form_description; ?></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-clock me-2"></i>
                            <strong class="text-muted">Duration:</strong>
                            <span>
                                <?php
                                echo date("M d, Y h:i A", strtotime($forms->start_date_evaluation)) .
                                    ' - ' .
                                    date("M d, Y h:i A", strtotime($forms->end_date_evaluation));
                                ?>
                            </span>
                        </div>
                        <?php if ($forms->status_evaluation == 'Ongoing'): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <a href="<?php echo site_url('student/evaluation-form-questions/' . $forms->form_id); ?>" class="btn btn-primary w-100" style="margin-top: auto;" disabled>
                                    Form Open
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
    <!-- You can duplicate the block above for multiple forms -->
</div>
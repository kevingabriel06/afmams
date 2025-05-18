<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $form->title; ?></title>

	<style>
		.card-header h5 {
			margin: 0;
		}

		.cover-photo {
			height: 250px;
			object-fit: cover;
		}

		.form-title {
			font-weight: bold;
			font-size: 24px;
			margin-top: 20px;
		}

		.form-description {
			font-size: 16px;
			color: #6c757d;
		}

		.duration {
			font-weight: bold;
			margin-top: 20px;
		}

		.dashed-line {
			border: 2px dashed #ccc;
			margin-top: 10px;
			margin-bottom: 20px;
		}

		.form-group {
			margin-bottom: 20px;
		}

		.rating-container {
			display: flex;
			gap: 5px;
		}

		.star {
			font-size: 30px;
			color: #ccc;
		}

		.star.selected {
			color: gold;
		}

		/* Added styles for background color and padding */
		.form-content {
			background-color: white;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
			margin-top: -30px;
			/* To offset space from cover photo */
		}

		/* Styles for the responses container */
		.responses-container {
			background-color: #f9f9f9;
			/* Light gray background */
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
			margin-top: 30px;
			/* Space between the main content and responses */
		}
	</style>
</head>

<body>

	<div class="card mb-3">
		<div class="card-body">
			<div class="row flex-between-center">
				<div class="col-md">
					<h5 class="mb-2 mb-md-0">Evaluation Form Responses - <?= $form->activity_title; ?></h5>
				</div>
			</div>
		</div>
	</div>

	<!-- COVER PHOTO SECTION -->
	<div class="position-relative text-center" style="max-width: 100%; overflow: hidden;">
		<?php if (!empty($form->cover_theme)) : ?>
			<img id="coverPhoto" class="img-fluid w-100 rounded cover-photo"
				src="<?= site_url('assets/theme_evaluation/' . $form->cover_theme); ?>"
				alt="Cover Photo">
		<?php else : ?>
			<img id="coverPhoto" class="img-fluid w-100 rounded cover-photo"
				src="<?= base_url('assets/image/OIP.jpg'); ?>" alt="Default Cover">
		<?php endif; ?>
	</div>

	<div class="row g-0 form-content">
		<!-- Form Title -->
		<div class="text-center mb-4">
			<h2 class="form-title"><?= $form->title; ?></h2>
			<p class="form-description"><?= $form->form_description; ?></p>
		</div>

		<!-- Dashed Border Line -->
		<hr class="dashed-line">

		<!-- Form Duration -->
		<p class="duration">📅 Duration:</p>
		<p class="fw-normal mb-0">
			<?= date('F d, Y h:i A', strtotime($form->start_date_evaluation)); ?>
			<span class="fw-semibold">to</span>
			<?= date('F d, Y h:i A', strtotime($form->end_date_evaluation)); ?>
		</p>
	</div>

	<!-- SEPARATE CONTAINER FOR RESPONSES -->
	<div class="responses-container">
		<!-- Display Responses -->
		<?php if (!empty($form_answers)): ?>
			<?php foreach ($form_answers as $index => $answer): ?>
				<div class="form-group">
					<label><strong><?= ($index + 1) . '. ' . $answer->label; ?></strong></label>

					<?php if ($answer->type == 'short' || $answer->type == 'textarea'): ?>
						<p class="border p-2"><?= !empty($answer->answer) ? $answer->answer : '<em>No response</em>'; ?></p>

					<?php elseif ($answer->type == 'rating'): ?>
						<div class="rating-container">
							<?php for ($i = 1; $i <= 5; $i++): ?>
								<span class="star <?= ($i <= $answer->answer) ? 'selected' : ''; ?>">&#9733;</span>
							<?php endfor; ?>
						</div>

					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p>No responses available for this form.</p>
		<?php endif; ?>
	</div>

</body>

</html>
<!-- Add this in the <head> section of your HTML -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
	.rating-stars i {
		margin-right: 5px;
	}
</style>



<div class="card mb-3">
	<div class="card-body">
		<div class="row flex-between-center">
			<div class="col-md">
				<h5 class="mb-2 mb-md-0">Evaluation Form Response - <?php echo $form_data->activity_title; ?></h5>
			</div>
		</div>
	</div>
</div>

<!-- COVER PHOTO SECTION -->
<div class="position-relative text-center" style="max-width: 100%; overflow: hidden;">
	<?php if (!empty($form_data->cover_theme)) : ?>
		<img id="coverPhoto" class="img-fluid w-100 rounded"
			src="<?php echo site_url('assets/theme_evaluation/' . $form_data->cover_theme); ?>"
			alt="Cover Photo" style="height: 250px; object-fit: cover;">
	<?php else : ?>
		<img id="coverPhoto" class="img-fluid w-100 rounded"
			src="<?php echo base_url(); ?>assets/image/OIP.jpg" alt="Cover Photo" style="height: 250px; object-fit: cover;">
	<?php endif; ?>
</div>

<div class="row g-0">
	<div class="card mx-auto mt-3 shadow-sm border-0 rounded">
		<div class="card-body p-4">
			<h3 class="fw-bold mb-2">
				<?php echo !empty($form_data->title) ? $form_data->title : 'Untitled Form'; ?>
			</h3>

			<hr class="border border-2 border-dashed mt-3">

			<p class="text-muted mb-3">
				<?php echo !empty($form_data->form_description) ? nl2br($form_data->form_description) : 'No description provided.'; ?>
			</p>

			<!-- Form Fields Container -->
			<div id="form-fields" class="mt-4"></div>

		</div>
	</div>
</div>

<script>
	let fieldCount = 0;
	let formFields = <?php echo json_encode($form_data->form_fields); ?>;

	function addField(form_fields_id, type, label = "", answer = "", required = false) {
		const formFieldsContainer = document.getElementById("form-fields");
		const fieldId = `field-${fieldCount}`;
		let newField = "";

		if (type === "short" || type === "textarea") {
			newField = `
                <div class="card shadow-sm mb-3" id="${fieldId}">
                    <div class="card-body">
                        <label class="form-label fw-semi-bold fs-9">${label} ${required ? "<span class='text-danger'>*</span>" : ""}</label>
                        <div class="form-control-plaintext">${answer}</div>
                    </div>
                </div>
            `;
		} else if (type === "rating") {
			let stars = [1, 2, 3, 4].map(i => {
				const isActive = (answer && parseInt(answer) >= i) ? 'fas fa-star text-warning' : 'far fa-star';
				return `<i class="${isActive} star fs-5" data-value="${i}" data-field="${fieldId}"></i>`;
			}).join("");

			newField = `
                <div class="card shadow-sm mb-3" id="${fieldId}">
                    <div class="card-body">
                        <label class="form-label fw-semi-bold fs-9">${label} ${required ? "<span class='text-danger'>*</span>" : ""}</label>
                        <div class="rating-stars mb-2" id="${fieldId}-stars">
                            ${stars}
                        </div>
                    </div>
                </div>
            `;
		}

		formFieldsContainer.insertAdjacentHTML("beforeend", newField);
		fieldCount++;
	}

	function loadExistingFields() {
		formFields.forEach(field => {
			addField(field.form_fields_id, field.type, field.label, field.answer, field.required == 1);
		});
	}

	window.onload = loadExistingFields;
</script>
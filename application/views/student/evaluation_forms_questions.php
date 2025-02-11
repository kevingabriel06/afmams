<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $form->title; ?></title>

	<!-- Alertify CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css"/>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Alertify JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.js"></script>	
    <style>
        .form-container {
            max-width: 700px;
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 0px auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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
            cursor: pointer;
            color: #ccc;
        }
        .star.selected {
            color: gold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <!-- Form Title -->
        <div class="form-header text-center mb-4">
            <h2 class="fw-bold"><?= $form->title; ?></h2>
            <p class="text-muted"><?= $form->description; ?></p>
        </div>

		<!-- Form with AJAX submission -->
<form id="evaluation-form" method="post" action="<?= base_url('student/evaluation-form-submit/' . $form->form_id); ?>">
    <?php if (!empty($questions)): ?>
        <?php foreach ($questions as $index => $question): ?>
            <div class="form-group">
                <label><?= ($index + 1) . '. ' . $question->label; ?> <?= $question->required ? '*' : ''; ?></label>
                <?php if ($question->type == 'short'): ?>
                    <input type="text" name="responses[<?= $question->form_fields_id; ?>]" class="form-control" 
                        placeholder="<?= $question->placeholder; ?>" <?= $question->required ? 'required' : ''; ?>>
                <?php elseif ($question->type == 'textarea'): ?>
                    <textarea name="responses[<?= $question->form_fields_id; ?>]" class="form-control" 
                        rows="4" placeholder="<?= $question->placeholder; ?>" <?= $question->required ? 'required' : ''; ?>></textarea>
                <?php elseif ($question->type == 'rating'): ?>
                    <div class="rating-container" id="rating-<?= $question->form_fields_id; ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star" data-value="<?= $i; ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="responses[<?= $question->form_fields_id; ?>]" id="rating-input-<?= $question->form_fields_id; ?>" required>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No questions available for this form.</p>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let ratingContainers = document.querySelectorAll(".rating-container");

        ratingContainers.forEach(container => {
            let stars = container.querySelectorAll(".star");
            let ratingInput = container.nextElementSibling;

            stars.forEach(star => {
                star.addEventListener("click", function() {
                    let value = this.getAttribute("data-value");
                    ratingInput.value = value;

                    stars.forEach(s => s.classList.remove("selected"));

                    for (let i = 0; i < value; i++) {
                        stars[i].classList.add("selected");
                    }
                });
            });
        });
    });
</script>


<!-- //ALERTIFY SCRIPT -->

<script>
$(document).ready(function () {
    $("#evaluation-form").submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "<?= base_url('student/evaluation-form-submit/' . $form->form_id); ?>", // Correct URL for form submission
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var res = JSON.parse(response);

                if (res.status === "success") {
                    alertify.alert("Success", res.message, function () {
                        // Use the student_id from the response to construct the redirect URL
                        window.location.href = res.redirect_url; // Redirection after success
                    });
                } else {
                    alertify.alert("Error", res.message);

                    // Optional: Restore any form data if validation fails (this is specific to your form needs)
                    if (res.form_data) {
                        // Example to restore any specific field, adjust according to your needs
                        $("input[name='responses[<?= $question->form_fields_id; ?>]']").val(res.form_data.responses);
                        $("textarea[name='responses[<?= $question->form_fields_id; ?>]']").val(res.form_data.responses);
                    }
                }
            },
			error: function (xhr, status, error) {
                console.error("AJAX Error: ", error); // Logs the error message
                console.error("Response Text: ", xhr.responseText); // Logs the raw response text

                // Show the real error from the server (if available)
                let errorMessage = xhr.responseText || "An unexpected error occurred."; // Default message if no response text is available
                alertify.alert("Error", errorMessage);
            }

        });
    });
});
</script>




<!-- 
</body>
</html> -->

<div class="card mb-3">
  <div class="card-body">
    <div class="row flex-between-center">
      <div class="col-md">
        <h5 class="mb-2 mb-md-0">Edit Evaluation Form</h5>
      </div>
    </div>
  </div>
</div>

<!-- COVER PHOTO SECTION -->
<div class="position-relative text-center" style="max-width: 100%; overflow: hidden;">
  <?php if (!empty($forms['cover_theme'])) : ?>
    <!-- Cover Photo -->
    <img id="coverPhoto" class="img-fluid w-100 rounded"
      src="<?php echo site_url('assets/theme_evaluation/' . $forms['cover_theme']); ?>"
      alt="Cover Photo"
      style="height: 250px; object-fit: cover;">
  <?php else : ?>
    <img id="coverPhoto" class="img-fluid w-100 rounded"
      src="<?php echo base_url(); ?>assets/image/OIP.jpg"
      alt="Cover Photo"
      style="height: 250px; object-fit: cover;">
  <?php endif; ?>

  <!-- Remove Button (Initially Hidden) -->
  <button id="removeCover" class="btn btn-danger position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm"
    style="display: none; border-radius: 50%; font-size: 16px; line-height: 1;">
    <i class="fas fa-times"></i>
  </button>

  <!-- Hidden File Input -->
  <input type="file" id="coverUpload" accept="image/*" class="d-none" name="coverUpload">

  <!-- Upload Button (Overlay at Top Left) -->
  <label for="coverUpload" class="btn btn-dark position-absolute top-0 start-0 m-3 px-3 py-2 shadow-sm"
    style="border-radius: 8px; font-size: 14px;">
    <i class="fas fa-camera"></i> Change activity photo
  </label>
</div>

<!-- JavaScript for Image Preview & Remove -->
<script>
  document.getElementById("coverUpload").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById("coverPhoto");
    const removeBtn = document.getElementById("removeCover");

    if (file && file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result; // Update image preview
        removeBtn.style.display = "block"; // Show remove button
      };
      reader.readAsDataURL(file);
    } else {
      alert("Invalid file. Please upload an image.");
    }
  });

  // Remove Image Functionality
  document.getElementById("removeCover").addEventListener("click", function() {
    const preview = document.getElementById("coverPhoto");
    preview.src = "<?php echo base_url(); ?>assets/image/OIP.jpg"; // Reset to default image
    document.getElementById("coverUpload").value = ""; // Clear file input
    this.style.display = "none"; // Hide remove button
  });
</script>

<div class="row g-0">
  <div class="card mt-3">
    <div class="card-body bg-body-tertiary">
      <form id="editForm" class="row g-3 needs-validation" novalidate="" data-dropzone="data-dropzone" enctype="multipart/form-data">
        <!-- Select Activity Dropdown -->
        <div class="mb-3">
          <label class="form-label" for="activity">Select Activity</label>
          <div class="input-group has-validation">
            <select class="form-control" id="activity" name="activity" required>
              <option value="<?= $forms['activity_id'] ?>" selected>
                <?= htmlspecialchars($forms['activity_title']); ?>
              </option>
            </select>
          </div>
        </div>

        <!-- Form Title -->
        <input type="hidden" name="form_id" id="form_id" value="<?php echo $forms['form_id']; ?> ">
        <div class="mb-3">
          <label class="form-label" for="formtitle">Form Title</label>
          <div class="input-group has-validation">
            <input class="form-control" id="formtitle" type="text" name="formtitle" placeholder="Untitled Form" required value="<?php echo $forms['title']; ?>" />
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="formdescription">Form Description</label>
          <textarea class="form-control" id="formdescription" name="formdescription" rows="3"><?php echo $forms['form_description']; ?></textarea>
        </div>

        <!-- Start Date and End Date (Two Columns) -->
        <div class="row mb-3 g-3">
          <!-- Start Date -->
          <div class="col-md-6">
            <label class="form-label">Date & Time Start:</label>
            <div class="input-group">
              <input class="form-control datetimepicker" id="time_start" type="text"
                placeholder="Select Date & Time" name="time_start" value="<?php echo date('Y-m-d h:i A', strtotime($forms['start_date_evaluation'])); ?>" />
              <span class="input-group-text" id="time_end_icon">
                <i class="fas fa-calendar-alt"></i>
              </span>
            </div>
          </div>
          <!-- DATE & TIME END -->
          <div class="col-md-6">
            <label class="form-label">Date & Time End:</label>
            <div class="input-group">
              <input class="form-control datetimepicker" id="time_end" type="text"
                placeholder="Select Date & Time" name="time_end" value="<?php echo date('Y-m-d h:i A', strtotime($forms['end_date_evaluation'])); ?>" />
              <span class="input-group-text" id="time_end_icon">
                <i class="fas fa-calendar-alt"></i>
              </span>
            </div>
          </div>
        </div>

        <script>
          document.addEventListener("DOMContentLoaded", function() {
            // Initialize flatpickr for the first set of inputs
            flatpickr("#time_start", {
              enableTime: true,
              noCalendar: false,
              dateFormat: "Y-m-d h:i K",
              time_24hr: false,
              disableMobile: true,
              minDate: "today",
            });

            flatpickr("#time_end", {
              enableTime: true,
              noCalendar: false,
              dateFormat: "Y-m-d h:i K",
              time_24hr: false,
              disableMobile: true,
              minDate: "today",
            });
          });
        </script>


        <div id="form-fields" class="border-bottom border-dashed my-3"></div>

        <!-- Add Field Button -->
        <div class="d-flex justify-content-center my-3">
          <button
            class="btn btn-primary btn-icon"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#addFieldModal">
            <i class="fas fa-plus"></i> Add Field
          </button>
        </div>

        <!-- Save and Cancel Buttons -->
        <div class="card-body">
          <div class="row justify-content-between align-items-center">
            <div class="col-md">
              <h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
            </div>
            <div class="col-auto">
              <button
                class="btn btn-danger btn-sm me-2"
                type="button"
                onclick="$('#createForm').get(0).reset()">
                Cancel
              </button>
              <button class="btn btn-falcon-default btn-sm me-2" type="submit" data-form-id="<?php echo $forms['form_id']; ?>">
                Save
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal for adding fields (remains unchanged) -->
<div
  class="modal fade"
  id="addFieldModal"
  tabindex="-1"
  aria-labelledby="addFieldModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addFieldModalLabel">Add Field</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Select the type of field you want to add:</p>
        <div class="d-flex justify-content-around">
          <button class="btn btn-outline-primary" onclick="addField('short')" data-bs-dismiss="modal">
            <i class="fas fa-text-width"></i> Short Answer
          </button>
          <button class="btn btn-outline-primary" onclick="addField('textarea')" data-bs-dismiss="modal">
            <i class="fas fa-align-left"></i> Long Answer
          </button>
          <button class="btn btn-outline-primary" onclick="addField('rating')" data-bs-dismiss="modal">
            <i class="fas fa-star"></i> Star Rating
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  let fieldCount = 0;
  let formFields = <?php echo json_encode($form_data['form_fields']); ?>; // Get existing fields

  function addField(type, label = "", answer = "", required = false) {
    const formFieldsContainer = document.getElementById("form-fields");
    const fieldId = `field-${fieldCount}`;
    let checked = required ? 'checked' : '';

    let newField = "";

    if (type === "short") {
      newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="label-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input type="hidden" name="required[${fieldId}]" value="0">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[${fieldId}]" value="1" ${checked} onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <div class="input-group has-validation">
          <input class="form-control mb-2" id="label-${fieldCount}" type="text" name="labels[]" value="${label}" placeholder="Enter your question" required/>
        </div>
        <input class="form-control mb-2" id="answer-${fieldCount}" type="text" name="answers[]" placeholder="Short Answer" disabled/>
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="short"/>
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
    } else if (type === "textarea") {
      newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="label-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
          <input type="hidden" name="required[${fieldId}]" value="0">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[${fieldId}]" value="1" ${checked} onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <div class="input-group has-validation">
          <input class="form-control mb-2" id="label-${fieldCount}" type="text" name="labels[]" value="${label}" placeholder="Enter your question" required />
        </div>
        <textarea class="form-control mb-2" id="answer-${fieldCount}" rows="3" name="answers[]" placeholder="Long Answer" disabled></textarea>
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="textarea" />
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
    } else if (type === "rating") {
      let stars = [1, 2, 3, 4, 5]
        .map(
          (i) => `<i class="far fa-star ${answer == i ? "fas" : ""}" onclick="setRating(this, ${i})" data-value="${i}"></i>`
        )
        .join("");

      newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="label-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input type="hidden" name="required[${fieldId}]" value="0">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[${fieldId}]" value="1" ${checked} onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <div class="input-group has-validation">
          <input class="form-control mb-2" id="label-${fieldCount}" type="text" name="labels[]" value="${label}" placeholder="Enter your question" required/>
        </div>
        <div class="rating-stars mb-2" id="rating-${fieldCount}">
          ${stars}
        </div>
        <input type="hidden" name="answers[]" id="rating-value-${fieldCount}" value="${answer}" />
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="rating" />
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
    }

    formFieldsContainer.insertAdjacentHTML("beforeend", newField);
    fieldCount++;
  }

  function loadExistingFields() {
    formFields.forEach(field => {
      addField(field.type, field.label, field.answer, field.required == 1);
    });
  }

  window.onload = loadExistingFields; // Load existing fields on page load

  function toggleRequired(fieldId, checkbox) {
    const field = document.getElementById(fieldId);
    const inputs = field.querySelectorAll("input, textarea");
    inputs.forEach((input) => {
      if (checkbox.checked) {
        input.setAttribute("required", "required");
      } else {
        input.removeAttribute("required");
      }
    });
  }

  function removeField(fieldId) {
    const field = document.getElementById(fieldId);
    field.remove();
  }

  function setRating(star, rating) {
    const stars = star.parentElement.children;
    for (let i = 0; i < stars.length; i++) {
      stars[i].className = i < rating ? "fas fa-star" : "far fa-star";
    }
    const ratingInput = star.parentElement.nextElementSibling;
    ratingInput.value = rating;
  }
</script>

<script>
  $("#editForm").on("submit", function(e) {
    e.preventDefault();

    let formId = $(this).find("button[type='submit']").data("form-id");
    const formData = new FormData();

    // Collect form fields dynamically
    const fields = [];
    $("#form-fields .form-group").each(function() {
      fields.push({
        field_id: $(this).data("field-id") || null, // Include field ID if updating existing fields
        label: $(this).find('input[name="labels[]"]').val(),
        type: $(this).find('input[name="type[]"]').val(),
        placeholder: $(this).find('input[name="answers[]"]').val() || null,
        required: $(this).find('input[type="checkbox"]').prop("checked") ? 1 : 0, // Ensure correct boolean value
      });
    });

    // Append form fields
    formData.append("form_id", formId);
    formData.append("activity", $("#activity").val());
    formData.append("formtitle", $("#formtitle").val());
    formData.append("formdescription", $("#formdescription").val());
    formData.append("startdate", $("#time_start").val());
    formData.append("enddate", $("#time_end").val());

    // Append file (if selected)
    const coverFile = $("#coverUpload")[0].files[0]; // Get the selected file
    if (coverFile) {
      formData.append("coverUpload", coverFile);
    }

    // Append fields array as JSON string
    formData.append("fields", JSON.stringify(fields));


    $.ajax({
      url: "<?php echo site_url('admin/edit-evaluation-form/update/'); ?>" + formId,
      method: "POST",
      data: formData,
      processData: false, // Important for FormData
      contentType: false, // Important for FormData
      dataType: "json",
      success: function(response) {
        if (response.success) {
          Swal.fire({
            icon: "success",
            title: "Form Updated!",
            text: response.message,
            showConfirmButton: true,
          }).then(() => {
            if (response.redirect) {
              window.location.href = response.redirect;
            } else {
              location.reload();
            }
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: response.message || "Update failed.",
            showConfirmButton: true,
          });
        }
      },
      error: function(xhr) {
        Swal.fire({
          icon: "error",
          title: "Update Failed!",
          text: "An error occurred while updating the form. Please try again.",
          showConfirmButton: true,
        });
      },
    });

  });
</script>

<!-- <script>
  $("#createForm").on("submit", function(e) {
    e.preventDefault();

    // Retrieve form ID from the button inside the form
    let formId = $(this).find("button[type='submit']").data("form-id");
    let isValid = true;
    $(".invalid-feedback").remove();
    $("input, select, textarea").removeClass("is-invalid is-valid");

    console.log("Form ID:", formId);

    // Validate required fields
    $("#form-fields .form-group").each(function() {
      const question = $(this).find('input[name="questions[]"]').val();
      if (!question) {
        $(this).find('input[name="questions[]"]').addClass("is-invalid").after('<div class="invalid-feedback">Please enter a question.</div>');
        isValid = false;
      }
    });

    // Validate general form fields
    const validateField = (selector, message) => {
      const inputField = $(selector);
      if (!inputField.val()) {
        inputField.addClass("is-invalid").after(`<div class="invalid-feedback">${message}</div>`);
        isValid = false;
      }
    };

    validateField("#activity", "Please select an activity.");
    validateField("#formtitle", "Please enter the form title.");
    validateField("#formdescription", "Please enter a description.");
    validateField("#time_start", "Please enter a start date.");
    validateField("#time_end", "Please enter an end date.");

    if (isValid) {
      const fields = [];
      $("#form-fields .form-group").each(function() {
        fields.push({
          label: $(this).find('input[name="questions[]"]').val(),
          type: $(this).find('input[name="type[]"]').val(),
          placeholder: $(this).find('input[name="answers[]"]').val() || null,
          required: $(this).find('input[type="checkbox"]').is(":checked"),
        });
      });

      const dataToSend = {
        form_id: formId,
        activity: $("#activity").val(),
        formtitle: $("#formtitle").val(),
        formdescription: $("#formdescription").val(),
        startdate: $("#time_start").val(),
        enddate: $("#time_end").val(),
        fields: fields,
      };

      // Send AJAX request
      $.ajax({
        url: "<?php echo site_url('admin/edit-evaluation-form/update/'); ?>" + formId,
        method: "POST",
        data: dataToSend,
        dataType: "json",
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: "success",
              title: "Form Updated!",
              text: response.message,
              showConfirmButton: true,
            }).then(() => {
              window.location.href = response.redirect_url || location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: response.message || "Update failed.",
              showConfirmButton: true,
            });
          }
        },
        error: function(xhr) {
          Swal.fire({
            icon: "error",
            title: "Update Failed!",
            text: "An error occurred while updating the form. Please try again.",
            showConfirmButton: true,
          });
        },
      });
    }
  });
</script> -->


<!-- <script>
  $("#createForm").on("submit", function(e) {
    e.preventDefault();
    let isValid = true;
    $(".invalid-feedback").remove();
    $("input, select, textarea").removeClass("is-invalid").removeClass("is-valid"); // Remove all validation classes first

    // Loop through required fields and validate
    $("#form-fields .form-group").each(function() {
      const question = $(this).find('input[name="questions[]"]').val();
      const type = $(this).find('input[name="type[]"]').val();

      if (!question) {
        $(this)
          .find('input[name="questions[]"]')
          .addClass("is-invalid")
          .after('<div class="invalid-feedback">Please enter a question.</div>');
        isValid = false;
      } else {
        $(this).find('input[name="questions[]"]').addClass("is-valid"); // Mark valid field
      }

      if (!type) {
        $(this)
          .find('input[name="type[]"]')
          .addClass("is-invalid")
          .after('<div class="invalid-feedback">Please enter the type.</div>');
        isValid = false;
      } else {
        $(this).find('input[name="type[]"]').addClass("is-valid");
      }
    });

    // Validate general form fields similarly
    const validateField = (selector, message) => {
      const inputField = $(selector);
      if (!inputField.val()) {
        inputField.addClass("is-invalid").after(`<div class="invalid-feedback">${message}</div>`);
        isValid = false;
      } else {
        inputField.addClass("is-valid");
      }
    };

    validateField("#activity", "Please select an activity.");
    validateField("#formtitle", "Please enter the form title.");
    validateField("#formdescription", "Please enter a description.");
    validateField("#time_start", "Please enter a start date.");
    validateField("#time_end", "Please enter an end date.");

    // If form is valid, proceed with submission
    if (isValid) {
      const fields = [];
      $("#form-fields .form-group").each(function() {
        const answerField = $(this).find('input[name="answers[]"]');
        const answerValue = answerField.length > 0 ? answerField.val() : null;

        fields.push({
          label: $(this).find('input[name="questions[]"]').val(),
          type: $(this).find('input[name="type[]"]').val(),
          placeholder: answerValue || null, // Ensures empty values convert to null
          required: $(this).find('input[type="checkbox"]').is(":checked"),
        });
      });

      const dataToSend = {
        form_id: $("#form_id").val(),
        activity: $("#activity").val(), // Send selected activity ID
        formtitle: $("#formtitle").val(),
        formdescription: $("#formdescription").val(),
        startdate: $("#time_start").val(), // Fixed date input name and ID
        enddate: $("#time_end").val(),
        fields: fields,
      };



      // Submit form using AJAX
      $.ajax({
        url: "<?php echo site_url('admin/edit-evaluation-form/update/' . $forms['form_id']); ?>", // Target URL
        method: "POST", // HTTP method
        data: dataToSend, // Serialized form data
        dataType: "json", // Expect JSON response from the server
        success: function(response) {
          if (response.success) {
            // Show success alert if form creation was successful
            Swal.fire({
              icon: "success",
              title: "Form Created!",
              text: "Your form has been created successfully.",
              showConfirmButton: true,
            }).then(() => {
              // Reset the form, clear dynamic fields, and reload or redirect
              $("#createForm").get(0).reset();
              $("#form-fields").empty();

              // Reload or redirect to a new page if specified in the response
              if (response.redirect_url) {
                window.location.href = response.redirect_url;
              } else {
                location.reload(); // Default reload if no redirect_url is provided
              }
            });
          } else {
            // Handle any server-side validation feedback (if response.success is false)
            Swal.fire({
              icon: "error",
              title: "Validation Error!",
              text: response.message || "Please review the form and try again.",
              showConfirmButton: true,
            });
          }
        },
        error: function(xhr) {
          // Show error alert if AJAX request fails
          Swal.fire({
            icon: "error",
            title: "Form Creation Failed!",
            text: "An error occurred while creating the form. Please try again.",
            showConfirmButton: true,
          });
        },
      });
    }
  });
</script> -->
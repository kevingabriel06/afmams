<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card mb-3">
  <div class="card-body">
    <div class="row flex-between-center">
      <div class="col-md">
        <h5 class="mb-2 mb-md-0">Evaluation Form - <?php echo $forms['activity_title']; ?></h5>
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
</div>

<div class="row g-0">
  <form id="editForm" class="needs-validation" novalidate="" data-dropzone="data-dropzone" enctype="multipart/form-data">
    <div class="card mx-auto mt-3 shadow-sm border-0 rounded">
      <div class="card-body p-4">
        <!-- Form Title -->
        <h3 class="fw-bold mb-2">
          <?php echo !empty($forms['title']) ? $forms['title'] : 'Untitled Form'; ?>
        </h3>

        <!-- Form Description -->
        <p class="text-muted mb-3">
          <?php echo !empty($forms['form_description']) ? nl2br($forms['form_description']) : 'No description provided.'; ?>
        </p>

        <!-- Dashed Border Line -->
        <hr class="border border-2 border-dashed mt-3">

        <p class="text-muted mb-0 fw-bold">📅 Duration:</p>
        <p class="fw-normal mb-0">
          <?php echo date('F d, Y h:i A', strtotime($forms['start_date_evaluation'])); ?>
          <span class="fw-semibold">to</span>
          <?php echo date('F d, Y h:i A', strtotime($forms['end_date_evaluation'])); ?>
        </p>
      </div>
    </div>

    <div id="form-fields" class="border-bottom border-dashed my-3"></div>

    <div class="card mt-3 shadow-sm">
      <div class="card-body bg-body-tertiary">
        <div class="row justify-content-between align-items-center">
          <div class="col-md">
            <h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
          </div>
          <div class="col-auto">
            <button class="btn btn-danger btn-sm me-2" type="button" disabled>
              Cancel
            </button>
            <button class="btn btn-primary btn-sm me-2" type="submit" data-form-id="<?php echo $forms['form_id']; ?>" disabled>
              Save
            </button>
          </div>
        </div>
      </div>
    </div>

  </form>
</div>
<script>
  let fieldCount = 0;
  let formFields = <?php echo json_encode($form_data['form_fields']); ?>; // Get existing fields

  function addField(type, label = "", answer = "", required = false) {
    const formFieldsContainer = document.getElementById("form-fields");
    const fieldId = `field-${fieldCount}`;
    let newField = "";

    if (type === "short") {
      newField = `
      <div class="card shadow-sm mb-3" id="${fieldId}">
        <div class="card-body">
          <label class="form-label fw-semi-bold fs-9">${label} ${required ? "<span class='text-danger'>*</span>" : ""}</label>
          <input class="form-control" type="text" name="answers[]" value="${answer}" ${required ? "required" : ""}/>
          <input type="hidden" name="type[]" value="short"/>
        </div>
      </div>
      `;
    } else if (type === "textarea") {
      newField = `
      <div class="card shadow-sm mb-3" id="${fieldId}">
        <div class="card-body">
          <label class="form-label fw-semi-bold fs-9">${label} ${required ? "<span class='text-danger'>*</span>" : ""}</label>
          <textarea class="form-control" rows="3" name="answers[]" ${required ? "required" : ""}>${answer}</textarea>
          <input type="hidden" name="type[]" value="textarea"/>
        </div>
      </div>
      `;
    } else if (type === "rating") {
      let stars = [1, 2, 3, 4, 5]
        .map(
          (i) => `<i class="far fa-star star fs-5" data-value="${i}" data-field="${fieldId}"></i>`
        )
        .join("");

      newField = `
      <div class="card shadow-sm mb-3" id="${fieldId}">
        <div class="card-body">
          <label class="form-label  fw-semi-bold fs-9">${label} ${required ? "<span class='text-danger'>*</span>" : ""}</label>
          <div class="rating-stars mb-2" id="${fieldId}-stars">
            ${stars}
          </div>
          <input type="hidden" name="answers[]" id="${fieldId}-value" value="${answer}"/>
          <input type="hidden" name="type[]" value="rating"/>
        </div>
      </div>
      `;

      // Ensure the rating field is inserted into the DOM
      formFieldsContainer.insertAdjacentHTML("beforeend", newField);
      fieldCount++;
      return;
    }

    // Ensure other field types are inserted properly
    formFieldsContainer.insertAdjacentHTML("beforeend", newField);
    fieldCount++;
  }

  function loadExistingFields() {
    formFields.forEach(field => {
      addField(field.type, field.label, field.answer, field.required == 1);
    });
  }

  window.onload = loadExistingFields;

  document.addEventListener("click", function(event) {
    if (event.target.classList.contains("star")) {
      let rating = parseInt(event.target.getAttribute("data-value"));
      let fieldId = event.target.getAttribute("data-field");
      setRating(fieldId, rating);

      // Log the selected rating value to the console
      //console.log(`Field: ${fieldId}, Selected Rating: ${rating}`);
    }
  });

  function setRating(fieldId, rating) {
    const stars = document.querySelectorAll(`#${fieldId}-stars .star`);
    stars.forEach((star, index) => {
      star.classList.remove("fas", "text-warning");
      star.classList.add(index < rating ? "fas" : "far");
      if (index < rating) star.classList.add("text-warning");
    });
    document.querySelector(`#${fieldId}-value`).value = rating;
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
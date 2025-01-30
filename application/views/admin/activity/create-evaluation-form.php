<div class="card mb-3">
    <div class="card-body">
    <div class="row flex-between-center">
        <div class="col-md">
        <h5 class="mb-2 mb-md-0">Create Evaluation Form</h5>
        </div>
    </div>
    </div>
</div>

<div class="card cover-image mb-3">
    <img id="coverPhoto" class="card-img-top" src="<?php echo base_url(); ?>assets/img/generic/13.jpg " alt="" />
</div>

<!-- Custom CSS to Set Standard Size -->
<style>
    /* Set fixed size for the image */
    #coverPhoto {
        width: 100%; /* Make the image width fill the container */
        height: 250px; /* Set a fixed height */
        object-fit: cover; /* Ensure the image covers the area without distortion */
    }

    /* Optional: Set specific dimensions for the card if necessary */
    .card {
        width: 100%; /* You can adjust the width of the card */
    }
</style>

<div class="row g-0">
    <div id="messages"></div>
        <div class="card mt-3">
            <div class="card-body bg-body-tertiary">
            <form
              id="createForm"
              class="row g-3 needs-validation dropzone dropzone-multiple p-0"
              data-dropzone="data-dropzone"
              enctype="multipart/form-data"
            >
              <!-- Activity Details -->
              <div class="mb-3">
                <label class="form-label" for="formtitle">Form Title</label>
                <input
                  class="form-control"
                  id="formtitle"
                  type="text"
                  name="formtitle"
                  placeholder="Untitled Form"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label" for="formdescription">Form Description</label>
                <textarea
                  class="form-control"
                  id="formdescription"
                  name="formdescription"
                  rows="3"
                  required
                ></textarea>
              </div>

              <div id="form-fields" class="border-bottom border-dashed my-3"></div>

              <!-- Add Field Button -->
              <div class="d-flex justify-content-center my-3">
                <button
                  class="btn btn-primary btn-icon"
                  type="button"
                  data-bs-toggle="modal"
                  data-bs-target="#addFieldModal"
                >
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
                      onclick="$('#createForm').get(0).reset()"
                    >
                      Cancel
                    </button>
                    <button class="btn btn-falcon-default btn-sm me-2" type="submit">
                      Save
                    </button>
                  </div>
                </div>
              </div>
            </form>
        </div>
    </div>
</div>

<div
  class="modal fade"
  id="addFieldModal"
  tabindex="-1"
  aria-labelledby="addFieldModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addFieldModalLabel">Add Field</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
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

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
  let fieldCount = 0;

function addField(type) {
  const formFields = document.getElementById("form-fields");
  const fieldId = `field-${fieldCount}`;
  let newField = "";

  if (type === "short") {
    newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="question-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[]" onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <input class="form-control mb-2" id="question-${fieldCount}" type="text" placeholder="Enter your question" name="questions[]" required />
        <input class="form-control mb-2" id="answer-${fieldCount}" type="text" placeholder="Short Answer" name="answers[]" required />
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="short" />
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
  } else if (type === "textarea") {
    newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="question-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[]" onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <input class="form-control mb-2" id="question-${fieldCount}" type="text" placeholder="Enter your question" name="questions[]" required />
        <textarea class="form-control mb-2" id="answer-${fieldCount}" rows="3" placeholder="Long Answer" name="answers[]"></textarea>
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="textarea" />
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
  } else if (type === "rating") {
    newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="question-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[]" onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <input class="form-control mb-2" id="question-${fieldCount}" type="text" placeholder="Enter your question" name="questions[]" required />
        <div class="rating-stars mb-2" id="rating-${fieldCount}">
          ${[1, 2, 3, 4, 5]
            .map(
              (i) =>
                `<i class="far fa-star" onclick="setRating(this, ${i})" data-value="${i}"></i>`
            )
            .join("")}
        </div>
        <input type="hidden" name="answers[]" id="rating-value-${fieldCount}" value="" />
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="rating" />
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
  }

  formFields.insertAdjacentHTML("beforeend", newField);
  fieldCount++;
}

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

$("#createForm").on("submit", function (e) {
  e.preventDefault();

  const fields = [];
  $("#form-fields .form-group").each(function () {
    fields.push({
      label: $(this).find('input[name="questions[]"]').val(),
      type: $(this).find('input[name="type[]"]').val(),
      placeholder: $(this).find('input[name="answers[]"]').val(),
      required: $(this).find('input[type="checkbox"]').is(":checked"),
    });
  });

  const dataToSend = {
    formtitle: $("#formtitle").val(),
    formdescription: $("#formdescription").val(),
    fields: fields,
  };

  $.ajax({
    url: "<?php echo site_url('admin/create-evaluation-form/create'); ?>",
    method: "POST",
    data: dataToSend,
    success: function (response) {
      $("#messages").html(
        `<div class="alert alert-success">Form created successfully!</div>`
      );
    },
    error: function (xhr) {
      $("#messages").html(
        `<div class="alert alert-danger">Error: ${xhr.responseText}</div>`
      );
    },
  });
});

</script>


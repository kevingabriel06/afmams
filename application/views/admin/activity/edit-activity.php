@extends('layout.layout')

@section('content')
        
<div class="card mb-3">
    <div class="card-body">
    <div class="row flex-between-center">
        <div class="col-md">
        <h5 class="mb-2 mb-md-0">Edit Activity</h5>
        </div>
    </div>
    </div>
</div>


<div class="card cover-image mb-3">
    <img id="coverPhoto" class="card-img-top" src="{{ asset($activity->image_path) }}" alt="" />
</div>

<div class="row g-0">
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Activity Details</h5>
        </div>
        <div class="card-body bg-body-tertiary">
            <form method="post" id="form1" action="{{Auth::user()->user_role === 'admin' ? route('update', ['activity' => $activity]) : (Auth::user()->user_role === 'officer' ? route('officer.update', ['activity' => $activity]) : null) }}
" novalidate="novalidate" class="row g-3 needs-validation dropzone dropzone-multiple p-0" id="my-awesome-dropzone" data-dropzone="data-dropzone" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="row gx-2">
                    <!-- Activity Title -->
                    <div class="col-12 mb-3">
                        <label class="form-label" for="activity-title">Activity Title</label>
                        <input class="form-control" id="activity-title" type="text" placeholder="Activity Title" name="title" value="{{ $activity->title }}" required=""/>
                        @error('title')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div> 
                    <div class="col-sm-6 mb-3"><label class="form-label" for="date-start">Start Date</label><input class="form-control datetimepicker" id="date_start" type="text" placeholder="yyyy-mm-dd" name="date_start" data-options='{"dateFormat":"Y-m-d","disableMobile":true}' value="{{ $activity->date_start }}" required="" />
                        @error('date_start')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
  
                    
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="end-date">End Date</label>
                        <input class="form-control datetimepicker" id="end-date" type="text" placeholder="yyyy-mm-dd" name="date_end" data-options='{"dateFormat":"Y-m-d","disableMobile":true}' value="{{ $activity->date_end }}" required=""/>
                        @error('date_end')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="registration-deadline">Registration Deadline</label>
                        <input class="form-control datetimepicker" id="registration-deadline" type="text" placeholder="yyyy-mm-dd" name="registration_deadline" data-options='{"dateFormat":"Y-m-d","disableMobile":true}' value="{{ $activity->registration_deadline }}" />
                        @error('registration_deadline')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="registration-fee">Registration Fee</label>
                        <input class="form-control" id="registration-fee" type="text" placeholder="₱ 00.00" name="registration_fee" value="{{ $activity->registration_fee }}"/>
                        @error('registration_fee')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="border-bottom border-dashed my-3"></div>

                    <!-- schedule details -->
                    <div class="card-header">
                        <h5 class="mb-1">Schedule Details</h5>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="am_in">Morning Time In <label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="am_in" type="text" placeholder="H:i " name="am_in" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->am_in }}" required=""/>
                        @error('am_in')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="am_out">Morning Time Out <label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="am_out" type="text" placeholder="H:i " name="am_out" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->am_out }}" required=""/>
                        @error('am_out')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="pm_in">Afternoon Time In <label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="pm_in" type="text" placeholder="H:i " name="pm_in" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->pm_in }}" required=""/>
                        @error('pm_in')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="pm_out">Afternoon Time Out <label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="pm_out" type="text" placeholder="H:i " name="pm_out" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->pm_out }}" required=""/>
                        @error('pm_out')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                   
                   
                    <div class="col-sm-6 mb-3" id="customFieldsAInC" style="display: none;">
                        <label class="form-label" for="am_inC">Morning Time In Cut-off <label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="am_inC" type="text" placeholder="H:i " name="am_in_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->am_in_cut }}" required=""/>
                        @error('am_inC')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3" id="customFieldsAOutC" style="display: none;">
                        <label class="form-label" for="am_outC">Morning Time Out Cut-off<label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="am_outC" type="text" placeholder="H:i " name="am_out_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->am_out_cut }}" required=""/>
                        @error('am_outC')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3" id="customFieldsPInC" style="display: none;">
                        <label class="form-label" for="pm_inC">Afternoon Time In Cut-off<label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="pm_inC" type="text" placeholder="H:i " name="pm_in_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->pm_in_cut }}" required=""/>
                        @error('pm_inC')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3" id="customFieldsPOutC" style="display: none;">
                        <label class="form-label" for="pm_outC">Afternoon Time Out Cut-off <label style="color: blue;"> * </label> </label>
                        <input class="form-control datetimepicker" id="pm_outC" type="text" placeholder="H:i " name="pm_out_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="{{ $activity->pm_out_cut }}" required=""/>
                        @error('pm_outC')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    <button class="btn btn-falcon-default btn-sm me-2" type="button" id="showCustomFieldsBtn">
                        <span class="far fa-clock text-danger me-1"></span>Edit Cut-Off Time
                    </button>

                    <div class="col-12">
                        <br>
                        <div class="form-text mt-0"><i> * Note: The default cut-off time for scheduling is 15 minutes, but it can be edited depending on the situation.</i></div>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Get the button and custom fields containers
                        var showCustomFieldsBtn = document.getElementById("showCustomFieldsBtn");
                        var customFieldsContainer1 = document.getElementById("customFieldsAInC");
                        var customFieldsContainer2 = document.getElementById("customFieldsAOutC");
                        var customFieldsContainer3 = document.getElementById("customFieldsPInC");
                        var customFieldsContainer4 = document.getElementById("customFieldsPOutC");

                        // Add event listener to the button
                        showCustomFieldsBtn.addEventListener("click", function() {
                            // Toggle the visibility of the custom fields containers
                            if (customFieldsContainer1.style.display === "none") {
                                customFieldsContainer1.style.display = "block";
                                customFieldsContainer2.style.display = "block";
                                customFieldsContainer3.style.display = "block";
                                customFieldsContainer4.style.display = "block";
                            } else {
                                // customFieldsContainer1.style.display = "none";
                                // customFieldsContainer2.style.display = "none";
                                // customFieldsContainer3.style.display = "none";
                                // customFieldsContainer4.style.display = "none";
                            }
                        });
                    });
                    </script>


                    <div class="col-12">
                        <div class="border-bottom border-dashed my-3"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="6" > {{ $activity->description }}</textarea>
                    </div>

                    <div class="border-bottom border-dashed my-3"></div>

                    <!-- upload photos -->
                    <div class="card-header">
                        <h5 class="mb-1">Upload Photos</h5>
                    </div>
                    <div class="fallback">
                        <input id="fileInput" type="file" name="image" multiple="multiple" value="{{ $activity->image_path }}"/>
                    </div>
                    <div class="dz-message" data-dz-message="data-dz-message">
                        <img class="me-2" src="{{ asset('assets/img/icons/cloud-upload.svg') }}" width="25" alt="" />
                        Drop your files here
                    </div>
                    <div class="dz-preview dz-preview-multiple m-0 p-0 d-flex flex-column" id="previewContainer"></div>

                    <script>
                        document.getElementById("fileInput").addEventListener("change", function() {
                            var files = this.files;
                            var previewContainer = document.getElementById("previewContainer");
                            previewContainer.innerHTML = ""; // Clear previous previews

                            for (var i = 0; i < files.length; i++) {
                                var file = files[i];
                                var reader = new FileReader();

                                reader.onload = function(e) {
                                    var thumbnail = document.createElement("div");
                                    thumbnail.classList.add("d-flex", "media", "align-items-center", "mb-3", "pb-3", "btn-reveal-trigger");

                                    thumbnail.innerHTML = `
                                        <img class="dz-image" src="${e.target.result}" alt="${file.name}" />
                                        <div class="flex-1 d-flex flex-between-center">
                                            <div>
                                                <h6>${file.name}</h6>
                                                <div class="d-flex align-items-center">
                                                    <p class="mb-0 fs-10 text-400 lh-1">${(file.size / (1024 * 1024)).toFixed(2)} MB</p>
                                                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
                                                </div>
                                            </div>
                                            <div class="dropdown font-sans-serif">
                                                <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal dropdown-caret-none" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="fas fa-ellipsis-h"></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end border">
                                                    <a class="dropdown-item remove-file" href="#!" data-dz-remove="data-dz-remove">Remove File</a>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    previewContainer.appendChild(thumbnail);
                                };

                                reader.readAsDataURL(file);
                            }
                        });

                        // Event delegation to handle remove file
                        document.getElementById("previewContainer").addEventListener("click", function(e) {
                            if (e.target && e.target.classList.contains("remove-file")) {
                                e.preventDefault();
                                e.target.closest(".media").remove();

                                // Clear file input value
                                document.getElementById("fileInput").value = "";
                            }
                        });
                    </script>

                    <script>
                        document.getElementById("fileInput").addEventListener("change", function() {
                            var file = this.files[0];
                            var reader = new FileReader();

                            reader.onload = function(e) {
                                document.getElementById("coverPhoto").src = e.target.result;
                            };

                            if (file) {
                                reader.readAsDataURL(file);
                            }
                        });
                    </script>

                    <!-- post privacy -->
                    <div class="border-bottom border-dashed my-3"></div>
                    <h6>Listing Privacy</h6>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" id="customRadio4" type="radio" name="listingPrivacy" checked="checked" />
                        <label class="form-label mb-0" for="customRadio4"> <strong>Public</strong></label>
                        <div class="form-text mt-0">Discoverable by anyone on City College of Calapan.</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" id="customRadio5" type="radio" name="listingPrivacy" />
                        <label class="form-label mb-0" for="customRadio5"> <strong>Private</strong></label>
                        <div class="form-text mt-0">Accessible only by organization and department specified. </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="row justify-content-between align-items-center">
            <div class="col-md">
                <h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
            </div>
            <div class="col-auto">
                <button class="btn btn-falcon-default btn-sm me-2" type="submit" form="form1">Update</button>
            </div>
        </div>
    </div>
</div>


@endsection
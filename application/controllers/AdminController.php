<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class StudentController
 * @property Student_model $Student_model
 * @property Admin_model $Admin_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Upload $upload
 * @property CI_Form_validation $form_validation
 * @property CI_DB_query_builder $db <-- Add this line
 */

class AdminController extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model', 'admin');
        $this->load->model('Student_model');

        if (!$this->session->userdata('student_id')) {
            redirect(site_url('login'));
        }
    }




    public function admin_dashboard()
    {

        $data['title'] = 'Dashboard';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);


        $this->load->view('layout/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }


    // <========= THIS PART IS FOR THE MANAGEMENT OF ACTIVITY =====>


    // VIEWING OF CREATE ACTIVITY PAGE
    public function create_activity()
    {
        $data['title'] = 'Create Activity';

        $student_id = $this->session->userdata('student_id');

        $data['users'] = $this->admin->get_student($student_id);

        $data['dept'] = $this->admin->get_department(); // this is the audience

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/create-activity', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save_activity()
    {
        if ($this->input->post()) {
            // Set Validation Rules
            $this->form_validation->set_rules('title', 'Activity Title', 'required|is_unique[activity.activity_title]', [
                'required'   => 'The {field} is required.',
                'is_unique'  => 'The {field} must be unique. This title is already taken.'
            ]);
            $this->form_validation->set_rules('date_start', 'Start Date', 'required');
            $this->form_validation->set_rules('date_end', 'End Date', 'required');
            $this->form_validation->set_rules('start_datetime[]', 'Start Date & Time', 'required');
            $this->form_validation->set_rules('end_datetime[]', 'End Date & Time', 'required');
            $this->form_validation->set_rules('session_type[]', 'Session Type', 'required');

            // Run Validation
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'errors' => validation_errors()]);
                return;
            }


            // Prepare Activity Data
            $data = [
                'activity_title'        => $this->input->post('title'),
                'start_date'            => $this->input->post('date_start'),
                'end_date'              => $this->input->post('date_end'),
                'description'           => $this->input->post('description'),
                'registration_deadline' => $this->input->post('registration_deadline'),
                'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
                'organizer'             => 'Student Parliament',
                'fines'                 => str_replace(",", "", $this->input->post('fines')),
                'audience'              => $this->input->post('audience'),
                'created_at'            => date('Y-m-d H:i:s')
            ];

            // Handle Cover Image Upload
            if (!empty($_FILES['coverUpload']['name'])) {
                $coverImage = $this->upload_file('coverUpload', './assets/coverEvent');
                if (!$coverImage['status']) {
                    echo json_encode(['status' => 'error', 'errors' => $coverImage['error']]);
                    return;
                }
                $data['activity_image'] = $coverImage['file_name'];
            }

            // Handle QR Code Upload
            if (!empty($_FILES['qrcode']['name'])) {
                $qrCode = $this->upload_file('qrcode', './assets/qrcodeRegistration');
                if (!$qrCode['status']) {
                    echo json_encode(['status' => 'error', 'errors' => $qrCode['error']]);
                    return;
                }
                $data['qr_code'] = $qrCode['file_name'];
            }

            // Insert Activity and Get ID
            $activity_id = $this->admin->save_activity($data);
            if (!$activity_id) {
                echo json_encode(['status' => 'error', 'errors' => 'Failed to Save Activity']);
                return;
            }

            // Fetch Input Data
            $start_datetimes = $this->input->post('start_datetime') ?? [];
            $end_datetimes = $this->input->post('end_datetime') ?? [];
            $session_types = $this->input->post('session_type') ?? [];

            // Prepare Schedule Data
            $schedules = [];
            foreach ($start_datetimes as $i => $start_datetime) {
                $schedules[] = [
                    'activity_id'  => $activity_id,
                    'date_time_in' => date('Y-m-d H:i:s', strtotime($start_datetime)),
                    'date_time_out' => date('Y-m-d H:i:s', strtotime($end_datetimes[$i])),
                    'slot_name'    => $session_types[$i],
                    'created_at'   => date('Y-m-d H:i:s')
                ];
            }

            // Save Schedules
            $this->admin->save_schedules($schedules);

            // Assign Students to Activity
            $this->assign_students_to_activity($activity_id, $data);

            // Return Success Response
            echo json_encode([
                'status'   => 'success',
                'message'  => 'Activity Saved Successfully',
                'redirect' => site_url('admin/list-of-activity')
            ]);
        }
    }

    private function upload_file($field_name, $upload_path)
    {
        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'gif|jpg|jpeg|png',
            'max_size' => 2048
        ];
        $this->load->library('upload', $config);

        if ($this->upload->do_upload($field_name)) {
            return ['status' => true, 'file_name' => $this->upload->data('file_name')];
        } else {
            return ['status' => false, 'error' => $this->upload->display_errors()];
        }
    }

    // this is the default it is for the student parliament
    private function assign_students_to_activity($activity_id, $data)
    {
        $this->db->select('student_id');

        if ($data['audience'] == 0) {
            $this->db->from('users');
        } else {
            $this->db->from('users');
            $this->db->where('dept_id', $data['audience']);
        }

        $students = $this->db->get()->result();

        foreach ($students as $student) {
            $this->db->insert('attendance', [
                'activity_id' => $activity_id,
                'student_id' => $student->student_id
            ]);

            $this->db->insert('fines', [
                'activity_id' => $activity_id,
                'student_id' => $student->student_id
            ]);
        }
    }

    // FETCHING ACTIVITY BASED ON WHERE THE USER IS ADMIN (STUDENT PARLIAMENT)
    public function list_activity()
    {
        $data['title'] = 'List of Activities';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // GETTING OF THE ACTIVITY FROM THE DATABASE
        $data['activities'] = $this->admin->get_activities();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities', $data);
        $this->load->view('layout/footer', $data);
    }

    // FETCHING DETAILS OF THE ACTIVITY
    public function activity_details($activity_id)
    {
        $data['title'] = 'Activity Details';

        $student_id = $this->session->userdata('student_id');

        $data['users'] = $this->admin->get_student($student_id);

        $data['activity'] = $this->admin->get_activity($activity_id); // SPECIFIC ACTIVITY
        $data['schedules'] = $this->admin->get_schedule($activity_id); // GETTING OF SCHEDULE

        $data['activities'] = $this->admin->get_activities(); // FOR UPCOMING ACTIVITY PART

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/activity-detail', $data);
        $this->load->view('layout/footer', $data);
    }

    // SHARING ACTIVITY FROM THE ACTIVITY DETAILS
    public function share_activity()
    {
        // Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['activity_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $activity_id = $data['activity_id'];

        // Example: Mark activity as shared in the database
        $this->db->set('is_shared', 'Yes')->where('activity_id', $activity_id)->update('activity');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
        }
    }

    // EDITING OF ACTIVITY TO DATABASE
    public function edit_activity($activity_id)
    {
        $data['title'] = 'Edit Activity';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['activity'] = $this->admin->get_activity($activity_id);
        $data['schedules'] = $this->admin->get_schedule($activity_id);
        $data['dept'] = $this->admin->get_department();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/edit-activity', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete_schedule($id)
    {

        if ($this->admin->delete_schedule($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function update_activity($activity_id)
    {
        if ($this->input->post()) {
            // Set Validation Rules
            $this->form_validation->set_rules('date_start', 'Start Date', 'required');
            $this->form_validation->set_rules('date_end', 'End Date', 'required');
            $this->form_validation->set_rules('start_datetime[]', 'Start Date & Time', 'required');
            $this->form_validation->set_rules('end_datetime[]', 'End Date & Time', 'required');
            $this->form_validation->set_rules('session_type[]', 'Session Type', 'required');

            // Run Validation
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'errors' => validation_errors()]);
                return;
            }

            // Check if Activity Exists
            $activity = $this->admin->get_activity($activity_id);
            if (!$activity) {
                echo json_encode(['status' => 'error', 'errors' => 'Activity not found.']);
                return;
            }

            // Prepare Activity Data
            $data = [
                'activity_title'        => $this->input->post('title'),
                'start_date'            => $this->input->post('date_start'),
                'end_date'              => $this->input->post('date_end'),
                'description'           => $this->input->post('description'),
                'registration_deadline' => $this->input->post('registration_deadline'),
                'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
                'organizer'             => 'Student Parliament',
                'fines'                 => str_replace(",", "", $this->input->post('fines')),
                'audience'              => $this->input->post('audience')
            ];

            // Handle Cover Image Upload
            if (!empty($_FILES['coverUpload']['name'])) {
                $coverImage = $this->upload_file('coverUpload', './assets/coverEvent');
                if (!$coverImage['status']) {
                    echo json_encode(['status' => 'error', 'errors' => $coverImage['error']]);
                    return;
                }
                $data['activity_image'] = $coverImage['file_name'];
            }

            // Handle QR Code Upload
            if (!empty($_FILES['qrcode']['name'])) {
                $qrCode = $this->upload_file('qrcode', './assets/qrcodeRegistration');
                if (!$qrCode['status']) {
                    echo json_encode(['status' => 'error', 'errors' => $qrCode['error']]);
                    return;
                }
                $data['qr_code'] = $qrCode['file_name'];
            }

            // Update Activity
            $this->admin->update_activity($activity_id, $data);

            // Fetch Input Data
            $start_datetimes = $this->input->post('start_datetime') ?? [];
            $start_cutoff = $this->input->post('start_cutoff') ?? [];
            $end_datetimes = $this->input->post('end_datetime') ?? [];
            $end_cutoff = $this->input->post('end_cutoff') ?? [];
            $session_types = $this->input->post('session_type') ?? [];
            $schedule_ids = $this->input->post('timeslot_id') ?? [];

            // Update or Insert Schedules
            foreach ($start_datetimes as $i => $start_datetime) {
                // Ensure array indexes exist before accessing them
                $start_cutoff = $start_cutoff[$i] ?? null;
                $end_datetime = $end_datetimes[$i] ?? null;
                $end_cutoff = $end_cutoff[$i] ?? null;
                $session_type = $session_types[$i] ?? null;
                $schedule_id = $schedule_ids[$i] ?? null;

                // Prepare schedule data
                $schedule_data = [
                    'activity_id'  => $activity_id,
                    'date_time_in' => date('Y-m-d H:i:s', strtotime($start_datetime)),
                    'date_cut_in' => date('Y-m-d H:i:s', strtotime($start_cutoff)),
                    'date_time_out' => date('Y-m-d H:i:s', strtotime($end_datetime)),
                    'date_cut_out' => date('Y-m-d H:i:s', strtotime($end_cutoff)),
                    'slot_name'    => $session_type
                ];

                if (!empty($schedule_id)) {
                    // Update existing schedule
                    $this->admin->update_schedule($schedule_id, $schedule_data);
                } else {
                    // Insert new schedule with created_at timestamp
                    $schedule_data['created_at'] = date('Y-m-d H:i:s');

                    $this->admin->save_schedule($schedule_data);
                }
            }

            // Return Success Response
            echo json_encode([
                'status'   => 'success',
                'message'  => 'Activity Updated Successfully',
                'redirect' => site_url('admin/list-of-activity')
            ]);
        }
    }

    // END EDITING OF ACTIVITY

    // START OF EVALUATION
    public function list_activity_evaluation()
    {
        $data['title'] = 'List of Evaluation';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // GET THE EVALUATION FOR STUDENT PARLIAMENT
        $data['evaluation'] = $this->admin->evaluation_form();

        // Pass data to the view
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/eval_list-activity-evaluation', $data);
        $this->load->view('layout/footer', $data);
    }

    // START CREATE EVALUATION
    public function create_evaluationform()
    {
        $data['title'] = 'Create Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // Fetch activities based on user role
        $data['activities'] = $this->admin->activity_organized();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/eval_create-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    // Controller function to create an evaluation form
    public function create_eval()
    {
        // Validate basic form inputs from the POST request
        $this->form_validation->set_rules('activity', 'Activity', 'required');
        $this->form_validation->set_rules('formtitle', 'Form Title', 'required');
        $this->form_validation->set_rules('formdescription', 'Form Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        // Prepare form data from POST request
        $formData = array(
            'title'       => $this->input->post('formtitle'),
            'form_description' => $this->input->post('formdescription'),
            'activity_id' => $this->input->post('activity'),
            'start_date_evaluation'  => $this->input->post('startdate') ?: date('Y-m-d H:i:s'),
            'end_date_evaluation'    => $this->input->post('enddate') ?: date('Y-m-d H:i:s', strtotime('+1 week')),
        );

        // Handle Cover Image Upload
        if (!empty($_FILES['coverUpload']['name'])) {
            $coverImage = $this->upload_file('coverUpload', './assets/theme_evaluation');
            if (!$coverImage['status']) {
                echo json_encode(['success' => false, 'message' => $coverImage['error']]);
                return;
            }
            $formData['cover_theme'] = $coverImage['file_name'];
        }

        // Decode fields JSON (ensuring proper format)
        $fields = json_decode($this->input->post('fields'), true);

        // Validate and check if fields are properly sent
        if (!is_array($fields) || count($fields) === 0) {
            echo json_encode(['success' => false, 'message' => 'Please provide at least one form field.']);
            return;
        }

        $this->db->trans_start(); // Start transaction

        $formId = $this->admin->saveForm($formData); // Save form data and get form ID

        if (!$formId) {
            $this->db->trans_rollback(); // Rollback if form insert fails
            echo json_encode(['success' => false, 'message' => 'Failed to create the form.']);
            return;
        }

        // Prepare and save form fields with related form_id
        $fieldData = [];
        foreach ($fields as $index => $field) {
            $fieldData[] = array(
                'form_id'     => $formId,
                'label'       => $field['label'] ?? '',
                'type'        => $field['type'] ?? '',
                'placeholder' => $field['placeholder'] ?? null,
                'required'    => !empty($field['required']) ? 1 : 0,
                'order'       => $index + 1,
            );
        }

        if (!empty($fieldData)) {
            $this->admin->saveFormFields($fieldData); // Save form fields
        }

        $this->db->trans_complete(); // Complete transaction

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Failed to create the form.']);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Form created successfully.',
                'redirect_url' => site_url('admin/list-activity-evaluation')
            ]);
        }
    }


    // START EDITING EVALUATION
    public function edit_evaluationform($form_id)
    {
        $data['title'] = 'Edit Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // Fetch activities based on user role
        $data['activities'] = $this->admin->activity_organized();
        $data['forms'] = $this->admin->get_evaluation_by_id($form_id);

        $form_data = $this->admin->get_evaluation_by_id($form_id);
        $data['form_data'] = $form_data;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/eval_edit-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    // UPDATE EVALUATION
    public function update_eval($form_id)
    {
        // Validate form ID
        if (!$form_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid form ID.']);
            return;
        }

        // Validate input fields
        $this->form_validation->set_rules('formtitle', 'Form Title', 'required');
        $this->form_validation->set_rules('formdescription', 'Form Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }

        // Prepare form data
        $formData = [
            'activity_id' => $this->input->post('activity'),
            'title' => $this->input->post('formtitle'),
            'form_description' => $this->input->post('formdescription'),
            'start_date_evaluation' => $this->input->post('startdate'),
            'end_date_evaluation' => $this->input->post('enddate'),
        ];

        // Handle Cover Image Upload (if new file is uploaded)
        if (!empty($_FILES['coverUpload']['name'])) {
            $coverImage = $this->upload_file('coverUpload', './assets/theme_evaluation');
            if (!$coverImage['status']) {
                echo json_encode(['success' => false, 'message' => $coverImage['error']]);
                return;
            }
            $formData['cover_theme'] = $coverImage['file_name'];
        }

        // Retrieve form fields (ensure correct format)
        $fields = json_decode($this->input->post('fields'), true);
        if (!is_array($fields)) {
            echo json_encode(['success' => false, 'message' => 'Invalid form fields data.']);
            return;
        }

        // Start database transaction
        $this->db->trans_start();

        // Update main form data
        $this->db->where('form_id', $form_id)->update('forms', $formData);

        // Fetch existing field IDs from database
        $existingFields = $this->db->select('form_fields_id')->where('form_id', $form_id)->get('formfields')->result_array();
        $existingFieldIds = array_column($existingFields, 'form_fields_id');

        $updatedFieldIds = []; // Track updated/new fields

        foreach ($fields as $index => $field) {
            $fieldId = $field['form_fields_id'] ?? null;

            $fieldData = [
                'form_id'     => $form_id,
                'label'       => $field['label'],
                'type'        => $field['type'],
                'placeholder' => $field['placeholder'] ?? null,
                'required'    => !empty($field['required']) ? 1 : 0,
                'order'       => $index + 1,
            ];

            if ($fieldId && in_array($fieldId, $existingFieldIds)) {
                // Update existing field
                $this->db->where('form_fields_id', $fieldId)->update('formfields', $fieldData);
                $updatedFieldIds[] = $fieldId;
            } else {
                // Insert new field
                $this->db->insert('formfields', $fieldData);
                $updatedFieldIds[] = $this->db->insert_id();
            }
        }

        // Identify and delete removed fields
        $fieldsToDelete = array_diff($existingFieldIds, $updatedFieldIds);
        if (!empty($fieldsToDelete)) {
            $this->db->where_in('form_fields_id', $fieldsToDelete)->delete('formfields');
        }

        // Commit transaction
        $this->db->trans_complete();

        // Check transaction status
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Failed to update the form.']);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Form updated successfully.',
                'redirect' => site_url('admin/list-activity-evaluation'),
            ]);
        }
    }

    public function view_evaluationform($form_id)
    {
        $data['title'] = 'Edit Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // Fetch activities based on user role
        $data['activities'] = $this->admin->activity_organized();
        $data['forms'] = $this->admin->get_evaluation_by_id($form_id);

        $form_data = $this->admin->get_evaluation_by_id($form_id);
        $data['form_data'] = $form_data;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/eval_view-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    //	END EVALUATION FROM



    // Helper function to get status class
    public function get_status_class($status)
    {
        $status_classes = [
            'Completed' => 'success',
            'Ongoing' => 'info',
            'Upcoming' => 'danger',
        ];

        return isset($status_classes[$status]) ? $status_classes[$status] : 'secondary'; // Default to 'secondary' if no status is found
    }




    public function list_evaluation_answers($activity_id)
    {
        // Load necessary models
        $this->load->model('Admin_model');

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $users = $this->admin->get_student($student_id);
        $data['role'] = $users['role'];
        $data['profile_pic'] = $users['profile_pic'];

        //  Fetch the specific activity instead of all activities
        $activity = $this->admin->get_activity_by_id($activity_id);

        if (!$activity) {
            show_404(); // Show a 404 error if the activity is not found
        }

        //  Fetch forms for the specific activity
        $forms = $this->admin->get_forms_by_activity($activity_id);

        // Initialize an array to hold form responses
        $form_responses = [];

        foreach ($forms as $form) {
            $responses = $this->admin->get_responses_by_form($form->form_id);

            foreach ($responses as $response) {
                // Get student information
                $student = $this->admin->get_student_by_id($response->student_id);

                $form_responses[] = [
                    'form_id' => $form->form_id,
                    'form_title' => $form->title,
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'submitted_at' => $response->submitted_at,
                    'evaluation_response_id' => $response->evaluation_response_id,
                ];
            }
        }

        //  Pass correct data to the view
        $data['form_responses'] = $form_responses;
        $data['activity_id'] = $activity_id;
        $data['activity'] = $activity; //  Pass the specific activity to the view
        $data['activities'] = [$activity]; //  Make it an array to avoid foreach() errors

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-evaluation-answers', $data);
        $this->load->view('layout/footer', $data);
    }



    // In your AdminController.php

    public function view_response($evaluation_response_id)
    {
        // Load necessary models
        $this->load->model('Admin_model');
        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $users = $this->admin->get_student($student_id);
        $data['role'] = $users['role'];
        $data['profile_pic'] = $users['profile_pic'];

        // Get answers for the given evaluation_response_id
        $answers = $this->admin->get_answers_by_evaluation_response($evaluation_response_id);
        $data['answers'] = $answers;

        // Check if answers have form_id and fetch form details
        if (!empty($answers) && isset($answers[0]->form_id)) {
            $form_id = $answers[0]->form_id;  // Get form_id from the first answer
            $form = $this->admin->get_form_by_id($form_id);  // Fetch form details using form_id
            $data['form'] = $form;
        } else {
            // If no answers or form_id, handle the case
            $data['form'] = null;
        }

        // Pass evaluation_response_id to the view
        $data['evaluation_response_id'] = $evaluation_response_id;

        // Load the views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/evaluation_responses_page', $data);
        $this->load->view('layout/footer', $data);
    }


    //	========= EVALUATION RESPONSES END




    //  START EXCUSE LETTER SECTION

    // FETCHING ACTIVITY
    public function list_activity_excuse()
    {
        $data['title'] = 'List of Activity for Excuse Letter';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['activities'] = $this->admin->get_activities();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/exc_list-activities-excuseletter', $data);
        $this->load->view('layout/footer', $data);
    }

    // LIST OF APPLICATION PER EVENT
    public function list_excuse_letter($activity_id)
    {
        $data['title'] = 'List of Excuse Letter';


        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['activities'] = $this->admin->fetch_application($activity_id);
        $data['letters'] = $this->admin->fetch_letters();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/exc_list-excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // EXCUSE LETTER PER STUDENT
    public function review_excuse_letter($excuse_id)
    {
        $data['title'] = 'Review Excuse Letter';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['excuse'] = $this->admin->review_letter($excuse_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/exc_excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // UPDATING REMARKS AND STATUS OF THE APPLICATION
    public function updateApprovalStatus()
    {
        $remarks = $this->input->post('remarks');
        $approvalStatus = $this->input->post('approvalStatus');
        $excuse_id = $this->input->post('excuse_id');  // Assuming the application ID is passed

        // Prepare the data for updating
        $data = [
            'excuse_id' => $excuse_id,
            'status' => $approvalStatus,
            'remarks' => $remarks
        ];

        // Call the model to update the database
        $result = $this->admin->updateApprovalStatus($data);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Approval status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update approval statu']);
        }
    }

    // <===== COMMUNITY SECTION =====>

    // VIEWING OF COMMUNITY SECTION
    public function community()
    {
        $data['title'] = 'Community';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // GETTING USER INFORMATION
        $data['authors'] = $this->admin->get_user();

        // CHECKING WHICH THE USER BELONGS FOR DEPT AND ORG
        $data['organization'] = $this->admin->get_student_organizations($student_id);
        $data['department'] = $this->admin->get_student_department($student_id);

        // POST DETAILS
        $data['posts'] = $this->admin->get_all_posts();

        // FETCH LIKE COUNTS FOR EACH POST
        foreach ($data['posts'] as &$post) {
            $post->like_count = $this->admin->get_like_count($post->post_id);
            $post->user_has_liked_post = $this->admin->user_has_liked($post->post_id, $student_id);
            $post->comments_count = $this->admin->get_comment_count($post->post_id);
        }

        // FETCH 2 COMMENTS ONLY IN A POST
        foreach ($data['posts'] as &$post) {
            $post->comments = $this->admin->get_comments_by_post($post->post_id);
        }

        // FETCHING ACTVITIES UPCOMING
        $data['activities'] = $this->admin->get_activities();


        // AMDIN SHARING OF ACTIVITY 
        $data['org_id'] = $this->admin->admin_org_id();
        $data['dept_id'] = $this->admin->admin_dept_id();


        // Load the views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/community', $data);
        $this->load->view('layout/footer', $data);
    }

    // LIKING OF POST
    public function like_post($post_id)
    {
        $student_id = $this->session->userdata('student_id');

        // Check if the user already liked the post
        if ($this->admin->user_has_liked($post_id, $student_id)) {
            // User already liked, so we will "unlike" the post
            $this->admin->unlike_post($post_id, $student_id);
            $like_img = base_url() . 'assets/img/icons/spot-illustrations/like-inactive.png';
            $like_text = 'Like';
        } else {
            // User has not liked the post yet, so we will "like" the post
            $this->admin->like_post($post_id, $student_id);
            $like_img = base_url() . 'assets/img/icons/spot-illustrations/like-active.png';
            $like_text = 'Liked';
        }

        // Get the updated like count
        $new_like_count = $this->admin->get_like_count($post_id);

        // Return the response
        echo json_encode([
            'like_img' => $like_img,
            'like_text' => $like_text,
            'new_like_count' => $new_like_count
        ]);
    }

    // ADDING COMMENTS
    public function add_comment()
    {
        if ($this->input->post()) {
            // Set validation rules
            $this->form_validation->set_rules('comment', 'Comment', 'required');

            if ($this->form_validation->run() == FALSE) {
                $response = [
                    'status' => 'error',
                    'errors' => validation_errors()
                ];
            } else {
                // Prepare data for insertion
                $data = array(
                    'post_id' => $this->input->post('post_id'),
                    'content' => $this->input->post('comment'),
                    'student_id' => $this->session->userdata('student_id')
                );

                $result = $this->admin->add_comment($data);
                if ($result) {
                    $post_id = $this->input->post('post_id');

                    // Fetch updated comment count
                    $comments_count = $this->admin->get_comment_count($post_id);

                    // Fetch the newly added comment only
                    $new_comment = $this->admin->get_latest_comment($post_id); // Ensure this function fetches only the latest

                    // Ensure correct JSON response
                    $response = array(
                        'status' => 'success',
                        'message' => 'Comment Saved Successfully',
                        'comments_count' => $comments_count,
                        'new_comment' => array( // Make sure this is an array with expected keys
                            'first_name' => $new_comment->first_name ?? 'Unknown',
                            'last_name' => $new_comment->last_name ?? 'Unknown',
                            'profile_pic' => base_url('assets/profile/') . ($new_comment->profile_pic ?? 'default.jpg'),
                            'content' => $new_comment->content ?? '',
                        )
                    );
                } else {
                    $response = array(
                        'status' => 'error',
                        'errors' => 'Failed to Save Comment'
                    );
                }
            }
        }

        echo json_encode($response);
    }

    public function delete_post()
    {
        $post_id = $this->input->post('post_id');

        if (!$post_id) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Post ID']);
            return;
        }

        // Fetch image path before deleting
        $this->db->select('media');
        $this->db->where('post_id', $post_id);
        $query = $this->db->get('post'); // Get image from images table

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if (!empty($row->media)) { // Check if media field is not empty
                    $image_path = FCPATH . 'assets/post/' . $row->media; // Change path as needed

                    if (file_exists($image_path)) {
                        unlink($image_path); // Delete image from folder
                    }
                }
            }
        }


        // Load model and delete post
        $deleted = $this->admin->delete_post($post_id);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete post']);
        }
    }

    // SHARING OF ACTIVITY TO THE FEED
    public function share()
    {
        // Only accept AJAX requests
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed.');
        }

        // Get the activity ID from the POST request
        $input = json_decode(file_get_contents('php://input'), true);
        $activity_id = $input['activity_id'];

        if ($activity_id) {
            // Update the activity's is_shared column
            $result = $this->admin->update_is_shared($activity_id);

            if ($result) {
                // Return success response
                echo json_encode(['success' => true]);
                return;
            }
        }

        // Return failure response
        echo json_encode(['success' => false]);
    }

    // ADDING OF POST
    public function add_post()
    {

        $student_id = $this->session->userdata('student_id');

        $dept = $this->admin->admin_dept();
        $org = $this->admin->admin_org();

        // Check if the form is submitted
        if ($this->input->post()) {
            // Set validation rules
            $this->form_validation->set_rules('content', 'Content', 'required');

            // Check if validation passes
            if ($this->form_validation->run() == FALSE) {
                // If validation fails, return validation errors
                $response = [
                    'status' => 'error',
                    'errors' => validation_errors()
                ];
                echo json_encode($response);
                return;
            }

            // Prepare data for insertion
            $data = [
                'student_id' => $student_id,
                'content' => $this->input->post('content'),
                'privacy' => $this->input->post('privacyStatus'),
                'dept_id' => !empty($dept->dept_id) ? $dept->dept_id : NULL,
                'org_id' => !empty($org->org_id) ? $org->org_id : NULL,
            ];

            // Handle file upload if an image is provided
            if (!empty($_FILES['image']['name'])) {
                $config = [
                    'upload_path'   => './assets/post/',
                    'allowed_types' => 'gif|jpg|jpeg|png',
                    'max_size'      => 2048, // 2MB limit
                    'encrypt_name'  => TRUE  // Encrypt file name for security
                ];

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {
                    $uploadData = $this->upload->data();
                    $data['media'] = $uploadData['file_name'];
                } else {
                    // Upload failed, return error response
                    $response = [
                        'status' => 'error',
                        'errors' => $this->upload->display_errors()
                    ];
                    echo json_encode($response);
                    return;
                }
            }

            // Insert post data into the database
            $result = $this->admin->insert_data($data);

            if ($result) {
                // Post saved successfully
                $response = [
                    'status' => 'success',
                    'message' => 'You shared a post.',
                    'redirect' => site_url('admin/community')
                ];
            } else {
                // Database insertion failed
                $response = [
                    'status' => 'error',
                    'errors' => 'Failed to post. Please try again.'
                ];
            }

            // Return the response
            echo json_encode($response);
            return;
        }

        // If no POST data, return an error response
        $response = [
            'status' => 'error',
            'errors' => 'Invalid request. No data received.'
        ];
        echo json_encode($response);
    }


    // <========= ATTENDANCE MONITORING ===========>

    public function list_activities_attendance()
    {
        $data['title'] = 'Manage Officers';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();

        $data['activities'] = $this->admin->get_activities();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/list-activities-attendance', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_department($activity_id)
    {
        $data['title'] = 'List of Department';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();

        $data['activity_id'] = $activity_id;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/department', $data);
        $this->load->view('layout/footer', $data);
    }

    // SHOWING ATTENDANCE LIST
    public function list_attendees($activity_id, $dept_id)
    {
        $data['title'] = 'List of Attendees';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['dept_id'] = $dept_id;
        $data['activity_id'] = $activity_id;

        // CHECKING WHERE THE USER ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['departments'] = $this->admin->admin_dept();

        $data['department'] = $this->admin->get_department();

        $data['activities'] = $this->admin->fetch_application($activity_id);

        $data['students'] = $this->admin->fetch_users();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/listofattendees', $data);
        $this->load->view('layout/footer', $data);
    }

    public function scanning_qr($activity_id)
    {
        $data['title'] = 'Scanning QR';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['activity'] = $this->admin->get_activity($activity_id);
        $data['schedule'] = $this->admin->get_schedule($activity_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/scanqr', $data);
        $this->load->view('layout/footer', $data);
    }

    public function face_recognition($activity_id)
    {
        $data['title'] = 'Face Recognition';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        $data['activity'] = $this->admin->get_activity($activity_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/face', $data);
        $this->load->view('layout/footer', $data);
    }

    // SCANNING OF QR
    public function scan()
    {

        date_default_timezone_set('Asia/Manila');

        // Validate input
        $this->form_validation->set_rules('student_id', 'Student ID', 'required');
        $this->form_validation->set_rules('activity_id', 'Activity', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        }

        $student_id = $this->input->post('student_id');
        $activity_id = $this->input->post('activity_id');
        $current_time = date('H:i:s');

        // Fetch the activity schedule
        $activity = $this->admin->get_activity_schedule($activity_id);
        $attendance = $this->admin->get_attendance_record($student_id, $activity_id);

        if (!$activity) {
            $this->session->set_flashdata('error', 'Activity not found.');
            return;
        }

        echo $activity->fines;

        $update_data = [];
        $update_fines = [];

        // CHECKING THE SCHEDULE
        if ($activity) {
            // Half-day PM (if AM schedule is empty)
            if (empty($activity->am_in) && empty($activity->am_out)) {

                if (empty($attendance->pm_in)) {
                    // Handling am_in scan
                    if (!empty($activity->pm_in)) {
                        if ($current_time <= $activity->pm_in) {
                            $update_data['pm_in'] =  date('Y-m-d H:i:s');
                            $update_data['pm_in_status'] = 'Present';
                            $update_fines['pm_in'] = '0';
                        } elseif ($current_time > $activity->pm_in && $current_time <= $activity->pm_in_cut) {
                            $update_data['pm_in'] = date('Y-m-d H:i:s');
                            $update_data['pm_in_status'] = 'Late';
                        } elseif ($current_time > $activity->pm_in_cut) {
                            $update_data['pm_in'] = date('Y-m-d H:i:s');
                            $update_data['pm_in_status'] = 'Absent';
                            $update_fines['pm_in'] = $activity->fines;
                        }
                    }
                } else {
                    // If pm_in_status is already recorded, process pm_out scan
                    if (!empty($attendance->pm_in)) {
                        if ($current_time < $activity->pm_out) {
                            // Early out before official out time
                            $update_data['pm_out'] = date('Y-m-d H:i:s');
                            $update_data['pm_out_status'] = 'Early Out';
                            $update_fines['pm_out'] = $activity->fines;
                        } elseif ($current_time >= $activity->pm_out && $current_time <= $activity->pm_out_cut) {
                            // On time or within grace period
                            $update_data['pm_out'] = date('Y-m-d H:i:s');
                            $update_data['pm_out_status'] = 'Present';
                            $update_fines['pm_out'] = "0";
                        } elseif ($current_time > $activity->pm_out_cut) {
                            // Late out
                            $update_data['pm_out'] = date('Y-m-d H:i:s');
                            $update_data['pm_out_status'] = 'Late Out';
                        }
                    }
                }

                if ($attendance->pm_in_status == 'Present' && $attendance->pm_out_status == 'Present') {
                    $update_data['attendance_status'] = 'Present';
                } elseif ($attendance->pm_in_status == 'Absent' && $attendance->pm_out_status == 'Absent') {
                    $update_data['attendance_status'] = 'Absent';
                } else {
                    $update_data['attendance_status'] = 'Incomplete';
                }
            }
            // Half-day AM (if PM schedule is empty)
            elseif (empty($activity->pm_in) && empty($activity->pm_out)) {
                if (empty($attendance->am_in)) {
                    // Handling am_in scan
                    if (!empty($activity->am_in)) {
                        if ($current_time <= $activity->am_in) {
                            $update_data['am_in'] = date('Y-m-d H:i:s');
                            $update_data['am_in_status'] = 'Present';
                            $update_fines['am_in'] = '0';
                        } elseif ($current_time > $activity->am_in && $current_time <= $activity->am_in_cut) {
                            $update_data['am_in'] = date('Y-m-d H:i:s');
                            $update_data['am_in_status'] = 'Late';
                        } elseif ($current_time > $activity->am_in_cut) {
                            $update_data['am_in'] = date('Y-m-d H:i:s');
                            $update_data['am_in_status'] = 'Absent';
                            $update_fines['am_in'] = $activity->fines;
                        }
                    }
                } else {
                    // If am_in_status is already recorded, process am_out scan
                    if (!empty($activity->am_in)) {
                        if ($current_time < $activity->am_out) {
                            // Early out before official out time
                            $update_data['am_out'] = date('Y-m-d H:i:s');
                            $update_data['am_out_status'] = 'Early Out';
                            $update_fines['am_out'] = $activity->fines;
                        } elseif ($current_time >= $activity->am_out && $current_time <= $activity->am_out_cut) {
                            // On time or within grace period
                            $update_data['am_out'] = date('Y-m-d H:i:s');
                            $update_data['am_out_status'] = 'Present';
                            $update_fines['am_out'] = '0';
                        } elseif ($current_time > $activity->am_out_cut) {
                            // Late out (if necessary to handle)
                            $update_data['am_out'] = date('Y-m-d H:i:s');
                            $update_data['am_out_status'] = 'Late Out';
                        }
                    }
                }

                if ($attendance->am_in_status == 'Present' && $attendance->am_out_status == 'Present') {
                    $update_data['attendance_status'] = 'Present';
                } elseif ($attendance->am_in_status == 'Absent' && $attendance->am_out_status == 'Absent') {
                    $update_data['attendance_status'] = 'Absent';
                } else {
                    $update_data['attendance_status'] = 'Incomplete';
                }
            }
            // Whole-day event
            else {

                // RECORDING AM 

                if (empty($attendance->am_in)) {
                    // Handling am_in scan
                    if ($current_time <= $activity->am_in) {
                        $update_data['am_in'] = date('Y-m-d H:i:s');
                        $update_data['am_in_status'] = 'Present';
                        $update_fines['am_in'] = "0";
                    } elseif ($current_time > $activity->am_in && $current_time <= $activity->am_in_cut) {
                        $update_data['am_in'] = date('Y-m-d H:i:s');
                        $update_data['am_in_status'] = 'Late';
                    } elseif ($current_time > $activity->am_in_cut) {
                        $update_data['am_in'] = date('Y-m-d H:i:s');
                        $update_data['am_in_status'] = 'Absent';
                        $update_fines['am_in'] = $activity->fines;
                    }
                } elseif (!empty($attendance->am_in) && empty($attendance->am_out)) {
                    // If am_in_status is already recorded, process am_out scan
                    if ($current_time < $activity->am_out) {
                        // Early out before official out time
                        $update_data['am_out'] = date('Y-m-d H:i:s');
                        $update_data['am_out_status'] = 'Early Out';
                        $update_fines['am_out'] = $activity->fines;
                    } elseif ($current_time >= $activity->am_out && $current_time <= $activity->am_out_cut) {
                        // On time or within grace period
                        $update_data['am_out'] = date('Y-m-d H:i:s');
                        $update_data['am_out_status'] = 'Present';
                        $update_fines['am_out'] = "0";
                    } elseif ($current_time > $activity->am_out_cut) {
                        // Late out (if necessary to handle)
                        $update_data['am_out'] = date('Y-m-d H:i:s');
                        $update_data['am_out_status'] = 'Late Out';
                    }
                }

                if (!empty($attendance->am_out) && empty($attendance->pm_in)) {
                    // Handling pm_in scan
                    if ($current_time <= $activity->pm_in) {
                        $update_data['pm_in'] =  date('Y-m-d H:i:s');
                        $update_data['pm_in_status'] = 'Present';
                        $update_fines['pm_in'] = "0";
                    } elseif ($current_time > $activity->pm_in && $current_time <= $activity->pm_in_cut) {
                        $update_data['pm_in'] = date('Y-m-d H:i:s');
                        $update_data['pm_in_status'] = 'Late';
                    } elseif ($current_time > $activity->pm_in_cut) {
                        $update_data['pm_in'] = date('Y-m-d H:i:s');
                        $update_data['pm_in_status'] = 'Absent';
                        $update_fines['pm_in'] = $activity->fines;
                    }
                } elseif (!empty($attendance->pm_in) && empty($attendance->pm_out)) {
                    // If pm_in_status is already recorded, process pm_out scan
                    if ($current_time < $activity->pm_out) {
                        // Early out before official out time
                        $update_data['pm_out'] = date('Y-m-d H:i:s');
                        $update_data['pm_out_status'] = 'Early Out';
                        $update_fines['pm_out'] = $activity->fines;
                    } elseif ($current_time >= $activity->pm_out && $current_time <= $activity->pm_out_cut) {
                        // On time or within grace period
                        $update_data['pm_out'] = date('Y-m-d H:i:s');
                        $update_data['pm_out_status'] = 'Present';
                        $update_fines['pm_out'] = "0";
                    } elseif ($current_time > $activity->pm_out_cut) {
                        // Late out
                        $update_data['pm_out'] = date('Y-m-d H:i:s');
                        $update_data['pm_out_status'] = 'Late Out';
                    }
                }

                if ($attendance->am_in_status == 'Present' && $attendance->am_out_status == 'Present' && $attendance->pm_in_status == 'Present' && $attendance->pm_out_status == 'Present') {
                    $update_data['attendance_status'] = 'Present';
                } elseif ($attendance->am_in_status == 'Absent' && $attendance->am_out_status == 'Absent' && $attendance->pm_in_status == 'Absent' && $attendance->pm_out_status == 'Absent') {
                    $update_data['attendance_status'] = 'Absent';
                } else {
                    $update_data['attendance_status'] = 'Incomplete';
                }
            }

            // Update attendance record
            $this->admin->update_attendance($student_id, $activity_id, $update_data);

            // Update fines
            $this->admin->update_fines($student_id, $activity_id, $update_fines);

            // **New: Compute and Update Total Fine**
            $this->compute_and_update_fine($student_id, $activity_id);
        }
    }

    // Function to Compute and Update Total Fine
    private function compute_and_update_fine($student_id, $activity_id)
    {
        $this->db->query(
            "
            UPDATE fines
            SET total_amount = (
                COALESCE(am_in, 0) + COALESCE(am_out, 0) + 
                COALESCE(pm_in, 0) + COALESCE(pm_out, 0)
            )
            WHERE student_id = ? AND activity_id = ?",
            [$student_id, $activity_id]
        );
    }


    // <====== FINES MONITORING =====>
    // public function list_activities_fines()
    // {
    //     $data['title'] = 'All Activities';

    //     // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
    //     $data['users'] = $this->admin->get_student($student_id);

    //     // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
    //     $data['organization'] = $this->admin->admin_org();
    //     $data['department'] = $this->admin->admin_dept();

    //     $data['activities'] = $this->admin->get_activities();

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/fines/list-activities-fines', $data);
    //     $this->load->view('layout/footer', $data);
    // }

    // public function list_department_fines($activity_id)
    // {
    //     $data['title'] = 'List of Department';

    //     $student_id = $this->session->userdata('student_id');

    //     // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
    //     $data['users'] = $this->admin->get_student($student_id);

    //     // FETCHING ALL THE DEPARTMENT
    //     $data['department'] = $this->admin->get_department();

    //     $data['activity_id'] = $activity_id;

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/fines/department', $data);
    //     $this->load->view('layout/footer', $data);
    // }


    //======> FINES
    public function list_fines()
    {
        $data['title'] = 'List of Fines';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // CHECKING WHERE THE USER ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['departments'] = $this->admin->admin_dept();

        $data['department'] = $this->admin->get_department();

        //$data['activities'] = $this->admin->fetch_application($activity_id);

        $data['fines'] = $this->admin->fetch_students();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/fines/listoffines', $data);
        $this->load->view('layout/footer', $data);
    }

    public function update_status()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        // Ensure required parameters exist
        if (!isset($input['student_id']) || !isset($input['activity_id']) || !isset($input['is_paid'])) {
            echo json_encode(["success" => false, "message" => "Invalid request"]);
            return;
        }

        $student_id = $input['student_id'];
        $activity_id = $input['activity_id'];
        $new_status = ($input['is_paid'] === "Yes") ? "Yes" : "No";

        // Update only the specific record with both student_id & activity_id
        if ($this->admin->updateFineStatus($student_id, $activity_id, $new_status)) {
            echo json_encode(["success" => true, "message" => "Fine status updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }


    // GETTING THE FACES 
    public function getFaces()
    {
        $this->load->database();
        $query = $this->db->select('*')->get('users');
        $faces = $query->result_array();

        // Ensure the full URL is returned
        foreach ($faces as &$face) {
            $face['profile_pic'] = base_url('assets/profile/' . $face['profile_pic']);
        }

        echo json_encode($faces);
    }


    // <=========== OTHER PAGES ===========>

    // PROFILE SETTINGS ====>
    //PROFILE SETTINGS

    public function profile_settings()
    {
        $data['title'] = 'Profile Settings';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');


        // Fetching student details from the database
        $student_details = $this->Student_model->get_student_full_details($student_id); // Using get_student_full_details method

        if ($student_details) {
            $data['first_name'] = isset($student_details['first_name']) ? $student_details['first_name'] : '';
            $data['last_name'] = isset($student_details['last_name']) ? $student_details['last_name'] : '';
            $data['email'] = isset($student_details['email']) ? $student_details['email'] : '';
            $data['profile_pic'] = isset($student_details['profile_pic']) ? $student_details['profile_pic'] : 'default-pic.jpg';
            $data['department_name'] = isset($student_details['department_name']) ? $student_details['department_name'] : '';
            $data['role'] = isset($student_details['role']) ? $student_details['role'] : '';

            // Pass the organizations data
            $data['organizations'] = isset($student_details['organizations']) ? $student_details['organizations'] : [];
        }

        // Fetch User Role
        $data['users'] = $this->admin->get_student($student_id);
        $data['role'] = $users['role'];

        // Pass student_id to the view
        $data['student_id'] = $student_id;

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/profile-settings', $data);
        $this->load->view('layout/footer', $data);
    }



    //UPDATE PROFILE PIC

    public function update_profile_pic()
    {
        // Get student ID from session
        $student_id = $this->session->userdata('student_id');

        if (!$student_id) {
            echo json_encode(['status' => 'error', 'error' => 'User not logged in']);
            return;
        }

        // Use absolute path to upload directory
        $upload_path = FCPATH . 'assets/profile/';

        // Check if the directory exists
        if (!is_dir($upload_path)) {
            // Try to create the directory if it doesn't exist
            if (!mkdir($upload_path, 0777, true)) {
                echo json_encode(['status' => 'error', 'error' => 'Failed to create directory.']);
                return;
            }
        }

        // Check if the directory is writable
        if (!is_writable($upload_path)) {
            echo json_encode(['status' => 'error', 'error' => 'Upload folder is not writable.']);
            return;
        }

        // Set up file upload configuration
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = $student_id . "_" . time();  // Unique filename
        $config['overwrite'] = true;

        // Load the upload library
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $this->load->model('Student_model');

        // Get the current profile pic from the database
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);

        // Check if a file is uploaded
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            // Try uploading the file
            if (!$this->upload->do_upload('profile_pic')) {
                echo json_encode(['status' => 'error', 'error' => strip_tags($this->upload->display_errors())]);
                return;
            }

            // Get the upload data (file name)
            $uploadData = $this->upload->data();
            $profile_pic = $uploadData['file_name'];

            // Remove old profile picture if exists
            if ($current_profile_pic && file_exists($upload_path . $current_profile_pic)) {
                unlink($upload_path . $current_profile_pic);
            }

            // Update the profile picture in the database
            if ($this->Student_model->update_profile_pic($student_id, ['profile_pic' => $profile_pic])) {
                // Respond with success and the new file name and URL
                echo json_encode([
                    'status' => 'success',
                    'file_name' => $profile_pic,
                    'file_url' => base_url('assets/profile/' . $profile_pic)  // URL to access the profile picture
                ]);
            } else {
                echo json_encode(['status' => 'error', 'error' => 'Failed to update profile picture in the database.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'error' => 'No file uploaded or an error occurred.']);
        }
    }


    //UPDATE PROFILE DETAILS
    public function update_profile()
    {
        // Get the student ID from the session
        $student_id = $this->session->userdata('student_id');

        if (!$student_id) {
            // If no student_id is in the session, redirect to the login page or show an error
            redirect('student/login');
        }

        // Load the model
        $this->load->model('Student_model');

        // Get the current user data using the student_id
        $student_details = $this->Student_model->get_student_full_details($student_id);

        if (!$student_details) {
            // If no details are found, redirect or show an error
            $this->session->set_flashdata('error', 'Student not found');
            redirect('student/login');
        }

        // Validation rules (if needed)
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('year_level', 'Year Level', 'required');

        // Check if form validation passes
        if ($this->form_validation->run() === FALSE) {
            // Reload the profile page with validation errors and pre-filled data
            $data = [
                'student_id' => $student_id,
                'first_name' => $this->input->post('first_name') ?: $student_details->first_name,
                'last_name' => $this->input->post('last_name') ?: $student_details->last_name,
                'email' => $this->input->post('email') ?: $student_details->email,
                'year_level' => $this->input->post('year_level') ?: $student_details->year_level,
            ];

            // Pass the data to the view
            $this->load->view('admin/profile-settings', $data);
        } else {
            // Form validated, proceed with update
            $update_data = [
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'year_level' => $this->input->post('year_level'),
            ];

            // Call the model function to update the user details
            $update_success = $this->Student_model->update_user_profile($student_id, $update_data);

            if ($update_success) {
                // Redirect to a success page or back to the profile with a success message
                $this->session->set_flashdata('success', 'Profile updated successfully');
                redirect('admin/profile-settings/' . $student_id);
            } else {
                // Handle any errors (if update failed)
                $this->session->set_flashdata('error', 'Failed to update profile');
                redirect('admin/profile-settings/' . $student_id);
            }
        }
    }


    public function manage_officers()
    {
        $data['title'] = 'Manage Officers';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();

        // FETCHING ALL THE DEPARTMENT
        $data['organization'] = $this->admin->get_organization();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/manage-officer', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_officers_dept($dept_id)
    {
        $data['title'] = 'List of Officer Department';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();
        $data['dept_id'] = $dept_id;

        $data['officers'] = $this->admin->get_officer_dept();


        $this->load->view('layout/header', $data);
        $this->load->view('admin/manage-department', $data);
        $this->load->view('layout/footer', $data);
    }

    public function update_status_dept()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        // Ensure required parameters exist
        if (!isset($input['student_id']) || !isset($input['is_admin'])) {
            echo json_encode(["success" => false, "message" => "Invalid request"]);
            return;
        }

        $student_id = $input['student_id'];
        $new_status = ($input['is_admin'] === "Yes") ? "Yes" : "No";

        // Update only the specific record with both student_id & activity_id
        if ($this->admin->update_status_dept($student_id, $new_status)) {
            echo json_encode(["success" => true, "message" => "Update admin updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }

    public function list_officers_org($org_id)
    {
        $data['title'] = 'List of Officer Organization';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->admin->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['organization'] = $this->admin->get_organization();
        $data['org_id'] = $org_id;

        $data['officers'] = $this->admin->get_officer_org();


        $this->load->view('layout/header', $data);
        $this->load->view('admin/manage-organization', $data);
        $this->load->view('layout/footer', $data);
    }

    public function update_status_org()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        // Ensure required parameters exist
        if (!isset($input['student_id']) || !isset($input['is_admin'])) {
            echo json_encode(["success" => false, "message" => "Invalid request"]);
            return;
        }

        $student_id = $input['student_id'];
        $new_status = ($input['is_admin'] === "Yes") ? "Yes" : "No";

        // Update only the specific record with both student_id & activity_id
        if ($this->admin->update_status_org($student_id, $new_status)) {
            echo json_encode(["success" => true, "message" => "Update admin updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }

    public function updateAttendance()
    {
        date_default_timezone_set('Asia/Manila');

        // Validate input
        $this->form_validation->set_rules('student_id', 'Student Number', 'required');
        $this->form_validation->set_rules('activityId', 'Activity ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => validation_errors()]));
            return;
        }

        $student_id = $this->input->post('student_id');
        $activity_id = $this->input->post('activityId');
        $current_time = date('H:i:s');

        // Fetch the activity schedule and existing attendance
        $activity = $this->admin->get_activity_schedule($activity_id);
        $attendance = $this->admin->get_attendance_record($student_id, $activity_id);

        if (!$activity) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Activity not found']));
            return;
        }

        $update_data = [];
        $update_fines = [];

        // **Determine if the event is AM, PM, or Whole-Day**
        if (empty($activity->am_in) && empty($activity->am_out)) {
            // **PM-Only Event**
            if (empty($attendance->pm_in)) {
                if ($current_time <= $activity->pm_in) {
                    $update_data['pm_in'] = date('Y-m-d H:i:s');
                    $update_data['pm_in_status'] = 'Present';
                    $update_fines['pm_in'] = '0';
                } elseif ($current_time > $activity->pm_in && $current_time <= $activity->pm_in_cut) {
                    $update_data['pm_in'] = date('Y-m-d H:i:s');
                    $update_data['pm_in_status'] = 'Late';
                } else {
                    $update_data['pm_in'] = date('Y-m-d H:i:s');
                    $update_data['pm_in_status'] = 'Absent';
                    $update_fines['pm_in'] = $activity->fines;
                }
            } elseif (!empty($attendance->pm_in) && empty($attendance->pm_out)) {
                if ($current_time < $activity->pm_out) {
                    $update_data['pm_out'] = date('Y-m-d H:i:s');
                    $update_data['pm_out_status'] = 'Early Out';
                    $update_fines['pm_out'] = $activity->fines;
                } elseif ($current_time >= $activity->pm_out && $current_time <= $activity->pm_out_cut) {
                    $update_data['pm_out'] = date('Y-m-d H:i:s');
                    $update_data['pm_out_status'] = 'Present';
                    $update_fines['pm_out'] = '0';
                } else {
                    $update_data['pm_out'] = date('Y-m-d H:i:s');
                    $update_data['pm_out_status'] = 'Late Out';
                }
            }
        } elseif (empty($activity->pm_in) && empty($activity->pm_out)) {
            // **AM-Only Event**
            if (empty($attendance->am_in)) {
                if ($current_time <= $activity->am_in) {
                    $update_data['am_in'] = date('Y-m-d H:i:s');
                    $update_data['am_in_status'] = 'Present';
                    $update_fines['am_in'] = '0';
                } elseif ($current_time > $activity->am_in && $current_time <= $activity->am_in_cut) {
                    $update_data['am_in'] = date('Y-m-d H:i:s');
                    $update_data['am_in_status'] = 'Late';
                } else {
                    $update_data['am_in'] = date('Y-m-d H:i:s');
                    $update_data['am_in_status'] = 'Absent';
                    $update_fines['am_in'] = $activity->fines;
                }
            } elseif (!empty($attendance->am_in) && empty($attendance->am_out)) {
                if ($current_time < $activity->am_out) {
                    $update_data['am_out'] = date('Y-m-d H:i:s');
                    $update_data['am_out_status'] = 'Early Out';
                    $update_fines['am_out'] = $activity->fines;
                } elseif ($current_time >= $activity->am_out && $current_time <= $activity->am_out_cut) {
                    $update_data['am_out'] = date('Y-m-d H:i:s');
                    $update_data['am_out_status'] = 'Present';
                    $update_fines['am_out'] = '0';
                } else {
                    $update_data['am_out'] = date('Y-m-d H:i:s');
                    $update_data['am_out_status'] = 'Late Out';
                }
            }
        } else {
            // **Whole-Day Event**
            if (empty($attendance->am_in)) {
                if ($current_time <= $activity->am_in) {
                    $update_data['am_in'] = date('Y-m-d H:i:s');
                    $update_data['am_in_status'] = 'Present';
                    $update_fines['am_in'] = '0';
                } elseif ($current_time > $activity->am_in && $current_time <= $activity->am_in_cut) {
                    $update_data['am_in'] = date('Y-m-d H:i:s');
                    $update_data['am_in_status'] = 'Late';
                } else {
                    $update_data['am_in'] = date('Y-m-d H:i:s');
                    $update_data['am_in_status'] = 'Absent';
                    $update_fines['am_in'] = $activity->fines;
                }
            } elseif (!empty($attendance->am_in) && empty($attendance->am_out)) {
                if ($current_time < $activity->am_out) {
                    $update_data['am_out'] = date('Y-m-d H:i:s');
                    $update_data['am_out_status'] = 'Early Out';
                    $update_fines['am_out'] = $activity->fines;
                } elseif ($current_time >= $activity->am_out && $current_time <= $activity->am_out_cut) {
                    $update_data['am_out'] = date('Y-m-d H:i:s');
                    $update_data['am_out_status'] = 'Present';
                    $update_fines['am_out'] = '0';
                } else {
                    $update_data['am_out'] = date('Y-m-d H:i:s');
                    $update_data['am_out_status'] = 'Late Out';
                }
            }

            if (!empty($attendance->am_out) && empty($attendance->pm_in)) {
                if ($current_time <= $activity->pm_in) {
                    $update_data['pm_in'] = date('Y-m-d H:i:s');
                    $update_data['pm_in_status'] = 'Present';
                    $update_fines['pm_in'] = '0';
                } elseif ($current_time > $activity->pm_in && $current_time <= $activity->pm_in_cut) {
                    $update_data['pm_in'] = date('Y-m-d H:i:s');
                    $update_data['pm_in_status'] = 'Late';
                } else {
                    $update_data['pm_in'] = date('Y-m-d H:i:s');
                    $update_data['pm_in_status'] = 'Absent';
                    $update_fines['pm_in'] = $activity->fines;
                }
            }
        }

        // Update attendance and fines
        $this->admin->update_attendance($student_id, $activity_id, $update_data);
        $this->admin->update_fines($student_id, $activity_id, $update_fines);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully']));
    }
}

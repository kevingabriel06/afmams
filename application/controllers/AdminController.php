<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model', 'admin');

        if(!$this->session->userdata('student_id'))
        {
            redirect(site_url('login'));
        }
    }

	public function admin_dashboard()
	{

        $data['title'] = 'Dashboard';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header', $data);
		$this->load->view('admin/dashboard', $data);
		$this->load->view('layout/footer', $data);
	}

    // <========= THIS PART IS FOR THE CREATE ACTIVITY =====>


    // VIEWING OF CREATE ACTIVITY PAGE
    public function create_activity() {
        $data['title'] = 'Create Activity';
   
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // WHERE THE USER BELONGS
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/create-activity', $data);
        $this->load->view('layout/footer', $data);
    }

    // SAVING OF ACTIVITY TO DATABASE
    public function save_activity() {

        // Check if the form is submitted
        if ($this->input->post()) {
            // Set validation rules
            $this->form_validation->set_rules('title', 'Activity', 'is_unique[activity.activity_title]', 
                array(
                    'is_unique' => 'The {field} must be unique. This title is already taken.'
                )
            );

            // Check if validation passes
            if ($this->form_validation->run() == FALSE) {
                // If validation fails, return validation errors
                $response = [
                    'status' => 'error',
                    'errors' => validation_errors()
                ];
            } else {

                // If validation passes, proceed to save the data
                $data = array(
                    'activity_title' => $this->input->post('title'),
                    'start_date' => $this->input->post('date_start'),
                    'end_date' => $this->input->post('date_end'),
                    'registration_deadline' => $this->input->post('registration_deadline'),
                    'registration_fee' => $this->input->post('registration_fee'),
                    'dept_id' => $this->input->post('dept'),
                    'org_id' => $this->input->post('org'),
                    'am_in' => $this->input->post('am_in'),
                    'am_out' => $this->input->post('am_out'),
                    'pm_in' => $this->input->post('pm_in'),
                    'pm_out' => $this->input->post('pm_out'),
                    'description' => $this->input->post('description'),
                    'privacy' => $this->input->post('privacy'),
                    'fines' => $this->input->post('fines'),
                );

                if (!empty($_FILES['image']['name'])) {
                    $config = [
                        'upload_path' => './assets/coverEvent',
                        'allowed_types' => 'gif|jpg|jpeg|png',
                        'max_size' => 2048, // Increased max_size for better handling
                    ];
                    $this->load->library('upload', $config);
    
                    if ($this->upload->do_upload('image')) {
                        $uploadData = $this->upload->data();
                        $data['activity_image'] = $uploadData['file_name'];
                    } else {
                        $response = [
                            'status' => 'error',
                            'errors' => $this->upload->display_errors()
                        ];
                        echo json_encode($response);
                        return;
                    }
                }

                $result = $this->admin->save_activity($data);
            
                if ($result) {
                    // If data is saved successfully, return success message
                    $response = array(
                        'status' => 'success',
                        'message' => 'Activity Saved Successfully',
                        'redirect' => site_url('admin/list-of-activity')
                    );
                } else {
                    // If data is not saved, return error message
                    $response = array(
                        'status' => 'error',
                        'errors' => 'Failed to Save Activity'
                    );
                }
            }
        } 
        
        // Return JSON response
        echo json_encode($response);
    }

    // EDITING OF ACTIVITY TO DATABASE
    public function edit_activity($activity_id) {
        $data['title'] = 'Edit Activity';
   
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // WHERE THE USER BELONGS
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();

        $data['activity'] = $this->admin->get_activity($activity_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/edit-activity', $data);
        $this->load->view('layout/footer', $data);
    }

    // SAVING OF ACTIVITY TO DATABASE
    public function update_activity() {

        $this->form_validation->set_rules('title', 'Activity Title', 'required');
        $this->form_validation->set_rules('date_start', 'Start Date', 'required');
        $this->form_validation->set_rules('date_end', 'End Date', 'required');

        $activity_id = $this->input->post('activity_id');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status' => 'error',
                'errors' => validation_errors()
            ];
            echo json_encode($response);
            return;
        }

        $data = array(
            'activity_title' => $this->input->post('title', TRUE),
            'start_date' => $this->input->post('date_start', TRUE),
            'end_date' => $this->input->post('date_end', TRUE),
            'registration_deadline' => $this->input->post('registration_deadline', TRUE),
            'registration_fee' => $this->input->post('registration_fee', TRUE),
            'dept_id' => $this->input->post('dept', TRUE),
            'org_id' => $this->input->post('org', TRUE),
            'am_in' => $this->input->post('am_in', TRUE),
            'am_out' => $this->input->post('am_out', TRUE),
            'pm_in' => $this->input->post('pm_in', TRUE),
            'pm_out' => $this->input->post('pm_out', TRUE),
            'am_in_cut' => $this->input->post('am_in_cut', TRUE),
            'am_out_cut' => $this->input->post('am_out_cut', TRUE),
            'pm_in_cut' => $this->input->post('pm_in_cut', TRUE),
            'pm_out_cut' => $this->input->post('pm_out_cut', TRUE),
            'description' => $this->input->post('description', TRUE),
            'privacy' => $this->input->post('privacy', TRUE),
            'fines' => $this->input->post('fines', TRUE)
        );

        if (!empty($_FILES['image']['name'])) {
            $config = [
                'upload_path' => './assets/coverEvent',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size' => 2048,
                'file_name' => uniqid() . '_' . $_FILES['image']['name']
            ];
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                $uploadData = $this->upload->data();
                $data['activity_image'] = $uploadData['file_name'];
            } else {
                echo json_encode(['status' => 'error', 'errors' => $this->upload->display_errors()]);
                return;
            }
        }

        $result = $this->admin->update_activity($activity_id, $data);

        echo json_encode(
            $result ? 
            ['status' => 'success', 'message' => 'Activity Updated Successfully', 'redirect' => site_url("admin/activity-details/$activity_id")] :
            ['status' => 'error', 'errors' => 'Failed to Update Activity']
        );
    }

    // FETCHING ACTIVITY BASED ON WHERE THE USER IS ADMIN
    public function list_activity() {
        $data['title'] = 'List of Activities';
        
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // ORGANIZATION AND DEPT ID OF THE ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();
        
        // GETTING OF THE ACTIVITY FROM THE DATABASE
        $data['activities'] = $this->admin->get_activities();

        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities', $data);
        $this->load->view('layout/footer', $data);
    }

    // FETCHING DETAILS OF THE ACTIVITY
    public function activity_details($activity_id) {
        $data['title'] = 'Activity Details';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
        $data['organization'] = $this->admin->admin_org_id();
        $data['department'] = $this->admin->admin_dept_id();

        $data['activity'] = $this->admin->get_activity($activity_id); // SPECIFIC ACTIVITY
        $data['activities'] = $this->admin->get_activities(); // FOR UPCOMING ACTIVITY PART

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/activity-detail', $data);
        $this->load->view('layout/footer', $data);
    }

    // SHARING ACTIVITY FROM THE ACTIVITY DETAILS
    public function share_activity(){
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


//========CREATE EVALUATION START
public function create_evaluationform() {
    $data['title'] = 'Create Evaluation Form';

    $this->load->model('Admin_model');
    
    
    $student_id = $this->session->userdata('student_id');



    // Fetch user roles
    $users= $this->Admin_model->get_roles($student_id);
    $data['role'] = $users['role'];

    // Fetch activities based on user role
    $data['activities'] = $this->Admin_model->get_filtered_activities($users['student_id']); 

    $this->load->view('layout/header', $data);
    $this->load->view('admin/activity/create-evaluation-form', $data);
    $this->load->view('layout/footer', $data);
}


public function list_activity_evaluation()
{
    $data['title'] = 'Dashboard';

    $student_id = $this->session->userdata('student_id');

    // FETCHING DATA BASED ON THE ROLES - NECESSARY
    $users = $this->admin->get_roles($student_id);
    $data['role'] = $users['role'];

    // Load the Activity model to fetch activities
    $this->load->model('Admin_model');

    // Fetch activities based on the logged-in user's role
    if ($data['role'] == 'Admin') {
        // For Admin, get all activities where org_id and dept_id are NULL
        $activities = $this->admin->get_admin_activities();

        // Map the status class to the activities
        foreach ($activities as &$activity) {
            $activity->status_class = $this->get_status_class($activity->status);
        }

        $data['activities'] = $activities;
    } else {
        $data['activities'] = []; // Default to empty if not an Admin
    }

    // Pass data to the view
    $this->load->view('layout/header', $data);
    $this->load->view('admin/activity/list-activity-evaluation', $data);
    $this->load->view('layout/footer', $data);
}

// create evaluation
public function create_eval()
{
    // Get the form data from POST request
    $formData = array(
        'title' => $this->input->post('formtitle'),
        'description' => $this->input->post('formdescription'),
        'activity_id' => $this->input->post('activity'), // Include activity_id from the form
        // Check if start_date is provided, otherwise use the current date and time
        'start_date' => $this->input->post('startdate') ? date('Y-m-d H:i:s', strtotime($this->input->post('startdate'))) : date('Y-m-d H:i:s'),
        // Check if end_date is provided, otherwise set it to 1 week from the start date
        'end_date' => $this->input->post('enddate') ? date('Y-m-d H:i:s', strtotime($this->input->post('enddate'))) : date('Y-m-d H:i:s', strtotime('+1 week')),
    );

    // Get form fields data
    $fields = $this->input->post('fields'); // Array of fields from the frontend

    // Validate that fields is an array
    if (is_array($fields) && count($fields) > 0) {
        $fieldsData = array();

        foreach ($fields as $index => $field) {
            // Ensure required keys exist in the field data
            $label = isset($field['label']) ? $field['label'] : '';
            $type = isset($field['type']) ? $field['type'] : ''; 
            $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
            $required = isset($field['required']) && $field['required'] ? 1 : 0; // 1 if required, 0 otherwise

            // Validate that the label is not empty
            if (!empty($label)) {
                $fieldsData[] = array(
                    'label' => $label,
                    'type' => $type, // Use the type provided by the frontend
                    'placeholder' => $placeholder,
                    'required' => $required,
                    'order' => $index + 1 // Order of the field
                );
            }
        }

        // Ensure at least one valid field is provided
        if (!empty($fieldsData)) {
            // Save the form and its fields
            $this->db->trans_start(); // Start a transaction

            // Insert the form data into the forms table
            $this->db->insert('forms', $formData);
            $formId = $this->db->insert_id(); // Get the last inserted form ID

            // Insert the fields into the form_fields table
            foreach ($fieldsData as &$fieldData) {
                $fieldData['form_id'] = $formId; // Associate the field with the form
            }

            $this->db->insert_batch('formfields', $fieldsData); // Insert multiple fields in one query

            $this->db->trans_complete(); // Complete the transaction

            // Check if the transaction was successful
            if ($this->db->trans_status() === FALSE) {
                // Rollback the transaction if something went wrong
                echo json_encode(['status' => 'error', 'message' => 'Failed to save the form. Please try again.']);
            } else {
                // Commit the transaction and return success
                echo json_encode(['status' => 'success', 'message' => 'Form saved successfully.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No valid fields provided.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
}

//========CREATE EVALUATION END

    //  <=== REVIEW EXCUSE LETTER SECTION ===>

    // FETCHING ACTIVITY
    public function list_activity_excuse() {
        $data['title'] = 'List of Activity for Excuse Letter';
        
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();

        $data['activities'] = $this->admin->get_activities();
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities-excuseletter', $data);
        $this->load->view('layout/footer', $data);

    }

    // LIST OF APPLICATION PER EVENT
    public function list_excuse_letter($activity_id) {
        $data['title'] = 'List of Excuse Letter';
    

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        $data['activities'] = $this->admin->fetch_application($activity_id);
        $data['letters'] = $this->admin->fetch_letters();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // EXCUSE LETTER PER STUDENT
    public function review_excuse_letter($excuse_id) {
        $data['title'] = 'Review Excuse Letter';
        
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];
        
        $data['excuse'] = $this->admin->review_letter($excuse_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // UPDATING REMARKS AND STATUS OF THE APPLICATION
    public function updateApprovalStatus() {
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
          echo json_encode(['success' => true]);
        } else {
          echo json_encode(['success' => false]);
        }
    }

    // <===== COMMUNITY SECTION =====>

    // VIEWING OF COMMUNITY SECTION
    public function community() {
        $data['title'] = 'Community';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];
    
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
    public function like_post($post_id) {
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
    public function add_comment() {
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
                            'name' => $new_comment->name ?? 'Unknown',
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

    public function delete_post() {
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
    public function share() {
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
    public function add_post() {
        $student_id = $this->session->userdata('student_id');
        $dept_id = $this->admin->admin_dept_id();
        $org_id = $this->admin->admin_org_id();
    
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
                'dept_id' => $dept_id,
                'org_id' => $org_id,
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

    public function list_activities_attendance(){
        $data['title'] = 'Manage Officers';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();

        $data['activities'] = $this->admin->get_activities();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/list-activities-attendance', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_department($activity_id){
        $data['title'] = 'List of Department';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();

        $data['activity_id'] = $activity_id;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/department', $data);
        $this->load->view('layout/footer', $data);
    }

    // SHOWING ATTENDANCE LIST
    public function list_attendees($activity_id, $dept_id){
        $data['title'] = 'List of Attendees';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

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

    public function scanning_qr($activity_id){
        $data['title'] = 'Scanning QR';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        $data['activity'] = $this->admin->get_activity($activity_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/scanqr', $data);
        $this->load->view('layout/footer', $data);

    }

    // SCANNING OF QR
    public function scan() {

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
                            $update_data['pm_in'] =date('Y-m-d H:i:s');
                            $update_data['pm_in_status'] = 'Late';
                        } elseif ($current_time > $activity->pm_in_cut){
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
                
                if ($attendance->pm_in_status == 'Present' && $attendance->pm_out_status == 'Present'){
                    $update_data['attendance_status'] = 'Present';
                }
                elseif ($attendance->pm_in_status == 'Absent' && $attendance->pm_out_status == 'Absent'){
                    $update_data['attendance_status'] = 'Absent';
                }
                else{
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
                        } elseif ($current_time > $activity->am_in_cut){
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
                
                if ($attendance->am_in_status == 'Present' && $attendance->am_out_status == 'Present'){
                    $update_data['attendance_status'] = 'Present';
                }
                elseif ($attendance->am_in_status == 'Absent' && $attendance->am_out_status == 'Absent'){
                    $update_data['attendance_status'] = 'Absent';
                }
                else{
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
                    } elseif ($current_time > $activity->am_in_cut){
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
                        $update_data['pm_in'] =date('Y-m-d H:i:s');
                        $update_data['pm_in_status'] = 'Late';
                    } elseif ($current_time > $activity->pm_in_cut){
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

                if ($attendance->am_in_status == 'Present' && $attendance->am_out_status == 'Present' && $attendance->pm_in_status == 'Present' && $attendance->pm_out_status == 'Present'){
                    $update_data['attendance_status'] = 'Present';
                }
                elseif ($attendance->am_in_status == 'Absent' && $attendance->am_out_status == 'Absent' && $attendance->pm_in_status == 'Absent' && $attendance->pm_out_status == 'Absent'){
                    $update_data['attendance_status'] = 'Absent';
                }
                else{
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
    private function compute_and_update_fine($student_id, $activity_id) {
        $this->db->query("
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
    public function list_activities_fines(){
        $data['title'] = 'All Activities';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['department'] = $this->admin->admin_dept();

        $data['activities'] = $this->admin->get_activities();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/fines/list-activities-fines', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_department_fines($activity_id){
        $data['title'] = 'List of Department';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();

        $data['activity_id'] = $activity_id;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/fines/department', $data);
        $this->load->view('layout/footer', $data);
    }


    //======> FINES
    public function list_fines($activity_id, $dept_id){
        $data['title'] = 'List of Fines';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        $data['dept_id'] = $dept_id;
        $data['activity_id'] = $activity_id;
        
        // CHECKING WHERE THE USER ADMIN
        $data['organization'] = $this->admin->admin_org();
        $data['departments'] = $this->admin->admin_dept();

        $data['department'] = $this->admin->get_department();

        $data['activities'] = $this->admin->fetch_application($activity_id);

        $data['fines'] = $this->admin->fetch_students();

        $this->load->view('layout/header',$data);
        $this->load->view('admin/fines/listoffines', $data);
        $this->load->view('layout/footer', $data);

    }

    public function update_status() {
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
    public function getFaces(){
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
    public function profile_settings(){
        $data['title'] = 'Profile Settings';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header', $data);
        $this->load->view('admin/profile-settings', $data);
        $this->load->view('layout/footer', $data);

    }

    
    public function manage_officers(){
        $data['title'] = 'Manage Officers';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();

        // FETCHING ALL THE DEPARTMENT
        $data['organization'] = $this->admin->get_organization();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/manage-officer', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_officers_dept($dept_id){
        $data['title'] = 'List of Fines';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->admin->get_department();
        $data['dept_id'] = $dept_id;

        $data['officers'] = $this->admin->get_officer_dept();
        

        $this->load->view('layout/header',$data);
        $this->load->view('admin/manage-department', $data);
        $this->load->view('layout/footer', $data);

    }

    public function update_status_dept() {
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

    public function list_officers_org($org_id){
        $data['title'] = 'List of Fines';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        // FETCHING ALL THE DEPARTMENT
        $data['organization'] = $this->admin->get_organization();
        $data['org_id'] = $org_id;

        $data['officers'] = $this->admin->get_officer_org();
        

        $this->load->view('layout/header',$data);
        $this->load->view('admin/manage-organization', $data);
        $this->load->view('layout/footer', $data);

    }

    public function update_status_org() {
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

    public function updateAttendance() {
        date_default_timezone_set('Asia/Manila');
    
        // Validate input
        $this->form_validation->set_rules('student', 'Registration Number', 'required');
        $this->form_validation->set_rules('activityId', 'Activity ID', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => validation_errors()]));
            return;
        }
    
        $student_id = $this->input->post('registrationNumber');
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

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

    //ACTIVITY MANAGEMENT ====>
    public function create_activity() {
        $data['title'] = 'Create Activity';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/create-activity', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_activity() {
        $data['title'] = 'List of Activities';
        
        // getting data of activity
        $data['activities'] = $this->admin->get_activities();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities', $data);
        $this->load->view('layout/footer', $data);
    }

    public function activity_details($activity_id) {
        $data['title'] = 'Activity Details';

        $data['activity'] = $this->admin->get_activity($activity_id);
        $data['activities'] = $this->admin->get_activities();
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/activity-detail', $data);
        $this->load->view('layout/footer', $data);
    }

    public function create_evaluationform() {
        $data['title'] = 'Create Evaluation Form';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/create-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    // ========> REVIEW EXCUSE LETTER SECTION
    public function list_activity_excuse() {
        $data['title'] = 'List of Activity for Excuse Letter';
        
        $data['activities'] = $this->admin->get_activities();

    
    
        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities-excuseletter', $data);
        $this->load->view('layout/footer', $data);

    }

  

    // list of excuse letter per event
    public function list_excuse_letter() {
        $data['title'] = 'List of Excuse Letter';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];

        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // excuse letter per student
    public function review_excuse_letter() {
        $data['title'] = 'Review Excuse Letter';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // ======> COMMUNITY SECTION
    public function community() {
        $data['title'] = 'Community';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $student_id = $this->session->userdata('student_id');
    
        $data['authors'] = $this->admin->get_user();
        $data['users'] = $this->admin->get_users();
    
        $data['posts'] = $this->admin->get_post();

        $limit = 3;
        $data['activities'] = $this->admin->get_activity_organized($limit);
    
        $data['activities_admin'] = $this->admin->get_admin_officer_activities();

        $data['posted_activity'] = $this->admin->activity_posted();

        // Fetch like counts for each post and the comments count
        foreach ($data['posts'] as &$post) {
            $post->like_count = $this->admin->get_like_count($post->post_id);
            $post->user_has_liked_post = $this->admin->has_liked($student_id, $post->post_id);
            $post->comments_count = $this->admin->get_comment_count($post->post_id);
        }
    
        // Fetch only the first 2 comments for each post
        foreach ($data['posts'] as &$post) {
            $post->comments = $this->admin->get_comments_by_post($post->post_id, 2); // Limit comments to 2
        }
    
        // Get the organization name from the model
        $organization = $this->admin->getOrgIdByStudent($student_id);
        $data['org_name'] = $organization->org_name;
    
        // Load the views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/community', $data);
        $this->load->view('layout/footer', $data);
    }

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

    // =====> LIKES
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


    // =====> COMMENTS
    public function add_comment()
    {
        // Check if the form is submitted
        if ($this->input->post()) {
            // Set validation rules
            $this->form_validation->set_rules('comment', 'Comment', 'required');
        
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
                    'post_id' => $this->input->post('post_id'),
                    'content' => $this->input->post('comment'),
                    'student_id' => $this->session->userdata('student_id')
                );

                $result = $this->admin->add_comment($data);
            
                if ($result) {
                    // Retrieve the updated comment count for the specific post
                    $post_id = $this->input->post('post_id');
                    $comments_count = $this->admin->get_comment_count($post_id); // Add this method to fetch the comment count

                    // If data is saved successfully, return success message along with the updated comment count
                    $response = array(
                        'status' => 'success',
                        'message' => 'Comment Saved Successfully',
                        'comments_count' => $comments_count // Include the updated count
                    );
                } else {
                    // If data is not saved, return error message
                    $response = array(
                        'status' => 'error',
                        'errors' => 'Failed to Save Comment'
                    );
                }
            }
        } 
        
        // Return JSON response
        echo json_encode($response);
    }

    // ====> POST
    public function add_post() {

        $student_id = $this->session->userdata('student_id');
        $dept_id = $this->admin->check_admin_dept($student_id);
        $org_id = $this->admin->check_admin_org($student_id);
       
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
            } else {
                // If validation passes, proceed to save the data
                $data = array(
                    'student_id' => $student_id,
                    'content' => $this->input->post('content'),
                    'privacy' => $this->input->post('privacyStatus'),
                    'dept_id' => $dept_id,
                    'org_id' => $org_id,
                );

                if (!empty($_FILES['image']['name'])) {
                    $config = [
                        'upload_path' => './assets/post',
                        'allowed_types' => 'gif|jpg|jpeg|png',
                        'max_size' => 2048, // Increased max_size for better handling
                    ];
                    $this->load->library('upload', $config);
    
                    if ($this->upload->do_upload('image')) {
                        $uploadData = $this->upload->data();
                        $data['media'] = $uploadData['file_name'];
                    } else {
                        $response = [
                            'status' => 'error',
                            'errors' => $this->upload->display_errors()
                        ];
                        echo json_encode($response);
                        return;
                    }
                }

                

                $result = $this->admin->insert_data($data);
            
                if ($result) {
                    // If data is saved successfully, return success message
                    $response = array(
                        'status' => 'success',
                        'message' => 'You shared post.',
                        'redirect' => site_url('create-activity')
                    );
                } else {
                    // If data is not saved, return error message
                    $response = array(
                        'status' => 'error',
                        'errors' => 'Failed to Post'
                    );
                }
        
              
            }
        }
    } 

    // create evaluation
    public function create()
    {
        // Get the form data from POST request
        $formData = array(
            'title' => $this->input->post('formtitle'),
            'description' => $this->input->post('formdescription')
        );

        $fields = $this->input->post('fields'); // Array of fields from the frontend

        // Validate that fields is an array
        if (is_array($fields) && count($fields) > 0) {
            $fieldsData = array();

            foreach ($fields as $index => $field) {
                // Ensure required keys exist in the field data
                $label = isset($field['label']) ? $field['label'] : '';
                $type = isset($field['type']) ? $field['type'] : ' '; 
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
                $result = $this->admin->create_form_with_fields($formData, $fieldsData);

                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Form saved successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to save the form. Please try again.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No valid fields provided.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
        }
    }

    //=====> ATTENDANCE
    public function list_attendees(){
        $data['title'] = 'List of Attendees';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/listofattendees', $data);
        $this->load->view('layout/footer', $data);
    }

    //======> FINES
    public function list_fines(){
        $data['title'] = 'List of Fines';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header',$data);
        $this->load->view('admin/fines/listoffines', $data);
        $this->load->view('layout/footer', $data);

    }

    public function scanning_qr(){
        $data['title'] = 'Scanning QR';

        $student_id = $this->session->userdata('student_id');
        
        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->admin->get_roles($student_id);
        $data['role'] = $users['role'];


        $this->load->view('layout/header', $data);
        $this->load->view('admin/attendance/scanqr', $data);
        $this->load->view('layout/footer', $data);

    }
    
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

        $this->load->view('layout/header', $data);
        $this->load->view('admin/manage-officer', $data);
        $this->load->view('layout/footer', $data);
    }
}

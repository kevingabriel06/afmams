<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class StudentController
 * 
 * @property Student_model $Student_model
 * @property Admin_model $Admin_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Upload $upload
 * @property CI_Form_validation $form_validation
 * @property CI_DB_query_builder $db   <-- Add this line for database
 */



class StudentController extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->model('Student_model', 'student');
        $this->load->helper('url');

        // CHECK IF THERE'S A STUDENT LOGIN
        if (!$this->session->userdata('student_id')) {
            redirect(site_url('login'));
        }
    }

    public function student_dashboard()
    {
        $data['title'] = 'Community';
        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);
        $data['authors'] = $this->student->get_user();

        // Get offset and limit from AJAX request
        $limit = $this->input->post('limit') ?: 5;
        $offset = $this->input->post('offset') ?: 0;

        // GET LIMITED POSTS
        $data['posts'] = $this->student->get_all_posts($limit, $offset);
        foreach ($data['posts'] as &$post) {
            $post->like_count = $this->student->get_like_count($post->post_id);
            $post->user_has_liked_post = $this->student->user_has_liked($post->post_id, $student_id);
            $post->comments_count = $this->student->get_comment_count($post->post_id);
            $post->comments = $this->student->get_comments_by_post($post->post_id);
            $post->type = 'post';
        }

        // GET LIMITED ACTIVITIES
        $data['activities'] = $this->student->get_shared_activities($limit, $offset);
        foreach ($data['activities'] as &$activity) {
            $activity->type = 'activity';
            $activity->registration_status = $this->student->get_registration_status_for_activity($activity->activity_id);
            $activity->attendees_status = $this->student->get_attendees_free_event($activity->activity_id);
        }

        // MERGE POSTS & ACTIVITIES BEFORE PAGINATION
        $merged_feed = array_merge($data['posts'], $data['activities']);

        // Remove duplicates by checking unique post/activity ID (you can use any unique field like post ID or activity ID)
        $merged_feed = array_map("unserialize", array_unique(array_map("serialize", $merged_feed)));

        // SORT MERGED DATA BY DATE (created_at for posts, updated_at for activities)
        usort($merged_feed, function ($a, $b) {
            // Determine the date field based on whether it's a post or activity
            $a_date = isset($a->updated_at) ? $a->updated_at : (isset($a->created_at) ? $a->created_at : '');
            $b_date = isset($b->updated_at) ? $b->updated_at : (isset($b->created_at) ? $b->created_at : '');

            // Return comparison result based on dates
            return strtotime($b_date) - strtotime($a_date);
        });

        // Apply pagination on the merged feed (with proper offset and limit)
        $data['feed'] = array_slice($merged_feed, 0, $limit);


        // Activity to post and show to the upcoming section
        $data['activities_upcoming'] = $this->student->get_activities_upcoming();

        // AJAX Request: Return only the next batch
        if ($this->input->is_ajax_request()) {
            $this->load->view('student/home_feed', $data);
        } else {
            // FULL PAGE LOAD
            $this->load->view('layout/header', $data);
            $this->load->view('student/home', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    // Method to fetch likes by post ID and return the list of users who liked
    public function view_likes($post_id)
    {
        // Fetch the likes data for the given post ID
        $likes = $this->student->get_likes_by_post($post_id);

        // Generate HTML to return as a response
        $output = '';
        if ($likes) {
            // Generate HTML for the list of likes
            foreach ($likes as $like) {
                echo '<li class="d-flex align-items-center mt-2">';
                echo '    <div class="avatar avatar-xl position-relative">';
                echo '        <img class="rounded-circle" src="' . base_url('assets/profile/') . ($like->profile_pic ? $like->profile_pic : 'default.jpg') . '" alt="Profile Picture" />';
                echo '        <div class="position-absolute top-0 start-50 translate-middle-x" style="width: 1rem; height: 1rem; margin-top: 60%; margin-left: 40%;">';
                echo '            <img src="' . base_url('assets/img/icons/spot-illustrations/like-active.png') . '" class="w-100 h-100" alt="Heart Icon" />';
                echo '        </div>';
                echo '    </div>';
                echo '    <div class="ms-3 flex-grow-1">';
                echo '        <h6 class="fw-semi-bold mb-0">' . htmlspecialchars($like->first_name . " " . $like->last_name) . '</h6>';
                echo '    </div>';
                echo '</li>';
            }
        } else {
            $output = '<li>No likes yet.</li>';
        }

        echo $output; // Output the HTML response
    }

    // LIKING OF POST
    public function like_post($post_id)
    {
        $student_id = $this->session->userdata('student_id');

        // Check if the user already liked the post
        if ($this->student->user_has_liked($post_id, $student_id)) {
            // User already liked, so we will "unlike" the post
            $this->student->unlike_post($post_id, $student_id);
            $like_img = base_url() . 'assets/img/icons/spot-illustrations/like-inactive.png';
            $like_text = 'Like';
        } else {
            // User has not liked the post yet, so we will "like" the post
            $this->student->like_post($post_id, $student_id);
            $like_img = base_url() . 'assets/img/icons/spot-illustrations/like-active.png';
            $like_text = 'Liked';
        }

        // Get the updated like count
        $new_like_count = $this->student->get_like_count($post_id);

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

                $result = $this->student->add_comment($data);
                if ($result) {
                    $post_id = $this->input->post('post_id');

                    // Fetch updated comment count
                    $comments_count = $this->student->get_comment_count($post_id);

                    // Fetch the newly added comment only
                    $new_comment = $this->student->get_latest_comment($post_id); // Ensure this function fetches only the latest

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

    // REGISTRATION
    public function register()
    {

        // Set validation rules
        $this->form_validation->set_rules('student_id', 'Student ID', 'required');
        $this->form_validation->set_rules('activity_id', 'Activity ID', 'required');
        $this->form_validation->set_rules('payment_type', 'Payment Type', 'required|in_list[Cash,Online Payment]');
        $this->form_validation->set_rules('amount', 'Amount Paid', 'required|numeric');

        // Additional rule for Online Payment: reference number required
        if ($this->input->post('payment_type') === 'Online Payment') {
            $this->form_validation->set_rules('reference_number', 'Reference Number', 'required');
        }

        // Run validation
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors()
            ]);
            return;
        }

        $student_id       = $this->input->post('student_id');
        $activity_id      = $this->input->post('activity_id');
        $payment_type     = $this->input->post('payment_type');
        $reference_number = $this->input->post('reference_number');
        $amount_paid      = $this->input->post('amount');
        $gcash_receipt    = null;

        // Handle file upload (optional for Cash, required for Online Payment)
        if (!empty($_FILES['receipt']['name'])) {
            $config['upload_path']   = './assets/registration_receipt/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'proof_' . time() . '_' . $student_id;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('receipt')) {
                echo json_encode([
                    'status' => 'error',
                    'message' => strip_tags($this->upload->display_errors())
                ]);
                return;
            }

            $upload_data   = $this->upload->data();
            $gcash_receipt = $upload_data['file_name'];
        }

        // If Online Payment and no receipt uploaded
        if ($payment_type === 'Online Payment' && $gcash_receipt === null) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gcash receipt image is required for Online Payment.'
            ]);
            return;
        }

        // Prepare data
        $data = [
            'student_id'       => $student_id,
            'activity_id'      => $activity_id,
            'payment_type'     => $payment_type,
            'reference_number' => ($payment_type === 'Cash') ? null : $reference_number,
            'amount_paid'      => $amount_paid,
            'receipt'          => $gcash_receipt,
            'registration_status' => 'Pending',
            'registered_at'    => date('Y-m-d H:i:s'),
        ];

        if ($this->student->insert_registration($data)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Registration submitted successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error. Please try again.'
            ]);
        }
    }

    // FOR FREE EVENT 
    public function attend_free_event()
    {
        $activity_id = $this->input->post('activity_id');
        $student_id = $this->input->post('student_id');

        // Check if the student is already marked as attending
        $exists = $this->db->get_where('attendees', [
            'activity_id' => $activity_id,
            'student_id' => $student_id
        ])->row();

        if ($exists) {
            // If the status is already 'Attending', return a message
            if ($exists->attendees_status === 'Attending') {
                echo json_encode(['status' => 'info', 'message' => 'You are already marked as attending.']);
            } else {
                // Update the status to 'Attending' if it's not already marked as such
                $this->db->where('activity_id', $activity_id);
                $this->db->where('student_id', $student_id);
                $this->db->update('attendees', ['attendees_status' => 'Attending']);

                echo json_encode(['status' => 'success', 'message' => 'Your attendance has been updated to attending.']);
            }
        } else {
            // If the student is not found in the attendees table, insert a new record
            $this->db->insert('attendees', [
                'activity_id' => $activity_id,
                'student_id' => $student_id,
                'attendees_status' => 'Attending'
            ]);

            echo json_encode(['status' => 'success', 'message' => 'You are now marked as attending.']);
        }
    }

    public function cancelAttendance()
    {
        // Get POST data
        $activityId = $this->input->post('activity_id');
        $studentId = $this->input->post('student_id');

        // Check if the attendance record exists and the status is 'Attending'
        $this->db->where('activity_id', $activityId);
        $this->db->where('student_id', $studentId);
        $attendance = $this->db->get('attendees')->row();  // Fetch the record

        if ($attendance && $attendance->attendees_status === 'Attending') {
            // Update status to 'Cancelled'
            $this->db->where('activity_id', $activityId);
            $this->db->where('student_id', $studentId);
            $updateStatus = $this->db->update('attendees', [
                'attendees_status' => 'Cancelled'
            ]);

            if ($updateStatus) {
                // Return success response
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Your attendance has been cancelled successfully.'
                ]);
            } else {
                // Return failure response
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to cancel your attendance. Please try again.'
                ]);
            }
        } else {
            // If attendance doesn't exist or is not marked as 'Attending'
            echo json_encode([
                'status' => 'error',
                'message' => 'You are not attending this event or your attendance has already been cancelled.'
            ]);
        }
    }


    public function attendance_history()
    {
        $data['title'] = 'Attendance History';

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        // Get the attendance data for the student

        // $data['attendances'] = $this->student->get_attendance_history($student_id);


        $this->load->view('layout/header', $data);
        $this->load->view('student/attendance_history', $data);
        $this->load->view('layout/footer', $data);
    }



    public function summary_fines()
    {
        $data['title'] = 'Summary of Fines';

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        $this->load->view('layout/header', $data);
        $this->load->view('student/summary_fines', $data);
        $this->load->view('layout/footer', $data);
    }

    //LIST OF ACTIVITY	
    public function list_activity()
    {
        $data['title'] = 'List of Activity';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        // Fetch activities based on department or organization membership
        $data['activities'] = $this->student->get_activities_for_users();

        $this->load->view('layout/header', $data);
        $this->load->view('student/list-activity', $data);
        $this->load->view('layout/footer', $data);
    }


    //ACTIVITY DETAILS

    public function activity_details($activity_id)
    {
        $data['title'] = 'Activity Details';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        // UPCOMING ACTIVITIES
        $data['upcoming_activities'] = $this->student->get_activities_upcoming();

        $data['activity'] = $this->student->get_activity($activity_id); // SPECIFIC ACTIVITY
        $data['schedules'] = $this->student->get_schedule($activity_id); // GETTING OF SCHEDULE

        $data['attendees'] = $this->student->count_attendees($activity_id); // COUNT THE ATTENDEES
        $data['registered'] = $this->student->count_registered($activity_id); // COUNT THE REGISTERED

        // Load the views
        $this->load->view('layout/header', $data);
        $this->load->view('student/activity-details', $data);
        $this->load->view('layout/footer', $data);
    }


    // ====================EVALUATION FORM FUNCTIONS START
    public function evaluation_form()
    {
        $data['title'] = 'Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        $data['forms'] = $this->student->forms();

        // // Fetch open (unanswered) forms
        // $data['evaluation_forms'] = $this->student->get_open_forms($student_id);

        // // Fetch answered forms
        // $data['answered_forms'] = $this->student->get_answered_forms($student_id);

        // Load the view files
        $this->load->view('layout/header', $data);
        $this->load->view('student/evaluation_forms_list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function evaluation_forms()
    {
        $data['title'] = 'Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        // Fetch open (unanswered) forms
        $data['evaluation_forms'] = $this->student->get_open_forms($student_id);

        // Load the view files
        $this->load->view('layout/header', $data);
        $this->load->view('student/evaluation_forms_list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function evaluation_form_questions($form_id)
    {
        $data['title'] = 'Evaluation Form';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);


        // Fetch Form Details
        $data['form'] = $this->Student_model->get_form_details($form_id);

        // Fetch Form Questions
        $data['questions'] = $this->Student_model->get_form_fields($form_id);

        // Load Views
        $this->load->view('layout/header', $data);
        $this->load->view('student/evaluation_forms_questions', $data);
        $this->load->view('layout/footer', $data);
    }


    //SUBMIT FORM ANSWERS

    public function submit($form_id)
    {
        // Get the student ID from the session
        $student_id = $this->session->userdata('student_id');



        // Load the Student_model to interact with the database
        $this->load->model('Student_model');

        // Fetch User Role
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Prepare the response data for evaluation submission
        $response_data = [
            'form_id' => $form_id,
            'student_id' => $student_id,
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        // Save evaluation response and get the evaluation response ID
        $evaluation_response_id = $this->Student_model->save_evaluation_response($response_data);

        // Save the answers for each question
        $responses = $this->input->post('responses');  // Ensure responses are available in POST
        if (!empty($responses)) {
            $this->Student_model->save_response_answers($evaluation_response_id, $responses);
        }

        // If it's an AJAX request, return a JSON response
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Evaluation submitted successfully!',
                'student_id' => $student_id, // Send student_id for redirection
                'redirect_url' => base_url('student/evaluation-form/' . $student_id) // Send redirect URL
            ]);
        }
    }

    // SHOW EVALUATION FORM ANSWERS
    public function view_evaluation_answers($form_id)
    {
        $data['title'] = 'Evaluation Responses';
        // Load the Student_model to interact with the database
        $this->load->model('Student_model');

        // Get the student ID from the session
        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';


        // Fetch User Role
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Get form answers from the model
        $data['form_answers'] = $this->Student_model->get_form_answers($form_id, $student_id);

        // Fetch form details
        $data['form'] = $this->Student_model->get_form_details($form_id);


        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('student/evaluation_form_answers', $data);
        $this->load->view('layout/footer', $data);
    }




    public function excuse_application_list()
    {
        $data['title'] = 'Excuse Application List';

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        $data['activities'] = $this->student->get_activities_for_users();

        // APPLICATIONS
        $data['applications'] = $this->student->applications();

        // Load views with merged data
        $this->load->view('layout/header', $data);
        $this->load->view('student/excuse_application_list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function excuse_application()
    {
        $data['title'] = 'Excuse Application';

        $student_id = $this->session->userdata('student_id');

        // FETCH USER DATA
        $data['users'] = $this->student->get_student($student_id);

        $data['activities'] = $this->student->get_activities_for_users_excuse_application();

        // Load views with merged data
        $this->load->view('layout/header', $data);
        $this->load->view('student/excuse_application', $data);
        $this->load->view('layout/footer', $data);
    }

    //EXCUSE APPLICATION SUBMIT

    public function submit_application()
    {
        // Set validation rules
        $this->form_validation->set_rules('student_id', 'Student ID', 'required');
        $this->form_validation->set_rules('activity_id', 'Activity', 'required');
        $this->form_validation->set_rules('emailSubject', 'Subject', 'required');
        $this->form_validation->set_rules('emailBody', 'Message', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors(),
                'form_data' => [
                    'activity_id' => $this->input->post('activity_id'),
                    'emailSubject' => $this->input->post('emailSubject'),
                    'emailBody' => $this->input->post('emailBody')
                ]
            ]);
            return;
        }

        // File upload handling
        $document = '';  // Initialize as an empty string, as we're uploading a single file
        if (!empty($_FILES['fileUpload']['name'])) {  // Check if a file has been uploaded
            $config['upload_path'] = './assets/excuseFiles/';
            $config['allowed_types'] = 'jpg|jpeg|png|pdf'; // Allow jpg, jpeg, png, and pdf files
            $config['max_size'] = 10240; // 10MB limit

            // Check if the upload directory exists; if not, create it
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config); // Load the upload library

            // Set the $_FILES superglobal for the single file upload
            $_FILES['fileUpload']['name'] = $_FILES['fileUpload']['name'];
            $_FILES['fileUpload']['type'] = $_FILES['fileUpload']['type'];
            $_FILES['fileUpload']['tmp_name'] = $_FILES['fileUpload']['tmp_name'];
            $_FILES['fileUpload']['error'] = $_FILES['fileUpload']['error'];
            $_FILES['fileUpload']['size'] = $_FILES['fileUpload']['size'];

            // Perform the upload
            if ($this->upload->do_upload('fileUpload')) {
                $uploadData = $this->upload->data();
                $document = $uploadData['file_name'];  // Store the uploaded file's name
            } else {
                // Handle upload errors
                echo json_encode([
                    'status' => 'error',
                    'message' => $this->upload->display_errors(),
                    'form_data' => [
                        'activity_id' => $this->input->post('activity_id'),
                        'emailSubject' => $this->input->post('emailSubject'),
                        'emailBody' => $this->input->post('emailBody')
                    ]
                ]);
                return;
            }
        }

        // Insert data into the database
        $data = [
            'student_id' => $this->input->post('student_id'),
            'activity_id' => $this->input->post('activity_id'),
            'subject' => $this->input->post('emailSubject'),
            'content' => $this->input->post('emailBody'),
            'document' => $document,  // Store single document name directly
            'status' => 'Pending'
        ];

        // Insert into the model
        $this->student->insert_application($data);

        // Success response
        echo json_encode(['status' => 'success', 'message' => 'Your application has been submitted successfully.']);
    }




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
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Pass student_id to the view
        $data['student_id'] = $student_id;

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('student/profile-settings', $data);
        $this->load->view('layout/footer', $data);
    }



    //===========PROFILE UPDATES

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
            $this->load->view('student/update_profile', $data);
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
                redirect('student/profile-settings/' . $student_id);
            } else {
                // Handle any errors (if update failed)
                $this->session->set_flashdata('error', 'Failed to update profile');
                redirect('student/update-profile/' . $student_id);
            }
        }
    }





    //ABOUT PAGE

    public function about_page()
    {
        $data['title'] = 'Evaluation Responses';
        // Load the Student_model to interact with the database
        $this->load->model('Student_model');

        // Get the student ID from the session
        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        // Fetch User Role
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Load header and views
        // $this->load->view('layout/header');
        $this->load->view('layout/header', $data);
        $this->load->view('about', $data);
        $this->load->view('layout/footer', $data);
        // $this->load->view('layout/footer');
    }
}

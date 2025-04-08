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
        }

        // MERGE POSTS & ACTIVITIES BEFORE PAGINATION
        $merged_feed = array_merge($data['posts'], $data['activities']);

        // SORT MERGED DATA BY DATE
        usort($merged_feed, function ($a, $b) {
            // For activities, use 'updated_at', for posts, use 'created_at'
            $a_date = isset($a->updated_at) ? $a->updated_at : (isset($a->created_at) ? $a->created_at : '');
            $b_date = isset($b->updated_at) ? $b->updated_at : (isset($b->created_at) ? $b->created_at : '');

            return strtotime($b_date) - strtotime($a_date);
        });

        //Apply pagination on the merged feed (with proper offset and limit)
        $data['feed'] = array_slice($merged_feed, $offset, $limit);

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


    // public function student_dashboard()
    // {
    //     $data['title'] = 'Student Home';

    //     $student_id = $this->session->userdata('student_id');

    //     // Fetch roles
    //     $data['users'] = $this->student->get_roles($student_id);

    //     // Fetch student activities
    //     $data['activities'] = $this->student->get_student_activities($student_id);
    //     $data['posts'] = $this->student->getFilteredPosts($student_id);
    //     $data['upcoming_activities'] = $this->student->get_upcoming_activities($student_id);

    //     // FETCH LIKE COUNTS FOR EACH POST
    //     foreach ($data['posts'] as &$post) {
    //         $post->like_count = $this->admin->get_like_count($post->post_id);
    //         $post->user_has_liked_post = $this->admin->user_has_liked($post->post_id, $student_id);
    //         $post->comments_count = $this->admin->get_comment_count($post->post_id);
    //     }

    //     // FETCH 2 COMMENTS ONLY IN A POST
    //     foreach ($data['posts'] as &$post) {
    //         $post->comments = $this->admin->get_comments_by_post($post->post_id);
    //     }

    //     // Loop through activities and check if the student has expressed interest || for count attendees
    //     foreach ($data['activities'] as &$activity) {
    //         // Use checkInterest method to check if the student has attended or expressed interest in the activity
    //         $activity['has_attended'] = $this->student->checkInterest($activity['activity_id'], $student_id);
    //     }

    //     // Pass the student_id to the view
    //     $data['student_id'] = $student_id;

    //     // Load header and views
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('student/home', $data);
    //     $this->load->view('layout/footer');
    // }

    // LIKING OF POST
    // public function like_post($post_id)
    // {
    //     $student_id = $this->session->userdata('student_id');

    //     $this->load->model('Admin_model', 'admin');

    //     // Check if the user already liked the post
    //     if ($this->admin->user_has_liked($post_id, $student_id)) {
    //         // User already liked, so we will "unlike" the post
    //         $this->admin->unlike_post($post_id, $student_id);
    //         $like_img = base_url() . 'assets/img/icons/spot-illustrations/like-inactive.png';
    //         $like_text = 'Like';
    //     } else {
    //         // User has not liked the post yet, so we will "like" the post
    //         $this->admin->like_post($post_id, $student_id);
    //         $like_img = base_url() . 'assets/img/icons/spot-illustrations/like-active.png';
    //         $like_text = 'Liked';
    //     }

    //     // Get the updated like count
    //     $new_like_count = $this->admin->get_like_count($post_id);

    //     // Return the response
    //     echo json_encode([
    //         'like_img' => $like_img,
    //         'like_text' => $like_text,
    //         'new_like_count' => $new_like_count
    //     ]);
    // }

    // // ADDING COMMENTS
    // public function add_comment()
    // {
    //     $this->load->model('Admin_model', 'admin');
    //     if ($this->input->post()) {
    //         // Set validation rules
    //         $this->form_validation->set_rules('comment', 'Comment', 'required');

    //         if ($this->form_validation->run() == FALSE) {
    //             $response = [
    //                 'status' => 'error',
    //                 'errors' => validation_errors()
    //             ];
    //         } else {
    //             // Prepare data for insertion
    //             $data = array(
    //                 'post_id' => $this->input->post('post_id'),
    //                 'content' => $this->input->post('comment'),
    //                 'student_id' => $this->session->userdata('student_id')
    //             );

    //             $result = $this->admin->add_comment($data);
    //             if ($result) {
    //                 $post_id = $this->input->post('post_id');

    //                 // Fetch updated comment count
    //                 $comments_count = $this->admin->get_comment_count($post_id);

    //                 // Fetch the newly added comment only
    //                 $new_comment = $this->admin->get_latest_comment($post_id); // Ensure this function fetches only the latest

    //                 // Ensure correct JSON response
    //                 $response = array(
    //                     'status' => 'success',
    //                     'message' => 'Comment Saved Successfully',
    //                     'comments_count' => $comments_count,
    //                     'new_comment' => array( // Make sure this is an array with expected keys
    //                         'name' => $new_comment->name ?? 'Unknown',
    //                         'profile_pic' => base_url('assets/profile/') . ($new_comment->profile_pic ?? 'default.jpg'),
    //                         'content' => $new_comment->content ?? '',
    //                     )
    //                 );
    //             } else {
    //                 $response = array(
    //                     'status' => 'error',
    //                     'errors' => 'Failed to Save Comment'
    //                 );
    //             }
    //         }
    //     }

    //     echo json_encode($response);
    // }



    //POST LIKES, COMMENTS CONTROLLERS END 


    public function attendance_history()
    {
        $data['title'] = 'Attendance History';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Get the attendance data for the student

        $data['attendances'] = $this->Student_model->get_attendance_history($student_id);


        $this->load->view('layout/header', $data);
        $this->load->view('student/attendance_history', $data);
        $this->load->view('layout/footer', $data);
    }



    public function summary_fines()
    {
        $data['title'] = 'Summary of Fines';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        // FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        $this->load->view('layout/header', $data);
        $this->load->view('student/summary_of_fines', $data);
        $this->load->view('layout/footer', $data);
    }

    //LIST OF ACTIVITY	
    public function list_activity()
    {
        $data['title'] = 'List of Activity';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        $student_details = $this->Student_model->get_student_details($student_id);

        // FETCHING DATA BASED ON THE ROLES - NECESSARY
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = isset($users['role']) ? $users['role'] : 'student'; // Default role as 'student'

        // Check if dept_id exists in $users to avoid "Undefined index" error
        $dept_id = isset($users['dept_id']) ? $users['dept_id'] : null;

        // Fetch activities based on department or organization membership
        $activities = $this->Student_model->get_activities_for_users($student_id, $student_details['dept_id']);
        $data['activities'] = $activities;

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

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        // Fetching roles based on the student ID
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Fetch the specific activity based on the activity_id
        $activity = $this->Student_model->get_activity_details($activity_id);
        $data['upcoming_activities'] = $this->Student_model->get_upcoming_activities($student_id);

        // Check if activity data exists
        if ($activity) {
            $data['activity'] = $activity;
            // Get the attendee count for this activity
            $attendee_count = $this->Student_model->getAttendeeCount($activity_id);
            $data['attendee_count'] = $attendee_count;
        } else {
            // Handle case when activity is not found
            $data['activity'] = null;
            // Optionally, you can redirect or show a custom message
            $data['message'] = "Activity not found!";
        }

        // Load the views
        $this->load->view('layout/header', $data);
        $this->load->view('student/activity-details', $data);
        $this->load->view('layout/footer', $data);
    }



    //FUNCTION TO STORE WHEN USER CLICKED ATTEND BUTTON

    public function express_interest()
    {
        if ($this->input->method() === 'post') {
            // Get activity_id from POST request
            $activityId = $this->input->post('activity_id');

            // Fetch student_id from session
            $studentId = $this->session->userdata('student_id');

            // Ensure student_id exists in the session
            if (!$studentId) {
                echo json_encode(['status' => 'error', 'message' => 'Student not logged in']);
                return;
            }

            // Load the model for checking interest
            $this->load->model('Student_model');

            // Check if student has already expressed interest in the activity
            $exists = $this->Student_model->checkInterest($activityId, $studentId);

            if (!$exists) {
                // Add student to the activity_attendance_interest table to track interest
                $this->Student_model->addInterest($activityId, $studentId);

                // Increment the attendee count in the activity table
                $this->Student_model->incrementAttendeeCount($activityId);

                // Return success response to change button text to "View Form"
                echo json_encode(['status' => 'success']);
            } else {
                // Return response indicating the student has already expressed interest
                echo json_encode(['status' => 'already-interested']);
            }
        } else {
            // Handle invalid request type (not POST)
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    }











    // ====================EVALUATION FORM FUNCTIONS START
    public function evaluation_form()
    {
        $data['title'] = 'Evaluation Form';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        // FETCHING DATA BASED ON THE ROLES - NECESSARY
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Fetch open (unanswered) forms
        $data['evaluation_forms'] = $this->Student_model->get_open_forms($student_id);

        // Fetch answered forms
        $data['answered_forms'] = $this->Student_model->get_answered_forms($student_id);



        // Load the view files
        $this->load->view('layout/header', $data);
        $this->load->view('student/evaluation_forms', $data);
        $this->load->view('layout/footer', $data);
    }



    public function evaluation_form_questions($form_id)
    {
        $data['title'] = 'Evaluation Form';
        $this->load->model('Student_model');

        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';


        // Fetch User Role
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];

        // Load the model
        $this->load->model('Student_model');

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




    public function excuse_application()
    {
        $data['title'] = 'Excuse Application';
        $this->load->model('Student_model');
        $student_id = $this->session->userdata('student_id');

        // Fetch student profile picture
        $current_profile_pic = $this->Student_model->get_profile_pic($student_id);
        // Ensure a default profile picture if none exists
        $data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

        // Fetching student details for pre-populating fields
        $student_details = $this->Student_model->get_student_details($student_id);

        // Check if student data is retrieved
        if ($student_details) {
            $data['first_name'] = $student_details['first_name'];
            $data['last_name'] = $student_details['last_name'];
            $data['department_name'] = $student_details['department_name']; // department name from dept_id
        } else {
            // Default values if no data is found (optional)
            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['department_name'] = '';
        }

        // Fetching data based on roles
        $users = $this->Student_model->get_roles($student_id);
        $data['role'] = $users['role'];


        // Fetch activities based on department or organization membership
        $activities = $this->Student_model->get_activities_for_user($student_id, $student_details['dept_id']);
        $data['activities'] = $activities;

        // Fetch the excuse applications
        $data['excuseApplications'] = $this->Student_model->get_excuse_applications($student_id);

        // Load views with merged data
        $this->load->view('layout/header', $data);
        $this->load->view('student/excuse_application', $data);
        $this->load->view('layout/footer', $data);
    }

    //EXCUSE APPLICATION SUBMIT

    public function submit_application()
    {
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->model('Student_model');

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
        $document = [];
        if (!empty($_FILES['fileUpload']['name'][0])) {
            $config['upload_path'] = './assets/excuseFiles/';
            $config['allowed_types'] = 'jpg|png|pdf|docx';
            $config['max_size'] = 2048; // 2MB limit

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->upload->initialize($config);
            $files = $_FILES['fileUpload'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                $_FILES['fileUpload']['name'] = $files['name'][$i];
                $_FILES['fileUpload']['type'] = $files['type'][$i];
                $_FILES['fileUpload']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['fileUpload']['error'] = $files['error'][$i];
                $_FILES['fileUpload']['size'] = $files['size'][$i];

                if ($this->upload->do_upload('fileUpload')) {
                    $uploadData = $this->upload->data();
                    $document[] = $uploadData['file_name'];
                } else {
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
        }

        $document_string = !empty($document) ? implode(',', $document) : '';

        // Insert into database
        $data = [
            'student_id' => $this->input->post('student_id'),
            'activity_id' => $this->input->post('activity_id'),
            'subject' => $this->input->post('emailSubject'),
            'content' => $this->input->post('emailBody'),
            'document' => $document_string,
            'status' => 'Pending'
        ];

        $this->Student_model->insert_application($data);

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

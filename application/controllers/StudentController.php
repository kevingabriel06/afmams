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

// Load the FPDF library from the third_party folder
require_once(APPPATH . 'third_party/fpdf.php');
class PDF extends FPDF
{
	function Header()
	{
		// Get the full path of the logo image
		$logoPath = APPPATH . 'third_party/header.jpg'; // Ensure the correct path

		// Get page width dynamically
		$pageWidth = $this->GetPageWidth();

		// Set the image to cover the full width (assuming A4 width is 210mm)
		$this->Image($logoPath, 0, 0, $pageWidth, 40); // Adjust height as needed

		// Move the cursor below the image to avoid overlapping
		$this->SetY(45); // Adjust if necessary
	}

	function Footer()
	{
		// Get the full path of the footer image
		$footerImage = APPPATH . 'third_party/footer.jpg';

		// Get page width and height dynamically
		$pageWidth = $this->GetPageWidth();
		$pageHeight = $this->GetPageHeight();
		$footerHeight = 20; // Adjust footer height as needed

		// Position the footer at the exact bottom with no margin
		$this->Image($footerImage, 0, $pageHeight - $footerHeight, $pageWidth, $footerHeight);
	}
}

class StudentController extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->model('Student_model', 'student');
		$this->load->model('Notification_model');

		// CHECK IF THERE'S A STUDENT LOGIN
		if (!$this->session->userdata('student_id')) {
			redirect(site_url('login'));
		}
	}

	// DASHBOARD - PAGE
	public function student_dashboard()
	{
		$data['title'] = 'Home';
		$this->load->model('Student_model');
		$this->load->model('Admin_model', 'admin');
		$this->load->model('Notification_model'); // Load the notification model
		$this->load->helper('url');


		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->student->get_student($student_id);
		$data['authors'] = $this->student->get_user();

		// Fetch student profile picture
		$current_profile_pic = $this->Student_model->get_profile_pic($student_id);
		// Ensure a default profile picture if none exists
		$data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

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

		// Fetch the post details
		$post = $this->student->get_post_by_id($post_id); // Make sure this returns the post

		// Check if the post exists
		if (!$post) {
			// Handle case where the post does not exist
			echo json_encode(['status' => 'error', 'message' => 'Post not found']);
			return;
		}

		// Check if the user already liked the post
		if ($this->student->user_has_liked($post_id, $student_id)) {
			// User already liked, so we will "unlike" the post
			$this->student->unlike_post($post_id, $student_id);
			$like_img = base_url() . 'assets/img/icons/spot-illustrations/like-inactive.png';
			$like_text = 'Like';

			// ❌ Delete the notification when unliked
			$this->db->delete('notifications', [
				'sender_student_id' => $student_id,
				'reference_id' => $post_id,
				'type' => 'post_liked'
			]);
		} else {
			// User has not liked the post yet, so we will "like" the post
			$this->student->like_post($post_id, $student_id);
			$like_img = base_url() . 'assets/img/icons/spot-illustrations/like-active.png';
			$like_text = 'Liked';

			// Fetch all likers except the current sender and post creator/admin
			$likers = $this->student->get_post_likers($post_id); // Includes all likers
			$other_likers = array_filter($likers, function ($liker) use ($student_id, $post) {
				return $liker->student_id != $student_id && $liker->student_id != $post->student_id; // Exclude post creator/admin
			});
			$other_likers = array_values($other_likers); // Reindex array
			$other_count = count($other_likers);

			// Build message
			if ($other_count === 0) {
				$message = 'liked your post.';
			} else {
				// Get the first other liker's name
				$name = ' and ' . $other_likers[0]->first_name . ' ' . $other_likers[0]->last_name;

				// Calculate how many other likers there are
				$remaining = $other_count - 1;

				// If there are remaining likers, append "and X other(s)"
				if ($remaining > 0) {
					$message = $name . ' and ' . $remaining . ' other' . ($remaining > 1 ? 's' : '') . ' liked your post.';
				} else {
					// Otherwise, just display the first liker
					$message = $name . ' liked your post.';
				}
			}



			// ✅ Send notification to post owner (admin or student who posted)
			if ($post && !empty($post->student_id)) {
				$this->db->insert('notifications', [
					'recipient_student_id' => null,
					'recipient_admin_id'   => $post->student_id,
					'sender_student_id'    => $student_id,
					'type'                 => 'post_liked',
					'reference_id'         => $post_id,
					'message'              => $message,
					'is_read'              => 0,
					'created_at'           => date('Y-m-d H:i:s'),
					'link'                 => base_url('admin/community/')
				]);
			}
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

					// ✅ Send notification to the post owner
					$post = $this->student->get_post_by_id($post_id); // You should have this method in your model
					if ($post && !empty($post->student_id) && $post->student_id !== $this->session->userdata('student_id')) {
						$this->db->insert('notifications', [
							'recipient_student_id' => null,
							'recipient_admin_id'   => $post->student_id,
							'sender_student_id'    => $this->session->userdata('student_id'),
							'type'                 => 'commented',
							'reference_id'         => $post_id,
							'message'              => 'commented on your post.',
							'is_read'              => 0,
							'created_at'           => date('Y-m-d H:i:s'),
							'link'                 => base_url('admin/community/')
						]);
					}

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
		$this->form_validation->set_rules('amount', 'Amount Paid');

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

			// === Send Notification to Admins ===
			$this->load->model('Notification_model');

			$activity_name = $this->Notification_model->get_activity_name($activity_id) ?? 'Unknown Activity';
			$admin_student_ids = $this->Notification_model->get_admin_student_ids(); // Get admin student_ids
			$notification_message = 'submitted a registration request for "' . $activity_name . '"';

			foreach ($admin_student_ids as $admin_student_id) {
				$this->Notification_model->add_notification(
					null,                  // recipient_student_id = NULL
					$student_id,           // sender_student_id
					'registration_submitted', // type (you can define it in your system)
					$activity_id,          // reference_id
					$notification_message, // message
					$admin_student_id,     // recipient_admin_id
					base_url('admin/activity-details/' . $activity_id)
				);
			}
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


	// ATTENDANCE HISTORY - PAGE
	public function attendance_history()
	{
		$data['title'] = 'Attendance History';

		$student_id = $this->session->userdata('student_id');

		// Fetching user details (with profile picture and role, etc.)
		$data['users'] = $this->student->get_student($student_id);

		// Fetch attendance of this student across all activities
		$data['attendances'] = $this->student->get_attendance($student_id);

		$this->load->view('layout/header', $data);
		$this->load->view('student/attendance_history', $data);  // change view to match your new setup
		$this->load->view('layout/footer', $data);
	}

	public function list_attendees() {}


	// SUMMARY OF FINES
	public function summary_fines()
	{
		$data['title'] = 'Summary of Fines';
		$student_id = $this->session->userdata('student_id');
		$this->load->model('Student_model');

		// Fetch user data
		$data['users'] = $this->student->get_student($student_id);

		// ✅ Fetch only this student's fines
		$data['fines'] = $this->Student_model->get_fines_with_summary_and_activity($student_id);

		// Load the views
		$this->load->view('layout/header', $data);
		$this->load->view('student/summary_fines', $data);
		$this->load->view('layout/footer', $data);
	}





	//PAY SUMMARY OF FINES START

	// Handle the payment submission
	public function pay_fines()
	{
		$this->load->model('Notification_model');
		$this->load->helper(['form', 'url']);
		$this->load->library('upload');

		$student_id = $this->input->post('student_id');
		$total_fines = $this->input->post('total_fines');
		$organizer = $this->input->post('organizer');
		$mode_payment = $this->input->post('mode_payment');
		$reference_number_students = $this->input->post('reference_number_students');
		$receipt_filename = '';

		// Handle receipt upload if provided
		if (!empty($_FILES['receipt']['name'])) {
			$config['upload_path'] = './uploads/fine_receipts/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['encrypt_name'] = TRUE;

			$this->upload->initialize($config);

			if ($this->upload->do_upload('receipt')) {
				$upload_data = $this->upload->data();
				$receipt_filename = $upload_data['file_name'];
			}
		}

		// Insert fine payment record
		$data = [
			'student_id' => $student_id,
			'organizer' => $organizer,
			'total_fines' => $total_fines,
			'fines_status' => 'Pending',
			'mode_payment' => $mode_payment,
			'reference_number_students' => $reference_number_students,
			'receipt' => $receipt_filename,
			'last_updated' => date('Y-m-d H:i:s'),
		];

		$this->db->insert('fines_summary', $data);
		$fine_summary_id = $this->db->insert_id(); // Reference for notifications

		// Get student name for message
		$this->db->select('first_name, last_name');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$student = $this->db->get()->row();
		$student_name = $student ? $student->first_name . ' ' . $student->last_name : 'A student';

		$notification_message = ' submitted a fine payment to ' . $organizer . '.';

		// Notify all admins
		$admin_student_ids = $this->Notification_model->get_admin_student_ids();
		foreach ($admin_student_ids as $admin_student_id) {
			$this->Notification_model->add_notification(
				null,                      // recipient_student_id
				$student_id,               // sender_student_id
				'fine_payment_uploaded',   // type
				$fine_summary_id,          // reference_id
				$notification_message,     // message
				$admin_student_id,       // recipient_admin_id
				base_url('admin/list-fines/')
			);
		}

		echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully.']);
	}



	//PAY SUMMARY OF FINES END





	// LIST OF ACTIVITY	- PAGE
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


	// SPECIFIC ACTIVITY DETAILS - PAGE
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


		// Get registration record directly from the 'registrations' table
		$registration = $this->db->get_where('registrations', [
			'student_id' => $student_id,
			'activity_id' => $activity_id,
		])->row_array();

		$data['registration'] = $registration;

		// Check if the registration is verified and has a corresponding receipt
		if ($registration && $registration['registration_status'] == 'Verified') {
			$registration_id = $registration['registration_id'];
			$data['receipt'] = $this->student->get_receipt_by_id($registration_id); // Pass receipt data
		} else {
			$data['receipt'] = null; // No receipt if not verified
		}


		// Load the views
		$this->load->view('layout/header', $data);
		$this->load->view('student/activity-details', $data);
		$this->load->view('layout/footer', $data);
	}

	// EVALUATION LIST - PAGE
	public function evaluation_form_list()
	{
		$data['title'] = 'List Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->student->get_student($student_id);

		$data['forms'] = $this->student->list_forms();

		// Fetch open (unanswered) forms
		$data['evaluation_forms'] = $this->student->get_open_forms_for_student_and_unanswered();

		// // Fetch answered forms
		// $data['answered_forms'] = $this->student->get_answered_forms($student_id);

		// Load the view files
		$this->load->view('layout/header', $data);
		$this->load->view('student/evaluation_forms_list', $data);
		$this->load->view('layout/footer', $data);
	}

	// EVALUATION FORM DESIGN OPENED FORM - PAGE
	public function evaluation_forms()
	{
		$data['title'] = 'Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->student->get_student($student_id);

		// Fetch open (unanswered) forms
		$data['evaluation_forms'] = $this->student->get_open_forms_for_student_and_unanswered();

		// Load the view files
		$this->load->view('layout/header', $data);
		$this->load->view('student/evaluation_forms', $data);
		$this->load->view('layout/footer', $data);
	}

	// EVALUATION VIEW FORM - PAGE
	public function evaluation_form_questions($form_id)
	{
		$data['title'] = 'Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->student->get_student($student_id);

		$data['forms'] = $this->student->get_evaluation_by_id($form_id);

		$form_data = $this->student->get_evaluation_by_id($form_id);
		$data['form_data'] = $form_data;

		// Load Views
		$this->load->view('layout/header', $data);
		$this->load->view('student/evaluation_forms_view', $data);
		$this->load->view('layout/footer', $data);
	}

	// FUNCTION FOR SUBMIT
	public function submit_form()
	{
		// Get form data
		$form_id   = $this->input->post('form_id');
		$answers   = $this->input->post('answers');     // array of answers
		$field_ids = $this->input->post('id');          // array of form_fields_id
		$types     = $this->input->post('type');        // array of types

		// Validate inputs
		if (!$form_id || empty($answers) || empty($field_ids)) {
			echo json_encode(['success' => false, 'message' => 'Invalid submission.']);
			return;
		}

		// Prepare data for evaluation_responses table
		$response_data = [
			'form_id'    => $form_id,
			'student_id' => $this->session->userdata('student_id'),
			'remarks'    => 'Answered'
		];

		// Insert response record
		$response_id = $this->student->save_response($response_data);

		if (!$response_id) {
			echo json_encode(['success' => false, 'message' => 'Failed to save response.']);
			return;
		}

		// Prepare answer entries
		$answer_data = [];
		foreach ($answers as $i => $answer) {
			$answer_data[] = [
				'evaluation_response_id' => $response_id,
				'form_fields_id'         => $field_ids[$i],  // FIXED: use matching index
				'answer'                 => $answer,

				// 'type' => $types[$i], // Uncomment if needed
			];
		}

		// Insert into response_answer table
		$save_answers = $this->student->save_answers($answer_data);

		if ($save_answers) {
			echo json_encode([
				'success'  => true,
				'message'  => 'Your answers have been submitted.',
				'redirect' => site_url('student/evaluation-form/list'),
			]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to submit answers.']);
		}
	}

	// SHOW EVALUATION FORM ANSWERS
	public function view_evaluation_answers($form_id)
	{
		$data['title'] = 'Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->student->get_student($student_id);

		// Fetch form data along with answers
		$data['form_data'] = $this->student->get_evaluation_answer($form_id);


		// Load Views
		$this->load->view('layout/header', $data);
		$this->load->view('student/evaluation_form_answers', $data);
		$this->load->view('layout/footer', $data);
	}



	// EXCUSE APPLICATION LIST - PAGE
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

	// EXCUSE APPLICATION - PAGE
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


		$this->load->model('Notification_model');
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



		// Notify Admins (send notifications)
		$this->load->model('Notification_model');
		$activity_name = $this->Notification_model->get_activity_name($data['activity_id']) ?? 'Unknown Activity';


		$admin_student_ids = $this->Notification_model->get_admin_student_ids(); // Get admin student_ids

		$notification_message = 'submitted an excuse application for "' . $activity_name . '"';

		$sender_student_id = $this->input->post('student_id'); // Already a student_id

		foreach ($admin_student_ids as $admin_student_id) {
			$this->Notification_model->add_notification(
				null,                      // recipient_student_id = NULL
				$sender_student_id,        // sender_student_id
				'excuse_submitted',        // type
				$data['activity_id'],      // reference_id
				$notification_message,     // message
				$admin_student_id,         // recipient_admin_id is used instead
				base_url('admin/list-of-excuse-letter/' . $data['activity_id'])
			);
		}


		// Success response
		echo json_encode(['status' => 'success', 'message' => 'Your application has been submitted successfully.']);
	}


	public function cancel_excuse_application($excuse_id)
	{
		$this->load->model('Student_model');
		$this->load->model('Notification_model');

		// Step 1: Get the document filename
		$excuse = $this->Student_model->get_excuse_by_id($excuse_id); // Make sure this function exists

		if ($excuse && !empty($excuse->document)) {
			$file_path = FCPATH . 'assets/excuseFiles/' . $excuse->document;

			// Step 2: Delete the file if it exists
			if (file_exists($file_path)) {
				unlink($file_path); // Delete the file from disk
			}
		}

		// Step 3: Delete the excuse application
		$deleted = $this->Student_model->delete_excuse_application($excuse_id);

		if ($deleted) {
			$this->Notification_model->delete_notifications_by_reference($excuse_id, 'excuse_submitted');

			echo json_encode(['status' => 'success', 'message' => 'Application cancelled and file deleted.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to cancel application.']);
		}
	}



	// OTHER PAGES 

	//PROFILE SETTINGS - PAGE
	public function profile_settings()
	{
		$data['title'] = 'Profile Settings';

		$student_id = $this->session->userdata('student_id');

		// Get user and their organizations
		$student_details = $this->student->get_user_profile();

		if ($student_details) {
			$data['student_details'] = $student_details;
			$data['organizations'] = $student_details->organizations ?? [];
		} else {
			$data['student_details'] = null;
			$data['organizations'] = [];
		}

		$data['users'] = $this->student->get_student($student_id);

		$this->load->view('layout/header', $data);
		$this->load->view('student/profile-settings', $data);
		$this->load->view('layout/footer', $data);
	}

	public function update_profile_pic()
	{
		$student_id = $this->session->userdata('student_id');

		if (!$student_id) {
			echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
			return;
		}

		// Fetch current profile picture
		$current_pic = $this->student->get_profile_pic($student_id);

		if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
			$config['upload_path']   = './assets/profile/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['max_size']      = 10240; // 10MB
			$config['file_name']     = 'profile_' . $student_id . '_' . time(); // Unique filename

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('profile_pic')) {
				echo json_encode([
					'status' => 'error',
					'message' => strip_tags($this->upload->display_errors())
				]);
				return;
			}

			$file_data = $this->upload->data();
			$file_name = $file_data['file_name'];

			// Update database with new profile picture
			$update_data = ['profile_pic' => $file_name];
			$this->student->update_profile_pic($student_id, $update_data);

			// Delete the old profile picture if it's not the default
			if ($current_pic && file_exists('./assets/profile/' . $current_pic) && $current_pic !== 'default.png') {
				unlink('./assets/profile/' . $current_pic); // Remove old image
			}

			echo json_encode([
				'status' => 'success',
				'message' => 'Profile picture updated successfully',
				'file_name' => $file_name
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'No file selected or file upload error'
			]);
		}
	}

	public function update_profile()
	{

		$student_id = $this->session->userdata('student_id');

		$data = [
			'first_name' => $this->input->post('first_name'),
			'middle_name' => $this->input->post('middle_name'),
			'last_name'  => $this->input->post('last_name'),
			'email'      => $this->input->post('email'),
			'year_level' => $this->input->post('year_level'),
			'sex' => $this->input->post('sex'),

		];

		$updated = $this->student->update_student($student_id, $data);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No changes made or something went wrong.']);
		}
	}

	public function update_password()
	{
		$student_id = $this->session->userdata('student_id');

		// Decode the raw JSON body
		$data = json_decode(file_get_contents("php://input"), true);

		$old_password = $data['old_password'] ?? null;
		$new_password = $data['new_password'] ?? null;
		$confirm_password = $data['confirm_password'] ?? null;

		if (!$student_id) {
			echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
			return;
		}

		if ($new_password !== $confirm_password) {
			echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
			return;
		}

		$student = $this->student->get_by_id($student_id);

		if (!$student || !password_verify($old_password, $student->password)) {
			echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
			return;
		}

		$hashed = password_hash($new_password, PASSWORD_DEFAULT);
		$this->student->update_password($student_id, $hashed);

		echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
	}

	public function get_qr_code_by_student()
	{
		// Get student_id from session
		if ($this->session->has_userdata('student_id')) {
			$student_id = $this->session->userdata('student_id');

			// Retrieve QR code from the database (ensure you have the correct query in place)
			$qr_code = $this->student->get_qr_code($student_id);

			if ($qr_code) {
				// Return the QR code as JSON (base64 string)
				echo json_encode(['status' => 'success', 'qr_code' => $qr_code]);
			} else {
				// No QR code found for the student
				echo json_encode(['status' => 'error', 'message' => 'QR Code not found for this student.']);
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No student found in session.']);
		}
	}

	//RECEIPTS PAGE START

	public function receipts_page()
	{
		$data['title'] = 'My Receipts';

		// Load the model
		$this->load->model('Student_model');
		$this->load->model('Notification_model');

		// Get the student ID from session
		$student_id = $this->session->userdata('student_id');

		if (!$student_id) {
			show_error('Unauthorized access.', 403);
		}

		// Fetch student profile picture
		$current_profile_pic = $this->Student_model->get_profile_pic($student_id);
		$data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

		// Get unread notifications count for the logged-in student
		$data['unread_count'] = $this->Notification_model->count_unread_notifications($student_id);
		$data['notifications'] = $this->Notification_model->get_unread_notifications($student_id);

		// // Fetch User Role
		// $users = $this->Student_model->get_roles($student_id);
		// $data['role'] = $users['role'] ?? 'Student';

		// FETCH USER DATA
		$data['users'] = $this->student->get_student($student_id);

		// Fetch Receipts Data
		$data['receipts'] = $this->Student_model->get_receipts($student_id);

		// Load views
		$this->load->view('layout/header', $data);
		$this->load->view('student/receipts_page', $data);
		$this->load->view('layout/footer', $data);
	}


	//RECEIPTS PAGE END



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

	public function about()
	{
		$data['title'] = 'About';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->student->get_student($student_id);


		$this->load->view('layout/header', $data);
		$this->load->view('admin/about', $data);
		$this->load->view('layout/footer', $data);
	}
}

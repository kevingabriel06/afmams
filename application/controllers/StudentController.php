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

		$organizer = $this->input->post('organizer');

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
			// Load Notification model
			$this->load->model('Notification_model');

			// Get organizer type and ID for the post
			$organizer_type = $this->Notification_model->get_post_organizer_type($post_id);
			$organizer_id = $this->Notification_model->get_post_organizer($post_id);


			// Set link based on organizer type
			if ($organizer_type === 'admin') {
				$link = base_url('admin/community/');
			} else {
				// For 'org' or 'dept' or any officer type
				$link = base_url('officer/community/');
			}

			switch ($organizer_type) {
				case 'admin':
					$admin_ids = $this->Notification_model->get_admin_student_ids();
					foreach ($admin_ids as $admin_id) {
						$this->Notification_model->add_notification(
							null,
							$student_id,
							'post_liked',
							$post_id,
							$message,
							$admin_id,
							$link
						);
					}
					break;

				case 'org':
					$org_officer_ids = $this->Notification_model->get_org_officer_ids_by_id($organizer_id);
					foreach ($org_officer_ids as $officer_id) {
						$this->Notification_model->add_notification(
							null,
							$student_id,
							'post_liked',
							$post_id,
							$message,
							null,
							$link,
							$officer_id,
							'recipient_officer_id'
						);
					}
					break;

				case 'dept':
					$dept_officer_ids = $this->Notification_model->get_dept_officer_ids_by_id($organizer_id);
					foreach ($dept_officer_ids as $officer_id) {
						$this->Notification_model->add_notification(
							null,
							$student_id,
							'post_liked',
							$post_id,
							$message,
							null,
							$link,
							$officer_id,
							'recipient_officer_id'
						);
					}
					break;

				default:
					log_message('error', "Unknown organizer type for post $post_id");
					break;
			}
		}

		// Get updated like count
		$new_like_count = $this->student->get_like_count($post_id);

		// Return response
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
				// Prepare comment data
				$data = array(
					'post_id' => $this->input->post('post_id'),
					'content' => $this->input->post('comment'),
					'student_id' => $this->session->userdata('student_id')
				);

				$result = $this->student->add_comment($data);

				if ($result) {
					$post_id = $this->input->post('post_id');
					$sender_id = $this->session->userdata('student_id');

					// Load Notification model
					$this->load->model('Notification_model');

					// Get post origin
					$organizer_type = $this->Notification_model->get_post_organizer_type($post_id);
					$organizer_id = $this->Notification_model->get_post_organizer($post_id);

					// Set link based on organizer type
					if ($organizer_type === 'admin') {
						$link = base_url('admin/community/');
					} else {
						// For 'org' or 'dept' or any officer type
						$link = base_url('officer/community/');
					}

					$message = 'commented on your post.';

					switch ($organizer_type) {
						case 'admin':
							$admin_ids = $this->Notification_model->get_admin_student_ids();
							foreach ($admin_ids as $admin_id) {
								if ($admin_id != $sender_id) {
									$this->Notification_model->add_notification(
										null,
										$sender_id,
										'commented',
										$post_id,
										$message,
										$admin_id,
										$link
									);
								}
							}
							break;

						case 'org':
							$officers = $this->Notification_model->get_org_officer_ids_by_id($organizer_id);
							foreach ($officers as $officer_id) {
								if ($officer_id != $sender_id) {
									$this->Notification_model->add_notification(
										null,
										$sender_id,
										'commented',
										$post_id,
										$message,
										null,
										$link,
										$officer_id,
										'recipient_officer_id'
									);
								}
							}
							break;

						case 'dept':
							$officers = $this->Notification_model->get_dept_officer_ids_by_id($organizer_id);
							foreach ($officers as $officer_id) {
								if ($officer_id != $sender_id) {
									$this->Notification_model->add_notification(
										null,
										$sender_id,
										'commented',
										$post_id,
										$message,
										null,
										$link,
										$officer_id,
										'recipient_officer_id'
									);
								}
							}
							break;

						default:
							log_message('error', "Unknown organizer type for post $post_id");
							break;
					}

					// Fetch updated comment count
					$comments_count = $this->student->get_comment_count($post_id);

					// Fetch the newly added comment only
					$new_comment = $this->student->get_latest_comment($post_id);

					// Send response
					$response = array(
						'status' => 'success',
						'message' => 'Comment Saved Successfully',
						'comments_count' => $comments_count,
						'new_comment' => array(
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
			echo json_encode($response);
		}
	}


	// REGISTRATION
	// public function register()
	// {

	// 	// Set validation rules
	// 	$this->form_validation->set_rules('student_id', 'Student ID', 'required');
	// 	$this->form_validation->set_rules('activity_id', 'Activity ID', 'required');
	// 	$this->form_validation->set_rules('payment_type', 'Payment Type', 'required|in_list[Cash,Online Payment]');
	// 	$this->form_validation->set_rules('amount', 'Amount Paid');

	// 	// Additional rule for Online Payment: reference number required
	// 	if ($this->input->post('payment_type') === 'Online Payment') {
	// 		$this->form_validation->set_rules('reference_number', 'Reference Number', 'required');
	// 	}

	// 	// Run validation
	// 	if ($this->form_validation->run() == FALSE) {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => validation_errors()
	// 		]);
	// 		return;
	// 	}

	// 	$student_id       = $this->input->post('student_id');
	// 	$activity_id      = $this->input->post('activity_id');
	// 	$payment_type     = $this->input->post('payment_type');
	// 	$reference_number = $this->input->post('reference_number');
	// 	$amount_paid      = $this->input->post('amount');
	// 	$gcash_receipt    = null;

	// 	// Handle file upload (optional for Cash, required for Online Payment)
	// 	if (!empty($_FILES['receipt']['name'])) {
	// 		$config['upload_path']   = './assets/registration_receipt/';
	// 		$config['allowed_types'] = 'jpg|jpeg|png';
	// 		$config['max_size']      = 2048; // 2MB
	// 		$config['file_name']     = 'proof_' . time() . '_' . $student_id;

	// 		$this->load->library('upload', $config);
	// 		$this->upload->initialize($config);

	// 		if (!$this->upload->do_upload('receipt')) {
	// 			echo json_encode([
	// 				'status' => 'error',
	// 				'message' => strip_tags($this->upload->display_errors())
	// 			]);
	// 			return;
	// 		}

	// 		$upload_data   = $this->upload->data();
	// 		$gcash_receipt = $upload_data['file_name'];
	// 	}

	// 	// If Online Payment and no receipt uploaded
	// 	if ($payment_type === 'Online Payment' && $gcash_receipt === null) {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => 'Gcash receipt image is required for Online Payment.'
	// 		]);
	// 		return;
	// 	}

	// 	// Prepare data
	// 	$data = [
	// 		'student_id'       => $student_id,
	// 		'activity_id'      => $activity_id,
	// 		'payment_type'     => $payment_type,
	// 		'reference_number' => ($payment_type === 'Cash') ? null : $reference_number,
	// 		'amount_paid'      => $amount_paid,
	// 		'receipt'          => $gcash_receipt,
	// 		'registration_status' => 'Pending',
	// 		'registered_at'    => date('Y-m-d H:i:s'),
	// 	];

	// 	if ($this->student->insert_registration($data)) {
	// 		// === Send Notifications with Organizer Type logic ===
	// 		$this->load->model('Notification_model');

	// 		$activity_name = $this->Notification_model->get_activity_name($activity_id) ?? 'Unknown Activity';
	// 		$organizer = $this->Notification_model->get_activity_organizer($activity_id);
	// 		$organizer_type = $this->Notification_model->get_activity_organizer_type($activity_id);
	// 		$sender_student_id = $student_id;

	// 		$notification_message = 'submitted a registration request for "' . $activity_name . '"';
	// 		// Set link based on organizer type
	// 		if ($organizer_type === 'admin') {
	// 			$link = base_url('admin/activity-details/' . $activity_id);
	// 		} else {
	// 			// For 'org' or 'dept' or any officer type
	// 			$link = base_url('officer/activity-details/' . $activity_id);
	// 		}

	// 		switch ($organizer_type) {
	// 			case 'admin':
	// 				$admin_ids = $this->Notification_model->get_admin_student_ids();
	// 				foreach ($admin_ids as $admin_id) {
	// 					$this->Notification_model->add_notification(
	// 						null,
	// 						$sender_student_id,
	// 						'registration_submitted',
	// 						$activity_id,
	// 						$notification_message,
	// 						$admin_id,
	// 						$link
	// 					);
	// 				}
	// 				break;

	// 			case 'org':
	// 				$org_officer_ids = $this->Notification_model->get_org_officer_ids_by_name($organizer);
	// 				foreach ($org_officer_ids as $officer_id) {
	// 					$this->Notification_model->add_notification(
	// 						null,
	// 						$sender_student_id,
	// 						'registration_submitted',
	// 						$activity_id,
	// 						$notification_message,
	// 						null,
	// 						$link,
	// 						$officer_id
	// 					);
	// 				}
	// 				break;

	// 			case 'dept':
	// 				$dept_officer_ids = $this->Notification_model->get_dept_officer_ids_by_name($organizer);
	// 				foreach ($dept_officer_ids as $officer_id) {
	// 					$this->Notification_model->add_notification(
	// 						null,
	// 						$sender_student_id,
	// 						'registration_submitted',
	// 						$activity_id,
	// 						$notification_message,
	// 						null,
	// 						$link,
	// 						$officer_id
	// 					);
	// 				}
	// 				break;

	// 			default:
	// 				log_message('error', "Unknown organizer type for activity $activity_id");
	// 				break;
	// 		}

	// 		echo json_encode([
	// 			'status' => 'success',
	// 			'message' => 'Registration submitted successfully!'
	// 		]);
	// 	} else {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => 'Database error. Please try again.'
	// 		]);
	// 	}
	// }

	// REGISTRATION
	public function register()
	{
		// Set validation rules
		$this->form_validation->set_rules('student_id', 'Student ID', 'required');
		$this->form_validation->set_rules('activity_id', 'Activity ID', 'required');
		$this->form_validation->set_rules('payment_type', 'Payment Type', 'required|in_list[Cash,Online Payment]');
		$this->form_validation->set_rules('amount', 'Amount Paid');

		if ($this->input->post('payment_type') === 'Online Payment') {
			$this->form_validation->set_rules('reference_number', 'Reference Number', 'required');
		}

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

		// Handle file upload
		if (!empty($_FILES['receipt']['name'])) {
			$config['upload_path']   = './assets/registration_receipt/';
			$config['allowed_types'] = 'jpg|jpeg|png';
			$config['max_size']      = 2048;
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

		if ($payment_type === 'Online Payment' && $gcash_receipt === null) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Gcash receipt image is required for Online Payment.'
			]);
			return;
		}

		$data = [
			'payment_type'     => $payment_type,
			'reference_number' => ($payment_type === 'Cash') ? null : $reference_number,
			'amount_paid'      => $amount_paid,
			'receipt'          => $gcash_receipt,
			'registration_status' => 'Pending',
			'registered_at'    => date('Y-m-d H:i:s'),
		];

		$this->load->model('Student_model');

		if ($this->student->registration_exists($student_id, $activity_id)) {
			$result = $this->student->update_registration($student_id, $activity_id, $data);
		} else {
			$data['student_id'] = $student_id;
			$data['activity_id'] = $activity_id;
			$result = $this->student->insert_registration($data);
		}

		if ($result) {
			// Notifications same as before...
			$this->load->model('Notification_model');
			$activity_name = $this->Notification_model->get_activity_name($activity_id) ?? 'Unknown Activity';
			$organizer = $this->Notification_model->get_activity_organizer($activity_id);
			$organizer_type = $this->Notification_model->get_activity_organizer_type($activity_id);
			$sender_student_id = $student_id;
			$notification_message = 'submitted a registration request for "' . $activity_name . '"';

			$link = ($organizer_type === 'admin')
				? base_url('admin/activity-details/' . $activity_id)
				: base_url('officer/activity-details/' . $activity_id);

			switch ($organizer_type) {
				case 'admin':
					$admin_ids = $this->Notification_model->get_admin_student_ids();
					foreach ($admin_ids as $admin_id) {
						$this->Notification_model->add_notification(null, $sender_student_id, 'registration_submitted', $activity_id, $notification_message, $admin_id, $link);
					}
					break;
				case 'org':
					$org_officer_ids = $this->Notification_model->get_org_officer_ids_by_name($organizer);
					foreach ($org_officer_ids as $officer_id) {
						$this->Notification_model->add_notification(null, $sender_student_id, 'registration_submitted', $activity_id, $notification_message, null, $link, $officer_id);
					}
					break;
				case 'dept':
					$dept_officer_ids = $this->Notification_model->get_dept_officer_ids_by_name($organizer);
					foreach ($dept_officer_ids as $officer_id) {
						$this->Notification_model->add_notification(null, $sender_student_id, 'registration_submitted', $activity_id, $notification_message, null, $link, $officer_id);
					}
					break;
				default:
					log_message('error', "Unknown organizer type for activity $activity_id");
					break;
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


	public function export_attendance_pdf()
	{
		$this->load->library('pdf');

		$student_id = $this->session->userdata('student_id');
		$attendances = $this->student->get_attendance($student_id);

		// Get student info including dept_name
		$student_info = $this->student->get_student_info($student_id);

		// Group by organizer
		$grouped = [];
		foreach ($attendances as $a) {
			$grouped[$a->organizer][] = $a;
		}

		$pdf = new PDF('P', 'mm', 'A4');
		$pdf->SetMargins(10, 10, 10);
		$pdf->AddPage();

		// Title
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, 'Attendance History', 0, 1, 'C');
		$pdf->Ln(5);

		// --- STUDENT INFO SECTION ---
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(100, 149, 237);  // Cornflower Blue for labels
		$pdf->SetTextColor(0);  // White text for labels

		// Student ID label
		$pdf->Cell(40, 8, 'Student ID:', 1, 0, 'L', true);
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetFillColor(255, 255, 255);  // White background for values
		$pdf->SetTextColor(0);
		$pdf->Cell(0, 8, $student_info['student_id'] ?? 'N/A', 1, 1, 'L', true);

		// Name label
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(100, 149, 237);
		$pdf->SetTextColor(0);
		$pdf->Cell(40, 8, 'Name:', 1, 0, 'L', true);
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(0);
		$full_name = trim(($student_info['first_name'] ?? '') . ' ' . ($student_info['middle_name'] ?? '') . ' ' . ($student_info['last_name'] ?? ''));
		$pdf->Cell(0, 8, $full_name ?: 'N/A', 1, 1, 'L', true);

		// Department label
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(100, 149, 237);
		$pdf->SetTextColor(0);
		$pdf->Cell(40, 8, 'Department:', 1, 0, 'L', true);
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(0);
		$pdf->Cell(0, 8, $student_info['dept_name'] ?? 'N/A', 1, 1, 'L', true);

		$pdf->Ln(8);

		// --- END STUDENT INFO SECTION ---

		$columns = ['Activity', 'Time Slot', 'Time-in', 'Time-out', 'Status'];

		$pdf->SetFont('Arial', 'B', 11);
		$max_widths = [];
		foreach ($columns as $col) {
			$max_widths[] = $pdf->GetStringWidth($col) + 6;
		}

		foreach ($grouped as $organizer => $activities) {
			foreach ($activities as $a) {
				$timeIns = explode(',', $a->all_time_in);
				$timeOuts = explode(',', $a->all_time_out);
				$slotNames = explode(',', $a->slot_name);

				$totalSlots = count($slotNames);

				for ($i = 0; $i < $totalSlots; $i++) {
					$vals = [
						$a->activity_title,
						$slotNames[$i] ?? 'No Data',
						isset($timeIns[$i]) && $timeIns[$i] !== '' ? date("M d, Y g:i A", strtotime($timeIns[$i])) : 'No Data',
						isset($timeOuts[$i]) && $timeOuts[$i] !== '' ? date("M d, Y g:i A", strtotime($timeOuts[$i])) : 'No Data',
						$a->attendance_status
					];

					foreach ($vals as $j => $val) {
						$w = $pdf->GetStringWidth($val) + 6;
						if ($w > $max_widths[$j]) $max_widths[$j] = $w;
					}
				}
			}
		}

		$page_width = 190;
		$total_width = array_sum($max_widths);

		if ($total_width < $page_width) {
			$remaining = $page_width - $total_width;
			$add_per_col = $remaining / count($max_widths);
			foreach ($max_widths as &$w) {
				$w += $add_per_col;
			}
			unset($w);
		} elseif ($total_width > $page_width) {
			$scale = $page_width / $total_width;
			foreach ($max_widths as &$w) {
				$w *= $scale;
			}
			unset($w);
		}

		$pdf->SetFont('Arial', '', 11);
		foreach ($grouped as $organizer => $activities) {
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->SetFillColor(100, 149, 237); // Cornflower Blue (darker blue)

			$pdf->SetTextColor(0);
			$pdf->Cell($page_width, 10, $organizer, 1, 1, 'L', true);

			$pdf->SetFont('Arial', 'B', 11);
			$pdf->SetFillColor(200, 230, 255); // Lighter blue for headers
			$pdf->SetTextColor(0);
			foreach ($columns as $i => $col) {
				$pdf->Cell($max_widths[$i], 8, $col, 1, 0, 'C', true);
			}
			$pdf->Ln();

			$pdf->SetFont('Arial', '', 11);
			foreach ($activities as $a) {
				$timeIns = explode(',', $a->all_time_in);
				$timeOuts = explode(',', $a->all_time_out);
				$slotNames = explode(',', $a->slot_name);

				$totalSlots = count($slotNames);

				for ($i = 0; $i < $totalSlots; $i++) {
					$time_in = isset($timeIns[$i]) && $timeIns[$i] !== '' ? date("M d, Y g:i A", strtotime($timeIns[$i])) : 'No Data';
					$time_out = isset($timeOuts[$i]) && $timeOuts[$i] !== '' ? date("M d, Y g:i A", strtotime($timeOuts[$i])) : 'No Data';

					$pdf->SetTextColor(0);
					$pdf->Cell($max_widths[0], 7, $a->activity_title, 1);
					$pdf->Cell($max_widths[1], 7, $slotNames[$i] ?? 'No Data', 1);
					$pdf->Cell($max_widths[2], 7, $time_in, 1);
					$pdf->Cell($max_widths[3], 7, $time_out, 1);

					// Set color only for status
					switch ($a->attendance_status) {
						case 'Present':
							$pdf->SetTextColor(0, 128, 0); // Green
							break;
						case 'Excused':
							$pdf->SetTextColor(0, 0, 255); // Blue
							break;
						case 'Absent':
							$pdf->SetTextColor(255, 0, 0); // Red
							break;
						case 'Incomplete':
							$pdf->SetTextColor(255, 165, 0); // Orange
							break;
						default:
							$pdf->SetTextColor(0);
					}

					$pdf->Cell($max_widths[4], 7, $a->attendance_status, 1, 0, 'C');
					$pdf->Ln();
				}
			}

			$pdf->Ln(5);
		}

		$pdf->Output('I', 'Attendance_History_Grouped.pdf');
	}











	public function list_attendees() {}


	// SUMMARY OF FINES
	public function summary_fines()
	{
		$data['title'] = 'Summary of Fines';
		$student_id = $this->session->userdata('student_id');

		$this->load->model('Student_model');

		// Get student info
		$data['users'] = $this->Student_model->get_student($student_id);

		// Get student's fines summary and individual fines
		$data['fines_summary'] = $this->Student_model->get_fines_summary_by_student($student_id);
		$data['fines'] = $this->Student_model->get_fines_by_student($student_id);


		// Pass student_id to the view
		$data['student_id'] = $student_id;


		// ✅ Load all time slots and organize them into a lookup
		// $data['slot_lookup'] = $this->Student_model->get_time_slot_lookup();

		// Load views
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

		// Handle receipt upload
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

		// Check for existing summary
		$this->db->where([
			'student_id' => $student_id,
			'organizer' => $organizer,
			'fines_status !=' => 'Paid'
		]);
		$existing_summary = $this->db->get('fines_summary')->row();

		if ($existing_summary) {
			// Update existing summary
			$update_data = [
				'total_fines' => $total_fines,
				'fines_status' => 'Pending',
				'mode_payment' => $mode_payment,
				'reference_number_students' => $reference_number_students,
				'receipt' => $receipt_filename,
				'last_updated' => date('Y-m-d H:i:s'),
			];

			$this->db->where('summary_id', $existing_summary->summary_id);
			$this->db->update('fines_summary', $update_data);

			$fine_summary_id = $existing_summary->summary_id;
		} else {
			// Optional: handle error if summary doesn't exist
			echo json_encode(['status' => 'error', 'message' => 'No existing fine summary found.']);
			return;
		}

		// Get student name for notification
		$this->db->select('first_name, last_name');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$student = $this->db->get()->row();
		$student_name = $student ? $student->first_name . ' ' . $student->last_name : 'A student';

		$notification_message = $student_name . ' submitted a fine payment to ' . $organizer . '.';

		// Send notification
		$organizer_info = $this->Notification_model->get_organizer_role_info($organizer);

		if (!$organizer_info) {
			log_message('error', 'Organizer not recognized for notification: ' . $organizer);
		} else {
			if ($organizer_info['type'] === 'admin') {
				foreach ($organizer_info['admin_student_ids'] as $admin_id) {
					$this->Notification_model->add_notification(
						null,
						$student_id,
						'fine_payment_uploaded',
						$fine_summary_id,
						$notification_message,
						$admin_id,
						base_url('admin/list-fines/'),
						null

					);
				}
			} else {
				foreach ($organizer_info['officer_student_ids'] as $officer_id) {
					$this->Notification_model->add_notification(
						null,
						$student_id,
						'fine_payment_uploaded',
						$fine_summary_id,
						$notification_message,
						null,
						base_url('officer/list-fines/'),
						$officer_id
					);
				}
			}
		}

		echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully.']);
	}



	//PAY SUMMARY OF FINES END



	public function export_fines_pdf($student_id)
	{
		$this->load->model('Student_model');
		$this->load->library('pdf');

		$student_info = $this->Student_model->get_student_info($student_id);
		$fines = $this->Student_model->get_fines_by_student($student_id);

		// Group fines by organizer and activity title + date + slot_name
		$grouped = [];
		foreach ($fines as $fine) {
			$organizer = $fine['organizer'] ?? 'Unknown Organizer';
			$key = $fine['activity_title'] . '|' . $fine['start_date'] . '|' . $fine['slot_name'];
			$grouped[$organizer][$key][] = $fine;
		}

		$pdf = new PDF('P', 'mm', 'A4');
		$pdf->SetMargins(10, 10, 10);
		$pdf->AddPage();

		// Title
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->SetTextColor(0);
		$pdf->Cell(0, 10, 'Summary of Fines', 0, 1, 'C');
		$pdf->Ln(5);

		// Student Info Section
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(100, 149, 237);
		$pdf->SetTextColor(0);
		$pdf->Cell(40, 8, 'Student ID:', 1, 0, 'L', true);
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Cell(0, 8, $student_info['student_id'] ?? 'N/A', 1, 1, 'L', true);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(100, 149, 237);
		$pdf->Cell(40, 8, 'Name:', 1, 0, 'L', true);
		$pdf->SetFont('Arial', '', 12);
		$full_name = trim(($student_info['first_name'] ?? '') . ' ' . ($student_info['middle_name'] ?? '') . ' ' . ($student_info['last_name'] ?? ''));
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Cell(0, 8, $full_name ?: 'N/A', 1, 1, 'L', true);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetFillColor(100, 149, 237);
		$pdf->Cell(40, 8, 'Department:', 1, 0, 'L', true);
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Cell(0, 8, $student_info['dept_name'] ?? 'N/A', 1, 1, 'L', true);

		$pdf->Ln(8);

		$columns = ['Activity', 'Date', 'Slot', 'Amount', 'Status'];
		$pdf->SetFont('Arial', 'B', 11);
		$max_widths = [];
		foreach ($columns as $col) {
			$max_widths[] = $pdf->GetStringWidth($col) + 6;
		}

		// Calculate max widths
		foreach ($grouped as $organizer => $activities) {
			foreach ($activities as $key => $fines_list) {
				list($title, $date, $slot) = explode('|', $key);
				foreach ($fines_list as $fine) {
					$vals = [
						$title,
						!empty($date) ? date('Y-m-d', strtotime($date)) : 'N/A',
						$slot,
						'Php ' . number_format($fine['fines_amount'], 2),
						ucfirst(strtolower($fine['attendance_status'] ?? 'Unknown'))
					];
					foreach ($vals as $i => $val) {
						$w = $pdf->GetStringWidth($val) + 6;
						if ($w > $max_widths[$i]) $max_widths[$i] = $w;
					}
				}
			}
		}

		// Scale column widths to fit page
		$total_width = array_sum($max_widths);
		$page_width = 190;
		if ($total_width < $page_width) {
			$remaining = $page_width - $total_width;
			$add_per_col = $remaining / count($max_widths);
			foreach ($max_widths as &$w) {
				$w += $add_per_col;
			}
			unset($w);
		} elseif ($total_width > $page_width) {
			$scale = $page_width / $total_width;
			foreach ($max_widths as &$w) {
				$w *= $scale;
			}
			unset($w);
		}

		$pdf->SetFont('Arial', '', 11);

		foreach ($grouped as $organizer => $activities) {
			$pdf->Ln(3); // small spacing before new organizer section

			// ORGANIZER label
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->SetFillColor(100, 149, 237);
			$pdf->SetTextColor(0);
			$pdf->Cell($page_width, 10, 'ORGANIZER: ' . $organizer, 1, 1, 'L', true);

			// Table headers
			$pdf->SetFont('Arial', 'B', 11);
			$pdf->SetFillColor(200, 230, 255);
			foreach ($columns as $i => $col) {
				$pdf->Cell($max_widths[$i], 8, $col, 1, 0, 'C', true);
			}
			$pdf->Ln();

			$pdf->SetFont('Arial', '', 11);
			$pdf->SetFillColor(255, 255, 255);
			foreach ($activities as $key => $fines_list) {
				list($title, $date, $slot) = explode('|', $key);
				foreach ($fines_list as $fine) {
					$pdf->SetTextColor(0);
					$pdf->Cell($max_widths[0], 7, $title, 1, 0, 'L', true);
					$pdf->Cell($max_widths[1], 7, !empty($date) ? date('Y-m-d', strtotime($date)) : 'N/A', 1, 0, 'L', true);
					$pdf->Cell($max_widths[2], 7, $slot, 1, 0, 'L', true);
					$pdf->Cell($max_widths[3], 7, 'Php ' . number_format($fine['fines_amount'], 2), 1, 0, 'R', true);

					$attendance = strtolower($fine['attendance_status'] ?? 'unknown');
					switch ($attendance) {
						case 'present':
							$pdf->SetTextColor(0, 128, 0);
							break;
						case 'absent':
							$pdf->SetTextColor(255, 0, 0);
							break;
						case 'incomplete':
							$pdf->SetTextColor(255, 165, 0);
							break;
						default:
							$pdf->SetTextColor(0);
							break;
					}
					$pdf->Cell($max_widths[4], 7, ucfirst($attendance), 1, 1, 'C', true);
				}
			}
		}

		$pdf->Output('I', 'Summary_of_Fines_' . $student_info['last_name'] . '.pdf');
	}


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

		// Fetch form details with fields
		$form_details = $this->student->get_form_details($form_id);

		// Fetch answers for that form and student
		$form_answers = $this->student->get_form_answers($form_id, $student_id);

		// Map answers to form_fields by form_fields_id to merge answers with fields
		$answers_map = [];
		foreach ($form_answers as $answer) {
			$answers_map[$answer->form_fields_id] = $answer->answer;
		}

		// Add 'answer' property to each form field
		foreach ($form_details->form_fields as &$field) {
			$field->answer = isset($answers_map[$field->form_fields_id]) ? $answers_map[$field->form_fields_id] : null;
		}

		$data['form_data'] = $form_details;

		// Fetch user data
		$data['users'] = $this->student->get_student($student_id);

		// Load views
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

		// Get activity details
		$activity_id = $data['activity_id'];
		$activity_name = $this->Notification_model->get_activity_name($activity_id) ?? 'Unknown Activity';
		$organizer = $this->Notification_model->get_activity_organizer($activity_id); // You'll write this method
		$organizer_type = $this->Notification_model->get_activity_organizer_type($activity_id); // You'll write this too
		$sender_student_id = $this->input->post('student_id');

		// Build the notification message and link
		$notification_message = 'submitted an excuse application for "' . $activity_name . '"';


		// Set link based on organizer type
		if ($organizer_type === 'admin') {
			$link = base_url('admin/list-of-excuse-letter/' . $activity_id);
		} else {
			// For 'org' or 'dept' or any officer type
			$link = base_url('officer/list-of-excuse-letter/' . $activity_id);
		}


		// Targeted notification based on organizer type
		switch ($organizer_type) {
			case 'admin':
				$admin_ids = $this->Notification_model->get_admin_student_ids();
				foreach ($admin_ids as $admin_id) {
					$this->Notification_model->add_notification(
						null,
						$sender_student_id,
						'excuse_submitted',
						$activity_id,
						$notification_message,
						$admin_id,
						$link
					);
				}
				break;

			case 'org':
				$org_officer_ids = $this->Notification_model->get_org_officer_ids_by_name($organizer);
				foreach ($org_officer_ids as $officer_id) {
					$this->Notification_model->add_notification(
						null,
						$sender_student_id,
						'excuse_submitted',
						$activity_id,
						$notification_message,
						null,
						$link,
						$officer_id
					);
				}
				break;

			case 'dept':
				$dept_officer_ids = $this->Notification_model->get_dept_officer_ids_by_name($organizer);
				foreach ($dept_officer_ids as $officer_id) {
					$this->Notification_model->add_notification(
						null,
						$sender_student_id,
						'excuse_submitted',
						$activity_id,
						$notification_message,
						null,
						$link,
						$officer_id
					);
				}
				break;

			default:
				log_message('error', "Unknown organizer type for activity $activity_id");
				break;
		}


		// Final success response
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

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Notification_model');
		$this->load->model('Student_model');
	}



	//Create an endpoint that returns the correct notifications based on session role:


	// public function get_notifications()
	// {
	// 	$this->load->model('Notification_model');
	// 	$this->load->model('Student_model');


	// 	$student_id = $this->session->userdata('student_id');
	// 	$user = $this->Student_model->get_student($student_id); // Gets full user row

	// 	if (!$user) {
	// 		echo json_encode([]);
	// 		return;
	// 	}

	// 	$role = $user['role']; // 'Student', 'Admin', or possibly 'Officer'
	// 	$is_officer_dept = ($user['is_officer_dept'] === 'Yes') ? true : false;

	// 	// Check if Org Officer: query student_org table where student_id = $student_id and is_officer = 'Yes'
	// 	$is_org_officer = $this->Notification_model->is_org_officer($student_id);

	// 	// Pass all flags to the model function
	// 	$notifications = $this->Notification_model->get_notifications($student_id, $role, $is_officer_dept, $is_org_officer);

	// 	echo json_encode($notifications);
	// }



	public function get_notifications()
	{
		// Load models (ideally load these once in constructor)
		$this->load->model('Notification_model');
		$this->load->model('Student_model');

		// Get current logged-in student ID
		$student_id = $this->session->userdata('student_id');

		// Get full user record
		$user = $this->Student_model->get_student($student_id);

		// If no user found, return empty JSON array
		if (!$user) {
			echo json_encode([]);
			return;
		}

		// Extract user role and flags
		$role = $user['role'];  // e.g., 'Student', 'Admin', 'Officer'
		$is_officer_dept = ($user['is_officer_dept'] === 'Yes');

		// Check if user is organization officer via Notification_model method
		$is_org_officer = $this->Notification_model->is_org_officer($student_id);

		// Fetch notifications filtered by user role and officer flags
		$notifications = $this->Notification_model->get_notifications(
			$student_id,
			$role,
			$is_officer_dept,
			$is_org_officer
		);

		// Output notifications as JSON
		echo json_encode($notifications);
	}




	public function mark_all_as_read()
	{
		$student_id = $this->input->post('student_id');
		$role = $this->input->post('role');

		if ($role === 'Student') {
			$this->db->where('recipient_student_id', $student_id);
		} else if ($role === 'Admin') {
			$this->db->where('recipient_admin_id', $student_id); // or admin_id if stored differently
		}

		$this->db->update('notifications', ['is_read' => 1]);
		echo json_encode(['status' => 'success']);
	}
}

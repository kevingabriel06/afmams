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


	public function get_notifications()
	{
		$this->load->model('Notification_model');
		$this->load->model('Student_model');

		$student_id = $this->session->userdata('student_id');
		$user = $this->Student_model->get_student($student_id); // Gets full user row

		if (!$user) {
			echo json_encode([]);
			return;
		}

		// Access 'role' from the array
		$role = $user['role']; // 'Student' or 'Admin'

		$notifications = $this->Notification_model->get_notifications($student_id, $role);
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

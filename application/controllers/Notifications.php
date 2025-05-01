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


	public function mark_as_read($notification_id)
	{
		$this->load->model('Notification_model');
		$this->Notification_model->mark_as_read($notification_id);
	}
}

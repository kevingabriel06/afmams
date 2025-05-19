<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	// Insert new notification
	public function add_notification($recipient_id, $sender_id, $type, $reference_id, $message, $recipient_admin_id = null, $link = null, $recipient_officer_id = null)
	{
		// Ensure only one recipient ID is set (priority order or validation)
		if ($recipient_admin_id !== null) {
			$recipient_id = null;
			$recipient_officer_id = null;
		} elseif ($recipient_officer_id !== null) {
			$recipient_id = null;
			$recipient_admin_id = null;
		} else {
			// If student recipient is set, clear admin/officer IDs
			$recipient_admin_id = null;
			$recipient_officer_id = null;
		}

		$data = [
			'recipient_student_id' => $recipient_id,
			'sender_student_id' => $sender_id,
			'type' => $type,
			'reference_id' => $reference_id,
			'message' => $message,
			'link' => $link,
			'is_read' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'recipient_admin_id' => $recipient_admin_id,
			'recipient_officer_id' => $recipient_officer_id
		];

		return $this->db->insert('notifications', $data) ? $this->db->insert_id() : false;
	}



	// Fetch Admin User IDs (who have the 'Admin' role) to notify admins
	public function get_admin_student_ids()
	{
		$this->db->select('student_id');
		$this->db->from('users');
		$this->db->where('is_admin', 'Yes'); // Check for the string 'Yes'
		$query = $this->db->get();

		return array_column($query->result_array(), 'student_id');
	}





	//Create a function that fetches notifications based on whether the user is a student or an admin:

	public function get_notifications($user_id, $role, $is_officer_dept = false, $is_org_officer = false)
	{
		$this->db->select('n.*, u.first_name, u.last_name, u.profile_pic');
		$this->db->from('notifications n');
		$this->db->join('users u', 'u.student_id = n.sender_student_id', 'left');

		// Officer check comes FIRST to avoid role overlap with Admin
		if (($role === 'Officer' && ($is_officer_dept || $is_org_officer)) || $is_officer_dept || $is_org_officer) {
			$this->db->where('n.recipient_officer_id', $user_id);
		} elseif ($role === 'Admin') {
			$this->db->where('n.recipient_admin_id', $user_id);
		} elseif ($role === 'Student') {
			$this->db->where('n.recipient_student_id', $user_id);
		} else {
			// No role match — return no notifications
			$this->db->where('1=0');
		}

		$this->db->order_by('n.created_at', 'DESC');

		return $this->db->get()->result();
	}





	public function get_activity_name($activity_id)
	{
		$this->db->select('activity_title');
		$this->db->where('activity_id', $activity_id);
		$query = $this->db->get('activity');

		if ($query->num_rows() > 0) {
			return $query->row()->activity_title;
		}

		return null;
	}


	public function mark_all_as_read($student_id)
	{
		$this->db->where('recipient_student_id', $student_id);
		$this->db->update('notifications', ['is_read' => 1]);
	}

	public function delete_notifications_by_reference($reference_id, $type)
	{
		$this->db->where('reference_id', $reference_id);
		$this->db->where('type', $type);
		return $this->db->delete('notifications');
	}


	public function is_org_officer($student_id)
	{
		$this->db->where('student_id', $student_id);
		$this->db->where('is_officer', 'Yes');
		$query = $this->db->get('student_org');
		return ($query->num_rows() > 0);
	}

	public function is_dept_officer($student_id)
	{

		$this->load->model('Admin_model');
		$user = $Admin_model->get_student($student_id);
		return (isset($user['is_officer_dept']) && $user['is_officer_dept'] === 'Yes');
	}




	public function get_org_officer_ids()
	{
		$this->db->select('student_id');
		$this->db->from('student_org');
		$this->db->where('is_officer', 'Yes');
		$query = $this->db->get();
		return array_column($query->result_array(), 'student_id');
	}

	public function get_dept_officer_ids()
	{
		$this->db->select('student_id');
		$this->db->from('users');
		$this->db->where('is_officer_dept', 'Yes');
		$query = $this->db->get();
		return array_column($query->result_array(), 'student_id');
	}


	public function get_organizer_role_info($organizer_name)
	{
		// Check if organizer is an Admin (from users table with full name match)
		$this->db->select('student_id');
		$this->db->from('users');
		$this->db->where('role', 'Admin');
		$this->db->where("CONCAT(first_name, ' ', last_name) =", $organizer_name);
		$admin = $this->db->get()->row();

		if ($admin) {
			return ['type' => 'admin', 'admin_student_ids' => [$admin->student_id]];
		}

		// Check if organizer is an Organization
		$org = $this->db->get_where('organization', ['org_name' => $organizer_name])->row();
		if ($org) {
			$this->db->select('student_id');
			$this->db->from('student_org');
			$this->db->where('org_id', $org->org_id);
			$this->db->where('is_officer', 'Yes');
			$officers = $this->db->get()->result();
			$officer_ids = array_column($officers, 'student_id');

			return ['type' => 'org', 'officer_student_ids' => $officer_ids];
		}

		// Check if organizer is a Department
		$dept = $this->db->get_where('department', ['dept_name' => $organizer_name])->row();
		if ($dept) {
			$this->db->select('student_id');
			$this->db->from('users');
			$this->db->where('dept_id', $dept->dept_id);
			$this->db->where('is_officer_dept', 'Yes');
			$officers = $this->db->get()->result();
			$officer_ids = array_column($officers, 'student_id');

			return ['type' => 'dept', 'officer_student_ids' => $officer_ids];
		}

		return null; // Unknown organizer type
	}
}

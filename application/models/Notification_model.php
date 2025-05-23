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


	//for like_post

	public function get_post_organizer_type($post_id)
	{
		$this->db->select('org_id, dept_id');
		$this->db->where('post_id', $post_id);
		$post = $this->db->get('post')->row();

		if (!$post) return null;

		if (!empty($post->org_id)) return 'org';
		if (!empty($post->dept_id)) return 'dept';

		// If neither org nor dept, then admin post
		return 'admin';
	}

	public function get_post_organizer($post_id)
	{
		$this->db->select('org_id, dept_id');
		$this->db->where('post_id', $post_id);
		$post = $this->db->get('post')->row();

		if (!$post) return null;

		if (!empty($post->org_id)) return $post->org_id;
		if (!empty($post->dept_id)) return $post->dept_id;

		return null; // admin posts have no org or dept id
	}


	public function get_org_officer_ids_by_id($org_id)
	{
		$this->db->select('student_id');
		$this->db->from('student_org');
		$this->db->where('org_id', $org_id);
		$this->db->where('is_officer', 'Yes');

		$query = $this->db->get();
		return array_column($query->result_array(), 'student_id');
	}

	public function get_dept_officer_ids_by_id($dept_id)
	{
		$this->db->select('student_id');
		$this->db->from('users');
		$this->db->where('dept_id', $dept_id);
		$this->db->where('is_officer_dept', 'Yes');
		$this->db->where('role', 'Officer');

		$query = $this->db->get();
		return array_column($query->result_array(), 'student_id');
	}



	//for like post end


	//used in excuse application, register notifications 

	public function get_activity_organizer($activity_id)
	{
		$this->db->select('organizer');
		$this->db->from('activity'); // ✅ Correct table name
		$this->db->where('activity_id', $activity_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row()->organizer;
		}
		return null;
	}


	public function get_activity_organizer_type($activity_id)
	{
		$this->db->select('organizer');
		$this->db->from('activity');
		$this->db->where('activity_id', $activity_id);
		$organizer = $this->db->get()->row('organizer');

		if (!$organizer) return null;

		if (strtolower(trim($organizer)) === 'student parliament') {
			return 'admin';
		}

		// Check if it's a department
		$this->db->where('dept_name', $organizer);
		if ($this->db->get('department')->num_rows() > 0) {
			return 'dept';
		}

		// Check if it's an organization
		$this->db->where('org_name', $organizer);
		if ($this->db->get('organization')->num_rows() > 0) {
			return 'org';
		}

		return null; // Unknown organizer
	}

	public function get_org_officer_ids_by_name($org_name)
	{
		$this->db->select('student_id');
		$this->db->from('student_org so');
		$this->db->join('organization o', 'so.org_id = o.org_id');
		$this->db->where('o.org_name', $org_name);
		$this->db->where('so.is_officer', 'Yes');

		$query = $this->db->get();
		return array_column($query->result_array(), 'student_id');
	}


	public function get_dept_officer_ids_by_name($dept_name)
	{
		$this->db->select('student_id');
		$this->db->from('users u');
		$this->db->join('department d', 'u.dept_id = d.dept_id');
		$this->db->where('d.dept_name', $dept_name);
		$this->db->where('u.is_officer_dept', 'Yes');
		$this->db->where('u.role', 'Officer');

		$query = $this->db->get();
		return array_column($query->result_array(), 'student_id');
	}








	// excuse notifs  end



	//For pay_fines notification 
	public function get_organizer_role_info($organizer_name)
	{
		// Check if organizer is an Admin
		// If organizer is Student Parliament (or admin group), get all admins
		if (strtolower($organizer_name) === 'student parliament') {
			$this->db->select('student_id');
			$this->db->from('users');
			$this->db->where('role', 'Admin');
			$admins = $this->db->get()->result();
			$admin_ids = array_column($admins, 'student_id');

			return ['type' => 'admin', 'admin_student_ids' => $admin_ids];
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
			$this->db->where('role', 'Officer'); // Added role check
			$this->db->where('is_officer_dept', 'Yes');
			$officers = $this->db->get()->result();
			$officer_ids = array_column($officers, 'student_id');

			return ['type' => 'dept', 'officer_student_ids' => $officer_ids];
		}

		return null; // Unknown organizer
	}



	public function get_organizer_info_by_student_id($student_id)
	{
		// Get role of the user (e.g. admin, officer, etc.)
		$this->db->select('role');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$user = $this->db->get()->row();

		if (!$user || !$user->role) {
			return null;
		}

		$role = $user->role;

		// Prepare the response to match your notification logic
		$organizer_info = ['type' => $role];

		if ($role === 'admin') {
			$organizer_info['admin_student_ids'] = [$student_id];
		} elseif ($role === 'officer') {
			$organizer_info['officer_student_ids'] = [$student_id];
		} else {
			return null; // Not an admin or officer
		}

		return $organizer_info;
	}


	//For pay_fines notification end
}

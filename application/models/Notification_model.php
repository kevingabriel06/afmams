<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	// Insert new notification
	public function add_notification($recipient_id, $sender_id, $type, $reference_id, $message, $recipient_admin_id = null, $link = null)
	{
		$data = [
			'recipient_student_id' => $recipient_id,  // Using student_id
			'sender_student_id' => $sender_id,  // Using student_id
			'type' => $type,
			'reference_id' => $reference_id,
			'message' => $message,
			'link'         => $link, // Optional but saved
			'is_read' => 0, // Unread by default
			'created_at' => date('Y-m-d H:i:s'),
			'recipient_admin_id' => $recipient_admin_id
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

		if ($role === 'Student') {
			$this->db->where('n.recipient_student_id', $user_id);
		} elseif ($role === 'Admin') {
			$this->db->where('n.recipient_admin_id', $user_id);
		} elseif ($role === 'Officer' || $is_officer_dept || $is_org_officer) {
			// Assuming you have a recipient_officer_id column or you want to check either admin or student notifications that are officer related
			$this->db->where('n.recipient_officer_id', $user_id);
		} else {
			// no notifications for unknown roles
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
}

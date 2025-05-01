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

	public function get_notifications($student_id, $role)
	{
		if ($role === 'Student') {
			$this->db->where('n.recipient_student_id', $student_id);
		} elseif ($role === 'Admin') {
			$this->db->where('n.recipient_admin_id', $student_id);
		}

		$this->db->select('n.*, u.first_name, u.last_name, u.profile_pic');
		$this->db->from('notifications n');
		$this->db->join('users u', 'u.student_id = n.sender_student_id', 'left');
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
}

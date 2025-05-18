<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Student_model extends CI_Model
{

	public function __construct()
	{
		$this->load->database();
	}


	// CHECKING OF ROLES FOR DISPLAYING DIFFERENT PARTS OF SYSTEM
	public function get_student($student_id)
	{
		$student_id = $this->session->userdata('student_id');

		return $this->db->get_where('users', ['student_id' => $student_id])->row_array();
	}

	// HOME

	// FECTCHING WHO IS THE AUTHOR OF THE POST
	public function get_user()
	{

		$student_id = $this->session->userdata('student_id');

		if ($student_id) {
			$this->db->select('*');
			$this->db->from('users');
			$this->db->where('student_id', $student_id);

			$query = $this->db->get();

			if ($query->num_rows() > 0) {
				return $query->row();
			} else {
				return null;
			}
		} else {
			// If student_id is not set in the session
			return null;
		}
	}

	public function get_all_posts($limit, $offset)
	{
		// 1. Get the logged-in student's ID
		$student_id = $this->session->userdata('student_id');

		// 2. Fetch user's dept_id
		$user = $this->db->select('dept_id')
			->from('users')
			->where('student_id', $student_id)
			->get()
			->row();

		$user_dept_id = $user ? $user->dept_id : null;

		// 3. Fetch all orgs that the student belongs to
		$orgs = $this->db->select('org_id')
			->from('student_org')
			->where('student_id', $student_id)
			->get()
			->result();

		// Convert to a flat array of org_ids
		$org_ids = array_map(function ($org) {
			return $org->org_id;
		}, $orgs);

		// 4. Main query
		$this->db->select('*');
		$this->db->from('post');
		$this->db->join('users', 'post.student_id = users.student_id', 'left');
		$this->db->join('department', 'department.dept_id = post.dept_id', 'left');
		$this->db->join('organization', 'organization.org_id = post.org_id', 'left');

		// 5. Filter: show public OR private posts that match dept/org
		$this->db->group_start();
		$this->db->where('post.privacy', 'public');

		if (!empty($org_ids) || $user_dept_id) {
			$this->db->or_group_start();
			$this->db->where('post.privacy', 'private');

			$this->db->group_start();
			if (!empty($org_ids)) {
				$this->db->where_in('post.org_id', $org_ids);
			}
			if ($user_dept_id) {
				$this->db->or_where('post.dept_id', $user_dept_id);
			}
			$this->db->group_end();
			$this->db->group_end();
		}

		$this->db->group_end();

		$this->db->order_by('post.post_id', 'DESC');
		$this->db->limit($limit, $offset);

		$query = $this->db->get();
		return $query->result();
	}

	public function get_registration_status_for_activity($activity_id)
	{
		// Get the currently logged-in student's ID
		$student_id = $this->session->userdata('student_id');

		// Fetch the registration status for the student and the specific activity
		$this->db->select('registrations.registration_status');
		$this->db->from('registrations');
		$this->db->where('registrations.student_id', $student_id);
		$this->db->where('registrations.activity_id', $activity_id); // Filter by specific activity

		// Execute the query
		$query = $this->db->get();

		// Check if any registration exists for this student and activity
		if ($query->num_rows() > 0) {
			return $query->row()->registration_status; // Return the registration status
		} else {
			return null; // If no registration found, return null
		}
	}

	// ATTENDEES STATUS
	public function get_attendees_free_event($activity_id)
	{
		// Get the currently logged-in student's ID
		$student_id = $this->session->userdata('student_id');

		// Fetch the registration status for the student and the specific activity
		$this->db->select('attendees.attendees_status');
		$this->db->from('attendees');
		$this->db->where('attendees.student_id', $student_id);
		$this->db->where('attendees.activity_id', $activity_id); // Filter by specific activity

		// Execute the query
		$query = $this->db->get();

		// Check if any registration exists for this student and activity
		if ($query->num_rows() > 0) {
			return $query->row()->attendees_status; // Return the registration status
		} else {
			return null; // If no registration found, return null
		}
	}

	public function get_shared_activities($limit, $offset)
	{
		// Get logged-in student ID
		$student_id = $this->session->userdata('student_id');

		// Get the student's department and name
		$user = $this->db->select('users.dept_id, department.dept_name')
			->from('users')
			->join('department', 'users.dept_id = department.dept_id')
			->where('student_id', $student_id)
			->get()
			->row();

		$user_dept_id = $user ? $user->dept_id : null;
		$user_dept_name = $user ? $user->dept_name : null;

		// Get all orgs the student belongs to
		$orgs = $this->db->select('student_org.org_id, organization.org_name')
			->from('student_org')
			->join('organization', 'student_org.org_id = organization.org_id')
			->where('student_id', $student_id)
			->get()
			->result();

		$org_ids = array_map(function ($org) {
			return $org->org_id;
		}, $orgs);

		$org_names = array_map(function ($org) {
			return $org->org_name;
		}, $orgs);

		// Start the query for activities
		$this->db->select('*');
		$this->db->from('activity');
		$this->db->where('is_shared', 'Yes');  // Ensure the activity is shared

		// Filter for public activities: organizer = "Student Parliament" and audience = "All"
		$this->db->group_start();
		$this->db->where('organizer', 'Student Parliament');
		$this->db->where('audience', 'All');
		$this->db->group_end();

		// Filter for private activities: audience matches the student's department or organization
		$this->db->or_group_start();
		$this->db->or_where("FIND_IN_SET(" . $this->db->escape($user_dept_name) . ", audience) >", 0);  // Check if audience matches the department 
		if (!empty($org_names)) {
			foreach ($org_names as $org_name) {
				$this->db->or_where("FIND_IN_SET(" . $this->db->escape($org_name) . ", audience) >", 0);
			}			  // Check if audience matches the organizations the student belongs to
		}
		$this->db->group_end();

		// Add pagination (LIMIT and OFFSET)
		$this->db->limit($limit, $offset);

		$query = $this->db->get();
		return $query->result();
	}

	// UPCOMING ACTIVITY
	public function get_activities_upcoming()
	{
		// Get logged-in student ID
		$student_id = $this->session->userdata('student_id');

		// Get the student's department and name
		$user = $this->db->select('users.dept_id, department.dept_name')
			->from('users')
			->join('department', 'users.dept_id = department.dept_id')
			->where('student_id', $student_id)
			->get()
			->row();

		$user_dept_name = $user ? $user->dept_name : null;

		// Get all orgs the student belongs to
		$orgs = $this->db->select('student_org.org_id, organization.org_name')
			->from('student_org')
			->join('organization', 'student_org.org_id = organization.org_id')
			->where('student_id', $student_id)
			->get()
			->result();

		$org_names = array_map(function ($org) {
			return $org->org_name;
		}, $orgs);

		// Fetch upcoming activities
		$this->db->select('*');
		$this->db->from('activity');
		$this->db->where('status', 'Upcoming');

		// Check if the audience is the student's department or if the student belongs to the organization of the activity
		$this->db->group_start();
		$this->db->or_where('audience', 'All'); // Student Parliament activities
		$this->db->or_where('audience', $user_dept_name); // Department-specific activities
		//$this->db->or_where_in('audience', $org_names); // Activities related to the student's organizations
		$this->db->group_end();

		// Execute the query
		$query = $this->db->get();
		return $query->result();
	}

	// LIKES FUNCTIONALITY
	public function like_post($post_id, $student_id)
	{
		// Insert a like into the database
		$data = [
			'post_id' => $post_id,
			'student_id' => $student_id
		];
		$this->db->insert('likes', $data); // Assuming a 'likes' table exists
		$this->update_like_count($post_id); // Update like count in the post table
	}

	public function unlike_post($post_id, $student_id)
	{
		// Remove the like from the database
		$this->db->delete('likes', ['post_id' => $post_id, 'student_id' => $student_id]);
		$this->update_like_count($post_id); // Update like count in the post table
	}

	public function get_post_likers($post_id)
	{
		$this->db->select('users.student_id, users.first_name, users.last_name');
		$this->db->from('likes');
		$this->db->join('users', 'users.student_id = likes.student_id');
		$this->db->where('likes.post_id', $post_id);
		$query = $this->db->get();
		return $query->result(); // array of objects
	}





	//FOR NOTIFICATIONS
	public function get_post_by_id($post_id)
	{
		return $this->db->select('post_id, student_id') // Add other fields if needed
			->from('post') // Make sure this is your actual posts table name
			->where('post_id', $post_id)
			->get()
			->row();
	}


	// ADDING OF COMMENTS
	public function add_comment($data = null)
	{
		$data = [
			'post_id' => $this->input->post('post_id'),
			'student_id' => $this->session->userdata('student_id'),
			'content' => $this->input->post('comment'),
			'created_at' => date('Y-m-d H:i:s') // Use PHP's date() function directly
		];

		return $this->db->insert('comment', $data);  // Assuming 'comments' is the correct table name
	}

	public function get_likes_by_post($post_id)
	{
		// Query to get the users who liked the post
		$this->db->select('users.first_name, users.last_name, profile_pic');
		$this->db->from('likes');
		$this->db->join('users', 'users.student_id = likes.student_id');
		$this->db->where('likes.post_id', $post_id);
		$query = $this->db->get();

		// Return the result as an array of objects
		return $query->result();
	}

	// GETTING LIKE COUNT
	public function get_like_count($post_id)
	{
		// Get the current like count for the post
		$this->db->where('post_id', $post_id);
		$this->db->from('likes');
		return $this->db->count_all_results();
	}

	// COUNTING THE COMMENTS
	public function get_comment_count($post_id)
	{
		$this->db->where('post_id', $post_id);
		$this->db->from('comment'); // Ensure this matches your table name
		return $this->db->count_all_results();
	}

	// CHECK THE USER IF ALREADY LIKE THE POST
	public function user_has_liked($post_id, $student_id)
	{
		// Check if the user has already liked the post
		$this->db->where('post_id', $post_id);
		$this->db->where('student_id', $student_id);
		$query = $this->db->get('likes');
		return $query->num_rows() > 0;
	}

	// UPDATING LIKE COUNT
	public function update_like_count($post_id)
	{
		// Update the like count for the post in the 'posts' table
		$like_count = $this->get_like_count($post_id);
		$this->db->where('post_id', $post_id);
		$this->db->update('post', ['like_count' => $like_count]);
	}

	// FETCH COMMENTS BY POST ID
	public function get_comments_by_post($post_id)
	{
		$this->db->select('comment.*, users.*');
		$this->db->from('comment');
		$this->db->join('users', 'users.student_id = comment.student_id');
		$this->db->where('comment.post_id', $post_id);
		$this->db->order_by('comment.created_at', 'DESC');

		return $this->db->get()->result(); // Return comments
	}

	public function get_latest_comment($post_id)
	{
		$this->db->select('c.*, s.*');
		$this->db->from('comment c');
		$this->db->join('users s', 'c.student_id = s.student_id', 'left');
		$this->db->where('c.post_id', $post_id);
		$this->db->order_by('c.created_at', 'DESC'); // Assuming you have a created_at column
		$this->db->limit(1); // Get only the latest comment

		$query = $this->db->get();
		return $query->row(); // Return a single comment object
	}

	// START REGISTRATION
	public function insert_registration($data)
	{
		return $this->db->insert('registrations', $data);
	}

	//POST LIKES, COMMENTS START


	// // Insert like for a post and update the like_count in the post table
	// public function like_post($post_id, $student_id)
	// {
	//     // Check if the user already liked the post
	//     $this->db->where('post_id', $post_id);
	//     $this->db->where('student_id', $student_id);
	//     $query = $this->db->get('likes');

	//     if ($query->num_rows() == 0) {
	//         // Insert a like if the user hasn't already liked the post
	//         $data = array(
	//             'post_id' => $post_id,
	//             'student_id' => $student_id,
	//             'liked_at' => date('Y-m-d H:i:s')
	//         );
	//         $this->db->insert('likes', $data);

	//         // Increment the like_count in the 'post' table
	//         $this->db->set('like_count', 'like_count + 1', FALSE)
	//             ->where('post_id', $post_id)
	//             ->update('post');
	//     }
	// }



	//POST LIKES COMMENTS END

	// ATTENDANCE HISTORY
	public function get_attendance_history()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('attendance.*, activity.*, activity_time_slots.*');
		$this->db->from('attendance');
		$this->db->join('activity', 'activity.activity_id = attendance.activity_id');
		$this->db->join('activity_time_slots', 'activity_time_slots.activity_id = attendance.activity_id', 'right');
		$this->db->where('attendance.student_id', $student_id);

		$query = $this->db->get();
		return $query->result();
	}

	public function get_attendance($student_id)
	{

		$student_id = $this->session->userdata('student_id');

		$this->db->select('');
		$this->db->from('attendance');
		$this->db->join('activity', 'activity.activity_id = attendance.activity_id');
		$this->db->join('activity_time_slots', 'activity_time_slots.timeslot_id = attendance.timeslot_id');
		$this->db->where('attendance.student_id', $student_id);

		$query = $this->db->get();
		return $query->result();
	}

	// Helper function to combine multiple slot statuses into one
	private function combineStatus($current, $new)
	{
		if ($current === 'Present' && $new === 'Present') return 'Present';
		if ($current === 'Absent' && $new === 'Absent') return 'Absent';
		return 'Incomplete';
	}



	// LIST OF ACTIVITY
	public function get_activities_for_users()
	{
		// Get logged-in student ID
		$student_id = $this->session->userdata('student_id');

		// Get the student's department and name
		$user = $this->db->select('users.dept_id, department.dept_name')
			->from('users')
			->join('department', 'users.dept_id = department.dept_id')
			->where('student_id', $student_id)
			->get()
			->row();

		$user_dept_name = $user ? $user->dept_name : null;

		// Get all orgs the student belongs to
		$orgs = $this->db->select('student_org.org_id, organization.org_name')
			->from('student_org')
			->join('organization', 'student_org.org_id = organization.org_id')
			->where('student_id', $student_id)
			->get()
			->result();

		$org_names = array_map(function ($org) {
			return $org->org_name;
		}, $orgs);

		// Fetch upcoming activities
		$this->db->select('activity.*, MIN(ats.date_time_in) as first_schedule');
		$this->db->from('activity');
		$this->db->join('activity_time_slots ats', 'activity.activity_id = ats.activity_id', 'LEFT');

		// Filter by audience
		$this->db->group_start();
		$this->db->or_where('activity.audience', 'All'); // Student Parliament activities
		if ($user_dept_name) {
			$this->db->or_where('activity.audience', $user_dept_name); // Department-specific activities
		}
		if (!empty($org_names)) {
			$this->db->or_where_in('activity.audience', $org_names); // Activities related to the student's organizations
		}
		$this->db->group_end();

		// Group by activity to allow aggregation
		$this->db->group_by('activity.activity_id');

		// Execute the query
		$query = $this->db->get();
		return $query->result();
	}

	// FETCHING SPECIFIC ACTIVITY USING ACTIVITY ID 
	public function get_activity($activity_id)
	{
		$this->db->select('a.*, MIN(ats.date_time_in) as first_schedule, MAX(ats.date_time_out) as last_schedule');
		$this->db->from('activity a');
		$this->db->join('activity_time_slots ats', 'ats.activity_id = a.activity_id', 'left');
		$this->db->group_by('a.activity_id');

		if ($activity_id !== null) {
			$this->db->where('a.activity_id', $activity_id);
			return $this->db->get()->row_array(); // Fetch single record
		}

		$query = $this->db->get();
		return $query->result(); // Fetch multiple records
	}

	// SCHEDULED OF ACTIVITY
	public function get_schedule($activity_id)
	{
		$this->db->select('*');
		$this->db->from('activity_time_slots ats');
		$this->db->where('ats.activity_id', $activity_id);

		$query = $this->db->get();
		return $query->result_array(); // Fetch all schedules
	}

	// COUNT ATTENDEES (FREE EVENT)
	public function count_attendees($activity_id)
	{
		$this->db->select('COUNT(*) as count');
		$this->db->from('attendees');
		$this->db->where('activity_id', $activity_id);
		$this->db->where('attendees_status', 'Attending');

		$query = $this->db->get();
		return $query->row()->count;
	}

	// COUNT REGISTERED
	public function count_registered($activity_id)
	{
		$this->db->select('COUNT(*) as count');
		$this->db->from('registrations');
		$this->db->where('activity_id', $activity_id);
		$this->db->where('registration_status', 'Verified');

		$query = $this->db->get();
		return $query->row()->count;
	}

	//EXCUSE APPLICATION

	//get users table data to pre-populate some fields

	public function get_student_details()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('*');
		$this->db->from('users');
		$this->db->join('department', 'users.dept_id = department.dept_id', 'left');
		$this->db->where('student_id', $student_id);

		$query = $this->db->get();
		return $query->row(); // Use ->row_array() if you want an associative array
	}

	public function applications()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('activity.*, ea.*, ea.status AS exStatus, ea.document, ea.remarks');
		$this->db->from('excuse_application ea');
		$this->db->join('activity', 'activity.activity_id = ea.activity_id', 'inner');
		$this->db->where('ea.student_id', $student_id);

		$query = $this->db->get();
		return $query->result();
	}


	public function get_activities_for_users_excuse_application()
	{
		$student_id = $this->session->userdata('student_id');

		// Get the student's department and name
		$user = $this->db->select('users.dept_id, department.dept_name')
			->from('users')
			->join('department', 'users.dept_id = department.dept_id')
			->where('student_id', $student_id)
			->get()
			->row();

		$user_dept_name = $user ? $user->dept_name : null;

		// Get all orgs the student belongs to
		$orgs = $this->db->select('student_org.org_id, organization.org_name')
			->from('student_org')
			->join('organization', 'student_org.org_id = organization.org_id')
			->where('student_id', $student_id)
			->get()
			->result();

		$org_names = array_map(function ($org) {
			return $org->org_name;
		}, $orgs);

		// Step 1: Get activity IDs where the student already submitted excuse
		$submitted_excuses = $this->db->select('activity_id')
			->from('excuse_application')
			->where('student_id', $student_id)
			->get()
			->result();

		$submitted_activity_ids = array_map(function ($row) {
			return $row->activity_id;
		}, $submitted_excuses);

		// Step 2: Fetch upcoming activities
		$this->db->select('activity.*');
		$this->db->from('activity');

		$this->db->where('activity.status', 'Upcoming');

		// Filter by audience
		$this->db->group_start();
		$this->db->or_where('activity.audience', 'All');
		if ($user_dept_name) {
			$this->db->or_where('activity.audience', $user_dept_name);
		}
		if (!empty($org_names)) {
			$this->db->or_where_in('activity.audience', $org_names);
		}
		$this->db->group_end();

		// Exclude activities where the student already submitted an excuse
		if (!empty($submitted_activity_ids)) {
			$this->db->where_not_in('activity.activity_id', $submitted_activity_ids);
		}

		$query = $this->db->get();
		return $query->result();
	}



	public function insert_application($data)
	{
		// Data should include 'student_id', 'activity_id', 'subject', 'content', 'document', 'status'
		$this->db->insert('excuse_application', $data); // Insert the data into the 'applications' table
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	//EXCUSE APPLICATION DROPDOWN

	public function get_activities_for_user($student_id, $dept_id)
	{
		// Get the organizations the student belongs to
		$this->db->select('org_id');
		$this->db->from('student_org');
		$this->db->where('student_id', $student_id);
		$query = $this->db->get();

		$org_ids = array_column($query->result_array(), 'org_id'); // Get org IDs as an array

		// Subquery: Get private activities where org_id and dept_id are NULL
		$this->db->select('activity_id');
		$this->db->from('activity');
		$this->db->where('dept_id IS NULL');
		$this->db->where('org_id IS NULL');
		$this->db->where('privacy', 'private');
		$subquery = $this->db->get_compiled_select(); // Generates SQL string

		// Fetch activities
		$this->db->select('a.activity_id, a.activity_title, a.start_date, a.end_date, a.registration_deadline, a.registration_fee, 
                        a.am_in, a.am_out, a.pm_in, a.pm_out, a.description, a.activity_image, a.privacy, 
                        a.org_id, a.dept_id, a.status, a.is_shared, a.fines, a.attendee_count,
                        COALESCE(d.dept_name, o.org_name, "Student Parliament") AS organizer');
		$this->db->from('activity a');
		$this->db->join('department d', 'd.dept_id = a.dept_id', 'left');
		$this->db->join('organization o', 'o.org_id = a.org_id', 'left');

		$this->db->group_start();
		// Include public activities (visible to all users)
		$this->db->where('a.privacy', 'public');

		// Include activities where org_id and dept_id are NULL (but only if public)
		$this->db->or_group_start();
		$this->db->where('a.dept_id IS NULL');
		$this->db->where('a.org_id IS NULL');
		$this->db->where('a.privacy', 'public'); // Ensure only public ones are included
		$this->db->group_end();

		// Include activities that match user's department
		$this->db->or_where('a.dept_id', $dept_id);

		// Include activities where user is part of the organization
		if (!empty($org_ids)) {
			$this->db->or_where_in('a.org_id', $org_ids);
		}
		$this->db->group_end();

		// Exclude private activities where both org_id and dept_id are NULL
		$this->db->where("a.activity_id NOT IN ($subquery)", null, false); // Prevent auto-escaping

		// Only include activities that are "Upcoming"
		$this->db->where('a.status', 'Upcoming');  // Only fetch activities with status "Upcoming"

		$query = $this->db->get();
		return $query->result_array(); // Return filtered activities
	}


	//GET EXCUSE APPLICATIONS AND DISPLAY IN THE TABLE
	public function get_excuse_applications($student_id)
	{
		// Select necessary fields including remarks_at
		$this->db->select('ea.*, a.activity_title, ea.remarks_at');
		$this->db->from('excuse_application ea');
		$this->db->join('activity a', 'a.activity_id = ea.activity_id', 'left');
		$this->db->where('ea.student_id', $student_id);
		$query = $this->db->get();
		return $query->result_array();
	}


	//for deleting image when excuse is cancelled
	public function get_excuse_by_id($excuse_id)
	{
		return $this->db->get_where('excuse_application', ['excuse_id' => $excuse_id])->row();
	}


	public function delete_excuse_application($excuse_id)
	{
		$this->db->where('excuse_id', $excuse_id); // use your primary key name
		return $this->db->delete('excuse_application'); // table name
	}








	//ACTIVITY DETAILS

	public function get_activity_details($activity_id)
	{
		$this->db->select('activity.*, department.dept_name, organization.org_name');
		$this->db->from('activity');
		$this->db->join('department', 'activity.dept_id = department.dept_id', 'left');
		$this->db->join('organization', 'activity.org_id = organization.org_id', 'left');
		$this->db->where('activity.activity_id', $activity_id);

		$query = $this->db->get();
		return $query->row(); // Make sure this is an object
	}

	// EVALUATION FORMS
	public function list_forms()
	{
		// Get logged-in student ID
		$student_id = $this->session->userdata('student_id');

		// Get the student's department and name
		$user = $this->db->select('users.dept_id, department.dept_name')
			->from('users')
			->join('department', 'users.dept_id = department.dept_id')
			->where('student_id', $student_id)
			->get()
			->row();

		$user_dept_name = $user ? $user->dept_name : null;

		// Get all orgs the student belongs to
		$orgs = $this->db->select('student_org.org_id, organization.org_name')
			->from('student_org')
			->join('organization', 'student_org.org_id = organization.org_id')
			->where('student_id', $student_id)
			->get()
			->result();

		$org_names = array_map(function ($org) {
			return $org->org_name;
		}, $orgs);

		// Fetch activities with forms and filter by audience
		$this->db->select('
		forms.form_id,
		forms.title AS title,
		forms.start_date_evaluation,
		forms.end_date_evaluation,
		forms.status_evaluation,
		activity.activity_id,
		activity.activity_title AS activity_title,
		activity.audience,
		evaluation_responses.remarks AS response_remarks,
		IF(
			evaluation_responses.remarks IS NOT NULL,
			evaluation_responses.remarks,
			IF(
				NOW() >= forms.start_date_evaluation AND NOW() <= forms.end_date_evaluation,
				"Pending",  
				IF(NOW() > forms.end_date_evaluation, "Missing", "Upcoming")
			)
		) AS remarks
	');


		$this->db->from('activity');
		$this->db->join('forms', 'forms.activity_id = activity.activity_id', 'inner');
		$this->db->join('evaluation_responses', 'evaluation_responses.form_id = forms.form_id AND evaluation_responses.student_id = ' . $this->db->escape($student_id), 'left');


		// Audience filters
		$this->db->group_start();
		$this->db->or_where('activity.audience', 'All');
		if ($user_dept_name) {
			$this->db->or_where('activity.audience', $user_dept_name);
		}
		if (!empty($org_names)) {
			$this->db->or_where_in('activity.audience', $org_names);
		}
		$this->db->group_end();

		$query = $this->db->get();
		return $query->result();
	}

	public function get_open_forms_for_student_and_unanswered()
	{
		// Get logged-in student ID
		$student_id = $this->session->userdata('student_id');

		// Get the student's department
		$user = $this->db->select('users.dept_id, department.dept_name')
			->from('users')
			->join('department', 'users.dept_id = department.dept_id', 'left')
			->where('users.student_id', $student_id)
			->get()
			->row();

		$user_dept_name = $user ? $user->dept_name : null;

		// Get all organizations the student belongs to
		$orgs = $this->db->select('student_org.org_id, organization.org_name')
			->from('student_org')
			->join('organization', 'student_org.org_id = organization.org_id', 'left')
			->where('student_org.student_id', $student_id)
			->get()
			->result();

		$org_names = array_map(function ($org) {
			return $org->org_name;
		}, $orgs);

		// Fetch open forms where the student has not answered yet or where the response is Pending
		$this->db->select(' forms.*, activity.*, 
    IF(
        forms.status_evaluation = "Completed" AND evaluation_responses.remarks IS NULL, 
        "Missing", 
        IFNULL(evaluation_responses.remarks, "Pending")
    ) AS remarks');
		$this->db->from('forms');
		$this->db->join('activity', 'activity.activity_id = forms.activity_id', 'inner');
		$this->db->join('evaluation_responses', 'evaluation_responses.form_id = forms.form_id AND evaluation_responses.student_id = ' . $this->db->escape($student_id), 'left');
		$this->db->where('forms.status_evaluation', 'Ongoing');
		$this->db->where('(evaluation_responses.remarks IS NULL OR evaluation_responses.remarks = "Pending")');

		// Filter by audience
		$this->db->group_start();
		$this->db->or_where('activity.audience', 'All');
		if (!empty($user_dept_name)) {
			$this->db->or_where('activity.audience', $user_dept_name);
		}
		if (!empty($org_names)) {
			$this->db->or_where_in('activity.audience', $org_names);
		}
		$this->db->group_end();

		$query = $this->db->get();
		return $query->result();
	}


	public function get_evaluation_by_id($form_id)
	{
		$this->db->select('*'); // Select form & activity details
		$this->db->from('forms');
		$this->db->join('activity', 'activity.activity_id = forms.activity_id');
		$this->db->where('forms.form_id', $form_id);

		$query = $this->db->get();
		$form_data = $query->row_array(); // Fetch form details

		// Fetch all form fields related to this form_id
		$this->db->select('*');
		$this->db->from('formfields');
		$this->db->where('form_id', $form_id);
		$query = $this->db->get();
		$form_data['form_fields'] = $query->result_array(); // Store fields as an array inside form_data

		return $form_data;
	}

	public function save_response($data)
	{
		// Insert into evaluation_responses table
		$this->db->insert('evaluation_responses', $data);
		return $this->db->insert_id(); // return the response ID
	}

	public function save_answers($data)
	{
		// Insert multiple answers into response_answer table
		if (!empty($data)) {
			return $this->db->insert_batch('response_answer', $data);
		}
		return false;
	}


	// Fetch Form Details
	public function get_form_details($form_id)
	{
		// Get form and activity details together using join
		$this->db->select('forms.*, activity.*'); // Select both form and activity details
		$this->db->from('forms');
		$this->db->join('activity', 'activity.activity_id = forms.activity_id');
		$this->db->where('forms.form_id', $form_id);
		$query = $this->db->get();
		$form_data = $query->row(); // Get form and activity details

		if ($form_data) {
			// Get form fields related to the form_id
			$this->db->select('*');
			$this->db->from('formfields');
			$this->db->where('form_id', $form_id);
			$query = $this->db->get();
			$form_data->form_fields = $query->result(); // Store form fields as an array in form_data
		}

		return $form_data;
	}


	public function get_form_fields($form_id)
	{
		return $this->db->get_where('formfields', ['form_id' => $form_id])->result();
	}


	//GET ANSWERED EVALUATION FORMS

	public function get_answered_forms($student_id)
	{
		$query = $this->db->query("
        SELECT 
            ef.form_id,
            ef.title AS form_title,
            ef.description AS form_description,
            ef.start_date,
            ef.end_date,
            a.activity_title,
            COALESCE(o.org_name, d.dept_name, 'Student Parliament') AS organizer,
            er.submitted_at AS answered_date,
            CASE 
                WHEN er.submitted_at IS NOT NULL THEN 'Answered'
                ELSE 'Not Answered'
            END AS answered_status
        FROM forms ef
        JOIN activity a ON ef.activity_id = a.activity_id
        LEFT JOIN department d ON a.dept_id = d.dept_id
        LEFT JOIN organization o ON a.org_id = o.org_id
        JOIN evaluation_responses er ON ef.form_id = er.form_id AND er.student_id = ?
        WHERE er.student_id = ?
    ", array($student_id, $student_id));

		return $query->result();
	}




	//SAVE EVALUATION FORM RESPONSES

	public function save_evaluation_response($data)
	{
		$this->db->insert('evaluation_responses', $data);
		return $this->db->insert_id(); // Return inserted ID
	}

	public function save_response_answers($evaluation_response_id, $responses)
	{
		foreach ($responses as $form_fields_id => $answer) {
			$answer_data = [
				'evaluation_response_id' => $evaluation_response_id,
				'form_fields_id' => $form_fields_id,
				'answer' => $answer
			];
			$this->db->insert('response_answer', $answer_data);
		}
	}

	//FETCH EVALUATION FROM RESPONSES

	public function get_form_answers($form_id, $student_id)
	{
		$this->db->select('ff.form_fields_id, ff.label, ff.type, fa.answer');
		$this->db->from('formfields ff');
		$this->db->join('response_answer fa', 'fa.form_fields_id = ff.form_fields_id', 'left');
		$this->db->join('evaluation_responses er', 'fa.evaluation_response_id = er.evaluation_response_id', 'left');
		$this->db->where('ff.form_id', $form_id);
		$this->db->where('er.student_id', $student_id);
		$this->db->order_by('ff.order', 'ASC'); // Order the questions
		$query = $this->db->get();
		return $query->result();
	}





	//HOMEPAGE POSTS

	//for shared activities
	public function get_student_activities($student_id)
	{
		// Get student's department
		$this->db->select('dept_id');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$dept = $this->db->get()->row();

		// Get student's organizations
		$this->db->select('org_id');
		$this->db->from('student_org');
		$this->db->where('student_id', $student_id);
		$orgs = $this->db->get()->result_array();

		// Extract org_ids into an array
		$org_ids = array_column($orgs, 'org_id');

		// Fetch activities based on conditions
		$this->db->select('activity.*, department.dept_name');
		$this->db->from('activity');
		$this->db->join('department', 'activity.dept_id = department.dept_id', 'left'); // Join department to get dept_name
		$this->db->where('activity.is_shared', 'Yes'); // Only fetch activities that are shared

		$this->db->group_start(); // Start group for OR conditions
		$this->db->where('activity.dept_id IS NULL'); // If dept_id is NULL, it should be visible to everyone
		$this->db->where('activity.org_id IS NULL'); // If org_id is NULL, it should be visible to everyone
		if ($dept) {
			$this->db->or_where('activity.dept_id', $dept->dept_id);
		}
		if (!empty($org_ids)) {
			$this->db->or_where_in('activity.org_id', $org_ids);
		}
		$this->db->group_end(); // End OR group
		$this->db->order_by('activity.start_date', 'DESC'); // Order by the latest activities
		$query = $this->db->get();

		return $query->result_array();
	}

	//for posts with image and texts

	public function getFilteredPosts($student_id)
	{
		// Get user's department
		$this->db->select('dept_id');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$user = $this->db->get()->row();
		$dept_id = $user ? $user->dept_id : null;

		// Get user's organizations
		$this->db->select('org_id');
		$this->db->from('student_org');
		$this->db->where('student_id', $student_id);
		$orgs = $this->db->get()->result();
		$org_ids = array_map(function ($org) {
			return $org->org_id;
		}, $orgs);

		// Query posts based on dept_id and org_id
		$this->db->select('post.*, users.first_name, users.last_name, users.profile_pic, 
                           department.dept_name, organization.org_name');
		$this->db->from('post');
		$this->db->join('users', 'users.student_id = post.student_id');
		$this->db->join('department', 'department.dept_id = post.dept_id', 'left');
		$this->db->join('organization', 'organization.org_id = post.org_id', 'left');

		// Filtering logic:
		$this->db->group_start();
		$this->db->where('post.dept_id IS NULL');
		$this->db->where('post.org_id IS NULL');
		$this->db->or_group_start();
		if ($dept_id) {
			$this->db->or_where('post.dept_id', $dept_id);
		}
		if (!empty($org_ids)) {
			$this->db->or_where_in('post.org_id', $org_ids);
		}
		$this->db->group_end();
		$this->db->group_end();

		$this->db->order_by('post.created_at', 'DESC');
		return $this->db->get()->result();
	}


	//for upcoming activities 

	public function get_upcoming_activities($student_id)
	{
		$this->db->select('activity.*, department.dept_name, organization.org_name, 
                        DATE_FORMAT(activity.start_date, "%b %d, %Y") AS activity_date, 
                        DATE_FORMAT(activity.start_date, "%h:%i %p") AS start_time, 
                        DATE_FORMAT(activity.end_date, "%h:%i %p") AS end_time');
		$this->db->from('activity');
		$this->db->join('users', 'users.student_id = ' . $this->db->escape($student_id), 'left');
		$this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');
		$this->db->join('department', 'activity.dept_id = department.dept_id', 'left');
		$this->db->join('organization', 'activity.org_id = organization.org_id', 'left');

		// Ensure the status is "Upcoming"
		$this->db->where('activity.status', 'Upcoming');

		// Check visibility conditions: either public or belongs to the user's department/organization
		$this->db->where("(activity.privacy = 'public' 
                       OR (activity.privacy = 'private' 
                           AND (activity.dept_id = users.dept_id 
                                OR student_org.org_id = activity.org_id)
                          )
                    )");

		// Group by activity_id to ensure uniqueness
		$this->db->group_by('activity.activity_id');

		// Limit results to 2 and shuffle them
		$this->db->limit(2); // Limit the number of activities
		$this->db->order_by('RAND()'); // Shuffle the activities

		// Execute the query and return the results
		return $this->db->get()->result_array();
	}



	//FOR CHECKING THE NUMBERS WHO CLICKED THE ATTEND BUTTON START

	public function checkInterest($activityId, $studentId)
	{
		// Check if there is already a record in the 'activity_attendance_interest' table for this student and activity.
		$this->db->where('activity_id', $activityId); // Filter by the activity ID.
		$this->db->where('student_id', $studentId);   // Filter by the student ID.
		$query = $this->db->get('activity_attendance_interest'); // Query the table for the matching record.

		// If the query returns any rows, it means the student has already expressed interest.
		return $query->num_rows() > 0; // Return true (1) if interest is already recorded, false (0) if not.
	}


	public function addInterest($activityId, $studentId)
	{
		// Prepare data to be inserted into the 'activity_attendance_interest' table
		$data = [
			'activity_id' => $activityId, // The ID of the activity the student is expressing interest in.
			'student_id' => $studentId,   // The ID of the student expressing interest.
			'expressed_interest' => 1 // Mark this record with a value indicating the student has expressed interest.
		];

		// Insert the record into the 'activity_attendance_interest' table.
		$this->db->insert('activity_attendance_interest', $data);
	}


	public function incrementAttendeeCount($activityId)
	{
		// Increment the 'attendee_count' column in the 'activity' table by 1 for the given activity.
		$this->db->set('attendee_count', 'attendee_count + 1', FALSE); // Increase the count by 1 without overwriting the value.
		$this->db->where('activity_id', $activityId); // Filter by the activity ID to identify the correct record.

		// Update the 'activity' table with the new attendee count.
		$this->db->update('activity');
	}

	//DISPLAYS THE ATTENDEE_COUNT

	public function getAttendeeCount($activityId)
	{
		$this->db->select('attendee_count');
		$this->db->from('activity');
		$this->db->where('activity_id', $activityId);
		$query = $this->db->get();
		return $query->row()->attendee_count;
	}



	//FOR CHECKING THE NUMBERS WHO CLICKED THE ATTEND BUTTON END


	//SUMMARY OF FINES START

	// PAY FINES


	// Method to get the fines status of a student
	public function get_fines_status($student_id)
	{
		// Query to fetch the fines status from the fines_summary table
		$this->db->select('fines_status');
		$this->db->from('fines_summary');
		$this->db->where('student_id', $student_id);
		$query = $this->db->get();

		// If there is a result, return the fines_status
		if ($query->num_rows() > 0) {
			return $query->row()->fines_status;
		}

		// Return null if no status found
		return null;
	}

	// Insert data into fines_summary table after paying online
	public function insert_fines_summary($data)
	{
		return $this->db->insert('fines_summary', $data);
	}

	// Method to fetch fines data by summary_id
	public function get_fine_summary_data($summary_id)
	{
		$this->db->select('
        fines_summary.summary_id,
        fines_summary.student_id,
        fines_summary.total_fines,
        fines_summary.mode_payment,
        fines_summary.reference_number_admin,
        fines_summary.reference_number_students,
        fines_summary.last_updated,
        users.first_name,
        users.last_name,
        fines.fines_id,
        fines.activity_id,
        fines.timeslot_id,
        fines.attendance_id,
        fines.fines_reason,
        fines.fines_amount,
        fines.status,
        fines.remarks,
        activity.activity_title,
        activity.organizer,
        activity.start_date
    ');
		$this->db->from('fines_summary');
		$this->db->join('users', 'fines_summary.student_id = users.student_id');
		$this->db->join('fines', 'fines.student_id = fines_summary.student_id'); // ✅ FIXED HERE
		$this->db->join('activity', 'activity.activity_id = fines.activity_id', 'left');
		$this->db->where('fines_summary.summary_id', $summary_id);
		$this->db->order_by('fines.fines_id', 'DESC');

		$query = $this->db->get();
		return $query->result_array();
	}




	public function get_fines_with_summary_and_activity($student_id = null)
	{
		$this->db->select('
        fines.*, 
        activity.activity_title, 
        activity.organizer, 
        activity.start_date, 
        fines_summary.summary_id,
        fines_summary.total_fines,
        fines_summary.fines_status,
        fines_summary.mode_payment,
        fines_summary.reference_number_admin,
        fines_summary.reference_number_students,
        fines_summary.last_updated,
        fines_summary.receipt,
        fines_summary.generated_receipt,
        attendance.time_in,
        attendance.time_out,
        activity_time_slots.slot_name
    ');

		$this->db->from('fines');

		// Join activity details
		$this->db->join('activity', 'activity.activity_id = fines.activity_id');

		// Join fines summary
		$this->db->join('fines_summary', 'fines_summary.student_id = fines.student_id', 'left');

		// Join attendance using attendance_id from fines table
		$this->db->join('attendance', 'attendance.attendance_id = fines.attendance_id', 'left');

		// Join activity time slot using timeslot_id from fines table
		$this->db->join('activity_time_slots', 'activity_time_slots.timeslot_id = fines.timeslot_id', 'left');

		// Optional filter by student ID
		if ($student_id !== null) {
			$this->db->where('fines.student_id', $student_id);
		}

		$query = $this->db->get();
		return $query->result_array();
	}













	//SUMMARY OF FINES END



	//REGISTRATION RECEIPTS START

	//display informations in table
	public function get_receipts($student_id)
	{
		$this->db->select('r.*, a.activity_title, r.generated_receipt');
		$this->db->from('registrations r');
		$this->db->join('activity a', 'r.activity_id = a.activity_id', 'left');
		$this->db->where('r.student_id', $student_id);
		$this->db->order_by('r.registered_at', 'DESC'); // Keep latest first

		return $this->db->get()->result_array();
	}


	// Get details of a specific receipt
	public function get_receipt_by_id($registration_id)
	{
		$this->db->select('r.*, a.activity_title, r.generated_receipt, u.student_id, 
                       u.first_name, u.last_name, u.middle_name'); // ✅ Include student details
		$this->db->from('registrations r');
		$this->db->join('activity a', 'r.activity_id = a.activity_id', 'left');
		$this->db->join('users u', 'r.student_id = u.student_id', 'left'); // ✅ Join users table
		$this->db->where('r.registration_id', $registration_id);
		$this->db->where('r.registration_status', 'Verified'); // ✅ Ensure only verified transactions can have receipts

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->row_array() : false; // ✅ Return false if no data
	}


	//get fines details for receipt verification
	public function get_fines_by_code($verification_code)
	{
		$this->db->select('fines_summary.student_id, activity.activity_title, fines_summary.total_fines as amount_paid, fines_summary.last_updated, fines_summary.verification_code');
		$this->db->from('fines_summary');
		$this->db->join('fines', 'fines.student_id = fines_summary.student_id', 'left');
		$this->db->join('activity', 'activity.activity_id = fines.activity_id', 'left');
		$this->db->where('fines_summary.verification_code', $verification_code);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row_array();
		}

		return null;
	}







	// Update database when a receipt is generated
	public function update_generated_receipt($registration_id, $filename, $verification_code)
	{
		$this->db->where('registration_id', $registration_id);
		$this->db->update('registrations', [
			'generated_receipt' => $filename,
			'verification_code' => $verification_code
		]);

		return $this->db->affected_rows() > 0; // ✅ Return true if the update was successful
	}


	// This method retrieves a registration based on the verification code
	public function get_registration_by_code($verification_code)
	{
		// Selecting the necessary fields
		$this->db->select('registrations.student_id, activity.activity_title, registrations.amount_paid, registrations.registration_status, registrations.registered_at');
		$this->db->from('registrations');
		$this->db->join('activity', 'activity.activity_id = registrations.activity_id', 'left'); // Ensure left join for activity_title
		$this->db->where('registrations.verification_code', $verification_code); // Match the verification code
		$query = $this->db->get();

		// If there is a matching record, return it
		if ($query->num_rows() > 0) {
			return $query->row_array();
		}

		// If no record is found, return null
		return null;
	}



	//REGISTRATION RECEIPTS END





	// PROFILE SETTINGS
	public function get_user_profile()
	{
		$student_id = $this->session->userdata('student_id');

		// Get basic user and department info
		$this->db->select('users.*, department.*');
		$this->db->from('users');
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$this->db->where('users.student_id', $student_id);
		$user = $this->db->get()->row();

		// Get organizations separately
		$this->db->select('organization.*');
		$this->db->from('student_org');
		$this->db->join('organization', 'organization.org_id = student_org.org_id');
		$this->db->where('student_org.student_id', $student_id);
		$organizations = $this->db->get()->result();

		if ($user) {
			$user->organizations = $organizations;
		}

		return $user;
	}

	public function get_profile_pic($student_id)
	{
		// Make sure student_id is passed correctly
		if (!$student_id) {
			return 'default.png';
		}

		$this->db->select('profile_pic');
		$this->db->from('users'); // Ensure this is your actual table name
		$this->db->where('student_id', $student_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$result = $query->row();
			return $result->profile_pic ?? 'default.png';
		}

		return 'default.png'; // Fallback if no profile found
	}

	// Function to update profile picture in the database
	public function update_profile_pic($student_id, $data)
	{
		$this->db->where('student_id', $student_id);
		return $this->db->update('users', $data);
	}

	public function update_student($student_id, $data)
	{
		$this->db->where('student_id', $student_id);
		return $this->db->update('users', $data);
	}

	public function get_by_id($student_id)
	{
		return $this->db->get_where('users', ['student_id' => $student_id])->row();
	}

	public function update_password($student_id, $hashed_password)
	{
		return $this->db->where('student_id', $student_id)
			->update('users', ['password' => $hashed_password]);
	}


	public function get_qr_code($student_id)
	{
		$this->db->select('qr_image');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row()->qr_image; // Return the base64 encoded QR code
		}

		return null; // No QR code found for this student
	}
}

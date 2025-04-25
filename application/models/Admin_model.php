<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
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

	// DASHBOARD 

	// Get the current semester count
	public function get_current_semester_count()
	{
		$today = date('Y-m-d');
		$current_year = date('Y');
		$month = date('n');

		// Determine current semester based on the month
		if ($month >= 7 && $month <= 12) {
			$semester = 'First Semester';
			$start_date = "$current_year-07-01";
			$end_date = "$current_year-12-31";
		} else {
			$semester = 'Second Semester';
			$start_date = "$current_year-01-01";
			$end_date = "$current_year-06-30";
		}

		// Get count of completed activities in the current semester
		$this->db->from('activity');
		$this->db->where('start_date >=', $start_date);
		$this->db->where('start_date <=', $end_date);
		$this->db->where('status', 'completed');
		$count = $this->db->count_all_results();

		return [
			'semester' => $semester,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'completed_count' => $count
		];
	}

	// Get the previous semester count
	public function get_previous_semester_count()
	{
		$today = date('Y-m-d');
		$current_year = date('Y');
		$month = date('n');

		// Determine previous semester based on the current month
		if ($month >= 7 && $month <= 12) {
			$semester = 'Second Semester'; // Previous semester is the second semester
			$start_date = ($current_year - 1) . "-01-01";  // Previous year, first semester
			$end_date = ($current_year - 1) . "-06-30";    // Previous year, first semester
		} else {
			$semester = 'First Semester'; // Previous semester is the first semester
			$start_date = ($current_year - 1) . "-07-01";  // Previous year, second semester
			$end_date = ($current_year - 1) . "-12-31";    // Previous year, second semester
		}

		// Get count of completed activities in the previous semester
		$this->db->from('activity');
		$this->db->where('start_date >=', $start_date);
		$this->db->where('start_date <=', $end_date);
		$this->db->where('status', 'completed');
		$count = $this->db->count_all_results();

		return [
			'semester' => $semester,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'completed_count' => $count
		];
	}

	public function get_monthly_activity_count($start_date, $end_date)
	{
		$this->db->select('MONTH(start_date) as month, COUNT(*) as count');
		$this->db->from('activity');
		$this->db->where('start_date >=', $start_date);
		$this->db->where('start_date <=', $end_date);
		$this->db->where('status', 'completed');
		$this->db->group_by('MONTH(start_date)');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_student_count_per_department()
	{
		// SQL Query to join 'users' (or 'students') table with 'departments' table on 'dept_id'
		$this->db->select('d.dept_name, COUNT(u.dept_id) as student_count');
		$this->db->from('users u'); // Assuming the table storing user data is 'users'
		$this->db->join('department d', 'u.dept_id = d.dept_id', 'inner'); // Join departments table to users table
		$this->db->group_by('u.dept_id'); // Group by dept_id to count students per department
		$query = $this->db->get();  // Execute the query

		// Return the result as an associative array
		return $query->result_array();
	}

	public function get_current_semester_count_organized()
	{
		$today = date('Y-m-d');
		$current_year = date('Y');
		$month = date('n');

		// Determine current semester based on the month
		if ($month >= 7 && $month <= 12) {
			$semester = 'First Semester';
			$start_date = "$current_year-07-01";
			$end_date = "$current_year-12-31";
		} else {
			$semester = 'Second Semester';
			$start_date = "$current_year-01-01";
			$end_date = "$current_year-06-30";
		}

		// Get count of completed activities in the current semester
		$this->db->from('activity');
		$this->db->where('start_date >=', $start_date);
		$this->db->where('start_date <=', $end_date);
		$this->db->where('status', 'completed');
		$this->db->where('organizer', 'Student Parliament');
		$count = $this->db->count_all_results();

		return [
			'semester' => $semester,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'completed_count' => $count
		];
	}

	// public function get_attendance_student(){

	//     thi
	// }

	// CREATING ACTIVITY
	public function get_department()
	{
		$this->db->select('*');
		$this->db->from('department');

		$query = $this->db->get();

		return $query->result();
	}

	public function save_activity($data)
	{
		$this->db->insert('activity', $data);
		return $this->db->insert_id(); // Get last inserted ID
	}

	public function save_schedules($schedules)
	{
		$timeslot_ids = [];

		foreach ($schedules as $schedule) {
			$schedule['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('activity_time_slots', $schedule);
			$timeslot_ids[] = $this->db->insert_id(); // Get and store inserted timeslot_id
		}

		return $timeslot_ids;
	}

	// LISTING AND VIEWING OF ACTIVITY
	public function get_activities()
	{
		$this->db->select('a.*, MIN(ats.date_time_in) as first_schedule'); // Pick the earliest schedule
		$this->db->from('activity a');
		$this->db->join('activity_time_slots ats', 'a.activity_id = ats.activity_id', 'LEFT');
		$this->db->group_by('a.activity_id'); // Ensure only one row per activity
		$this->db->where('organizer', 'Student Parliament');

		$query = $this->db->get();
		return $query->result();
	}

	// FETCHING SPECIFIC ACTIVITY USING ACTIVITY ID 
	public function get_activity($activity_id)
	{
		$this->db->select('a.*, ats.*, MIN(ats.date_time_in) as first_schedule, MAX(ats.date_time_out) as last_schedule');
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

	// GETTING SCHEDULE PER ACTIVITY
	public function get_schedule($activity_id)
	{
		$this->db->select('ats.*');
		$this->db->from('activity_time_slots ats');
		$this->db->where('ats.activity_id', $activity_id);

		$query = $this->db->get();
		return $query->result_array(); // Fetch all schedules
	}

	// FOR REGISTRATION
	public function registrations($activity_id)
	{

		$this->db->select('registrations.*, users.*, department.*');
		$this->db->from('registrations');
		$this->db->join('users', 'registrations.student_id = users.student_id');
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$this->db->where('activity_id', $activity_id);

		$query = $this->db->get();
		return $query->result(); // Fetch all registrations for the activity
	}

	public function insert_cash_payment($data)
	{
		$this->db->insert('registrations', $data);

		// Check if insertion was successful
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	// FOR REFERENCE NUMBER
	public function get_reference_data($student_id, $activity_id)
	{
		return $this->db
			->select('reference_number')
			->where('student_id', $student_id)
			->where('activity_id', $activity_id)
			->get('registrations')
			->row();
	}

	// FOR VALIDATION OF THE REGISTRATION
	public function validate_registration($student_id, $activity_id, $data)
	{
		return $this->db
			->where('student_id', $student_id)
			->where('activity_id', $activity_id)
			->update('registrations', $data);
	}

	// COUNTER FOR THE REGISTERED IN THE ACTIVITY
	public function get_registered_count($activity_id)
	{
		$this->db->select('COUNT(*) as total');
		$this->db->from('registrations');
		$this->db->where('registration_status', 'Verified');
		$this->db->where('activity_id', $activity_id);

		$query = $this->db->get();
		$result = $query->row();

		return $result->total; // Return the count
	}

	// COUNTER FOR THE ATTENDEES IN THE ACTIVITY
	public function get_attendees_count($activity_id)
	{
		$this->db->select('COUNT(*) as total');
		$this->db->from('attendees');
		$this->db->where('attendees_status', 'Attending');
		$this->db->where('activity_id', $activity_id);

		$query = $this->db->get();
		$result = $query->row();

		return $result->total; // Return the count
	}

	//registration receipt
	public function get_registration_id($student_id, $activity_id)
	{
		return $this->db->select('registration_id')
			->from('registrations')
			->where('student_id', $student_id)
			->where('activity_id', $activity_id)
			->get()
			->row('registration_id');
	}

	// DELETE SCHEDULE BY ID
	public function delete_schedule($id)
	{
		return $this->db->delete('activity_time_slots', ['timeslot_id' => $id]);
	}

	// UPDATING ACTIVITY TO DATABASE *
	public function update_activity($activity_id, $data)
	{
		$this->db->where('activity_id', $activity_id);
		return $this->db->update('activity', $data);
	}

	// UPDATING SCHEDULE TO DATABASE
	public function update_schedule($schedule_id, $schedule_data)
	{
		$this->db->where('timeslot_id', $schedule_id);
		return $this->db->update('activity_time_slots', $schedule_data);
	}

	// SAVING THE SCHEDULE
	public function save_schedule($schedule_data)
	{
		$this->db->insert('activity_time_slots', $schedule_data);
		return $this->db->affected_rows() > 0; // Returns true if inserted, false otherwise
	}

	// EVALUATION FORM 

	// LIST OF EVALUATION FORM
	public function evaluation_form()
	{
		$this->db->select('*');
		$this->db->from('forms');
		$this->db->join('activity', 'activity.activity_id = forms.activity_id');  // Fixed JOIN condition
		$this->db->where('activity.organizer', 'Student Parliament');

		return $this->db->get()->result();
	}

	// FETCHING ACTIVITIES
	public function activity_organized()
	{
		$this->db->select('activity.*');  // Select all columns from the activity table
		$this->db->from('activity');
		$this->db->where('activity.organizer', 'Student Parliament');

		// LEFT JOIN to match activities with forms (if they exist)
		$this->db->join('forms', 'activity.activity_id = forms.activity_id', 'left');

		// Filter to get only activities that do NOT have a form (form.activity_id is NULL)
		$this->db->where('forms.activity_id IS NULL');

		return $this->db->get()->result();
	}

	// Save form data to the 'forms' table
	public function saveForm($data)
	{
		$this->db->insert('forms', $data);
		return $this->db->insert_id(); // Return the newly inserted form ID
	}

	// Save form fields to the 'formfields' table
	public function saveFormFields($fields)
	{
		if (!empty($fields)) {
			$this->db->insert_batch('formfields', $fields); // Batch insert for multiple fields
		}
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

	public function get_student_evaluation_responses($form_id)
	{
		$this->db->select("
            evaluation_responses.evaluation_response_id,
            evaluation_responses.student_id,
            CONCAT(users.first_name, ' ', users.last_name) AS name,
            department.dept_name,
            formfields.label AS question,
            formfields.type,
            response_answer.answer,
            evaluation_responses.submitted_at
        ");
		$this->db->from('evaluation_responses');
		$this->db->join('response_answer', 'response_answer.evaluation_response_id = evaluation_responses.evaluation_response_id');
		$this->db->join('formfields', 'formfields.form_fields_id = response_answer.form_fields_id');
		$this->db->join('users', 'users.student_id = evaluation_responses.student_id');
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$this->db->where('evaluation_responses.form_id', $form_id);

		$query = $this->db->get();

		// Check if the query executed successfully
		if ($query->num_rows() > 0) {
			return $query->result(); // Return results if available
		} else {
			return []; // Return empty array if no results found
		}
	}

	public function forms($form_id)
	{
		$this->db->select('form_id, title');
		$this->db->from('forms');
		$this->db->where('form_id', $form_id);
		$query = $this->db->get();
		return $query->row();
	}

	public function count_attendees($form_id)
	{
		// Get the activity_id linked to the form
		$this->db->select('activity_id');
		$this->db->from('forms');
		$this->db->where('form_id', $form_id);
		$activityQuery = $this->db->get();

		if ($activityQuery->num_rows() > 0) {
			$activity_id = $activityQuery->row()->activity_id;

			// Get distinct student IDs from attendance for the specific activity
			$this->db->distinct();
			$this->db->select('student_id');
			$this->db->from('attendance');
			$this->db->where('activity_id', $activity_id);
			$query = $this->db->get();

			return $query->num_rows();
		}

		return 0; // If no matching form/activity found
	}

	public function total_respondents($form_id)
	{
		$this->db->select('COUNT(DISTINCT student_id) AS total');
		$this->db->where('form_id', $form_id);
		$query = $this->db->get('evaluation_responses');
		return $query->row()->total;
	}

	public function rating_summary($form_id)
	{
		$this->db->select('formfields.form_id, formfields.form_fields_id, formfields.label AS question, 
                       AVG(response_answer.answer) AS avg_rating, 
                       SUM(CASE WHEN response_answer.answer = 5 THEN 1 ELSE 0 END) AS five_star,
                       SUM(CASE WHEN response_answer.answer = 4 THEN 1 ELSE 0 END) AS four_star,
                       SUM(CASE WHEN response_answer.answer = 3 THEN 1 ELSE 0 END) AS three_star,
                       SUM(CASE WHEN response_answer.answer = 2 THEN 1 ELSE 0 END) AS two_star,
                       SUM(CASE WHEN response_answer.answer = 1 THEN 1 ELSE 0 END) AS one_star,
                       COUNT(response_answer.answer) AS total_responses');
		$this->db->from('response_answer');
		$this->db->join('formfields', 'response_answer.form_fields_id = formfields.form_fields_id');
		$this->db->where('formfields.form_id', $form_id);
		$this->db->where('formfields.type', 'rating'); // Only get rating-type fields
		$this->db->group_by('formfields.form_fields_id, formfields.form_id');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function overall_rating($form_id)
	{
		$this->db->select('AVG(CAST(response_answer.answer AS DECIMAL(10,2))) AS overall_rating');
		$this->db->from('response_answer');
		$this->db->join('formfields', 'response_answer.form_fields_id = formfields.form_fields_id');
		$this->db->where('formfields.form_id', $form_id);

		$query = $this->db->get();

		// Return the overall rating or 0 if no results
		$result = $query->row();
		return $result ? $result->overall_rating : 0;
	}

	public function answer_summary($form_id)
	{
		// Select long answer and short answer responses
		$this->db->select('ff.label AS question, 
                           GROUP_CONCAT(DISTINCT ra.answer ORDER BY ra.answer SEPARATOR "||") AS answers');
		$this->db->from('response_answer ra');
		$this->db->join('formfields ff', 'ff.form_fields_id = ra.form_fields_id');

		// Get answers where the type is either 'textarea' (long answer) or 'text' (short answer)
		$this->db->where_in('ff.type', ['textarea', 'short']);
		$this->db->where('ff.form_id', $form_id);

		// Group by question (label) and order by question label alphabetically
		$this->db->group_by('ff.label');
		$this->db->order_by('ff.order', 'ASC');

		// Execute query and return result
		$query = $this->db->get();
		return $query->result_array();
	}

	// EXCUSE APPLICATION

	// FETCHING EXCUSE APPLICATION PER EVENT
	public function fetch_application($activity_id)
	{
		return $this->db->get_where('activity', ['activity_id' => $activity_id])->row_array();
	}

	// FETCHING DETAILS OF APPLICATION
	public function fetch_letters()
	{
		$this->db->select('*');
		$this->db->from('excuse_application');
		$this->db->join('users', 'excuse_application.student_id = users.student_id');
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$query = $this->db->get();

		return $query->result();
	}

	// FETCHING EXCUSE LETTER PER STUDENT
	public function review_letter($excuse_id)
	{
		$this->db->select('excuse_application.*, users.*');
		$this->db->from('excuse_application');
		$this->db->join('users', 'excuse_application.student_id = users.student_id');
		$this->db->join('department', 'users.dept_id = department.dept_id');
		$this->db->where('excuse_application.excuse_id', $excuse_id); // Add condition to filter by excuse_id
		$query = $this->db->get();

		// Return the result as an array
		return $query->row_array();
	}

	// UPDATING STATUS AND REMARKS FOR THE EXCUSE APPLICATION *
	public function updateApprovalStatus($data)
	{

		$this->db->where('excuse_id', $data['excuse_id']);  // Ensure you use the correct column for identification
		$this->db->update('excuse_application', $data);

		// Check if any rows were affected (successful update)
		return $this->db->affected_rows() > 0;
	}

	// COMMUNITY SECTION

	// FETCHING USER TABLE BY STUDENT ID
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

	// GETTING POST DETAILS AND AUTHOR *
	public function get_all_posts($limit, $offset)
	{
		$this->db->select('*');
		$this->db->from('post');
		$this->db->join('users', 'post.student_id = users.student_id', 'left');
		$this->db->join('department', 'department.dept_id = post.dept_id', 'left');
		$this->db->join('organization', 'organization.org_id = post.org_id', 'left');
		$this->db->order_by('post.post_id', 'DESC');

		// Add LIMIT and OFFSET for pagination
		$this->db->limit($limit, $offset);

		$query = $this->db->get();
		return $query->result();
	}

	public function get_activities_upcoming()
	{
		$this->db->select('*');
		$this->db->from('activity');
		$this->db->where('status', 'Upcoming');

		$query = $this->db->get();
		return $query->result();
	}

	public function get_shared_activities($limit, $offset)
	{
		$this->db->select('*');
		$this->db->from('activity');
		$this->db->where('is_shared', 'Yes');
		$this->db->order_by('updated_at', 'DESC'); // Sort by newest activity first
		$this->db->limit($limit, $offset); // Apply pagination

		$query = $this->db->get();
		return $query->result();
	}

	// ADDING POST
	public function insert_data($data)
	{
		return $this->db->insert('post', $data);
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

	// DELETION OF POST
	public function delete_post($post_id)
	{
		$this->db->where('post_id', $post_id)->delete('comment');
		$this->db->where('post_id', $post_id)->delete('likes');
		$this->db->where('post_id', $post_id);
		return $this->db->delete('post'); // Assuming your table name is 'posts'
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

	// ATTENDANCE MONITORING

	// START FACIAL RECOGNITION AND SCANNING QR CODES
	public function get_students_realtime_time_in($activity_id)
	{
		// Set timezone
		date_default_timezone_set('Asia/Manila');

		// Select relevant fields: student ID, names, and time-in
		$this->db->select('s.student_id, s.first_name, s.last_name, a.time_in');
		$this->db->from('attendance a');
		$this->db->join('users s', 's.student_id = a.student_id');
		$this->db->where('a.activity_id', $activity_id);
		$this->db->order_by('a.time_in', 'DESC');

		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_students_realtime_time_out($activity_id)
	{
		// Set timezone
		date_default_timezone_set('Asia/Manila');

		// Select relevant fields: student ID, names, and time-in
		$this->db->select('s.student_id, s.first_name, s.last_name, a.time_out');
		$this->db->from('attendance a');
		$this->db->join('users s', 's.student_id = a.student_id');
		$this->db->where('a.activity_id', $activity_id);
		$this->db->order_by('a.time_out', 'DESC');

		$query = $this->db->get();
		return $query->result_array();
	}

	// FUNCTIONALITY FOR SCANNING AND RECOGNITION
	public function update_attendance_time_in($student_id, $activity_id, $timeslot_id, $update_data)
	{
		if (!empty($update_data)) {
			$this->db->where('student_id', $student_id);
			$this->db->where('activity_id', $activity_id);
			$this->db->where('timeslot_id', $timeslot_id);
			$this->db->update('attendance', $update_data);

			return $this->db->affected_rows() > 0;
		}
		return false;
	}

	public function update_attendance_time_out($student_id, $activity_id, $timeslot_id, $update_data)
	{
		if (!empty($update_data)) {
			$this->db->where('student_id', $student_id);
			$this->db->where('activity_id', $activity_id);
			$this->db->where('timeslot_id', $timeslot_id);
			$this->db->update('attendance', $update_data);

			return $this->db->affected_rows() > 0;
		}
		return false;
	}

	// PROVIDING DATA IN THE TAKING OF ATTENDANCE INTERFACE
	public function get_attendance_record_time_in($student_id, $activity_id, $timeslot_id)
	{
		return $this->db->get_where('attendance', [
			'student_id' => $student_id,
			'timeslot_id' => $timeslot_id,
			'activity_id' => $activity_id
		])->row();
	}

	public function get_attendance_record_time_out($student_id, $activity_id, $timeslot_id)
	{
		return $this->db->get_where('attendance', [
			'student_id' => $student_id,
			'timeslot_id' => $timeslot_id,
			'activity_id' => $activity_id
		])->row();
	}

	public function get_activities_by_sp()
	{
		$this->db->select('activity.*');
		$this->db->from('activity');
		$this->db->where('organizer', 'Student Parliament');

		$query = $this->db->get();
		return $query->result();
	}

	public function get_activity_specific($activity_id)
	{
		return $this->db->get_where('activity', ['activity_id' => $activity_id])->row_array();
	}

	public function get_timeslots_by_activity($activity_id)
	{
		$this->db->select('*');
		$this->db->from('activity_time_slots');
		$this->db->where('activity_id', $activity_id);
		$this->db->order_by('timeslot_id', 'asc'); // or custom order
		return $this->db->get()->result();
	}

	public function get_all_students_attendance_by_activity($activity_id)
	{
		// Step 1: Get all students who have attendance records with department info
		$this->db->select('users.*, department.dept_name');
		$this->db->from('attendance');
		$this->db->join('users', 'attendance.student_id = users.student_id', 'inner'); // Only include students with attendance records
		$this->db->join('department', 'department.dept_id = users.dept_id', 'left');
		$this->db->where('attendance.activity_id', $activity_id);
		$this->db->group_by('users.student_id');  // Ensure students appear once
		$students = $this->db->get()->result();

		// Step 2: Get all timeslots for the activity
		$timeslots = $this->db->get_where('activity_time_slots', ['activity_id' => $activity_id])->result();

		// Step 3: Build data
		$data = [];

		foreach ($students as $student) {
			$row = [
				'student_id' => $student->student_id,
				'name'       => $student->first_name . ' ' . $student->last_name,
				'dept_name'  => isset($student->dept_name) ? $student->dept_name : 'No Department',
			];

			$slot_statuses = [];

			foreach ($timeslots as $slot) {
				// Determine period (morning or afternoon)
				$period = strtolower($slot->slot_name) === 'morning' ? 'am' : 'pm';
				$row["in_$period"] = 'No Data';  // Default value for time_in
				$row["out_$period"] = 'No Data'; // Default value for time_out

				// Get attendance for the student and current slot
				$this->db->where([
					'student_id'  => $student->student_id,
					'timeslot_id' => $slot->timeslot_id
				]);
				$attendance = $this->db->get('attendance')->row();

				// Debugging log
				log_message('debug', "Attendance for student {$student->student_id}, {$slot->slot_name}: " . print_r($attendance, true));

				// Check if attendance exists
				if ($attendance) {
					$time_in = !empty($attendance->time_in) ? date('F j, Y | g:i a', strtotime($attendance->time_in)) : 'No Data';
					$time_out = !empty($attendance->time_out) ? date('F j, Y | g:i a', strtotime($attendance->time_out)) : 'No Data';

					// Set the correct period (AM/PM) for time_in and time_out
					$row["in_$period"] = $time_in;
					$row["out_$period"] = $time_out;

					// Determine slot status for AM/PM separately
					if ($time_in === 'No Data' && $time_out === 'No Data') {
						$slot_statuses[] = 'Absent';
					} elseif ($time_in === 'No Data' || $time_out === 'No Data') {
						$slot_statuses[] = 'Incomplete';
					} else {
						$slot_statuses[] = 'Present';
					}
				} else {
					// If no attendance record exists, mark as Absent
					$slot_statuses[] = 'Absent';
				}
			}

			// Determine overall status based on all timeslot statuses
			if (count(array_unique($slot_statuses)) === 1 && $slot_statuses[0] === 'Absent') {
				$row['status'] = 'Absent';
			} elseif (count(array_unique($slot_statuses)) === 1 && $slot_statuses[0] === 'Present') {
				$row['status'] = 'Present';
			} else {
				$row['status'] = 'Incomplete';
			}

			$data[] = $row;
		}

		return $data;
	}

	// CONNECTING FINES AND ATTENDANCE
	public function get_fines_amount($activity_id)
	{
		$this->db->select('fines');
		$this->db->from('activity');
		$this->db->where('activity_id', $activity_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row()->fines; // Return just the fine amount
		} else {
			return null; // Or 0, depending on how you want to handle it
		}
	}

	public function auto_fines_missing_time_in()
	{
		date_default_timezone_set('Asia/Manila');
		$now = date('Y-m-d H:i:s');

		// Get all active timeslots where cut-off has passed
		$this->db->select('timeslot_id, activity_id, cut_off');
		$this->db->from('activity_timeslots');
		$this->db->where('date_cut_in <=', $now);
		$timeslots = $this->db->get()->result();

		foreach ($timeslots as $slot) {
			// Get all attendees for this activity/timeslot
			$this->db->select('student_id');
			$this->db->from('attendance');
			$this->db->where([
				'activity_id' => $slot->activity_id,
				'timeslot_id' => $slot->timeslot_id,
				'time_in' => NULL // Those who did NOT time in
			]);
			$students = $this->db->get()->result();

			foreach ($students as $student) {
				// Check if fine already exists
				$exists = $this->db->get_where('fines', [
					'student_id' => $student->student_id,
					'activity_id' => $slot->activity_id,
					'timeslot_id' => $slot->timeslot_id
				])->row();

				if ($exists) {
					// If no fine exists, insert it
					$activity = $this->db->get_where('activity', [
						'activity_id' => $slot->activity_id
					])->row();

					$fine_amount = $activity->fines ?? 0;

					// If fine exists, update it
					$this->db->where('student_id', $student->student_id);
					$this->db->where('activity_id', $slot->activity_id);
					$this->db->where('timeslot_id', $slot->timeslot_id);
					$this->db->update('fines', [
						'fines_amount' => $exists->amount + $fine_amount,  // Increase amount if necessary
						'updated_at' => date('Y-m-d H:i:s') // Set the updated timestamp
					]);
				} else {
					// If no fine exists, insert it
					$activity = $this->db->get_where('activity', [
						'activity_id' => $slot->activity_id
					])->row();

					$fine_amount = $activity->fines ?? 0;

					// Insert into fines table
					$this->db->insert('fines', [
						'student_id' => $student->student_id,
						'activity_id' => $slot->activity_id,
						'timeslot_id' => $slot->timeslot_id,
						'amount' => $fine_amount,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
			}
		}

		// After inserting or updating fines, update fines_summary
		$this->update_fines_summary();
	}

	public function auto_fines_missing_time_out()
	{
		date_default_timezone_set('Asia/Manila');
		$now = date('Y-m-d H:i:s');

		// Get all active timeslots where cut-off has passed
		$this->db->select('timeslot_id, activity_id, cut_off');
		$this->db->from('activity_timeslots');
		$this->db->where('date_cut_in <=', $now);
		$timeslots = $this->db->get()->result();

		foreach ($timeslots as $slot) {
			// Get all attendees for this activity/timeslot
			$this->db->select('student_id');
			$this->db->from('attendance');
			$this->db->where([
				'activity_id' => $slot->activity_id,
				'timeslot_id' => $slot->timeslot_id,
				'time_out' => NULL // Those who did NOT time in
			]);
			$students = $this->db->get()->result();

			foreach ($students as $student) {
				// Check if fine already exists
				$exists = $this->db->get_where('fines', [
					'student_id' => $student->student_id,
					'activity_id' => $slot->activity_id,
					'timeslot_id' => $slot->timeslot_id
				])->row();

				if ($exists) {
					// If no fine exists, insert it
					$activity = $this->db->get_where('activity', [
						'activity_id' => $slot->activity_id
					])->row();

					$fine_amount = $activity->fines ?? 0;

					// If fine exists, update it
					$this->db->where('student_id', $student->student_id);
					$this->db->where('activity_id', $slot->activity_id);
					$this->db->where('timeslot_id', $slot->timeslot_id);
					$this->db->update('fines', [
						'fines_amount' => $exists->amount + $fine_amount,  // Increase amount if necessary
						'updated_at' => date('Y-m-d H:i:s') // Set the updated timestamp
					]);
				} else {
					// If no fine exists, insert it
					$activity = $this->db->get_where('activity', [
						'activity_id' => $slot->activity_id
					])->row();

					$fine_amount = $activity->fines ?? 0;

					// Insert into fines table
					$this->db->insert('fines', [
						'student_id' => $student->student_id,
						'activity_id' => $slot->activity_id,
						'timeslot_id' => $slot->timeslot_id,
						'amount' => $fine_amount,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
			}
		}

		// After inserting or updating fines, update fines_summary
		$this->update_fines_summary();
	}

	public function update_fines_summary()
	{
		// Get all fines grouped by student and activity
		$this->db->select('student_id, activity_id, SUM(fines_amount) as total_fines');
		$this->db->from('fines');
		$this->db->group_by(['student_id', 'activity_id']);
		$summaries = $this->db->get()->result();

		foreach ($summaries as $summary) {
			// Check if record exists in fines_summary
			$exists = $this->db->get_where('fines_summary', [
				'student_id' => $summary->student_id,
				'activity_id' => $summary->activity_id
			])->row();

			// Determine fines status (whether it is paid or unpaid)
			$fines_status = $summary->total_fines > 0 ? 'Unpaid' : 'Paid';

			if ($exists) {
				// Update existing fines summary record
				$this->db->where([
					'student_id' => $summary->student_id,
					'activity_id' => $summary->activity_id
				]);
				$this->db->update('fines_summary', [
					'total_fines' => $summary->total_fines,
					'fines_status' => $fines_status,
					'updated_at' => date('Y-m-d H:i:s') // Adding updated timestamp
				]);
			} else {
				// Insert new fines summary record
				$this->db->insert('fines_summary', [
					'student_id' => $summary->student_id,
					'activity_id' => $summary->activity_id,
					'total_fines' => $summary->total_fines,
					'fines_status' => $fines_status,
					'created_at' => date('Y-m-d H:i:s') // Adding created timestamp
				]);
			}
		}
	}


	//






















	//ACTIVITY DROPDOWN BASED ON LOGGED IN USER

	public function get_filtered_activities()
	{
		$this->db->select('*');
		$this->db->from('activity');

		return $this->db->get()->result();
	}

	// Function to fetch activities for Admin (where org_id and dept_id are NULL) || used for list activity evaluations
	public function get_admin_activities()
	{
		$this->db->select('activity_id, activity_title, status, activity_image');
		$this->db->from('activity');
		$this->db->where('org_id', NULL);
		$this->db->where('dept_id', NULL);
		$query = $this->db->get();

		// Return the result as an array of objects
		return $query->result();
	}


	// When activity is clicked, forms are fetched
	public function get_forms_by_activity($activity_id)
	{
		$this->db->where('activity_id', $activity_id);
		$query = $this->db->get('forms');
		return $query->result();  // Return forms for the specific activity
	}


	// FOR VIEW RESPONSE BUTTON
	public function get_responses_by_form($form_id)
	{
		$this->db->where('form_id', $form_id);
		$query = $this->db->get('evaluation_responses');
		return $query->result();  // This should now include 'evaluation_response_id'
	}


	public function get_student_by_id($student_id)
	{
		$this->db->where('student_id', $student_id);
		$query = $this->db->get('users');
		return $query->row();  // Return a single user record
	}


	//FETCH FORM ANSWERS WHEN CLICKING VIEW RESPONSE BUTTON

	public function get_form_by_id($form_id)
	{
		// Select form details based on form_id
		$this->db->where('form_id', $form_id);
		$query = $this->db->get('forms');

		return $query->row();  // Return a single row (form details)
	}


	public function get_answers_by_evaluation_response($evaluation_response_id)
	{
		// Select the answer, label (question), type (question type), and form_id
		$this->db->select('response_answer.answer, formfields.label, formfields.type, formfields.form_id');
		$this->db->from('response_answer');
		$this->db->join('formfields', 'formfields.form_fields_id = response_answer.form_fields_id');
		$this->db->where('response_answer.evaluation_response_id', $evaluation_response_id);
		$query = $this->db->get();

		return $query->result();  // Return all answers for the evaluation response
	}

	public function get_activity_by_id($activity_id)
	{
		$this->db->where('activity_id', $activity_id);
		$query = $this->db->get('activity'); // Assuming your table name is 'activities'

		return $query->row(); // Returns a single activity
	}

	// END OF EVALUATION 

	// START COMMUNITY SECTION


	// FETCHING ORGANIZATION WHERE THE ADMIN BELONGS - CONTAINS ORGID AND ORGNAME *
	public function admin_org()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('student_org.org_id, organization.org_name');
		$this->db->from('users');
		$this->db->join('student_org', 'student_org.student_id = users.student_id');
		$this->db->join('organization', 'student_org.org_id = organization.org_id');
		$this->db->where('users.student_id', $student_id);
		$this->db->where('users.is_admin', 'Yes');
		$this->db->where('student_org.is_officer', 'Yes');

		$query = $this->db->get();
		return $query->row(); // Returns a single row as an object
	}

	// FETCHING DEPARTMENT WHERE THE ADMIN BELONGS - CONTAINS DEPTID AND DEPTNAME *
	public function admin_dept()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('users.dept_id, department.dept_name');
		$this->db->from('users');
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$this->db->where('users.student_id', $student_id);
		$this->db->where('users.is_admin', 'Yes');
		$this->db->where('users.is_officer_dept', 'Yes');

		$result = $this->db->get()->row();
		return $result;  // Return department data for the logged-in user
	}


	// START FINES


	public function get_activity_fines()
	{
		$this->db->where('organizer', 'Student Parliament'); // Apply WHERE condition before fetching
		return $this->db->get('activity')->result_array(); // Fetch all events
	}

	public function get_students()
	{
		return $this->db->get('users')->result_array(); // Fetch all students
	}

	public function get_fines()
	{
		return $this->db->get('fines')->result_array(); // Fetch all fines
	}

	public function flash_fines()
	{
		$this->db->select('*');
		$this->db->from('fines_summary');
		$this->db->join('users', 'users.student_id = fines_summary.student_id'); // Fixed join condition
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$this->db->join('fines', 'fines.student_id = fines_summary.student_id'); // Ensures fines are matched by both activity_id and student_id
		$this->db->join('activity', 'activity.activity_id = fines.activity_id'); // Fixed join condition
		$this->db->where('activity.organizer', 'Student Parliament'); // Apply correct where condition
		$this->db->order_by('users.student_id, activity.activity_id'); // Order by student and then event/activity

		$query = $this->db->get();
		return $query->result_array(); // Return result as an array
	}

	public function verify_reference($student_id, $reference_number)
	{
		$this->db->where('student_id', $student_id);
		$query = $this->db->get('fines_summary');

		if ($query->num_rows() > 0) {
			$row = $query->row();
			// Check if the reference number matches
			return $row->reference_number_students === $reference_number;
		}

		return false;
	}

	public function update_payment_status($student_id, $reference_number, $mode_of_payment)
	{
		// Check reference match
		$this->db->where('student_id', $student_id);
		$summary = $this->db->get('fines_summary')->row();

		if ($summary &&  $summary->reference_number_students === $reference_number) {
			// Reference number matched – mark as Paid
			$this->db->where('student_id', $student_id);
			$this->db->update('fines_summary', [
				'mode_payment' => $mode_of_payment,
				'reference_number_admin' => $reference_number,
				'fines_status' => 'Paid'
			]);

			$this->db->where('student_id', $student_id);
			$this->db->update('fines', ['status' => 'Paid']);
		} else {
			// Reference number mismatched or not yet assigned – mark as Processing
			$this->db->where('student_id', $student_id);
			$this->db->update('fines_summary', [
				'mode_payment' => $mode_of_payment,
				'reference_number_admin' => $reference_number,
				'fines_status' => 'Pending'
			]);
		}
	}

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

	// MANAGE OFFICER AND PRIVILEGE
	public function manage_privilege()
	{
		// Select the columns you need
		$this->db->select('privilege.*, users.*');  // Adjust the columns to your need
		$this->db->from('privilege');
		$this->db->join('users', 'users.student_id = privilege.student_id', 'left');  // Adjust join type as necessary

		// Execute the query
		$query = $this->db->get();

		// Return the results
		return $query->result();
	}

	public function update_privileges($privileges_data)
	{
		foreach ($privileges_data as $privilege) {
			if (!isset($privilege['privilege_id'])) {
				continue; // Skip if privilege_id is missing
			}

			$data = [];

			// Only update columns that are set in the privilege data
			if (isset($privilege['manage_fines'])) {
				$data['manage_fines'] = $privilege['manage_fines'] === 'Yes' ? 'Yes' : 'No';
			}
			if (isset($privilege['manage_evaluation'])) {
				$data['manage_evaluation'] = $privilege['manage_evaluation'] === 'Yes' ? 'Yes' : 'No';
			}
			if (isset($privilege['manage_applications'])) {
				$data['manage_applications'] = $privilege['manage_applications'] === 'Yes' ? 'Yes' : 'No';
			}
			if (isset($privilege['able_scan'])) {
				$data['able_scan'] = $privilege['able_scan'] === 'Yes' ? 'Yes' : 'No';
			}
			if (isset($privilege['able_create_activity'])) {
				$data['able_create_activity'] = $privilege['able_create_activity'] === 'Yes' ? 'Yes' : 'No';
			}

			// Only proceed if there's at least one column to update
			if (!empty($data)) {
				$this->db->where('privilege_id', $privilege['privilege_id']);
				$this->db->update('privilege', $data);
			}
		}

		return $this->db->affected_rows() > 0;
	}

	public function get_qr_code_by_student_id($student_id)
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('qr_image');
		$this->db->from('users');
		$this->db->where('student_id', $student_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row()->qr_image;
		}

		return null;  // QR code not found
	}


	// GENERAL SETTINGS
	protected $table = 'users'; // Your database table name

	public function insert_batch($data_batch)
	{
		return $this->db->insert_batch($this->table, $data_batch);
	}















	// FETCHING ORGANIZATION WHERE THE STUDENT BELONG *
	public function get_student_organizations($student_id)
	{
		$this->db->select('student_org.org_id');
		$this->db->from('student_org');
		$this->db->where('student_org.student_id', $student_id);

		$query = $this->db->get();
		return $query->result(); // Returns an array of objects
	}

	// FETCHING DEPARTMENT WHERE THE STUDENT BELONG *
	public function get_student_department($student_id)
	{
		$this->db->select('users.dept_id');
		$this->db->from('users');
		$this->db->where('users.student_id', $student_id);

		$query = $this->db->get();
		$result = $query->row();

		return ($result) ? $result : null;
	}

	// FETCHING ADMIN ORG ID *
	public function admin_org_id()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('student_org.org_id');
		$this->db->from('student_org');
		$this->db->join('users', 'student_org.student_id = users.student_id');
		$this->db->join('organization', 'student_org.org_id = organization.org_id');
		$this->db->where('users.student_id', $student_id);
		$this->db->where('users.is_admin', 'Yes');
		$this->db->where('student_org.is_officer', 'Yes');

		$result = $this->db->get()->row();

		// Return only the org_id value instead of the object
		return $result ? $result->org_id : null;
	}

	// FETCHING ADMIN DEPT ID *
	public function admin_dept_id()
	{
		$student_id = $this->session->userdata('student_id');

		$this->db->select('users.dept_id');
		$this->db->from('users');
		$this->db->join('department', 'department.dept_id = users.dept_id');
		$this->db->where('users.student_id', $student_id);
		$this->db->where('users.is_admin', 'Yes');
		$this->db->where('users.is_officer_dept', 'Yes');

		$result = $this->db->get()->row();
		return $result;  // Return department data for the logged-in user
	}

	public function update_is_shared($activity_id)
	{
		$this->db->set('is_shared', 'Yes'); // Set is_shared to Yes (or TRUE)
		$this->db->where('activity_id', $activity_id);
		return $this->db->update('activity'); // Returns TRUE on success
	}



	// <====== ATTENDANCE MONITORING ======>





	public function department_selection()
	{
		// Select all columns from the department table
		$this->db->select('*');
		$this->db->from('department');

		$query = $this->db->get();

		return $query->result();
	}









	public function get_organization()
	{
		$this->db->select('*');
		$this->db->from('organization');

		$query = $this->db->get();

		return $query->result();
	}

	public function fetch_users()
	{
		$this->db->select('users.student_id, users.first_name, users.last_name, users.role, users.dept_id, department.dept_name, attendance.*, student_org.org_id');
		$this->db->from('users');
		$this->db->join('department', 'department.dept_id = users.dept_id', 'left');
		$this->db->join('attendance', 'attendance.student_id = users.student_id', 'left');
		$this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');

		$query = $this->db->get();
		return $query->result();
	}

	// GETTING THE SCHEDULE
	public function get_activity_schedule($activity_id)
	{
		$this->db->select('*');
		$this->db->from('activity');
		$this->db->where('activity_id', $activity_id);
		$query = $this->db->get();

		return $query->row(); // Return a single row object
	}




	public function update_fines($student_id, $activity_id, $update_fines)
	{
		// Ensure that $update_data is not empty before updating
		if (!empty($update_fines)) {
			$this->db->where('student_id', $student_id);
			$this->db->where('activity_id', $activity_id);
			$this->db->update('fines', $update_fines);

			// Check if the update was successful
			if ($this->db->affected_rows() > 0) {
				return true; // Successfully updated
			} else {
				return false; // No changes were made or the record does not exist
			}
		}
		return false; // No data to update
	}


	// <======== FINES =========>

	public function fetch_students()
	{
		$this->db->select('users.student_id, users.first_name, users.last_name, users.role, users.dept_id, department.dept_name, fines.*, student_org.*');
		$this->db->from('users');
		$this->db->join('department', 'department.dept_id = users.dept_id', 'left');
		$this->db->join('fines', 'fines.student_id = users.student_id', 'left');
		$this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');

		$query = $this->db->get();
		return $query->result();
	}

	public function updateFineStatus($student_id, $activity_id, $status)
	{
		$this->db->where('student_id', $student_id);
		$this->db->where('activity_id', $activity_id); // Ensure correct activity
		return $this->db->update('fines', ['is_paid' => $status]); // Update only one record
	}

	public function get_officer_dept()
	{
		$this->db->select('*');
		$this->db->from('department');
		$this->db->join('users', 'department.dept_id = users.dept_id');
		$this->db->where('users.role', 'Officer');
		$this->db->where('users.is_officer_dept', 'Yes');

		$query = $this->db->get();

		return $query->result();
	}

	public function update_status_dept($student_id, $status)
	{
		$this->db->where('student_id', $student_id);
		return $this->db->update('users', ['is_admin' => $status]); // Update only one record
	}

	public function get_officer_org()
	{
		$this->db->select('*');
		$this->db->from('users');
		$this->db->join('student_org', 'student_org.student_id = users.student_id');
		$this->db->join('organization', 'organization.org_id = student_org.org_id');
		$this->db->where('users.role', 'Officer');
		$this->db->where('student_org.is_officer', 'Yes');

		$query = $this->db->get();

		return $query->result();
	}

	public function update_status_org($student_id, $status)
	{
		$this->db->where('student_id', $student_id);
		return $this->db->update('users', ['is_admin' => $status]); // Update only one record
	}










	public function get_attendance($activity_id, $student_id)
	{
		return $this->db->get_where('attendance', [
			'activity_id' => $activity_id,
			'student_id' => $student_id
		])->row();
	}

	public function save_attendance($data)
	{
		$this->db->insert('attendance', $data);
	}


	public function get_student_attendance($student_id)
	{
		return $this->db->where('student_id', $student_id)->get('attendance')->row_array();
	}


	// GENERAL SETTINGS
	public function get_students_without_qr()
	{
		return $this->db->where('qr_image IS NULL')->get('users')->result();
	}

	public function assign_qr($student_id, $qrBase64)
	{
		// Assuming you have a 'students' table with a 'qr_code' column
		$this->db->where('student_id', $student_id);
		$this->db->update('users', ['qr_image' => $qrBase64]);
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

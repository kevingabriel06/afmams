<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    // CHECKING OF ROLES FOR DISPLAYING DIFFERENT PARTS OF SYSTEM
    public function get_roles($student_id){
        $student_id = $this->session->userdata('student_id');

        return $this->db->get_where('users', ['student_id' => $student_id])->row_array();
    }

    // <========= THIS PART IS FOR THE CREATE ACTIVITY =====>
    
    // FETCHING ORGANIZATION WHERE THE ADMIN BELONGS - CONTAINS ORGID AND ORGNAME *
    public function admin_org() {
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
    public function admin_dept() {
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

    // INSERTING ACTIVITY TO DATABASE *
    public function save_activity($data) {
       
        // Prepare data for insertion
        $data = [
            'activity_title' => $this->input->post('title'),
            'start_date' => $this->input->post('date_start'),
            'end_date' => $this->input->post('date_end'),
            'registration_deadline' => $this->input->post('registration_deadline'),
            'registration_fee' => $this->input->post('registration_fee'),
            'dept_id' => $this->input->post('dept'),
            'org_id' => $this->input->post('org'),
            'am_in' => $this->input->post('am_in'),
            'am_out' => $this->input->post('am_out'),
            'pm_in' => $this->input->post('pm_in'),
            'pm_out' => $this->input->post('pm_out'),
            'description' => $this->input->post('description'),
            'activity_image' => $this->upload->data('file_name'),
            'privacy' => $this->input->post('privacy'),
            'fines' => $this->input->post('fines')
        ];
    
        // Insert the data into the database
        return $this->db->insert('activity', $data);
    }

    // UPDATING ACTIVITY TO DATABASE *
    public function update_activity($activity_id, $data) {
        $this->db->where('activity_id', $activity_id);
        return $this->db->update('activity', $data);
    }
    
    // GETTING ACTIVITY TABLE WITH ORGANIZER NAME *
    public function get_activities() {
        $this->db->select('activity.*, department.dept_name, organization.org_name');
        $this->db->from('activity');
        $this->db->join('organization', 'organization.org_id = activity.org_id', 'left');
        $this->db->join('department', 'department.dept_id = activity.dept_id', 'left');


        $query = $this->db->get();
        return $query->result(); 
    }

    // FETCHING SPECIFIC ACTIVITY USING ACTIVITY ID 
    public function get_activity($activity_id) {
        $this->db->select('activity.*, department.dept_name, organization.org_name');
        $this->db->from('activity');
        $this->db->join('organization', 'organization.org_id = activity.org_id', 'left');
        $this->db->join('department', 'department.dept_id = activity.dept_id', 'left');
    
        if ($activity_id !== null) {
            $this->db->where('activity.activity_id', $activity_id);
            return $this->db->get()->row_array(); // Fetch single record
        }
    
        $query = $this->db->get();
        return $query->result(); // Fetch multiple records
    }

    // FETCHING EXCUSE APPLICATION PER EVENT *
    public function fetch_application($activity_id) {
        return $this->db->get_where('activity', ['activity_id' => $activity_id])->row_array();
    }

    // FETCHING DETAILS OF APPLICATION
    public function fetch_letters(){
        $this->db->select('*');
        $this->db->from('excuse_application');
        $this->db->join('users', 'excuse_application.student_id = users.student_id');
        $query = $this->db->get();

        return $query->result(); 
    }

    // FETCHING EXCUSE LETTER PER STUDENT
    public function review_letter($excuse_id) {
        $this->db->select('*');
        $this->db->from('excuse_application');
        $this->db->join('users', 'excuse_application.student_id = users.student_id');
        $this->db->join('department', 'users.dept_id = department.dept_id');
        $this->db->where('excuse_application.excuse_id', $excuse_id); // Add condition to filter by excuse_id
        $query = $this->db->get();
    
        // Return the result as an array
        return $query->row_array();
    }

    // UPDATING STATUS AND REMARKS FOR THE EXCUSE APPLICATION *
    public function updateApprovalStatus($data) {
        
        $this->db->where('excuse_id', $data['excuse_id']);  // Ensure you use the correct column for identification
        $this->db->update('excuse_application', $data);
    
        // Check if any rows were affected (successful update)
        return $this->db->affected_rows() > 0;
    }


    //  <=== QUERY FOR COMMUNITY SECTION ===>

    // FETCHING USER TABLE BY STUDENT ID
    public function get_user() {
    
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
    public function get_all_posts() {
        $this->db->select('*');
        $this->db->from('post');
        $this->db->join('users', 'post.student_id = users.student_id', 'left');
        $this->db->join('department', 'department.dept_id = post.dept_id', 'left');
        $this->db->join('organization', 'organization.org_id = post.org_id', 'left');
        $this->db->order_by('post.post_id', 'DESC');
        
        $query = $this->db->get();
        return $query->result(); 
    }

    // ADDING POST
    public function insert_data($data){
        return $this->db->insert('post', $data);
    }

    // LIKES FUNCTIONALITY
    public function like_post($post_id, $student_id) {
        // Insert a like into the database
        $data = [
            'post_id' => $post_id,
            'student_id' => $student_id
        ];
        $this->db->insert('likes', $data); // Assuming a 'likes' table exists
        $this->update_like_count($post_id); // Update like count in the post table
    }

    public function unlike_post($post_id, $student_id) {
        // Remove the like from the database
        $this->db->delete('likes', ['post_id' => $post_id, 'student_id' => $student_id]);
        $this->update_like_count($post_id); // Update like count in the post table
    }

    // ADDING OF COMMENTS
    public function add_comment($data = null) {
        $data = [
            'post_id' => $this->input->post('post_id'),
            'student_id' => $this->session->userdata('student_id'),
            'content' => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s') // Use PHP's date() function directly
        ];
    
        return $this->db->insert('comment', $data);  // Assuming 'comments' is the correct table name
    }

    // DELETION OF POST
    public function delete_post($post_id) {
        $this->db->where('post_id', $post_id)->delete('comment');
        $this->db->where('post_id', $post_id)->delete('likes');
        $this->db->where('post_id', $post_id);
        return $this->db->delete('post'); // Assuming your table name is 'posts'
    }

    // GETTING LIKE COUNT
    public function get_like_count($post_id) {
        // Get the current like count for the post
        $this->db->where('post_id', $post_id);
        $this->db->from('likes');
        return $this->db->count_all_results();
    }

    // COUNTING THE COMMENTS
    public function get_comment_count($post_id) {
        $this->db->where('post_id', $post_id);
        $this->db->from('comment'); // Ensure this matches your table name
        return $this->db->count_all_results();
    }

    // CHECK THE USER IF ALREADY LIKE THE POST
    public function user_has_liked($post_id, $student_id) {
        // Check if the user has already liked the post
        $this->db->where('post_id', $post_id);
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('likes');
        return $query->num_rows() > 0;
    }

    // UPDATING LIKE COUNT
    public function update_like_count($post_id) {
        // Update the like count for the post in the 'posts' table
        $like_count = $this->get_like_count($post_id);
        $this->db->where('post_id', $post_id);
        $this->db->update('post', ['like_count' => $like_count]);
    }

    // FETCH COMMENTS BY POST ID
    public function get_comments_by_post($post_id) {
        $this->db->select('comment.*, users.*');
        $this->db->from('comment');
        $this->db->join('users', 'users.student_id = comment.student_id');
        $this->db->where('comment.post_id', $post_id);
        $this->db->order_by('comment.created_at', 'DESC');
        
        return $this->db->get()->result(); // Return comments
    }

    public function get_latest_comment($post_id) {
        $this->db->select('c.*, s.*');
        $this->db->from('comment c');
        $this->db->join('users s', 'c.student_id = s.student_id', 'left');
        $this->db->where('c.post_id', $post_id);
        $this->db->order_by('c.created_at', 'DESC'); // Assuming you have a created_at column
        $this->db->limit(1); // Get only the latest comment
    
        $query = $this->db->get();
        return $query->row(); // Return a single comment object
    }

    // FETCHING ORGANIZATION WHERE THE STUDENT BELONG *
    public function get_student_organizations($student_id) {
        $this->db->select('student_org.org_id');
        $this->db->from('student_org');
        $this->db->where('student_org.student_id', $student_id);
        
        $query = $this->db->get();
        return $query->result(); // Returns an array of objects
    } 
    
    // FETCHING DEPARTMENT WHERE THE STUDENT BELONG *
    public function get_student_department($student_id) {
        $this->db->select('users.dept_id');
        $this->db->from('users');
        $this->db->where('users.student_id', $student_id);
        
        $query = $this->db->get();
        $result = $query->row(); 
        
        return ($result) ? $result : null; 
    }

    // FETCHING ADMIN ORG ID *
    public function admin_org_id() {
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
    public function admin_dept_id() {
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

    public function update_is_shared($activity_id) {
        $this->db->set('is_shared', 'Yes'); // Set is_shared to Yes (or TRUE)
        $this->db->where('activity_id', $activity_id);
        return $this->db->update('activity'); // Returns TRUE on success
    }

    //========CREATE EVALUATION START
    public function create_form_with_fields($formData, $fieldsData)
    {
        // Start the transaction
        $this->db->trans_start();

        // Insert form data into the 'forms' table
        $this->db->insert('forms', $formData);
        
        // Fallback to get the last insert ID, based on the DB driver
        if (method_exists($this->db, 'insert_id')) {
            $formId = $this->db->insert_id(); // MySQL or compatible DB
        } else {
            // For Cubrid or other DB drivers, use a custom approach to retrieve the last insert ID
            $this->db->select('MAX(form_id) AS form_id');
            $query = $this->db->get('forms');
            $result = $query->row(); // Fetch the latest inserted ID
            $formId = isset($result->form_id) ? $result->form_id : null; // Return form_id or null if not found
        }

        if ($formId) {
            // Add form_id to each field before inserting into formfields table
            foreach ($fieldsData as &$field) {
                $field['form_id'] = $formId;
            }

            // Insert form fields into the 'formfields' table
            $this->db->insert_batch('formfields', $fieldsData);
        } else {
            // If form insertion failed, rollback the transaction
            $this->db->trans_rollback();
            return false; // Optionally return false to indicate failure
        }

        // Complete the transaction
        $this->db->trans_complete();

        // Return the status of the transaction (TRUE if successful, FALSE otherwise)
        return $this->db->trans_status();
    }


        //ACTIVITY DROPDOWN BASED ON LOGGED IN USER

        public function get_filtered_activities($student_id)
        {
            $this->db->select('*');
            $this->db->from('activity');
            $this->db->where('org_id', NULL);
            $this->db->where('dept_id', NULL);
        
            return $this->db->get()->result();
        }
        
        	//====EVALUATION RESPONSES START

			// Function to fetch activities for Admin (where org_id and dept_id are NULL) || used for list activity evaluations
			public function get_admin_activities() {
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


	//====EVALUATION RESPONSES END

        

    //========CREATE EVALUATION END
    
    // <====== ATTENDANCE ======>
    public function get_department(){
        $this->db->select('*');
        $this->db->from('department');

        $query = $this->db->get();

        return $query->result(); 
    }

    public function get_organization(){
        $this->db->select('*');
        $this->db->from('organization');

        $query = $this->db->get();

        return $query->result(); 
    }

    public function fetch_users(){
        $this->db->select('users.student_id, users.first_name, users.last_name, users.dept_id, department.dept_name, attendance.*, student_org.*');
        $this->db->from('users');
        $this->db->join('department', 'department.dept_id = users.dept_id', 'left');
        $this->db->join('attendance', 'attendance.student_id = users.student_id', 'left');
        $this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');
        
        $query = $this->db->get();
        return $query->result();
    }

    // GETTING THE SCHEDULE
    public function get_activity_schedule($activity_id) {
        $this->db->select('*');
        $this->db->from('activity');
        $this->db->where('activity_id', $activity_id);
        $query = $this->db->get();
    
        return $query->row(); // Return a single row object
    }

    public function update_attendance($student_id, $activity_id, $update_data) {
        // Ensure that $update_data is not empty before updating
        if (!empty($update_data)) {
            $this->db->where('student_id', $student_id);
            $this->db->where('activity_id', $activity_id);
            $this->db->update('attendance', $update_data);
            
            // Check if the update was successful
            if ($this->db->affected_rows() > 0) {
                return true; // Successfully updated
            } else {
                return false; // No changes were made or the record does not exist
            }
        }
        return false; // No data to update
    }
    
    public function get_attendance_record($student_id, $activity_id) {
        $this->db->select('*');
        $this->db->from('attendance');
        $this->db->where('student_id', $student_id);
        $this->db->where('activity_id', $activity_id);
    
        $query = $this->db->get(); // Execute query
    
        return $query->row(); // Return a single record (or use ->result() for multiple)
    }
    
    public function update_fines($student_id, $activity_id, $update_fines) {
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

    public function fetch_students(){
        $this->db->select('users.student_id, users.first_name, users.last_name, users.dept_id, department.dept_name, fines.*, student_org.*');
        $this->db->from('users');
        $this->db->join('department', 'department.dept_id = users.dept_id', 'left');
        $this->db->join('fines', 'fines.student_id = users.student_id', 'left');
        $this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');
        
        $query = $this->db->get();
        return $query->result();
    }

    public function updateFineStatus($student_id, $activity_id, $status) {
        $this->db->where('student_id', $student_id);
        $this->db->where('activity_id', $activity_id); // Ensure correct activity
        return $this->db->update('fines', ['is_paid' => $status]); // Update only one record
    }

    public function get_officer_dept(){
        $this->db->select('*');
        $this->db->from('department');
        $this->db->join('users', 'department.dept_id = users.dept_id');
        $this->db->where('users.role' , 'Officer');
        $this->db->where('users.is_admin', 'Yes');
        $this->db->where('users.is_officer_dept', 'Yes');

        $query = $this->db->get();

        return $query->result(); 
    }

    public function update_status_dept($student_id, $status) {
        $this->db->where('student_id', $student_id);
        return $this->db->update('users', ['is_admin' => $status]); // Update only one record
    }

    public function get_officer_org(){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('student_org', 'student_org.student_id = users.student_id');
        $this->db->join('organization', 'organization.org_id = student_org.org_id');
        $this->db->where('users.role' , 'Officer');
        $this->db->where('users.is_admin', 'Yes');
        $this->db->where('student_org.is_officer', 'Yes');

        $query = $this->db->get();

        return $query->result(); 
    }

    public function update_status_org($student_id, $status) {
        $this->db->where('student_id', $student_id);
        return $this->db->update('users', ['is_admin' => $status]); // Update only one record
    }










    public function get_attendance($activity_id, $student_id) {
        return $this->db->get_where('attendance', [
            'activity_id' => $activity_id,
            'student_id' => $student_id
        ])->row();
    }

    public function save_attendance($data) {
        $this->db->insert('attendance', $data);
    }
    
    
    public function get_student_attendance($student_id) {
        return $this->db->where('student_id', $student_id)->get('attendance')->row_array();
    }
    

}

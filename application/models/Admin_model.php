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


    
    // FETCHING ORGANIZATION WHERE THE USER BELONGS *
    public function get_organizer_org() {
        $student_id = $this->session->userdata('student_id');

        $this->db->select('student_org.org_id');
        $this->db->from('users');
        $this->db->join('student_org', 'student_org.student_id = users.student_id');
        $this->db->where('users.student_id', $student_id);
        $this->db->where('users.is_admin', 'Yes');
        $this->db->where('student_org.is_officer', 'Yes');

        $result = $this->db->get()->row();
        return $result ? $result->org_id : null;
    }

    // FETCHING DEPARTMENT WHERE THE USER BELONGS *
    public function get_organizer_dept() {
        $student_id = $this->session->userdata('student_id');

        $this->db->select('dept_id');
        $this->db->from('users');
        $this->db->where('users.student_id', $student_id);
        $this->db->where('users.is_admin', 'Yes');
        $this->db->where('users.is_officer_dept', 'Yes');

        $result = $this->db->get()->row();
        return $result ? $result->dept_id : null;
    }

    // FETCHING SPECIFIC ACTIVITY USING ACTIVITY ID 
    public function get_activity($activity_id = null) {
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

    // PREPARING ORG ID FOR THE CHECKING OF DATA *
    public function get_student_organizations($student_id) {
        $this->db->select('student_org.org_id');
        $this->db->from('student_org');
        $this->db->where('student_org.student_id', $student_id);
        
        $query = $this->db->get();
        return $query->result(); // Returns an array of objects
    } 
    
    // PREPARING DEPT ID FOR THE CHECKING OF DATA *
    public function get_student_department($student_id) {
        $this->db->select('users.dept_id');
        $this->db->from('users');
        $this->db->where('users.student_id', $student_id);
        
        $query = $this->db->get();
        $result = $query->row(); 
        
        return ($result) ? $result : null; // ✅ Returns null if no result is found
    }

    // GETTING POST DETAILS AND AUTHOR *
    public function get_all_posts() {
        $this->db->select('post.*, users.first_name, users.last_name, users.profile_pic, department.dept_name, organization.org_name');
        $this->db->from('post');
        $this->db->join('users', 'post.student_id = users.student_id', 'left');
        $this->db->join('department', 'department.dept_id = post.dept_id', 'left');
        $this->db->join('organization', 'organization.org_id = post.org_id', 'left');
        $this->db->order_by('post.post_id', 'DESC');
        
        $query = $this->db->get();
        return $query->result(); 
    }

    // FECTHING ACTIVITIES FOR COMMUNITY AND ACTVITY LIST *
    public function get_activities_with_organizer_name() {
        $this->db->select('activity.*, department.dept_name, organization.org_name');
        $this->db->from('activity');
        $this->db->join('organization', 'organization.org_id = activity.org_id', 'left');
        $this->db->join('department', 'department.dept_id = activity.dept_id', 'left');
        $this->db->where('activity.start_date >', date('Y-m-d')); 


        $query = $this->db->get();
        return $query->result(); // Returns an array of objects
    }

    // FETCHING USER TABLE AND WHERE THE USER BELONGS
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

    public function get_like_count($post_id) {
        // Get the current like count for the post
        $this->db->where('post_id', $post_id);
        $this->db->from('likes');
        return $this->db->count_all_results();
    }

    public function update_like_count($post_id) {
        // Update the like count for the post in the 'posts' table
        $like_count = $this->get_like_count($post_id);
        $this->db->where('post_id', $post_id);
        $this->db->update('post', ['like_count' => $like_count]);
    }

    public function user_has_liked($post_id, $student_id) {
        // Check if the user has already liked the post
        $this->db->where('post_id', $post_id);
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('likes');
        return $query->num_rows() > 0;
    }

    //Check if a user has liked a specific post
    public function has_liked($student_id, $post_id) {
        $this->db->where('student_id', $student_id);
        $this->db->where('post_id', $post_id);
        $query = $this->db->get('likes'); // Assuming the table name is 'likes'
        return $query->num_rows() > 0; // If there is a like record
    }

    public function check_like($student_id, $post_id) {
        $this->db->where('student_id', $student_id);
        $this->db->where('post_id', $post_id);
        $query = $this->db->get('likes'); // Assuming post_likes table is used for tracking likes

        return $query->num_rows() > 0;
    }

    // counting the comments
    public function get_comment_count($post_id) {
        $this->db->where('post_id', $post_id);
        $this->db->from('comment'); // Ensure this matches your table name
        return $this->db->count_all_results();
    }

     // Fetch comments by post_id
     public function get_comments_by_post($post_id, $limit = 2, $offset = 0) {
        $this->db->select('comment.*, users.name, users.profile_pic');
        $this->db->from('comment');
        $this->db->join('users', 'users.student_id = comment.student_id');
        $this->db->where('comment.post_id', $post_id);
        $this->db->order_by('comment.created_at', 'DESC');
        $this->db->limit($limit, $offset); // Limit the number of comments
        
        return $this->db->get()->result(); // Return comments
    }

    public function get_comment_by_id($comment_id) {
        $this->db->select('c.*, u.profile_pic, u.name');
        $this->db->from('comment c');
        $this->db->join('users u', 'u.student_id = c.student_id');
        $this->db->where('c.id', $comment_id);
        $query = $this->db->get();
        return $query->row();  // Return the comment object
    }
   
    // Insert comment into the database for community posts
    public function add_comment($data = null) {
        $data = [
            'post_id' => $this->input->post('post_id'),
            'student_id' => $this->session->userdata('student_id'),
            'content' => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s') // Use PHP's date() function directly
        ];
    
        return $this->db->insert('comment', $data);  // Assuming 'comments' is the correct table name
    }

    // Insert comment into the database for community posts
    public function insert_data($data){
        return $this->db->insert('post', $data);
    }

    public function update_is_shared($activity_id) {
        $this->db->set('is_shared', 'Yes'); // Set is_shared to Yes (or TRUE)
        $this->db->where('activity_id', $activity_id);
        return $this->db->update('activity'); // Returns TRUE on success
    }

    public function activity_posted() {
        $student_id = $this->session->userdata('student_id');

        // Subquery to filter organizations where the student is an officer and an admin
        $subquery = $this->db->select('org_id')
                             ->from('student_org')
                             ->where('student_id', $student_id)
                             ->where('is_officer', 'Yes')
                            //  ->where('is_admin', 'Yes')
                             ->get_compiled_select();

        // Main query
        $this->db->select('activity.*, organization.org_name');
        $this->db->from('activity');
        $this->db->join('organization', 'organization.org_id = activity.org_id');
        $this->db->where("activity.org_id IN ($subquery)", NULL, FALSE);
        $this->db->where('is_shared', 'Yes');
        $this->db->order_by('activity.start_date', 'DESC');

        // Execute the query
        $query = $this->db->get();
        return $query->result();
    }

    // evaluation
    public function create_form_with_fields($formData, $fieldsData) {
        $this->db->trans_start();
    
        // Insert form data
        $this->db->insert('forms', $formData);
        $formId = $this->db->insert_id();
    
        // Add form_id to each field and insert fields
        foreach ($fieldsData as &$field) {
            $field['form_id'] = $formId;
        }
        $this->db->insert_batch('formfields', $fieldsData);
    
        $this->db->trans_complete();
    
        return $this->db->trans_status(); // Returns TRUE if successful, FALSE otherwise
    }
    
    


    

    public function fetch_users(){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('department', 'department.dept_id = users.dept_id');
        $this->db->join('attendance', 'attendance.student_id = users.student_id');
        $query = $this->db->get();

        return $query->result(); 
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
    

}

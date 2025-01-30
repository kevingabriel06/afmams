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

    //Getting the activity
    public function get_activities() {
        $activity = $this->db->get_where('activity')->result();
        return $activity;
    }

     // Fetch a specific activity by ID
    public function get_activity($activity_id) {
        return $this->db->get_where('activity', ['activity_id' => $activity_id])->row_array();
    }

    public function save_activity($data) {
        $data = [
            'activity_title'  => $this->input->post('title'),
            'start_date' => $this->input->post('date_start'),
            'end_date' => $this->input->post('date_end'),
            'registration_deadline' => $this->input->post('registration_deadline'),
            'registration_fee' => $this->input->post('registration_fee'),
            'am_in' => $this->input->post('am_in'),
            'am_out' => $this->input->post('am_out'),
            'pm_in' => $this->input->post('pm_in'),
            'pm_out' => $this->input->post('pm_out'),
            'description' => $this->input->post('description'),
            'activity_image' => $this->upload->data('file_name'),
            'privacy' => $this->input->post('privacy')
        ];
 
        $result = $this->db->insert('activity', $data);
        return $result;
    }

    //===========> COMMUNITY SECTION
    public function get_user() {
        
        // Get the student_id from the session
        $student_id = $this->session->userdata('student_id');

        // Check if student_id exists in the session
        if ($student_id) {
            // Query the database to get student details
            $this->db->select('*'); // Select all columns or specific ones as needed
            $this->db->from('users'); // Replace 'students' with your student table name
            $this->db->where('student_id', $student_id); // Match the session student_id
            
            $query = $this->db->get();
    
            // Return the student details if found
            if ($query->num_rows() > 0) {
                return $query->row(); // Return a single student as an object
            } else {
                return null; // No student found
            }
        } else {
            // If student_id is not set in the session
            return null;
        }
    }

    public function get_users() {
        // Select all columns
        $this->db->select('*');
        // Specify the table you want to fetch data from
        $this->db->from('users');  // Assuming 'users' is the table name
        // Execute the query
        $query = $this->db->get();
        
        // Return the result
        if ($query->num_rows() > 0) {
            return $query->result(); // Return the result as an array of objects
        } else {
            return null; // Return null if no users are found
        }
    }

    public function get_post() {
        // Build the query to select all posts and the user's name from the 'post' table
        $this->db->select('post.*, users.name AS name, users.profile_pic AS profile_pic')
                 ->from('post')
                 ->join('users', 'users.student_id = post.student_id', 'left'); // Join the 'users' table with a LEFT JOIN
        
        $query = $this->db->get(); // Execute the query
    
        // Check if there are results
        if ($query->num_rows() > 0) {
            return $query->result(); // Return all posts as an array of objects
        } else {
            return []; // Return an empty array if no posts are found
        }
    }
    
    public function getOrgIdByStudent($student_id) {
        // Use the query builder to construct the query
        $this->db->select('student_org.org_id, organization.org_name'); // Include org_name in the select statement
        $this->db->from('student_org');
        $this->db->join('organization', 'student_org.org_id = organization.org_id');
        
        // Add conditions for student_id, role checks (is_admin and is_officer)
        $this->db->where('student_org.student_id', $student_id);
        $this->db->where('student_org.is_admin', 'Yes');
        $this->db->where('student_org.is_officer', 'Yes');
        
        // Execute the query
        $query = $this->db->get();
        
        // Check if the query returned a result
        if ($query->num_rows() > 0) {
            return $query->row();  // Return the first row (org_id and org_name)
        }
        return null;  // Return null if no result found
    }

    // =====> LIKES
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
    public function has_liked($student_id, $post_id)
    {
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
    public function get_comment_count($post_id)
{
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

    public function get_comment_by_id($comment_id)
    {
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
    public function insert_data($data)
    {
        return $this->db->insert('post', $data);
    }

    // checking organization where the user belong
    public function check_admin_org($student_id = null){
        $student_id = $this->session->userdata('student_id');  // Get the student ID from session
    
        // Query the database to check if the student is both an officer and admin
        $this->db->select('org_id');
        $this->db->from('student_org');
        $this->db->where('is_officer', 'Yes');
        $this->db->where('is_admin', 'Yes');
        $this->db->where('student_id', $student_id);
    
        $query = $this->db->get();  // Execute the query
    
        // Check if any rows are returned
        if ($query->num_rows() > 0) {
            // Return the first row's org_id value
            return $query->row()->org_id; // Use row() to fetch a single row and access org_id directly
        } else {
            // Return 0 if no results found
            return 0;
        }
    }

    public function check_admin_dept($student_id = null) {
        $student_id = $this->session->userdata('student_id');  // Get the student ID from session
    
        // Query the database to check if the student is both a departmental officer and admin
        $this->db->select('dept_id');
        $this->db->from('users');
        $this->db->where('is_officer_dept', 'Yes');
        $this->db->where('is_admin', 'Yes');
        $this->db->where('student_id', $student_id);
    
        $query = $this->db->get();  // Execute the query
    
        // Check if any rows are returned
        if ($query->num_rows() > 0) {
            // Return the first row's dept_id value
            return $query->row()->dept_id; // Use row() to fetch a single row and access dept_id directly
        } else {
            // Return 0 if no results found
            return 0;
        }
    }


    public function get_activity_organized($limit = 3) {
        $student_id = $this->session->userdata('student_id');

        $subquery = $this->db->select('org_id')
                             ->from('student_org')
                             ->where('student_id', $student_id)
                             ->get_compiled_select();

        // Main query using the compiled subquery
        $this->db->select('activity.*, organization.org_name');
        $this->db->from('activity');
        $this->db->join('organization', 'organization.org_id = activity.org_id');
        $this->db->where("activity.org_id IN ($subquery)", NULL, FALSE);
        $this->db->order_by('activity.start_date', 'DESC');
        $this->db->limit($limit);

        // Execute query
        $query = $this->db->get();

        return $query->result(); 
        
    }

    public function get_admin_officer_activities() {
        $student_id = $this->session->userdata('student_id');

        // Subquery to filter organizations where the student is an officer and an admin
        $subquery = $this->db->select('org_id')
                             ->from('student_org')
                             ->where('student_id', $student_id)
                             ->where('is_officer', 'Yes')
                             ->where('is_admin', 'Yes')
                             ->get_compiled_select();

        // Main query
        $this->db->select('activity.*, organization.org_name');
        $this->db->from('activity');
        $this->db->join('organization', 'organization.org_id = activity.org_id');
        $this->db->where("activity.org_id IN ($subquery)", NULL, FALSE);
        $this->db->order_by('activity.start_date', 'DESC');

        // Execute the query
        $query = $this->db->get();
        return $query->result();
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
                             ->where('is_admin', 'Yes')
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
    public function create_form_with_fields($formData, $fieldsData)
    {
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
    
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Student_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    // CHECKING OF ROLES FOR DISPLAYING DIFFERENT PARTS OF SYSTEM
    public function get_roles($student_id)
    {
        $this->db->select('role');
        $this->db->from('users');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();

        return $query->row_array(); // Ensure it returns an array
    }


    //POST LIKES, COMMENTS START


    // Insert like for a post and update the like_count in the post table
    public function like_post($post_id, $student_id)
    {
        // Check if the user already liked the post
        $this->db->where('post_id', $post_id);
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('likes');

        if ($query->num_rows() == 0) {
            // Insert a like if the user hasn't already liked the post
            $data = array(
                'post_id' => $post_id,
                'student_id' => $student_id,
                'liked_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('likes', $data);

            // Increment the like_count in the 'post' table
            $this->db->set('like_count', 'like_count + 1', FALSE)
                ->where('post_id', $post_id)
                ->update('post');
        }
    }



    //POST LIKES COMMENTS END

    // Get attendance history for a specific student
    public function get_attendance_history($student_id, $search = '', $status = 'all')
    {
        $this->db->select('act.activity_title AS Activity, 
                       act.start_date AS Date, 
                       a.am_in AS AM_IN, 
                       a.am_out AS AM_OUT, 
                       a.pm_in AS PM_IN, 
                       a.pm_out AS PM_OUT, 
                       a.attendance_status AS Status, 
                       act.dept_id, 
                       act.org_id, 
                       dep.dept_name, 
                       org.org_name');
        $this->db->from('attendance a');
        $this->db->join('activity act', 'act.activity_id = a.activity_id');
        $this->db->join('department dep', 'dep.dept_id = act.dept_id', 'left');
        $this->db->join('organization org', 'org.org_id = act.org_id', 'left');
        $this->db->where('a.student_id', $student_id);

        if (!empty($search)) {
            $this->db->like('act.title', $search);
        }

        if ($status !== 'all') {
            $this->db->where('a.attendance_status', $status);
        }

        $query = $this->db->get();
        $attendances = $query->result();

        // Add organizer and status logic
        foreach ($attendances as $attendance) {
            // First, check the database status
            $status = isset($attendance->Status) ? $attendance->Status : 'N/A';

            // Override status based on attendance times
            if ($status === 'N/A') {
                // If status is not provided, check times
                if (is_null($attendance->AM_IN) && is_null($attendance->AM_OUT) && is_null($attendance->PM_IN) && is_null($attendance->PM_OUT)) {
                    $attendance->attendance_status = "Absent";
                } elseif (is_null($attendance->AM_IN) || is_null($attendance->AM_OUT) || is_null($attendance->PM_IN) || is_null($attendance->PM_OUT)) {
                    $attendance->attendance_status = "Incomplete";
                } else {
                    $attendance->attendance_status = "Present";
                }
            } else {
                // Keep the status from the database if available
                $attendance->attendance_status = $status;
            }

            // Set the status badge
            switch ($attendance->attendance_status) {
                case 'Present':
                    $attendance->status_badge = "badge-success"; // Badge for Present
                    break;
                case 'Absent':
                    $attendance->status_badge = "badge-danger"; // Badge for Absent
                    break;
                case 'Incomplete':
                    $attendance->status_badge = "badge-warning"; // Badge for Incomplete
                    break;
                default:
                    $attendance->status_badge = "badge-secondary"; // Default Badge
            }

            // Set the organizer name based on dept_id or org_id
            if (!is_null($attendance->dept_id)) {
                $attendance->organizer = $attendance->dept_name;
            } elseif (!is_null($attendance->org_id)) {
                $attendance->organizer = $attendance->org_name;
            } else {
                $attendance->organizer = "Student Parliament";
            }
        }

        return $attendances;
    }


    //LIST OF ACTIVITY
    public function get_activities_for_users($student_id, $dept_id)
    {
        // Get the organizations the student belongs to
        $this->db->select('org_id');
        $this->db->from('student_org');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();
        $org_ids = array_column($query->result_array(), 'org_id');

        // Subquery: Get private activities where org_id and dept_id are NULL
        $this->db->select('activity_id');
        $this->db->from('activity');
        $this->db->where('dept_id IS NULL');
        $this->db->where('org_id IS NULL');
        $this->db->where('privacy', 'private');
        $subquery = $this->db->get_compiled_select();

        // Fetch activities with status and formatted dates
        $this->db->select('a.activity_id, a.activity_title, a.start_date, a.end_date, a.registration_fee, a.am_in, a.activity_image, 
                        a.status,
                        COALESCE(d.dept_name, o.org_name, "Student Parliament") AS organizer,
                        DATE_FORMAT(a.start_date, "%b %d, %Y") AS activity_date, 
                        DATE_FORMAT(a.start_date, "%h:%i %p") AS start_time,
                        DATE_FORMAT(a.end_date, "%h:%i %p") AS end_time,
                        CASE 
                            WHEN a.start_date > CURDATE() THEN 1  -- Upcoming
                            WHEN a.start_date <= CURDATE() AND a.end_date >= CURDATE() THEN 2  -- Ongoing
                            ELSE 3  -- Completed
                        END AS activity_order');
        $this->db->from('activity a');
        $this->db->join('department d', 'd.dept_id = a.dept_id', 'left');
        $this->db->join('organization o', 'o.org_id = a.org_id', 'left');

        $this->db->group_start();
        $this->db->where('a.privacy', 'public');
        $this->db->or_group_start();
        $this->db->where('a.dept_id IS NULL');
        $this->db->where('a.org_id IS NULL');
        $this->db->where('a.privacy', 'public');
        $this->db->group_end();
        $this->db->or_where('a.dept_id', $dept_id);
        if (!empty($org_ids)) {
            $this->db->or_where_in('a.org_id', $org_ids);
        }
        $this->db->group_end();

        $this->db->where("a.activity_id NOT IN ($subquery)", null, false);

        // Sorting logic to ensure upcoming first, then ongoing, then completed activities
        $this->db->order_by('activity_order', 'ASC'); // Sort by activity order: upcoming, ongoing, completed
        $this->db->order_by('a.start_date', 'ASC'); // Secondary sort by start_date for tie-breaking within each category

        $query = $this->db->get();
        return $query->result_array();
    }






    //EXCUSE APPLICATION

    //get users table data to pre-populate some fields

    public function get_student_details($student_id)
    {
        // Query to fetch student details from the 'users' table
        $this->db->select('first_name, last_name, dept_id'); // Select necessary fields
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('users'); // Assuming 'users' is the table name

        if ($query->num_rows() > 0) {
            $student = $query->row_array();

            // Get the department name using dept_id from the 'department' table
            $this->db->select('dept_name');
            $this->db->where('dept_id', $student['dept_id']);
            $department_query = $this->db->get('department'); // Assuming 'department' is the table name

            if ($department_query->num_rows() > 0) {
                $student['department_name'] = $department_query->row_array()['dept_name']; // Fetch department name
            } else {
                $student['department_name'] = ''; // No department found
            }

            return $student;
        } else {
            return false; // If no data found
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



    //EXCUSE APPLICATION SUBMIT TO DATABASE
    public function insert_application($data)
    {
        // Insert the data into the 'excuse_application' table
        return $this->db->insert('excuse_application', $data);
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







    //DISPLAY EVALUATION FORM INFORMATION

    public function get_open_forms($student_id)
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
            -- Check if the current time is greater than the end date
            IF(NOW() > ef.end_date, 0, 1) AS is_form_open
        FROM forms ef
        JOIN activity a ON ef.activity_id = a.activity_id
        LEFT JOIN department d ON a.dept_id = d.dept_id
        LEFT JOIN organization o ON a.org_id = o.org_id
        LEFT JOIN evaluation_responses er ON ef.form_id = er.form_id AND er.student_id = ?  -- Checking if answered
        WHERE ef.end_date >= NOW() 
        AND er.evaluation_response_id IS NULL  -- Only select forms that have not been answered
        AND (
            (a.dept_id IS NULL AND a.org_id IS NULL) 
            OR a.dept_id = (SELECT dept_id FROM users WHERE student_id = ?)
            OR a.org_id IN (SELECT org_id FROM student_org WHERE student_id = ?)
        )
    ", array($student_id, $student_id, $student_id));

        $result = $query->result();

        if (!$result) {
            return []; // Ensure it returns an empty array if no results are found
        }

        return $result;
    }



    // Fetch Form Details
    public function get_form_details($form_id)
    {
        return $this->db->get_where('forms', ['form_id' => $form_id])->row();
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







    //FOR PROFILE SETTINGS

    public function get_student_full_details($student_id)
    {
        // Query to fetch basic student details from the 'users' table
        $this->db->select('first_name, middle_name, last_name, email, profile_pic, dept_id, year_level');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $student = $query->row_array();

            // Set default profile picture if it's empty
            $student['profile_pic'] = !empty($student['profile_pic']) ? $student['profile_pic'] : 'default-pic.jpg';

            // Get the department name using dept_id from the 'department' table
            $this->db->select('dept_name');
            $this->db->where('dept_id', $student['dept_id']);
            $department_query = $this->db->get('department');

            if ($department_query->num_rows() > 0) {
                $student['department_name'] = $department_query->row_array()['dept_name']; // Fetch department name
            } else {
                $student['department_name'] = ''; // No department found
            }

            // Get the list of organizations the student belongs to from 'student_org' table
            $this->db->select('o.org_name AS organization_name');
            $this->db->from('student_org so');
            $this->db->join('organization o', 'so.org_id = o.org_id'); // Joining with 'organization' table
            $this->db->where('so.student_id', $student_id);
            $organization_query = $this->db->get();

            if ($organization_query->num_rows() > 0) {
                // Fetch organization names
                $student['organizations'] = $organization_query->result_array();
            } else {
                $student['organizations'] = []; // No organizations found
            }

            return $student;
        } else {
            return false; // If no data found
        }
    }


    //=======PROFILE UPDATE=========

    // Get the current profile picture from the users table
    public function get_profile_pic($student_id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->profile_pic; // Return the current profile pic
        }

        return null; // Return null if no profile pic exists
    }

    // Update the profile picture in the users table
    public function update_profile_pic($student_id, $data)
    {
        // Check if data is not empty
        if (!empty($data)) {
            $this->db->where('student_id', $student_id); // Ensure you're targeting the correct student
            $this->db->update('users', $data); // Assuming you are updating the 'users' table
            return $this->db->affected_rows(); // Return the number of affected rows
        }
        return false;
    }



    //UPDATE PROFILE DETAILS

    public function update_user_profile($student_id, $update_data)
    {
        // Set the user data and update based on student_id
        $this->db->where('student_id', $student_id);
        return $this->db->update('users', $update_data);
    }



    public function update_password($student_id, $new_password)
    {
        // Update the password in the database (hash it before storing)
        $data = array('password' => $new_password);
        $this->db->where('student_id', $student_id);
        $this->db->update('users', $data);
    }
}

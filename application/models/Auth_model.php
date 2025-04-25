<?php
class Auth_model extends CI_Model
{
    public function login($student_id, $password)
    {
        $this->db->where('student_id', $student_id);
        $query = $this->db->get('users');

        if ($query->num_rows() == 1) {
            $user = $query->row();

            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    // Method to fetch user details based on student_id and role (same method for all roles)
    public function getUserDetails($student_id, $role)
    {
        $this->db->where('student_id', $student_id);
        $this->db->where('role', $role); // Ensure role matches
        $query = $this->db->get('users');

        if ($query->num_rows() == 1) {
            return $query->row(); // Return the user details
        }
        return false; // Return false if user not found or role mismatch
    }

    public function get_user_by_student($student_id)
    {
        $this->db->select('users.*, department.dept_name');
        $this->db->from('users');
        $this->db->join('department', 'department.dept_id = users.dept_id', 'left');
        $this->db->where('users.student_id', $student_id);
        return $this->db->get()->row(); // returns single user
    }
}

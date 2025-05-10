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

    public function is_student_admin($student_id)
    {
        // Select relevant fields including names from department and organization
        $this->db->select('
            users.student_id,
            users.dept_id,
            users.is_officer_dept AS is_officer_dept,
            users.is_admin AS dept_admin,
            department.dept_name AS dept_name,
            student_org.org_id,
            student_org.is_officer AS is_officer_org,
            student_org.is_admin AS org_admin,
            organization.org_name AS org_name
        ');
        $this->db->from('users');
        $this->db->join('department', 'department.dept_id = users.dept_id', 'left');
        $this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');
        $this->db->join('organization', 'organization.org_id = student_org.org_id', 'left');
        $this->db->where('users.student_id', $student_id);
        $result = $this->db->get()->row();

        if (!$result) return false;

        // Check for department admin
        $is_dept_admin = ($result->is_officer_dept === 'Yes' && $result->dept_admin === 'Yes');

        // Check for organization admin
        $is_org_admin = ($result->is_officer_org === 'Yes' && $result->org_admin === 'Yes');

        // Return data if student is an admin in either context
        if ($is_dept_admin || $is_org_admin) {
            return [
                'is_admin'   => true,
                'dept_id'    => $is_dept_admin ? $result->dept_id : null,
                'dept_name'  => $is_dept_admin ? $result->dept_name : null,
                'org_id'     => $is_org_admin ? $result->org_id : null,
                'org_name'   => $is_org_admin ? $result->org_name : null,
            ];
        }

        return false;
    }
}

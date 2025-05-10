<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class LoginController
 * @property LoginModel $LoginModel
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 */
class AuthController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth'); // Load the LoginModel
    }

    public function login()
    {
        // Set validation rules
        $this->form_validation->set_rules('student_id', 'Student ID', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Load the login view with validation errors
            $this->load->view('login');
        } else {
            $student_id = $this->input->post('student_id');
            $password = $this->input->post('password');

            // Use the LoginModel to authenticate the user
            $user = $this->auth->login($student_id, $password);

            if ($user) {
                if ($user->role == 'Admin') {
                    $this->session->set_userdata([
                        'student_id' => $user->student_id,
                        'role' => $user->role
                    ]);
                    redirect('admin/dashboard');
                } elseif ($user->role == 'Student') {
                    $this->session->set_userdata([
                        'student_id' => $user->student_id,
                        'role' => $user->role
                    ]);
                    redirect('student/home');
                } else {
                    // Check if student is officer and admin of either department or organization
                    $officer = $this->auth->is_student_admin($student_id);

                    if ($officer && $officer['is_admin']) {
                        // Set session data for officer role
                        $this->session->set_userdata([
                            'student_id'       => $student_id, // Use the provided student_id
                            'role'             => 'Officer',
                            'is_officer_dept'  => isset($officer['dept_id']) ? 'Yes' : 'No',
                            'is_officer_org'   => isset($officer['org_id']) ? 'Yes' : 'No',
                            'is_admin'         => 'Yes',
                            'dept_id'          => $officer['dept_id'] ?? null,
                            'dept_name'        => $officer['dept_name'] ?? null,
                            'org_id'           => $officer['org_id'] ?? null,
                            'org_name'         => $officer['org_name'] ?? null
                        ]);

                        redirect('officer/dashboard');
                    } else {
                        $this->session->set_flashdata('error', 'Unauthorized access.');
                        redirect('login');
                    }
                }
            } else {
                // Handle login failure (invalid credentials)
                $this->session->set_flashdata('error', 'Invalid Student ID or Password');
                redirect('login');
            }
        }
    }

    private function set_session($id_number, $username, $email, $user_type)
    {
        $this->session->set_userdata([
            'id_number' => $id_number,
            'username' => $username,
            'email' => $email,
            'user_type' => $user_type
        ]);
    }
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/'); // Redirect to login page
    }
}

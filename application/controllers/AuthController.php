<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class LoginController
 * @property LoginModel $LoginModel
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 */
class AuthController extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model', 'auth'); // Load the LoginModel
    }

    public function login() {
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

            if ($user->role == 'Admin') {
                $this->session->set_userdata('student_id', $user->student_id);
                redirect('admin/dashboard'); // Redirect to the admin dashboard

            } else if ($user->role == 'Student') {
                $this->session->set_userdata('student_id', $user->student_id);
                redirect('student/home'); // Redirect to the admin dashboard

            } else if ($user->role == 'Officer') {
                $this->session->set_userdata('student_id', $user->student_id);
                redirect('admin/dashboard'); // Redirect to the admin dashboard

            } else {
                // Handle login failure
                $this->session->set_flashdata('error', 'Invalid Student ID or Password');
                redirect('login');
            }
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login'); // Redirect to login page
    }
}

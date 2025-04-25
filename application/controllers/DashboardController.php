<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class Auth
 * @property LoginModel $LoginModel
 * @property CI_Input $input
 * @property CI_Session $session
 */
class DashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session'); // Load the session library
        $this->load->helper('url'); // Load the URL helper

        // Ensure the user is logged in, otherwise redirect to login
        if (!$this->session->userdata('student_id')) {
            redirect('login'); // Redirect to login if not logged in
        }
    }

    public function index()
    {
        // Get role and student_id (to use for redirection)
        $role = $this->session->userdata('role');
        $student_id = $this->session->userdata('student_id'); // Student ID (for all roles)

        // Logic to redirect to the appropriate dashboard route based on user role
        if ($role === 'admin') {
            redirect('admin/dashboard/' . $student_id); // Redirect to admin dashboard with student_id
        } elseif ($role === 'officer') {
            redirect('officer/dashboard/' . $student_id); // Redirect to officer dashboard with student_id
        } elseif ($role === 'student') {
            redirect('student/dashboard/' . $student_id); // Redirect to student dashboard with student_id
        } else {
            // If role is not recognized, clear session and redirect to login
            $this->session->sess_destroy();
            redirect('login');
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentController extends CI_Controller {


	public function __construct()
    {
        parent::__construct();

		$this->load->model('Student_model', 'student');

        if(!$this->session->userdata('student_id'))
        {
            redirect(site_url('login'));
        }
    }

	public function student_dashboard()
	{
		$data['title'] = 'Student Home';
		
		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->student->get_roles($student_id);
        $data['role'] = $users['role'];
		
		$this->load->view('layout/header', $data);
		$this->load->view('student/home', $data);
		$this->load->view('layout/footer', $data);
	}

	public function attendance_history()
	{
		$data['title'] = 'Attendance History';

		$student_id = $this->session->userdata('student_id');
		
		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->student->get_roles($student_id);
        $data['role'] = $users['role'];
		
		$this->load->view('layout/header', $data);
		$this->load->view('student/attendance_history', $data);
		$this->load->view('layout/footer', $data);
	}



	public function summary_fines()
	{
		$data['title'] = 'Summary of Fines';

		$student_id = $this->session->userdata('student_id');
		
		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->student->get_roles($student_id);
        $data['role'] = $users['role'];
		
		$this->load->view('layout/header', $data);
		$this->load->view('student/summary_of_fines', $data);
		$this->load->view('layout/footer', $data);
		
	}

	public function list_activity(){
        $data['title'] = 'List of Activity';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
		$users= $this->student->get_roles($student_id);
		$data['role'] = $users['role'];

        $this->load->view('layout/header', $data);
        $this->load->view('student/list-activity', $data);
        $this->load->view('layout/footer', $data);

    }

	public function evaluation_form()
	{
		$data['title'] = 'Evaluation Form';

		$student_id = $this->session->userdata('student_id');
		
		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->student->get_roles($student_id);
        $data['role'] = $users['role'];
		
		$this->load->view('layout/header', $data);
		$this->load->view('student/evaluation_forms', $data);
		$this->load->view('layout/footer', $data);
		
	}

	public function excuse_application()
	{
		$data['title'] = 'Excuse Application';

		$student_id = $this->session->userdata('student_id');
		
		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
        $users= $this->student->get_roles($student_id);
        $data['role'] = $users['role'];
		
		$this->load->view('layout/header', $data);
		$this->load->view('student/excuse_application', $data);
		$this->load->view('layout/footer', $data);
	}
	

	public function profile_settings(){
        $data['title'] = 'Profile Settings';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES - NECESSARRY
		$users= $this->student->get_roles($student_id);
		$data['role'] = $users['role'];

        $this->load->view('layout/header', $data);
        $this->load->view('student/profile-settings', $data);
        $this->load->view('layout/footer', $data);

    }
}

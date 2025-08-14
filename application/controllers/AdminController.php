<?php

use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class StudentController
 * @property Student_model $Student_model
 * @property Admin_model $Admin_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Upload $upload
 * @property CI_Form_validation $form_validation
 * @property CI_DB_query_builder $db <-- Add this line
 */

// Load the FPDF library from the third_party folder
require_once(APPPATH . 'third_party/fpdf.php');
class PDF extends FPDF
{
	public $headerImage = '';
	public $footerImage = '';
	public $watermarkPath = '';

	function Header()
	{
		if (!empty($this->headerImage)) {
			$pageWidth = $this->GetPageWidth();
			$this->Image('./uploads/headerandfooter/' . $this->headerImage, 0, 0, $pageWidth, 40);
			$this->SetY(45);
		}
		// Watermark

		if (!empty($this->watermarkPath) && file_exists($this->watermarkPath)) {
			$this->Image($this->watermarkPath, 35, 70, 140, 100, '', '', '', true); // Watermark centered
		}
	}

	function Footer()
	{
		if (!empty($this->footerImage)) {
			$pageWidth = $this->GetPageWidth();
			$pageHeight = $this->GetPageHeight();
			$this->Image('./uploads/headerandfooter/' . $this->footerImage, 0, $pageHeight - 20, $pageWidth, 20);
		}
	}

	// Add transparency support
	function SetAlpha($alpha, $bm = 'Normal')
	{
		// only available with PDF alpha support (FPDI or modified FPDF)
		if ($this->PDFVersion < '1.4') {
			$this->PDFVersion = '1.4';
		}
		$gs = sprintf('/GS%d gs', 1);
		$this->_out(sprintf('/GS%d << /ca %.3F /CA %.3F /BM /%s >>', 1, $alpha, $alpha, $bm));
		$this->_out($gs);
	}


	public function getRightMargin()
	{
		return $this->rMargin;
	}

	public function getLeftMargin()
	{
		return $this->lMargin;
	}



	function NbLines($w, $txt)
	{
		$cw = &$this->CurrentFont['cw'];
		if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while ($i < $nb) {
			$c = $s[$i];
			if ($c == "\n") {
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				continue;
			}
			if ($c == ' ') $sep = $i;
			$l += $cw[$c];
			if ($l > $wmax) {
				if ($sep == -1) {
					if ($i == $j) $i++;
				} else $i = $sep + 1;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
			} else $i++;
		}
		return $nl;
	}
}


// function Footer()
// {
// 	if (!empty($this->footerImage)) {
// 		$pageWidth = $this->GetPageWidth();
// 		$pageHeight = $this->GetPageHeight();
// 		$this->Image('./uploads/headerandfooter/' . $this->footerImage, 0, $pageHeight - 20, $pageWidth, 20);
// 	}
// }


class AdminController extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin_model', 'admin');
		$this->load->model('Notification_model');

		if (!$this->session->userdata('student_id')) {
			redirect(site_url('login'));
		}
	}


	public function admin_dashboard()
	{

		$data['title'] = 'Dashboard';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);


		// DATA
		// Get current semester count and comparison with previous semester
		$data['current_semester'] = $this->admin->get_current_semester_count();
		$data['previous_semester'] = $this->admin->get_previous_semester_count();

		// Get breakdown of activities per month for the current semester
		$data['monthly_breakdown'] = $this->admin->get_monthly_activity_count($data['current_semester']['start_date'], $data['current_semester']['end_date']);

		// COUNT OF STUDENT PER DEPARTMENT
		// Get the count of students per department
		$data['student_counts'] = $this->admin->get_student_count_per_department();

		// Calculate the total number of students
		$total_students = array_sum(array_column($data['student_counts'], 'student_count'));
		$data['total_students'] = $total_students;

		// ACTIVITY ORGANIZED
		$data['activity_count'] = $this->admin->get_current_semester_count_organized();

		// TOTAL FINES
		$data['fines_per_activity'] = $this->admin->get_total_fines_per_activity();

		// EXPECTED ATTENDEES TO ACTUAL
		$attendance_data_result = $this->admin->fetch_attendance_data();

		// Assigning the fetched data and the attendance rate to $data
		$data['attendance_data'] = $attendance_data_result['attendance_data'];
		$data['attendance_rate'] = $attendance_data_result['attendance_rate'];

		$data['dept_attendance_data'] = $this->admin->get_department_attendance_data();

		//To get which semester and academic year is currently active
		$data['semester_ay'] = $this->db->get('semester_ay')->row(); // fetch the only row



		// UPCOMING & ONGOING ACTIVITIES (For Student Parliament)
		$semester_ay = $data['semester_ay'];

		$this->db->select('activity_id, activity_title, start_date, end_date, status');
		$this->db->from('activity');
		$this->db->where('organizer', 'Student Parliament');
		$this->db->where_in('status', ['Upcoming', 'Ongoing']);
		$this->db->where('semester', $semester_ay->semester);
		$this->db->where('academic_year', $semester_ay->academic_year);
		$activities = $this->db->get()->result_array();


		$thisMonth = [];
		$nextMonth = [];
		$ongoing = [];

		$today = new DateTime();
		$nextMonthDate = (clone $today)->modify('+1 month');

		foreach ($activities as $activity) {
			if ($activity['status'] === 'Upcoming' && !empty($activity['start_date'])) {
				$date = new DateTime($activity['start_date']);
				if ($date->format('Y-m') === $today->format('Y-m')) {
					$activity['attendance'] = $this->db
						->where('activity_id', $activity['activity_id'])
						->where('attendees_status', 'Attending')
						->count_all_results('attendees');
					$thisMonth[] = $activity;
				} elseif ($date->format('Y-m') === $nextMonthDate->format('Y-m')) {
					$activity['attendance'] = $this->db
						->where('activity_id', $activity['activity_id'])
						->where('attendees_status', 'Attending')
						->count_all_results('attendees');
					$nextMonth[] = $activity;
				}
			} elseif ($activity['status'] === 'Ongoing') {
				// Fetch earliest and latest time from activity_time_slots
				$time_slot = $this->db->query("
	SELECT 
		MIN(date_time_in) AS start_time,
		MAX(date_time_out) AS end_time
	FROM activity_time_slots
	WHERE activity_id = ?
", [$activity['activity_id']])->row();


				$startTimestamp = strtotime($time_slot->start_time ?? '');
				$endTimestamp   = strtotime($time_slot->end_time ?? '');
				$currentTimestamp = time();

				if ($startTimestamp && $endTimestamp && $endTimestamp > $startTimestamp) {
					if ($currentTimestamp < $startTimestamp) {
						$progress = 0; // not started yet
					} elseif ($currentTimestamp >= $endTimestamp) {
						$progress = 100; // already ended
					} else {
						$total = $endTimestamp - $startTimestamp;
						$elapsed = $currentTimestamp - $startTimestamp;
						$progress = round(($elapsed / $total) * 100);
					}
				} else {
					$progress = 0;
				}



				// Fetch attendance count (unique student per activity with any time_in)
				$activity['attendance'] = $this->db->query("
					SELECT COUNT(DISTINCT student_id) as attendee_count
					FROM attendance
					WHERE activity_id = ? AND time_in IS NOT NULL
				", [$activity['activity_id']])->row()->attendee_count ?? 0;

				$activity['start_time'] = $startTimestamp ? date('g:i A', $startTimestamp) : 'N/A';
				$activity['end_time'] = $endTimestamp ? date('g:i A', $endTimestamp) : 'N/A';
				$activity['progress'] = $progress;
				$activity['start'] = $activity['start_date'] ? date('M d, Y', strtotime($activity['start_date'])) : 'N/A';
				$activity['end'] = $activity['end_date'] ? date('M d, Y', strtotime($activity['end_date'])) : 'N/A';

				$ongoing[] = $activity;
			}
		}




		$data['thisMonthActivities'] = $thisMonth;
		$data['nextMonthActivities'] = $nextMonth;
		$data['ongoingActivities'] = $ongoing;
		// END UPCOMING & ONGOING ACTIVITIES (For Student Parliament)

		//Limit to 2 activities only 
		$data['thisMonthActivities'] = array_slice($thisMonth, 0, 2);
		$data['nextMonthActivities'] = array_slice($nextMonth, 0, 2);
		$data['ongoingActivities'] = array_slice($ongoing, 0, 2);


		$this->load->view('layout/header', $data);
		$this->load->view('admin/dashboard', $data);
		$this->load->view('layout/footer', $data);
	}


	// ACTIVITY MANAGEMENT

	// CREATING ACTIVITY - PAGE (FINAL CHECK)
	public function create_activity()
	{
		$data['title'] = 'Create Activity';

		$student_id = $this->session->userdata('student_id');

		$data['users'] = $this->admin->get_student($student_id);

		$data['dept'] = $this->admin->get_department(); // this is the audience

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/create-activity', $data);
		$this->load->view('layout/footer', $data);
	}

	// INSERTING THE ACTIVITY
	public function save_activity()
	{
		if ($this->input->post()) {
			// Set Validation Rules
			$this->form_validation->set_rules('title', 'Activity Title', 'required|is_unique[activity.activity_title]', [
				'required'   => 'The {field} is required.',
				'is_unique'  => 'The {field} must be unique. This title is already taken.'
			]);
			$this->form_validation->set_rules('date_start', 'Start Date', 'required');
			$this->form_validation->set_rules('date_end', 'End Date', 'required');
			$this->form_validation->set_rules('start_datetime[]', 'Start Date & Time', 'required');
			$this->form_validation->set_rules('end_datetime[]', 'End Date & Time', 'required');
			$this->form_validation->set_rules('session_type[]', 'Session Type', 'required');

			// Run Validation
			if ($this->form_validation->run() == FALSE) {
				echo json_encode(['status' => 'error', 'errors' => validation_errors()]);
				return;
			}

			// Extract activity start date and month
			$startDate = $this->input->post('date_start');
			$startMonth = (int)date('n', strtotime($startDate)); // 1 to 12

			// Get semester range (assuming single row)
			$range = $this->db->get_where('semester_range', ['id' => '1'])->row();

			if (!$range) {
				return $this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'status' => 'error',
						'message' => 'Semester range not found.'
					]));
			}

			$validSemester = false;
			$semester = '';
			$academicYear = $range->academic_year;

			// Check if start month falls within the 1st semester
			if ($startMonth >= (int)$range->first_start && $startMonth <= (int)$range->first_end) {
				$validSemester = true;
				$semester = '1st Semester';
			}

			// Check if start month falls within the 2nd semester
			if ($startMonth >= (int)$range->second_start && $startMonth <= (int)$range->second_end) {
				$validSemester = true;
				$semester = '2nd Semester';
			}

			if (!$validSemester) {
				return $this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'status' => 'error',
						'message' => 'The activity start date does not fall within the semester range.'
					]));
			}

			// Prepare Activity Data if valid
			$data = [
				'activity_title'        => $this->input->post('title'),
				'start_date'            => $startDate,
				'end_date'              => $this->input->post('date_end'),
				'description'           => $this->input->post('description'),
				'registration_deadline' => $this->input->post('registration_deadline'),
				'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
				'organizer'             => 'Student Parliament',
				'fines'                 => str_replace(",", "", $this->input->post('fines')),
				'audience'              => implode(',', $this->input->post('audience')),
				'created_at'            => date('Y-m-d H:i:s'),
				'academic_year'         => $academicYear,
				'semester'              => $semester
			];



			// // Prepare Activity Data
			// $data = [
			// 	'activity_title'        => $this->input->post('title'),
			// 	'start_date'            => $this->input->post('date_start'),
			// 	'end_date'              => $this->input->post('date_end'),
			// 	'description'           => $this->input->post('description'),
			// 	'registration_deadline' => $this->input->post('registration_deadline'),
			// 	'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
			// 	'organizer'             => 'Student Parliament',
			// 	'fines'                 => str_replace(",", "", $this->input->post('fines')),
			// 	'audience'             => implode(',', $this->input->post('audience')),
			// 	'created_at'            => date('Y-m-d H:i:s')
			// ];

			// Handle Cover Image Upload
			if (!empty($_FILES['coverUpload']['name'])) {
				$coverImage = $this->upload_file('coverUpload', './assets/coverEvent');
				if (!$coverImage['status']) {
					echo json_encode(['status' => 'error', 'errors' => $coverImage['error']]);
					return;
				}

				$data['activity_image'] = $coverImage['file_name'];
			}

			// Handle QR Code Upload
			if (!empty($_FILES['qrcode']['name'])) {
				$qrCode = $this->upload_file('qrcode', './assets/qrcodeRegistration');
				if (!$qrCode['status']) {
					echo json_encode(['status' => 'error', 'errors' => $qrCode['error']]);
					return;
				}
				$data['qr_code'] = $qrCode['file_name'];
			}

			// Insert Activity and Get ID
			$activity_id = $this->admin->save_activity($data);
			if (!$activity_id) {
				echo json_encode(['status' => 'error', 'errors' => 'Failed to Save Activity']);
				return;
			}

			// Fetch Input Data
			$start_datetimes = $this->input->post('start_datetime') ?? [];
			$end_datetimes = $this->input->post('end_datetime') ?? [];
			$session_types = $this->input->post('session_type') ?? [];

			// Prepare Schedule Data
			$schedules = [];
			foreach ($start_datetimes as $i => $start_datetime) {
				$schedules[] = [
					'activity_id'  => $activity_id,
					'date_time_in' => date('Y-m-d H:i:s', strtotime($start_datetime)),
					'date_time_out' => date('Y-m-d H:i:s', strtotime($end_datetimes[$i])),
					'slot_name'    => $session_types[$i],
					'created_at'   => date('Y-m-d H:i:s')
				];
			}


			// Save Schedules and get inserted timeslot IDs
			$timeslot_ids = $this->admin->save_schedules($schedules);

			// Assign Students to Activity
			$this->assign_students_to_activity($activity_id, $data, $timeslot_ids);

			// Return Success Response
			echo json_encode([
				'status'   => 'success',
				'message'  => 'Activity Saved Successfully',
				'redirect' => site_url('admin/list-of-activity')
			]);
		}
	}

	private function upload_file($field_name, $upload_path)
	{
		$config = [
			'upload_path' => $upload_path,
			'allowed_types' => 'gif|jpg|jpeg|png',
			'max_size' => 2048
		];
		$this->load->library('upload', $config);

		if ($this->upload->do_upload($field_name)) {
			return ['status' => true, 'file_name' => $this->upload->data('file_name')];
		} else {
			return ['status' => false, 'error' => $this->upload->display_errors()];
		}
	}

	// ASSIGN STUDENT IF THEIR ARE THE AUDIENCE
	// private function assign_students_to_activity($activity_id, $data, $timeslot_ids = [])
	// {
	// 	// Select student IDs by joining users and departments based on department name
	// 	$this->db->select('u.student_id');
	// 	$this->db->from('users u');
	// 	$this->db->join('department d', 'u.dept_id = d.dept_id');
	// 	$this->db->where('role', 'Student');

	// 	// Filter by department name if provided and not "all"
	// 	if (!empty($data['audience']) && strtolower($data['audience']) !== 'all') {
	// 		$this->db->where('d.dept_name', $data['audience']); // assuming 'dept_name' is the correct column
	// 	}

	// 	$students = $this->db->get()->result();

	// 	// Insert each student into attendance for each timeslot
	// 	foreach ($students as $student) {
	// 		foreach ($timeslot_ids as $timeslot_id) {
	// 			// Step 1: Insert into attendance table
	// 			$this->db->insert('attendance', [
	// 				'activity_id' => $activity_id,
	// 				'timeslot_id' => $timeslot_id,
	// 				'student_id'  => $student->student_id
	// 			]);

	// 			// Step 2: Get the auto-incremented attendance_id
	// 			$attendance_id = $this->db->insert_id();

	// 			// Step 3: Insert into fines table using the retrieved attendance_id
	// 			$this->db->insert('fines', [
	// 				'activity_id'    => $activity_id,
	// 				'timeslot_id'    => $timeslot_id,
	// 				'student_id'     => $student->student_id,
	// 				'attendance_id'  => $attendance_id,
	// 				'status'         => 'Pending', // optional defaults
	// 				'fines_amount'   => 0           // or whatever default you want
	// 			]);
	// 		}

	// 		// Step 4: Insert one fines_summary per student if student_id and organizer pair does not already exist
	// 		$this->db->where('student_id', $student->student_id);
	// 		$this->db->where('organizer', $data['organizer']);
	// 		$exists = $this->db->get('fines_summary')->num_rows();

	// 		if ($exists === 0) {
	// 			$this->db->insert('fines_summary', [
	// 				'student_id' => $student->student_id,
	// 				'organizer' => $data['organizer']
	// 			]);
	// 		}
	// 	}
	// }

	// ASSIGN STUDENT IF THEY ARE THE AUDIENCE
	private function assign_students_to_activity($activity_id, $data, $timeslot_ids = [])
	{

		// Get organizer from activity table
		$activity = $this->db->select('organizer, start_date, registration_deadline, registration_fee')->from('activity')->where('activity_id', $activity_id)->get()->row();
		$organizer = $activity ? $activity->organizer : null;


		// Select student IDs by joining users and departments based on department name
		$this->db->select('u.student_id');
		$this->db->from('users u');
		$this->db->join('department d', 'u.dept_id = d.dept_id');
		$this->db->where('role', 'Student');
		$this->db->where('u.is_active', 'Active'); // ✅ Only active students

		// Filter by department name if provided and not "all"
		if (!empty($data['audience']) && strtolower($data['audience']) !== 'all') {
			$this->db->where('d.dept_name', $data['audience']);
		}

		$students = $this->db->get()->result();

		foreach ($students as $student) {
			// Check if student is exempted
			$is_exempted = $this->db
				->where('student_id', $student->student_id)
				->get('exempted_students')
				->num_rows() > 0;

			foreach ($timeslot_ids as $timeslot_id) {
				// Attendance: If exempted, status = 'Exempted', else leave as default or null
				$attendance_data = [
					'activity_id' => $activity_id,
					'timeslot_id' => $timeslot_id,
					'student_id'  => $student->student_id,
				];

				if ($is_exempted) {
					$attendance_data = [
						'activity_id' => $activity_id,
						'timeslot_id' => $timeslot_id,
						'student_id'  => $student->student_id,
						'attendance_status' => 'Exempted'
					];
				}

				$this->db->insert('attendance', $attendance_data);

				$attendance_id = $this->db->insert_id();

				// Fines: If exempted, status = 'No Fines' and amount = 0
				$fines_data = [
					'activity_id'    => $activity_id,
					'timeslot_id'    => $timeslot_id,
					'student_id'     => $student->student_id,
					'attendance_id'  => $attendance_id,
					'status'         => 'Pending', // optional defaults
					'fines_amount'   => 0           // or whatever default you want
				];

				$this->db->insert('fines', $fines_data); // ✅ Add this line
			}

			// Step 4: Check if a summary already exists
			$this->db->where('student_id', $student->student_id);
			$this->db->where('organizer', $organizer);
			$existing_summary = $this->db->get('fines_summary')->row();

			// Derive semester and academic year from start date
			$start_date = strtotime($activity->start_date); // Ensure $start_datetime is defined

			$month = (int)date('m', $start_date);
			$year = (int)date('Y', $start_date);

			// Determine semester and academic year
			if ($month >= 8 && $month <= 12) {
				$semester = '1st Semester';
				$academic_year = $year . '-' . ($year + 1);
			} else {
				$semester = '2nd Semester';
				$academic_year = ($year - 1) . '-' . $year;
			}

			// Update or Insert
			if ($existing_summary) {
				// Update the existing summary
				$this->db->where('summary_id', $existing_summary->summary_id);
				$this->db->update('fines_summary', [
					'fines_status'   => 'Unpaid',
					'semester'       => $semester,
					'academic_year'  => $academic_year,
					'last_updated'   => date('Y-m-d H:i:s')
				]);
			} else {
				// Insert new summary
				$this->db->insert('fines_summary', [
					'student_id'     => $student->student_id,
					'organizer'      => $organizer,
					'fines_status'   => 'Unpaid',
					'semester'       => $semester,
					'academic_year'  => $academic_year,
					'last_updated'   => date('Y-m-d H:i:s')
				]);
			}

			// Step 5: Registration
			$fee_valid = !empty($activity->registration_fee) || $activity->registration_fee == 0;
			$deadline_valid = !empty($activity->registration_deadline) || $activity->registration_deadline == '0000-00-00';

			if ($fee_valid && $deadline_valid) {
				$exists = $this->db
					->where('activity_id', $activity_id)
					->where('student_id', $student->student_id)
					->get('registrations')
					->num_rows();

				if ($exists === 0) {
					$this->db->insert('registrations', [
						'activity_id' => $activity_id,
						'student_id' => $student->student_id,
						'payment_type' => 'No Status',
						'registration_status' => 'No Status'
					]);
				}
			}
		}
	}


	// FETCHING ACTIVITY (STUDENT PARLIAMENT) - PAGE (FINAL CHECK)
	public function list_activity()
	{
		$data['title'] = 'List of Activities';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// GETTING OF THE ACTIVITY FROM THE DATABASE
		$data['activities'] = $this->admin->get_activities_active(); // FOR STUDENT PARLIAMENT

		// $data['semester_range'] = $this->db->get_where('semester_range', ['id' => 1])->row();

		// Get active semester and academic year || used for read only in filters
		$semester_ay = $this->db->where('is_active', 1)->get('semester_ay')->row();
		if ($semester_ay) {
			$data['active_semester'] = $semester_ay->semester;
			$data['active_academic_year'] = $semester_ay->academic_year;
		} else {
			$data['active_semester'] = 'N/A';
			$data['active_academic_year'] = 'N/A';
		}


		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/list-activities', $data);
		$this->load->view('layout/footer', $data);
	}

	// FETCHING DETAILS OF THE ACTIVITY - PAGE (FINAL CHECK)
	public function activity_details($activity_id)
	{
		$data['title'] = 'Activity Details';

		$student_id = $this->session->userdata('student_id');

		$data['users'] = $this->admin->get_student($student_id);

		$data['activity'] = $this->admin->get_activity($activity_id); // SPECIFIC ACTIVITY
		$data['schedules'] = $this->admin->get_schedule($activity_id); // GETTING OF SCHEDULE

		$data['activities'] = $this->admin->get_activities(); // FOR UPCOMING ACTIVITY PART
		$data['registrations'] = $this->admin->registrations($activity_id); // FOR REGISTRATION
		$data['departments'] = $this->admin->get_department();

		$data['verified_count'] = $this->admin->get_registered_count($activity_id);
		$data['attendees_count'] = $this->admin->get_attendees_count($activity_id);


		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/activity-detail', $data);
		$this->load->view('layout/footer', $data);
	}

	// FUNCTIONALITY FOR THE REGISTRATION
	public function validate_registrations()
	{
		$student_id        = $this->input->post('student_id');
		$activity_id       = $this->input->post('activity_id');
		$reference_number  = trim($this->input->post('reference_number'));
		$action            = $this->input->post('action'); // 'Verified' or 'Rejected'
		$remarks           = $this->input->post('remarks');


		$this->load->model('Notification_model');;

		$record = $this->admin->get_reference_data($student_id, $activity_id);

		if (!$record) {
			echo json_encode(['status' => 'error', 'message' => 'Registration record not found.']);
			return;
		}

		// Get activity title
		$activity = $this->db->select('activity_title')->from('activity')->where('activity_id', $activity_id)->get()->row();
		if (!$activity) {
			echo json_encode(['success' => false, 'message' => 'Activity not found.']);
			return;
		}
		$activity_title = $activity->activity_title;

		$admin_id = $this->session->userdata('student_id'); // The admin validating

		// Reference mismatch = auto rejection
		if ($reference_number !== $record->reference_number) {
			$this->admin->validate_registration($student_id, $activity_id, [
				'reference_number_admin' => $reference_number,
				'registration_status'    => 'Rejected',
				'remark' => "Reference number doesn't match the admin's record. Please contact support if this is a mistake.",
				'approved_by'            => $admin_id  // <-- Add this line here as well
			]);

			$this->Notification_model->add_notification(
				$student_id,
				$admin_id,
				'registration_rejected',
				$activity_id,
				"has rejected your registration for '{$activity_title}' due to mismatched reference number."
			);

			echo json_encode(['status' => 'warning', 'message' => 'Reference number does not match. Registration rejected.']);
			return;
		}

		// If reference number matches
		$update = $this->admin->validate_registration($student_id, $activity_id, [
			'reference_number_admin' => $reference_number,
			'registration_status'    => $action,
			'remark'                 => $remarks,
			'approved_by'            => $admin_id  // <-- Add this line here as well
		]);

		if ($update) {
			if ($action === 'Verified') {
				// Generate receipt
				$registration_id = $this->admin->get_registration_id($student_id, $activity_id);
				if ($registration_id) {
					$this->generate_and_store_receipt($registration_id);
				}

				// Notify student of approval
				$this->Notification_model->add_notification(
					$student_id,
					$admin_id,
					'registration_approved',
					$activity_id,
					"has approved your registration for '{$activity_title}'.",
					null,
					base_url('student/activity-details/' . $activity_id)
				);
			} elseif ($action === 'Rejected') {
				// Notify student of rejection
				$this->Notification_model->add_notification(
					$student_id,
					$admin_id,
					'registration_rejected',
					$activity_id,
					"has rejected your registration for '{$activity_title}'.",
					null,
					base_url('student/activity-details/' . $activity_id)
				);
			}

			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
		}
	}

	public function generate_and_store_receipt($registration_id)
	{
		$this->load->model('Student_model');
		require_once(APPPATH . 'third_party/fpdf.php');

		// Get receipt data with approver info (adjust this method as needed)
		$receipt_data = $this->Student_model->get_receipt_by_id($registration_id);
		if (!$receipt_data) return;

		// Fetch approver name if approved_by_student_id exists
		$approver_name = 'N/A';
		if (!empty($receipt_data['approved_by'])) {
			$approver = $this->db->select('first_name, middle_name, last_name')
				->where('student_id', $receipt_data['approved_by'])
				->get('users')
				->row();
			if ($approver) {
				$approver_name = strtoupper(trim($approver->first_name . ' ' . $approver->middle_name . ' ' . $approver->last_name));
			}
		}

		$filename = 'receipt_' . $registration_id . '.pdf';
		$folder_path = FCPATH . 'uploads/generated_receipts/';
		$filepath = $folder_path . $filename;

		if (!is_dir($folder_path)) {
			mkdir($folder_path, 0777, true);
		}

		$verification_code = strtoupper(substr(md5($registration_id . time()), 0, 8));

		// --- User session and header/footer setup (your existing code) ---
		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		$headerImage = '';
		$footerImage = '';
		$watermarkPath = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
				if (!empty($settings->logo)) $watermarkPath = $settings->logo;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db->select('organization.header, organization.footer, organization.logo')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
				if (!empty($org->logo)) $watermarkPath = $org->logo;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db->select('header, footer, logo')->where('dept_id', $user['dept_id'])->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
				if (!empty($dept->logo)) $watermarkPath = $dept->logo;
			}
		}

		$pdf = new PDF();
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->watermarkPath = $watermarkPath;

		$pdf->AddPage();
		$pdf->SetAutoPageBreak(true, 10);

		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, 'OFFICIAL RECEIPT', 0, 1, 'C');
		$pdf->Ln(5);



		// Define tighter layout sizes
		$label_w_left  = 28;   // Left-side label width
		$value_w_left  = 37;   // Left-side value width
		$label_w_right = 28;   // Right-side label width
		$value_w_right = 37;   // Right-side value width
		$gap_w         = 30;   // Space between left/right pairs

		// Row 1: Receipt No and Semester
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_left, 8, 'Receipt No:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_left, 8, strtoupper($registration_id), 0, 0);

		$pdf->Cell($gap_w); // spacer

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_right, 8, 'Semester:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_right, 8, isset($receipt_data['semester']) ? $receipt_data['semester'] : 'N/A', 0, 1);

		// Row 2: Date and Academic Year
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_left, 8, 'Date:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_left, 8, date('F j, Y', strtotime($receipt_data['registered_at'])), 0, 0);

		$pdf->Cell($gap_w); // spacer

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_right, 8, 'Academic Year:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_right, 8, isset($receipt_data['academic_year']) ? $receipt_data['academic_year'] : 'N/A', 0, 1);

		$pdf->Ln(5);





		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'From:', 0, 0, 'L');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, 'Student Parliament', 0, 1, 'L');

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Received By:', 0, 0, 'L');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, strtoupper($receipt_data['student_id'] . ' - ' . $receipt_data['first_name'] . ' ' . $receipt_data['middle_name'] . ' ' . $receipt_data['last_name']), 0, 1, 'L');


		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(150, 8, 'Description', 1, 0, 'C');
		$pdf->Cell(40, 8, 'Amount (PHP)', 1, 1, 'C');

		$pdf->SetFont('Arial', '', 10);
		$description = 'Payment for ' . $receipt_data['activity_title'] . ' Registration';
		$pdf->Cell(150, 8, $description, 1, 0, 'C');
		$pdf->Cell(40, 8, number_format($receipt_data['amount_paid'], 2), 1, 1, 'C');

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(150, 8, 'Total Amount:', 1, 0, 'R');
		$pdf->Cell(40, 8, 'P ' . number_format($receipt_data['amount_paid'], 2), 1, 1, 'C');

		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Payment Type:', 0, 0, 'L');
		$pdf->Cell(0, 8, $receipt_data['reference_number'] ? 'E-Payment (GCash)' : 'Cash', 0, 1, 'L');


		// Add Approved By section here
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Approved By:', 0, 0, 'L');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $approver_name, 0, 1, 'L');


		$pdf->Ln(15);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 8, 'Verification Code:', 0, 1, 'C');
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, $verification_code, 0, 1, 'C');

		$pdf->Ln(10);
		$pdf->SetFont('Arial', 'I', 8);
		$pdf->Cell(0, 8, 'Enter this code on the receipt verification page to check validity.', 0, 1, 'C');

		$pdf->Output($filepath, 'F');

		$this->db->where('registration_id', $registration_id);
		$this->db->update('registrations', [
			'generated_receipt' => $filename,
			'remark' => 'Receipt generated.',
			'verification_code' => $verification_code,
		]);
	}


	// // CASH REGISTRATION
	// public function save_cash_payment()
	// {
	// 	$student_id = $this->input->post('student_id', TRUE);  // Sanitize input
	// 	$activity_id = $this->input->post('activity_id', TRUE);
	// 	$receipt_number = $this->input->post('receipt_number', TRUE);
	// 	$amount_paid = $this->input->post('amount_paid', TRUE);
	// 	$remark = $this->input->post('remark', TRUE);

	// 	// Validate required fields
	// 	if (empty($student_id) || empty($activity_id) || empty($receipt_number) || empty($amount_paid)) {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => 'All fields are required. Please fill in the missing information.'
	// 		]);
	// 		return;
	// 	}

	// 	// Check if student is already registered for this activity
	// 	$already_registered = $this->admin->is_student_registered($student_id, $activity_id);

	// 	if ($already_registered) {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => 'This student is already registered for the selected activity.'
	// 		]);
	// 		return;
	// 	}

	// 	// Prepare data for insertion
	// 	$data = array(
	// 		'student_id'             => $student_id,
	// 		'activity_id'            => $activity_id,
	// 		'payment_type'           => 'Cash',
	// 		'reference_number'       => $receipt_number,
	// 		'reference_number_admin' => $receipt_number,
	// 		'amount_paid'            => $amount_paid,
	// 		'registration_status'    => 'Verified',
	// 		'remark'                 => $remark,
	// 		'registered_at'          => date('Y-m-d H:i:s'),
	// 		'updated_at'             => date('Y-m-d H:i:s')
	// 	);

	// 	// Insert into the database
	// 	$inserted = $this->admin->insert_cash_payment($data);

	// 	// Return the response as JSON
	// 	if ($inserted) {
	// 		echo json_encode([
	// 			'status' => 'success',
	// 			'message' => 'Cash payment recorded successfully!'
	// 		]);
	// 	} else {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => 'Failed to record cash payment. Please try again.'
	// 		]);
	// 	}
	// }

	// CASH REGISTRATION
	public function save_cash_payment()
	{
		$student_id     = $this->input->post('student_id', TRUE);
		$activity_id    = $this->input->post('activity_id', TRUE);
		$receipt_number = $this->input->post('receipt_number', TRUE);
		$amount_paid    = $this->input->post('amount_paid', TRUE);
		$remark         = $this->input->post('remark', TRUE);

		// Validate required fields
		if (empty($student_id) || empty($activity_id) || empty($receipt_number) || empty($amount_paid)) {
			echo json_encode([
				'status' => 'error',
				'message' => 'All fields are required. Please fill in the missing information.'
			]);
			return;
		}

		// Prepare the data
		$data = array(
			'student_id'             => $student_id,
			'activity_id'            => $activity_id,
			'payment_type'           => 'Cash',
			'reference_number'       => $receipt_number,
			'reference_number_admin' => $receipt_number,
			'amount_paid'            => $amount_paid,
			'registration_status'    => 'Verified',
			'remark'                 => $remark,
			'updated_at'             => date('Y-m-d H:i:s'),
		);

		// Check if the student is already registered
		$already_registered = $this->admin->is_student_registered($student_id, $activity_id);

		if ($already_registered) {
			// Update existing registration
			$updated = $this->admin->update_cash_payment($student_id, $activity_id, $data);

			if ($updated) {
				echo json_encode([
					'status' => 'success',
					'message' => 'Cash payment updated successfully!'
				]);
			} else {
				echo json_encode([
					'status' => 'error',
					'message' => 'Failed to update cash payment. Please try again.'
				]);
			}
		} else {
			// Add registered_at field for new insert
			$data['registered_at'] = date('Y-m-d H:i:s');

			// Insert new record
			$inserted = $this->admin->insert_cash_payment($data);

			if ($inserted) {
				echo json_encode([
					'status' => 'success',
					'message' => 'Cash payment recorded successfully!'
				]);
			} else {
				echo json_encode([
					'status' => 'error',
					'message' => 'Failed to record cash payment. Please try again.'
				]);
			}
		}
	}



	public function export_registered_students_pdf()
	{
		$activity_id = $this->input->get('activity_id');
		if (!$activity_id) {
			show_error('Activity ID is required.');
		}

		$this->load->model('Admin_model');

		$activity = $this->Admin_model->get_activity_specific($activity_id);
		if (!$activity) {
			show_error('Activity not found.');
		}

		$activity_title = $activity['activity_title'];
		$registrations = $this->Admin_model->registrations($activity_id);

		// Manually build the user array from session data
		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		if (!$user['role'] || !$user['student_id']) {
			echo json_encode(['success' => false, 'message' => 'Missing session data.']);
			return;
		}

		$headerImage = '';
		$footerImage = '';

		// Determine correct header/footer based on role
		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db
				->select('organization.header, organization.footer')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db
				->select('header, footer')
				->where('dept_id', $user['dept_id'])
				->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
			}
		}


		// Setup PDF using custom class
		$pdf = new PDF('P', 'mm', 'Letter');
		$pdf->headerImage = $headerImage; // Set header image
		$pdf->footerImage = $footerImage; // Set footer image
		$pdf->SetMargins(10, 10, 10); // standard margins
		$pdf->AddPage();

		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, 'Registered Participants - Activity: ' . $activity_title, 0, 1, 'C');
		$pdf->Ln(5);

		$header = ['Student ID', 'Name', 'Department', 'Amount', 'Reference Number', 'Status'];

		// Calculate column widths
		$pdf->SetFont('Arial', '', 8);
		$colWidths = [];
		foreach ($header as $col) {
			$colWidths[] = $pdf->GetStringWidth($col);
		}

		foreach ($registrations as $reg) {
			$row = [
				$reg->student_id,
				$reg->first_name . ' ' . $reg->last_name,
				$reg->dept_name,
				$reg->amount_paid,
				$reg->reference_number,
				$reg->registration_status
			];
			foreach ($row as $i => $val) {
				$w = $pdf->GetStringWidth($val);
				if ($w > $colWidths[$i]) {
					$colWidths[$i] = $w;
				}
			}
		}

		// Add padding
		$padding = 6;
		foreach ($colWidths as &$width) {
			$width += $padding;
		}

		// Scale total width to fit printable area (Letter width = 215.9mm, margins = 10mm on each side)
		$total_content_width = array_sum($colWidths);
		$usable_page_width = 215.9 - 20; // 10mm margins on each side
		$scaling_factor = $usable_page_width / $total_content_width;

		foreach ($colWidths as &$width) {
			$width = round($width * $scaling_factor, 2); // scale to fit
		}

		// Center table
		$startX = 10 + ($usable_page_width - array_sum($colWidths)) / 2;

		// Header row
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetX($startX);
		foreach ($header as $i => $colName) {
			$pdf->Cell($colWidths[$i], 10, $colName, 1, 0, 'C');
		}
		$pdf->Ln();

		// Data rows
		$pdf->SetFont('Arial', '', 9);
		foreach ($registrations as $reg) {
			$row = [
				$reg->student_id,
				$reg->first_name . ' ' . $reg->last_name,
				$reg->dept_name,
				$reg->amount_paid,
				$reg->reference_number,
				$reg->registration_status
			];

			$pdf->SetX($startX);
			foreach ($row as $i => $val) {
				$pdf->Cell($colWidths[$i], 8, $val, 1, 0, 'C');
			}
			$pdf->Ln();
		}

		// Output PDF
		$filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $activity_title) . '_registered_participants.pdf';
		$pdf->Output('I', $filename);
	}

	// SHARING ACTIVITY FROM THE ACTIVITY DETAILS
	public function share_activity()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		if (!isset($data['activity_id'])) {
			echo json_encode(['success' => false, 'message' => 'Invalid request.']);
			return;
		}

		$activity_id = $data['activity_id'];

		// Mark the activity as shared
		$this->db->set('is_shared', 'Yes')->where('activity_id', $activity_id)->update('activity');

		if ($this->db->affected_rows() <= 0) {
			echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
			return;
		}

		// Get activity title
		$activity = $this->db->select('activity_title')->from('activity')->where('activity_id', $activity_id)->get()->row();
		if (!$activity) {
			echo json_encode(['success' => false, 'message' => 'Activity not found.']);
			return;
		}

		// Get sender ID (admin), fallback to 0
		$sender_id = $this->session->userdata('student_id') ?? 0;

		// Get all students (exclude admins)
		$students = $this->db->select('student_id')->where('role', 'student')->get('users')->result_array();

		// Prepare notifications
		$notification_data = [];
		foreach ($students as $student) {
			$notification_data[] = [
				'recipient_student_id' => $student['student_id'],
				'recipient_admin_id'   => null,
				'sender_student_id'    => $sender_id,
				'type'                 => 'activity_shared',
				'reference_id'         => $activity_id,
				'message'              => 'Shared a new activity with you: ' . $activity->activity_title,
				'is_read'              => 0,
				'created_at'           => date('Y-m-d H:i:s'),
				'link' => base_url('student/home/')
			];
		}

		// Insert all notifications
		if (!empty($notification_data)) {
			$this->db->insert_batch('notifications', $notification_data);
		}

		echo json_encode(['success' => true]);
	}

	//UNSHARE ACTIVITY
	public function unshare_activity()
	{
		$activity_id = $this->input->post('activity_id');

		if ($activity_id) {
			// First, mark the activity as unshared
			$this->db->where('activity_id', $activity_id);
			$this->db->update('activity', ['is_shared' => 'No']);

			// If the activity is successfully updated, proceed to delete notifications
			if ($this->db->affected_rows() > 0) {
				// Delete notifications related to this activity
				$this->load->model('Notification_model');
				$this->Notification_model->delete_notifications_by_reference($activity_id, 'activity_shared');

				echo json_encode(['status' => 'success', 'message' => 'Activity unshared and notifications deleted.']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Failed to unshare activity or activity not found.']);
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Invalid activity ID']);
		}
	}


	// EDITING OF ACTIVITY - PAGE
	public function edit_activity($activity_id)
	{
		$data['title'] = 'Edit Activity';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['activity'] = $this->admin->get_activity($activity_id);
		$data['schedules'] = $this->admin->get_schedule($activity_id);
		$data['dept'] = $this->admin->get_department();

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/edit-activity', $data);
		$this->load->view('layout/footer', $data);
	}

	// DELETE THE TIMESLOTS
	public function delete_schedule($id)
	{

		if ($this->admin->delete_schedule($id)) {
			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}
	}

	// UPDATE THE ACTIVITY
	public function update_activity($activity_id)
	{
		if ($this->input->post()) {
			// Set Validation Rules
			$this->form_validation->set_rules('date_start', 'Start Date', 'required');
			$this->form_validation->set_rules('date_end', 'End Date', 'required');
			$this->form_validation->set_rules('start_datetime[]', 'Start Date & Time', 'required');
			$this->form_validation->set_rules('end_datetime[]', 'End Date & Time', 'required');
			$this->form_validation->set_rules('session_type[]', 'Session Type', 'required');

			// Run Validation
			if ($this->form_validation->run() == FALSE) {
				echo json_encode(['status' => 'error', 'errors' => validation_errors()]);
				return;
			}

			// Check if Activity Exists
			$activity = $this->admin->get_activity($activity_id);
			if (!$activity) {
				echo json_encode(['status' => 'error', 'errors' => 'Activity not found.']);
				return;
			}

			// Prepare Activity Data
			$data = [
				'activity_title'        => $this->input->post('title'),
				'start_date'            => $this->input->post('date_start'),
				'end_date'              => $this->input->post('date_end'),
				'description'           => $this->input->post('description'),
				'registration_deadline' => $this->input->post('registration_deadline'),
				'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
				'organizer'             => 'Student Parliament',
				'fines'                 => str_replace(",", "", $this->input->post('fines')),
				'audience'             => implode(',', $this->input->post('audience')),

			];

			// Handle Cover Image Upload
			if (!empty($_FILES['coverUpload']['name'])) {
				$coverImage = $this->upload_file('coverUpload', './assets/coverEvent');
				if (!$coverImage['status']) {
					echo json_encode(['status' => 'error', 'errors' => $coverImage['error']]);
					return;
				}
				$data['activity_image'] = $coverImage['file_name'];
			}

			// Handle QR Code Upload
			if (!empty($_FILES['qrcode']['name'])) {
				$qrCode = $this->upload_file('qrcode', './assets/qrcodeRegistration');
				if (!$qrCode['status']) {
					echo json_encode(['status' => 'error', 'errors' => $qrCode['error']]);
					return;
				}
				$data['qr_code'] = $qrCode['file_name'];
			}

			// Get the logged-in user's ID (editor)
			$editor_id = $this->session->userdata('user_id'); // Or use appropriate session variable

			// Track changes (this is where we log the changes)
			$changes = $this->get_activity_changes($activity, $data);

			// Update Activity and log changes
			$updated = $this->admin->update_activity($activity_id, $data, $editor_id, $changes);


			// Fetch Input Data
			$start_datetimes = $this->input->post('start_datetime') ?? [];
			$start_cutoff = $this->input->post('start_cutoff') ?? [];
			$end_datetimes = $this->input->post('end_datetime') ?? [];
			$end_cutoff = $this->input->post('end_cutoff') ?? [];
			$session_types = $this->input->post('session_type') ?? [];
			$schedule_ids = $this->input->post('timeslot_id') ?? [];

			$timeslot_ids = [];

			// Update or Insert Schedules
			foreach ($start_datetimes as $i => $start_datetime) {
				// Ensure array indexes exist before accessing them
				$start_cutoff = $start_cutoff[$i] ?? null;
				$end_datetime = $end_datetimes[$i] ?? null;
				$end_cutoff = $end_cutoff[$i] ?? null;
				$session_type = $session_types[$i] ?? null;
				$schedule_id = $schedule_ids[$i] ?? null;

				// Prepare schedule data
				$schedule_data = [
					'activity_id'  => $activity_id,
					'date_time_in' => date('Y-m-d H:i:s', strtotime($start_datetime)),
					'date_cut_in' => date('Y-m-d H:i:s', strtotime($start_cutoff)),
					'date_time_out' => date('Y-m-d H:i:s', strtotime($end_datetime)),
					'date_cut_out' => date('Y-m-d H:i:s', strtotime($end_cutoff)),
					'slot_name'    => $session_type
				];

				if (!empty($schedule_id)) {
					// Update existing schedule
					$this->admin->update_schedule($schedule_id, $schedule_data);
					$timeslot_ids[] = $schedule_id;
				} else {
					// Insert new schedule with created_at timestamp
					$schedule_data['created_at'] = date('Y-m-d H:i:s');
					$new_schedule_id = $this->admin->save_schedule($schedule_data);
					$timeslot_ids[] = $new_schedule_id;
				}
			}

			// // Assign students again after update
			// $this->assign_students_to_activity_again($activity_id, $data, $timeslot_ids);

			// Return Success Response
			echo json_encode([
				'status'   => 'success',
				'message'  => 'Activity Updated Successfully',
				'redirect' => site_url('admin/activity-details/' . $activity_id)
			]);
		}
	}

	// private function assign_students_to_activity_again($activity_id, $data, $timeslot_ids = [])
	// {
	// 	// Get organizer from activity table
	// 	$activity = $this->db->select('organizer')->from('activity')->where('activity_id', $activity_id)->get()->row();
	// 	$organizer = $activity ? $activity->organizer : null;

	// 	// ✅ First, delete existing attendance and fines related to this activity
	// 	$this->db->where('activity_id', $activity_id)->delete('fines');
	// 	$this->db->where('activity_id', $activity_id)->delete('attendance');

	// 	// Select student IDs by joining users and departments based on department name
	// 	$this->db->select('u.student_id');
	// 	$this->db->from('users u');
	// 	$this->db->join('department d', 'u.dept_id = d.dept_id');
	// 	$this->db->where('role', 'Student');

	// 	if (!empty($data['audience']) && strtolower($data['audience']) !== 'all') {
	// 		$this->db->where('d.dept_name', $data['audience']);
	// 	}

	// 	$students = $this->db->get()->result();

	// 	foreach ($students as $student) {
	// 		// Check if student is exempted
	// 		$is_exempted = $this->db
	// 			->where('student_id', $student->student_id)
	// 			->get('exempted_student')
	// 			->num_rows() > 0;

	// 		foreach ($timeslot_ids as $timeslot_id) {
	// 			// Attendance
	// 			$attendance_data = [
	// 				'activity_id' => $activity_id,
	// 				'timeslot_id' => $timeslot_id,
	// 				'student_id'  => $student->student_id,
	// 			];

	// 			if ($is_exempted) {
	// 				$attendance_data['status'] = 'Exempted';
	// 			}

	// 			$this->db->insert('attendance', $attendance_data);
	// 			$attendance_id = $this->db->insert_id();

	// 			// Fines
	// 			$fines_data = [
	// 				'activity_id'    => $activity_id,
	// 				'timeslot_id'    => $timeslot_id,
	// 				'student_id'     => $student->student_id,
	// 				'attendance_id'  => $attendance_id,
	// 				'status'         => 'Pending',
	// 				'fines_amount'   => 0
	// 			];

	// 			$this->db->insert('fines', $fines_data);
	// 		}

	// 		// Summary (either update or insert)
	// 		$this->db->where('student_id', $student->student_id);
	// 		$this->db->where('organizer', $organizer);
	// 		$existing_summary = $this->db->get('fines_summary')->row();

	// 		if ($existing_summary) {
	// 			$this->db->where('summary_id', $existing_summary->summary_id);
	// 			$this->db->update('fines_summary', [
	// 				'fines_status' => 'Unpaid',
	// 				'last_updated' => date('Y-m-d H:i:s')
	// 			]);
	// 		} else {
	// 			$this->db->insert('fines_summary', [
	// 				'student_id' => $student->student_id,
	// 				'organizer'  => $organizer,
	// 				'fines_status' => 'Unpaid',
	// 				'last_updated' => date('Y-m-d H:i:s')
	// 			]);
	// 		}
	// 	}
	// }


	public function get_edit_logs($activity_id)
	{
		$logs = $this->admin->get_activity_logs($activity_id);
		echo json_encode($logs);
	}

	// Get the changes made between the original activity and the new data
	private function get_activity_changes($original, $new_data)
	{
		$changes = [];
		foreach ($new_data as $key => $new_value) {
			if (isset($original[$key]) && $original[$key] != $new_value) {
				$changes[$key] = [
					'old' => $original[$key],
					'new' => $new_value
				];
			}
		}

		return $changes;
	}




	public function download_edit_logs($activity_id)
	{
		$this->load->library('fpdf'); // Adjust based on your setup
		$this->load->model('Admin_model');

		// Retrieve the activity title using the activity ID
		$activity = $this->Admin_model->get_activity_by_id($activity_id);
		$activity_title = $activity->activity_title; // Assuming 'title' is the column for the activity title

		// Sanitize the title to avoid any invalid characters in the filename (e.g., /, \, ?, etc.)
		$sanitized_title = preg_replace('/[^A-Za-z0-9 _.-]/', '', $activity_title);


		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		$headerImage = '';
		$footerImage = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db
				->select('organization.header, organization.footer')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db
				->select('header, footer')
				->where('dept_id', $user['dept_id'])
				->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
			}
		}

		// Retrieve logs
		$logs = $this->Admin_model->get_activity_logs($activity_id);
		$pdf = new PDF('P', 'mm', 'Letter');
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, 'Edit Logs for Activity: ' . $activity_title, 0, 1, 'C');
		$pdf->Ln(5);

		if (empty($logs)) {
			$pdf->SetFont('Arial', '', 12);
			$pdf->Cell(0, 10, 'No edit logs found.', 0, 1, 'C');
			// Use the sanitized activity title in the filename
			$pdf->Output('D', $sanitized_title . '_editlogs.pdf');
			return;
		}

		// Define column widths
		$colWidths = [
			'num' => 10,
			'edited_by' => 40,
			'changes' => 100,
			'datetime' => 46
		];


		// Header
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($colWidths['num'], 10, '#', 1);
		$pdf->Cell($colWidths['edited_by'], 10, 'Edited By', 1);
		$pdf->Cell($colWidths['changes'], 10, 'Changes', 1);
		$pdf->Cell($colWidths['datetime'], 10, 'Date/Time', 1);
		$pdf->Ln();

		$pdf->SetFont('Arial', '', 11);

		foreach ($logs as $i => $log) {
			$editedBy = $log['first_name'] . ' ' . $log['last_name'];
			$formattedTime = $log['formatted_time'];

			// Decode and format changes
			$changes = '';
			$decoded = json_decode($log['changes'], true);  // Assuming changes are in JSON format
			if ($decoded) {
				foreach ($decoded as $field => $change) {
					$old = isset($change['old']) ? $change['old'] : 'N/A';
					$new = isset($change['new']) ? $change['new'] : 'N/A';

					// Convert to 12-hour format if values look like datetime or time
					$old = $this->formatIfTime($old);
					$new = $this->formatIfTime($new);

					// Sanitize text
					$old = $this->sanitize_text($old);
					$new = $this->sanitize_text($new);

					$changes .= "$field: Old: $old -- New: $new\n";
				}
			} else {
				// If not decoded, directly sanitize and clean up the changes
				$changes = $this->sanitize_text($log['changes']);
			}

			// Column widths
			$lineHeight = 5;


			// 1. Calculate number of lines in the "Changes" cell
			$changesLines = $pdf->NbLines($colWidths['changes'], $changes);
			$rowHeight = $changesLines * $lineHeight;


			// 2. Check if the row fits in the remaining space (assuming default 10mm bottom margin)
			$bottomMargin = 10;
			if ($pdf->GetY() + $rowHeight > ($pdf->GetPageHeight() - $bottomMargin)) {
				$pdf->AddPage();
				// Re-draw table headers after the page break
				$pdf->SetFont('Arial', 'B', 10);
				$pdf->Cell($colWidths['num'], 10, '#', 1);
				$pdf->Cell($colWidths['edited_by'], 10, 'Edited By', 1);
				$pdf->Cell($colWidths['changes'], 10, 'Changes', 1);
				$pdf->Cell($colWidths['datetime'], 10, 'Date/Time', 1);
				$pdf->Ln();
				$pdf->SetFont('Arial', '', 11); // Reset font
			}

			// 2. Save current X and Y
			$x = $pdf->GetX();
			$y = $pdf->GetY();

			// 3. Draw static-height cells
			$pdf->Cell($colWidths['num'], $rowHeight, $i + 1, 1);
			$pdf->Cell($colWidths['edited_by'], $rowHeight, $editedBy, 1);

			// 4. Draw MultiCell for "Changes"
			$pdf->SetXY($x + $colWidths['num'] + $colWidths['edited_by'], $y);
			$pdf->MultiCell($colWidths['changes'], $lineHeight, $changes, 1);

			// 5. Manually position and draw Date/Time cell
			$pdf->SetXY($x + $colWidths['num'] + $colWidths['edited_by'] + $colWidths['changes'], $y);
			$pdf->Cell($colWidths['datetime'], $rowHeight, $formattedTime, 1);

			// 6. Move cursor to the next row
			$pdf->SetY($y + $rowHeight);
		}

		// Use the sanitized activity title in the filename
		$pdf->Output('D', $sanitized_title . '_editlogs.pdf');
	}

	// Sanitize function to remove or replace problematic characters
	private function sanitize_text($text)
	{
		// Ensure text is UTF-8 encoded and sanitize it
		$text = utf8_encode($text); // Ensure the text is in UTF-8
		$text = preg_replace('/[^\x20-\x7E]/', '', $text); // Remove non-ASCII characters
		return $text;
	}

	private function formatIfTime($value)
	{
		$formats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d\TH:i:s', 'H:i:s', 'H:i'];

		foreach ($formats as $format) {
			$dt = DateTime::createFromFormat($format, $value);
			if ($dt && $dt->format($format) === $value) {
				return $dt->format('M d, Y | g:ia');  // May 25. 2025 | 8:00pm
			}
		}

		// Also try generic strtotime (for cases like "2025-05-25 20:00")
		if (strtotime($value)) {
			$dt = new DateTime($value);
			return $dt->format('M d, Y | g:ia');
		}

		return $value; // Return unchanged if not a datetime
	}









	// EVALUATION LIST - PAGE (FINAL CHECK)
	public function list_activity_evaluation()
	{
		$data['title'] = 'List of Evaluation';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// GET THE EVALUATION FOR STUDENT PARLIAMENT
		$data['evaluation'] = $this->admin->evaluation_form();

		// Get active semester and academic year || used for read only in filters
		$semester_ay = $this->db->where('is_active', 1)->get('semester_ay')->row();
		if ($semester_ay) {
			$data['active_semester'] = $semester_ay->semester;
			$data['active_academic_year'] = $semester_ay->academic_year;
		} else {
			$data['active_semester'] = 'N/A';
			$data['active_academic_year'] = 'N/A';
		}

		// Pass data to the view
		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/eval_list-activity-evaluation', $data);
		$this->load->view('layout/footer', $data);
	}

	// CREATE EVALUATION - PAGE (FINAL CHECK)
	public function create_evaluationform()
	{
		$data['title'] = 'Create Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// Fetch activities based on user role
		$data['activities'] = $this->admin->activity_organized();

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/eval_create-evaluation-form', $data);
		$this->load->view('layout/footer', $data);
	}

	// FUNCTION CREATION OF EVALUATION FORM
	public function create_eval()
	{
		// Validate basic form inputs from the POST request
		$this->form_validation->set_rules('activity', 'Activity', 'required');
		$this->form_validation->set_rules('formtitle', 'Form Title', 'required');
		$this->form_validation->set_rules('formdescription', 'Form Description', 'required');

		if ($this->form_validation->run() === FALSE) {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
			return;
		}

		// Prepare form data from POST request
		$formData = array(
			'title'              => $this->input->post('formtitle'),
			'form_description'   => $this->input->post('formdescription'),
			'activity_id'        => $this->input->post('activity'),
			'start_date_evaluation' => $this->input->post('startdate')
				? date('Y-m-d H:i:s', strtotime($this->input->post('startdate')))  // 24-hour format
				: date('Y-m-d H:i:s'),

			'end_date_evaluation' => $this->input->post('enddate')
				? date('Y-m-d H:i:s', strtotime($this->input->post('enddate')))    // 24-hour format
				: date('Y-m-d H:i:s', strtotime('+1 week')),
			'status_evaluation'     => $this->input->post('status_evaluation'),
		);


		// Handle Cover Image Upload
		if (!empty($_FILES['coverUpload']['name'])) {
			$coverImage = $this->upload_file('coverUpload', './assets/theme_evaluation');
			if (!$coverImage['status']) {
				echo json_encode(['success' => false, 'message' => $coverImage['error']]);
				return;
			}
			$formData['cover_theme'] = $coverImage['file_name'];
		}

		// Decode fields JSON (ensuring proper format)
		$fields = json_decode($this->input->post('fields'), true);

		// Validate and check if fields are properly sent
		if (!is_array($fields) || count($fields) === 0) {
			echo json_encode(['success' => false, 'message' => 'Please provide at least one form field.']);
			return;
		}

		$this->db->trans_start(); // Start transaction

		$formId = $this->admin->saveForm($formData); // Save form data and get form ID

		if (!$formId) {
			$this->db->trans_rollback(); // Rollback if form insert fails
			echo json_encode(['success' => false, 'message' => 'Failed to create the form.']);
			return;
		}

		// Prepare and save form fields with related form_id
		$fieldData = [];
		foreach ($fields as $index => $field) {
			$fieldData[] = array(
				'form_id'     => $formId,
				'label'       => $field['label'] ?? '',
				'type'        => $field['type'] ?? '',
				'placeholder' => $field['placeholder'] ?? null,
				'required'    => !empty($field['required']) ? 1 : 0,
				'order'       => $index + 1,
			);
		}

		if (!empty($fieldData)) {
			$this->admin->saveFormFields($fieldData); // Save form fields
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['success' => false, 'message' => 'Failed to create the form.']);
		} else {
			// === SEND NOTIFICATIONS BASED ON ACTIVITY AUDIENCE ===
			$activity_id = $formData['activity_id'];
			$activity = $this->admin->get_activity_by_id($activity_id); // Make sure you have this method to get activity details

			$students_to_notify = [];

			if ($activity->audience === 'All') {
				// Notify all students except admins
				$this->db->select('student_id, first_name, last_name, role');
				$this->db->from('users');
				$this->db->where('role !=', 'Admin');
				$students_to_notify = $this->db->get()->result();
			} else {
				// Notify students in the specified department
				$this->db->select('u.student_id, u.first_name, u.last_name, role');
				$this->db->from('users u');
				$this->db->join('department d', 'd.dept_id = u.dept_id');
				$this->db->where('d.dept_name', $activity->audience);
				$this->db->where('u.role !=', 'Admin');
				$students_to_notify = $this->db->get()->result();
			}

			foreach ($students_to_notify as $student) {
				$notification_data = [
					'recipient_student_id' => $student->student_id,
					'sender_student_id'    => $this->session->userdata('student_id'), // System/Admin
					'type'                 => 'evaluation_uploaded',
					'reference_id'         => $formId, // Link to the form
					'message'              => 'uploaded a new evaluation form for the activity "' . $activity->activity_title . '"',
					'is_read'              => 0,
					'created_at'           => date('Y-m-d H:i:s'),
					'link' => base_url('student/evaluation-form/list')

				];
				$this->db->insert('notifications', $notification_data);

				if ($student->role === 'Student') {
					// Check if the student is present in the attendance table for the activity
					$this->db->where('student_id', $student->student_id);
					$this->db->where('activity_id', $activity_id); // Make sure $activityId is defined
					$this->db->where('attendance_status', 'Present'); // Make sure $activityId is defined
					$attendance = $this->db->get('attendance')->row();

					if ($attendance) {
						// Insert into evaluation_responses table
						$this->db->insert('evaluation_responses', [
							'form_id'    => $formId,
							'student_id' => $student->student_id,
						]);
					}
				}
			}

			echo json_encode([
				'success' => true,
				'message' => 'Form created successfully.',
				'redirect_url' => site_url('admin/list-activity-evaluation')
			]);
		}
	}


	// EDITING EVALUATION - PAGE (FINAL CHECK)
	public function edit_evaluationform($form_id)
	{
		$data['title'] = 'Edit Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// Fetch activities based on user role
		$data['activities'] = $this->admin->activity_organized();
		$data['forms'] = $this->admin->get_evaluation_by_id($form_id);

		$form_data = $this->admin->get_evaluation_by_id($form_id);
		$data['form_data'] = $form_data;

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/eval_edit-evaluation-form', $data);
		$this->load->view('layout/footer', $data);
	}

	public function update_eval($form_id)
	{
		// Validate form ID
		if (!$form_id || !is_numeric($form_id)) {
			echo json_encode(['success' => false, 'message' => 'Invalid form ID.']);
			return;
		}

		// Validate required input fields
		$this->form_validation->set_rules('formtitle', 'Form Title', 'required');
		$this->form_validation->set_rules('formdescription', 'Form Description', 'required');

		if ($this->form_validation->run() === FALSE) {
			echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
			return;
		}

		// Prepare main form data
		$formData = [
			'activity_id'           => $this->input->post('activity'),
			'title'                 => $this->input->post('formtitle'),
			'form_description'      => $this->input->post('formdescription'),
			'start_date_evaluation' => date('Y-m-d H:i:s', strtotime($this->input->post('startdate'))),
			'end_date_evaluation'   => date('Y-m-d H:i:s', strtotime($this->input->post('enddate'))),
		];

		// Handle optional cover image upload
		if (!empty($_FILES['coverUpload']['name'])) {
			$coverImage = $this->upload_file('coverUpload', './assets/theme_evaluation');
			if (!$coverImage['status']) {
				echo json_encode(['success' => false, 'message' => $coverImage['error']]);
				return;
			}
			$formData['cover_theme'] = $coverImage['file_name'];
		}

		// Decode JSON field input
		$fields = json_decode($this->input->post('fields'), true);
		if (!is_array($fields)) {
			echo json_encode(['success' => false, 'message' => 'Invalid form fields data.']);
			return;
		}

		log_message('debug', 'Received fields: ' . print_r($fields, true));

		// Begin transaction
		$this->db->trans_start();

		// Update form
		$this->db->where('form_id', $form_id)->update('forms', $formData);

		// Get current field IDs
		$existingFields = $this->db->select('form_fields_id')->where('form_id', $form_id)->get('formfields')->result_array();
		$existingFieldIds = array_column($existingFields, 'form_fields_id');

		$updatedFieldIds = [];

		foreach ($fields as $index => $field) {
			// Normalize field ID
			$fieldId = isset($field['form_fields_id']) && is_numeric($field['form_fields_id']) ? (int)$field['form_fields_id'] : null;

			// Build field data
			$fieldData = [
				'form_id'     => $form_id,
				'label'       => $field['label'] ?? '',
				'type'        => $field['type'] ?? 'text',
				'placeholder' => $field['placeholder'] ?? null,
				'required'    => !empty($field['required']) ? 1 : 0,
				'order'       => $index + 1,
			];

			if ($fieldId && in_array($fieldId, $existingFieldIds)) {
				// Update existing field
				$this->db->where('form_fields_id', $fieldId)->update('formfields', $fieldData);
				$updatedFieldIds[] = $fieldId;
			} else {
				// Insert new field
				$this->db->insert('formfields', $fieldData);
				$updatedFieldIds[] = $this->db->insert_id();
			}
		}

		// Delete fields that were removed
		$fieldsToDelete = array_diff($existingFieldIds, $updatedFieldIds);
		if (!empty($fieldsToDelete)) {
			$this->db->where_in('form_fields_id', $fieldsToDelete)->delete('formfields');
		}

		// Commit transaction
		$this->db->trans_complete();

		// Check transaction result
		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['success' => false, 'message' => 'Failed to update the form.']);
		} else {
			echo json_encode([
				'success'  => true,
				'message'  => 'Form updated successfully.',
				'redirect' => site_url('admin/list-activity-evaluation'),
			]);
		}
	}


	// VIEWING OF EVALUATION FORM - PAGE (FINAL CHECK)
	public function view_evaluationform($form_id)
	{
		$data['title'] = 'View Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// Fetch activities based on user role
		$data['activities'] = $this->admin->activity_organized();
		$data['forms'] = $this->admin->get_evaluation_by_id($form_id);

		$form_data = $this->admin->get_evaluation_by_id($form_id);
		$data['form_data'] = $form_data;



		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/eval_view-evaluation-form', $data);
		$this->load->view('layout/footer', $data);
	}

	// EVALUATION RESPONSES - PAGE (FINAL CHECK)
	public function list_evaluation_responses($form_id)
	{
		$data['title'] = 'Evaluation Responses';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARY
		$data['users'] = $this->admin->get_student($student_id);

		$data['departments'] = $this->admin->get_department();
		$data['forms'] = $this->admin->forms($form_id);

		$responses = $this->admin->get_student_evaluation_responses($form_id);

		$grouped_responses = [];
		$questions = [];
		$question_types = [];

		foreach ($responses as $row) {
			$student_id = $row->student_id;

			// Initialize the student entry if it hasn't been set yet
			if (!isset($grouped_responses[$student_id])) {
				$grouped_responses[$student_id] = [
					'student_id'   => $row->student_id,
					'name'         => $row->name,
					'dept_name'    => $row->dept_name,
					'submitted_at' => $row->submitted_at,
					'answers'      => [],
					'type'         => [] // Hold types per question
				];
			}

			// Add the answer and the corresponding question type
			$grouped_responses[$student_id]['answers'][$row->question] = $row->answer;
			$grouped_responses[$student_id]['type'][$row->question] = $row->type;

			// Store unique questions and their types
			if (!in_array($row->question, $questions)) {
				$questions[] = $row->question;
			}

			if (!isset($question_types[$row->question])) {
				$question_types[$row->question] = $row->type;
			}
		}

		$data['responses'] = $grouped_responses;
		$data['questions'] = $questions;
		$data['question_types'] = $question_types; // Optional if needed in view

		$data['form_id'] = $form_id; // Pass form_id to the view

		// Load views
		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/eval_list-evaluation-responses', $data);
		$this->load->view('layout/footer', $data);
	}



	public function export_evaluation_responses_pdf($form_id)
	{
		ob_start();

		$this->load->library('pdf');
		$this->load->model('admin');

		$form = $this->admin->forms($form_id);
		$responses = $this->admin->get_student_evaluation_responses($form_id);

		// Separate rating and long-answer questions
		$rating_questions = [];
		$long_answers = [];

		foreach ($responses as $r) {
			if ($r->type === 'rating') {
				if (!isset($rating_questions[$r->question])) {
					$rating_questions[$r->question] = [];
				}
				$rating_questions[$r->question][] = $r->answer;
			} elseif ($r->type === 'short') {
				if (!isset($long_answers[$r->question])) {
					$long_answers[$r->question] = [];
				}
				$long_answers[$r->question][] = $r->answer;
			}
		}

		// Load header/footer based on user
		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		$headerImage = '';
		$footerImage = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db
				->select('organization.header, organization.footer')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db
				->select('header, footer')
				->where('dept_id', $user['dept_id'])
				->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
			}
		}

		$pdf = new PDF('P', 'mm', 'Legal');
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->AddPage();
		$pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
		$pdf->AddFont('DejaVuSans', 'B', 'DejaVuSans-Bold.php');

		$pdf->SetFont('DejaVuSans', 'B', 14);
		$pdf->Cell(0, 10, 'Rating Questions Summary: ' . $form->title, 0, 1, 'C');
		$pdf->Ln(5);

		// 🔧 Rating Scale Table (Full Width: 190mm)
		$pdf->SetFont('DejaVuSans', 'B', 11);
		$pdf->Cell(95, 8, 'Average Rating', 1, 0, 'C');
		$pdf->Cell(95, 8, 'Category', 1, 1, 'C');

		$pdf->SetFont('DejaVuSans', '', 11);
		$pdf->Cell(95, 8, '1', 1, 0, 'C');
		$pdf->Cell(95, 8, 'Poor', 1, 1, 'C');
		$pdf->Cell(95, 8, '2', 1, 0, 'C');
		$pdf->Cell(95, 8, 'Needs Improvement', 1, 1, 'C');
		$pdf->Cell(95, 8, '3', 1, 0, 'C');
		$pdf->Cell(95, 8, 'Average', 1, 1, 'C');
		$pdf->Cell(95, 8, '4', 1, 0, 'C');
		$pdf->Cell(95, 8, 'Good', 1, 1, 'C');
		$pdf->Ln(5);

		// 🔵 Rating Summary Table Header (also 190mm total)
		$pdf->SetFont('DejaVuSans', 'B', 11);
		$pdf->SetFillColor(204, 229, 255);
		$pdf->Cell(70, 8, 'Question', 1, 0, 'C', true);
		$pdf->Cell(25, 8, 'Avg Rating', 1, 0, 'C', true);
		$pdf->Cell(15, 8, '4', 1, 0, 'C', true);
		$pdf->Cell(15, 8, '3', 1, 0, 'C', true);
		$pdf->Cell(15, 8, '2', 1, 0, 'C', true);
		$pdf->Cell(15, 8, '1', 1, 0, 'C', true);
		$pdf->Cell(35, 8, 'Total Responses', 1, 1, 'C', true); // adjusted to keep total 190mm

		$overallSum = 0;
		$overallCount = 0;

		$pdf->SetFont('DejaVuSans', '', 11);
		foreach ($rating_questions as $question => $answers) {
			$counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
			$sum = 0;
			$total = 0;

			foreach ($answers as $answer) {
				if (is_numeric($answer)) {
					$val = intval($answer);
					if ($val >= 1 && $val <= 4) {
						$counts[$val]++;
						$sum += $val;
						$total++;
					}
				}
			}

			$avg = $total > 0 ? $sum / $total : 0;
			$overallSum += $sum;
			$overallCount += $total;

			$pdf->Cell(70, 8, $question, 1);
			$pdf->Cell(25, 8, number_format($avg, 2), 1, 0, 'C');
			$pdf->Cell(15, 8, round(($counts[4] / max(1, $total)) * 100) . '%', 1, 0, 'C');
			$pdf->Cell(15, 8, round(($counts[3] / max(1, $total)) * 100) . '%', 1, 0, 'C');
			$pdf->Cell(15, 8, round(($counts[2] / max(1, $total)) * 100) . '%', 1, 0, 'C');
			$pdf->Cell(15, 8, round(($counts[1] / max(1, $total)) * 100) . '%', 1, 0, 'C');
			$pdf->Cell(35, 8, $total, 1, 1, 'C');
		}

		$overallAvg = $overallCount > 0 ? $overallSum / $overallCount : 0;
		$category = 'Poor';
		if ($overallAvg >= 3.5) $category = 'Good';
		elseif ($overallAvg >= 2.5) $category = 'Average';
		elseif ($overallAvg >= 1.5) $category = 'Needs Improvement';

		$pdf->Ln(5);
		$pdf->SetFont('DejaVuSans', 'B', 12);
		$pdf->Cell(0, 10, 'Overall Average Rating: ' . number_format($overallAvg, 2) . " ($category)", 0, 1, 'C');
		$pdf->Ln(10);

		// 🟡 Long Answer Section (Same width: 190mm)
		foreach ($long_answers as $question => $responses) {
			$pdf->SetFont('DejaVuSans', 'B', 12);
			$pdf->MultiCell(0, 8, "Question: " . $question, 0, 'L');
			$pdf->Ln(2);

			$pdf->SetFillColor(230, 240, 255);
			$pdf->SetFont('DejaVuSans', 'B', 11);
			$pdf->Cell(10, 8, '#', 1, 0, 'C', true);
			$pdf->Cell(180, 8, 'Response', 1, 1, 'C', true);

			$pdf->SetFont('DejaVuSans', '', 11);
			$row = 1;
			$fill = false;
			foreach (array_unique($responses) as $response) {
				$pdf->SetFillColor(245, 250, 255);
				$pdf->Cell(10, 8, $row++, 1, 0, 'C', $fill);
				$pdf->MultiCell(180, 8, $response, 1, 'L', $fill);
				$fill = !$fill;
			}
			$pdf->Ln(10);
		}

		ob_end_clean();
		$filename = 'Evaluation_Responses_' . date('Ymd_His') . '.pdf';
		$pdf->Output('D', $filename);
	}






	// EVALUATION STATISTIC - PAGE (FINAL CHECK)
	public function evaluation_statistic($form_id)
	{
		$data['title'] = 'Evaluation Statistic';

		$student_id = $this->session->userdata('student_id');

		// Fetch required data
		$total_attendees = $this->admin->count_attendees($form_id);
		$total_respondents = $this->admin->total_respondents($form_id);
		$rating_summary = $this->admin->rating_summary($form_id);
		$overall_rating = $this->admin->overall_rating($form_id);
		$answer_summary = $this->admin->answer_summary($form_id);

		// ✅ FIX: Compute weighted average overall_rating
		$total_sum = 0;
		$total_count = 0;
		foreach ($rating_summary as $rating) {
			$total_sum += $rating['avg_rating'] * $rating['total_responses'];
			$total_count += $rating['total_responses'];
		}
		$overall_rating = $total_count > 0 ? $total_sum / $total_count : 0;

		$percentage = ($total_attendees > 0) ? round(($total_respondents / $total_attendees) * 100, 2) : 0;


		// Set header/footer image filenames
		$data['headerImage'] = 'header.png'; // Or whatever your actual filename is
		$data['footerImage'] = 'footer.png';

		$data += [
			'users' => $this->admin->get_student($student_id),
			'form_id' => $form_id,
			'total_attendees' => $total_attendees,
			'total_respondents' => $total_respondents,
			'rating_summary' => $rating_summary,
			'overall_rating' => $overall_rating,
			'answer_summary' => $answer_summary,
			'respondent_percentage' => $percentage,
			'forms' => $this->admin->forms($form_id),

			//for graphs
			'rating_distribution' => $this->admin->get_rating_distribution($form_id),
			'respondent_departments' => $this->admin->respondent_departments($form_id),
			'respondent_year_levels' => $this->admin->respondent_year_levels($form_id)
		];

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/eval_statistic', $data);
		$this->load->view('layout/footer', $data);
	}







	//  EXCUSE APPLICATION

	// FETCHING ACTIVITY - PAGE (FINAL CHECK)
	public function list_activity_excuse()
	{
		$data['title'] = 'List of Activity for Excuse Letter';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['activities'] = $this->admin->get_activities_active();

		// Get active semester and academic year || used for read only in filters
		$semester_ay = $this->db->where('is_active', 1)->get('semester_ay')->row();
		if ($semester_ay) {
			$data['active_semester'] = $semester_ay->semester;
			$data['active_academic_year'] = $semester_ay->academic_year;
		} else {
			$data['active_semester'] = 'N/A';
			$data['active_academic_year'] = 'N/A';
		}

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/exc_list-activities-excuseletter', $data);
		$this->load->view('layout/footer', $data);
	}

	// LIST OF APPLICATION PER EVENT - PAGE (FINAL CHECK)
	public function list_excuse_letter($activity_id)
	{
		$data['title'] = 'List of Excuse Letter';


		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['activities'] = $this->admin->fetch_application($activity_id);
		$data['letters'] = $this->admin->fetch_letters();

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/exc_list-excuse-letter', $data);
		$this->load->view('layout/footer', $data);
	}

	// EXCUSE LETTER PER STUDENT - PAGE (FINAL CHECK)
	public function review_excuse_letter($excuse_id)
	{
		$data['title'] = 'Review Excuse Letter';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['excuse'] = $this->admin->review_letter($excuse_id);

		$this->load->view('layout/header', $data);
		$this->load->view('admin/activity/exc_excuse-letter', $data);
		$this->load->view('layout/footer', $data);
	}

	// UPDATING REMARKS AND STATUS OF THE APPLICATION
	public function updateApprovalStatus()
	{
		$remarks = $this->input->post('remarks');
		$approvalStatus = $this->input->post('approvalStatus');
		$excuse_id = $this->input->post('excuse_id');
		$activity_id = $this->input->post('activity_id');
		$student_id = $this->input->post('student_id');


		// Fetch the student_id from the excuse_application table based on the excuse_id
		$this->db->select('student_id');
		$this->db->from('excuse_application');
		$this->db->where('excuse_id', $excuse_id);
		$query = $this->db->get();

		// Ensure a valid student_id is found
		if ($query->num_rows() > 0) {
			$student_id = $query->row()->student_id;
		} else {
			// Handle the case if no student is found for the given excuse_id
			echo json_encode(['success' => false, 'message' => 'Invalid excuse ID']);
			return;
		}

		// Prepare the data for updating the excuse
		$data = [
			'excuse_id' => $excuse_id,
			'status' => $approvalStatus,
			'remarks' => $remarks
		];

		// Call the model to update the excuse status
		$result = $this->admin->updateApprovalStatus($data);
		$link = base_url('student/excuse-application/list/');


		if ($result) {

			// Prepare the message for the notification
			$message = ($approvalStatus === 'Approved')
				? 'has approved your excuse application!'
				: 'has disapproved your excuse application! Remarks: ' . $remarks;

			// Prepare notification data for the student who applied
			$notification_data = [
				'recipient_student_id' => $student_id, // The student receiving the notification
				'sender_student_id' => $this->session->userdata('student_id'), // Admin sending the notification
				'type' => ($approvalStatus === 'Approved') ? 'excuse_approved' : 'excuse_not_approved',
				'reference_id' => $excuse_id, // Reference to the excuse application
				'message' => $message,
			];

			// Load the Notification model
			$this->load->model('Notification_model');

			// Call the add_notification method to insert the notification into the database
			$this->Notification_model->add_notification(
				$notification_data['recipient_student_id'],
				$notification_data['sender_student_id'],
				$notification_data['type'],
				$notification_data['reference_id'],
				$notification_data['message'],
				null,
				$link
			);

			// If approved, update attendance status to 'excuse'
			if ($approvalStatus == 'Approved') {
				$this->db->where('student_id', $student_id)
					->where('activity_id', $activity_id)
					->update('attendance', ['attendance_status' => 'Excused']);
			}

			echo json_encode(['success' => true, 'message' => 'Approval status updated successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to update approval status.']);
		}
	}


	public function export_excuse_applications_pdf($activity_id)
	{
		$this->load->library('fpdf');
		$this->load->model('Admin_model');

		$activity = $this->Admin_model->fetch_application($activity_id);
		$letters = $this->Admin_model->fetch_letters();

		$activity_letters = array_filter($letters, function ($letter) use ($activity_id) {
			return $letter->activity_id == $activity_id;
		});


		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		$headerImage = '';
		$footerImage = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db
				->select('organization.header, organization.footer')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db
				->select('header, footer')
				->where('dept_id', $user['dept_id'])
				->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
			}
		}


		$pdf = new PDF('P', 'mm', 'Letter');
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;

		$pdf->SetMargins(10, 10, 10); // standard margins
		$pdf->AddPage();

		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, $activity['activity_title'] . ' | List of Excuse Applications', 0, 1, 'C');
		$pdf->Ln(5);

		// Base font for measurements
		$pdf->SetFont('Arial', '', 8);

		$headers = [
			'student' => 'Student',
			'department' => 'Department',
			'subject' => 'Subject',
			'status' => 'Status'
		];

		// Measure max content width
		$max_widths = [
			'student'    => $pdf->GetStringWidth($headers['student']),
			'department' => $pdf->GetStringWidth($headers['department']),
			'subject'    => $pdf->GetStringWidth($headers['subject']),
			'status'     => $pdf->GetStringWidth($headers['status'])
		];

		foreach ($activity_letters as $letter) {
			$max_widths['student']    = max($max_widths['student'], $pdf->GetStringWidth($letter->first_name . ' ' . $letter->last_name));
			$max_widths['department'] = max($max_widths['department'], $pdf->GetStringWidth($letter->dept_name));
			$max_widths['subject']    = max($max_widths['subject'], $pdf->GetStringWidth($letter->subject));
			$max_widths['status']     = max($max_widths['status'], $pdf->GetStringWidth($letter->status));
		}

		// Add padding
		$padding = 6;
		foreach ($max_widths as &$width) {
			$width += $padding;
		}

		// Scale total width to fit full printable width (Letter: 216mm, margins: 10mm each → usable: 196mm)
		$total_content_width = array_sum($max_widths);
		$usable_page_width = 196;
		$scaling_factor = $usable_page_width / $total_content_width;

		foreach ($max_widths as &$width) {
			$width = round($width * $scaling_factor, 2); // scale to fit
		}

		// Table Header
		$pdf->SetFont('Arial', 'B', 9);
		foreach (['student', 'department', 'subject', 'status'] as $col) {
			$pdf->Cell($max_widths[$col], 8, $headers[$col], 1, 0, 'C');
		}
		$pdf->Ln();

		// Table Body
		$pdf->SetFont('Arial', '', 8);
		foreach ($activity_letters as $letter) {
			$pdf->Cell($max_widths['student'], 8, $letter->first_name . ' ' . $letter->last_name, 1, 0, 'L');
			$pdf->Cell($max_widths['department'], 8, $letter->dept_name, 1, 0, 'L');
			$pdf->Cell($max_widths['subject'], 8, $letter->subject, 1, 0, 'L');
			$pdf->Cell($max_widths['status'], 8, $letter->status, 1, 1, 'L');
		}

		$filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $activity['activity_title'] . '_excuse_applications') . '.pdf';
		$pdf->Output('I', $filename);
	}


	// COMMUNITY SECTION (FINAL CHECK)

	// COMMUNITY - PAGE
	public function community()
	{
		$data['title'] = 'Community';
		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->admin->get_student($student_id);
		$data['authors'] = $this->admin->get_user();

		// Get offset and limit from AJAX request
		$limit = $this->input->post('limit') ?: 5;
		$offset = $this->input->post('offset') ?: 0;

		// GET LIMITED POSTS
		$data['posts'] = $this->admin->get_all_posts();
		foreach ($data['posts'] as &$post) {
			$post->like_count = $this->admin->get_like_count($post->post_id);
			$post->user_has_liked_post = $this->admin->user_has_liked($post->post_id, $student_id);
			$post->comments_count = $this->admin->get_comment_count($post->post_id);
			$post->comments = $this->admin->get_comments_by_post($post->post_id);
			$post->type = 'post';
		}

		// GET LIMITED ACTIVITIES
		$data['activities'] = $this->admin->get_shared_activities();
		foreach ($data['activities'] as &$activity) {
			$activity->type = 'activity';
		}

		// MERGE POSTS & ACTIVITIES BEFORE PAGINATION
		$merged_feed = array_merge($data['posts'], $data['activities']);

		// SORT MERGED DATA BY DATE
		usort($merged_feed, function ($a, $b) {
			// For activities, use 'updated_at', for posts, use 'created_at'
			$a_date = isset($a->updated_at) ? $a->updated_at : (isset($a->created_at) ? $a->created_at : '');
			$b_date = isset($b->updated_at) ? $b->updated_at : (isset($b->created_at) ? $b->created_at : '');

			return strtotime($b_date) - strtotime($a_date);
		});

		//Apply pagination on the merged feed (with proper offset and limit)
		$data['feed'] = array_slice($merged_feed, $offset, $limit);

		// Activity to post and show to the upcoming section
		$data['activities_upcoming'] = $this->admin->get_activities_upcoming();

		// AJAX Request: Return only the next batch
		if ($this->input->is_ajax_request()) {
			$this->load->view('admin/activity/community_feed', $data);
		} else {
			// FULL PAGE LOAD
			$this->load->view('layout/header', $data);
			$this->load->view('admin/activity/community', $data);
			$this->load->view('layout/footer', $data);
		}
	}

	// Method to fetch likes by post ID and return the list of users who liked
	public function view_likes($post_id)
	{
		// Fetch the likes data for the given post ID
		$likes = $this->admin->get_likes_by_post($post_id);

		// Generate HTML to return as a response
		$output = '';
		if ($likes) {
			// Generate HTML for the list of likes
			foreach ($likes as $like) {
				echo '<li class="d-flex align-items-center mt-2">';
				echo '    <div class="avatar avatar-xl position-relative">';
				echo '        <img class="rounded-circle" src="' . base_url('assets/profile/') . ($like->profile_pic ? $like->profile_pic : 'default.jpg') . '" alt="Profile Picture" />';
				echo '        <div class="position-absolute top-0 start-50 translate-middle-x" style="width: 1rem; height: 1rem; margin-top: 60%; margin-left: 40%;">';
				echo '            <img src="' . base_url('assets/img/icons/spot-illustrations/like-active.png') . '" class="w-100 h-100" alt="Heart Icon" />';
				echo '        </div>';
				echo '    </div>';
				echo '    <div class="ms-3 flex-grow-1">';
				echo '        <h6 class="fw-semi-bold mb-0">' . htmlspecialchars($like->first_name . " " . $like->last_name) . '</h6>';
				echo '    </div>';
				echo '</li>';
			}
		} else {
			$output = '<li>No likes yet.</li>';
		}

		echo $output; // Output the HTML response
	}

	// LIKING OF POST
	public function like_post($post_id)
	{
		$student_id = $this->session->userdata('student_id');

		// Check if the user already liked the post
		if ($this->admin->user_has_liked($post_id, $student_id)) {
			// User already liked, so we will "unlike" the post
			$this->admin->unlike_post($post_id, $student_id);
			$like_img = base_url() . 'assets/img/icons/spot-illustrations/like-inactive.png';
			$like_text = 'Like';
		} else {
			// User has not liked the post yet, so we will "like" the post
			$this->admin->like_post($post_id, $student_id);
			$like_img = base_url() . 'assets/img/icons/spot-illustrations/like-active.png';
			$like_text = 'Liked';
		}

		// Get the updated like count
		$new_like_count = $this->admin->get_like_count($post_id);

		// Return the response
		echo json_encode([
			'like_img' => $like_img,
			'like_text' => $like_text,
			'new_like_count' => $new_like_count
		]);
	}

	// ADDING COMMENTS
	public function add_comment()
	{
		if ($this->input->post()) {
			// Set validation rules
			$this->form_validation->set_rules('comment', 'Comment', 'required');

			if ($this->form_validation->run() == FALSE) {
				$response = [
					'status' => 'error',
					'errors' => validation_errors()
				];
			} else {
				// Prepare data for insertion
				$data = array(
					'post_id' => $this->input->post('post_id'),
					'content' => $this->input->post('comment'),
					'student_id' => $this->session->userdata('student_id')
				);

				$result = $this->admin->add_comment($data);
				if ($result) {
					$post_id = $this->input->post('post_id');

					// Fetch updated comment count
					$comments_count = $this->admin->get_comment_count($post_id);

					// Fetch the newly added comment only
					$new_comment = $this->admin->get_latest_comment($post_id); // Ensure this function fetches only the latest

					// Ensure correct JSON response
					$response = array(
						'status' => 'success',
						'message' => 'Comment Saved Successfully',
						'comments_count' => $comments_count,
						'new_comment' => array( // Make sure this is an array with expected keys
							'first_name' => $new_comment->first_name ?? 'Unknown',
							'last_name' => $new_comment->last_name ?? 'Unknown',
							'profile_pic' => base_url('assets/profile/') . ($new_comment->profile_pic ?? 'default.jpg'),
							'content' => $new_comment->content ?? '',
						)
					);
				} else {
					$response = array(
						'status' => 'error',
						'errors' => 'Failed to Save Comment'
					);
				}
			}
		}

		echo json_encode($response);
	}

	public function delete_post()
	{
		$post_id = $this->input->post('post_id');

		if (!$post_id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Post ID']);
			return;
		}

		// Fetch image path before deleting
		$this->db->select('media');
		$this->db->where('post_id', $post_id);
		$query = $this->db->get('post'); // Get image from images table

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				if (!empty($row->media)) { // Check if media field is not empty
					$image_path = FCPATH . 'assets/post/' . $row->media; // Change path as needed

					if (file_exists($image_path)) {
						unlink($image_path); // Delete image from folder
					}
				}
			}
		}


		// Load model and delete post
		$deleted = $this->admin->delete_post($post_id);

		if ($this->db->affected_rows() > 0) {
			echo json_encode(['status' => 'success', 'message' => 'Post deleted successfully']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete post']);
		}
	}

	// SHARING OF ACTIVITY TO THE FEED
	public function share()
	{
		// Only accept AJAX requests
		if (!$this->input->is_ajax_request()) {
			show_error('No direct script access allowed.');
		}

		// Get the activity ID from the POST request
		$input = json_decode(file_get_contents('php://input'), true);
		$activity_id = $input['activity_id'];

		if ($activity_id) {
			// Update the activity's is_shared column
			$result = $this->admin->update_is_shared($activity_id);

			if ($result) {
				// Return success response
				echo json_encode(['success' => true]);
				return;
			}
		}

		// Return failure response
		echo json_encode(['success' => false]);
	}

	// ADDING OF POST
	public function add_post()
	{

		$student_id = $this->session->userdata('student_id');

		// Check if the form is submitted
		if ($this->input->post()) {
			// Set validation rules
			$this->form_validation->set_rules('content', 'Content', 'required');

			// Check if validation passes
			if ($this->form_validation->run() == FALSE) {
				// If validation fails, return validation errors
				$response = [
					'status' => 'error',
					'errors' => validation_errors()
				];
				echo json_encode($response);
				return;
			}


			// Get active semester and academic year from semester_range or semester_ay table
			$semester_ay = $this->db->get('semester_ay')->row();
			if (!$semester_ay) {
				echo json_encode(['status' => 'error', 'errors' => 'Active semester and academic year not set.']);
				return;
			}
			$active_semester = $semester_ay->semester;
			$active_ay = $semester_ay->academic_year;




			// Prepare data for insertion
			$data = [
				'student_id' => $student_id,
				'content' => $this->input->post('content'),
				'privacy' => $this->input->post('privacyStatus'),
				'dept_id' => NULL,
				'org_id' => NULL,

				//added for ay and semester
				'semester' => $active_semester,
				'academic_year' => $active_ay,
				'created_at' => date('Y-m-d H:i:s')
			];

			// Handle file upload if an image is provided
			if (!empty($_FILES['image']['name'])) {
				$config = [
					'upload_path'   => './assets/post/',
					'allowed_types' => 'gif|jpg|jpeg|png',
					'max_size'      => 2048, // 2MB limit
					'encrypt_name'  => TRUE  // Encrypt file name for security
				];

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('image')) {
					$uploadData = $this->upload->data();
					$data['media'] = $uploadData['file_name'];
				} else {
					// Upload failed, return error response
					$response = [
						'status' => 'error',
						'errors' => $this->upload->display_errors()
					];
					echo json_encode($response);
					return;
				}
			}

			// Insert post data into the database
			$result = $this->admin->insert_data($data);


			if ($result) {
				// notifications start
				// $students = $this->db->select('student_id')->where('role', 'student')->get('users')->result_array();
				$students = $this->db
					->select('student_id')
					->where('role', 'student')
					->where('is_active', 'Active') // ✅ Only active students
					->get('users')
					->result_array();

				$notification_data = [];
				foreach ($students as $student) {
					$notification_data[] = [
						'recipient_student_id' => $student['student_id'],
						'sender_student_id' => $student_id, // Admin who posted
						'type' => 'new_post',
						'reference_id' => $result, // Post ID
						'message' => 'created a new post.',
						'is_read' => 0,
						'created_at' => date('Y-m-d H:i:s'),
						'link' => base_url('student/home/')
					];
				}

				if (!empty($notification_data)) {
					$this->db->insert_batch('notifications', $notification_data);
				} //notifications end

				// Post saved successfully
				$response = [
					'status' => 'success',
					'message' => 'You shared a post.',
					'redirect' => site_url('admin/community')
				];
			} else {
				// Database insertion failed
				$response = [
					'status' => 'error',
					'errors' => 'Failed to post. Please try again.'
				];
			}

			// Return the response
			echo json_encode($response);
			return;
		}

		// If no POST data, return an error response
		$response = [
			'status' => 'error',
			'errors' => 'Invalid request. No data received.'
		];
		echo json_encode($response);
	}

	// ATTENDANCE RECORDING

	// GETTING THE FACES 
	public function getFaces()
	{
		$this->load->database();
		$query = $this->db->select('*')->get('users');
		$faces = $query->result_array();

		// Ensure the full URL is returned
		foreach ($faces as &$face) {
			$face['profile_pic'] = base_url('assets/profile/' . $face['profile_pic']);
		}

		echo json_encode($faces);
	}

	// SCANNING AND FACIAL RECOGNITION - PAGE (FINAL CHECK)
	public function time_in($activity_id, $timeslot_id)
	{
		$data['title'] = 'Taking Attendance - Time in';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['activity'] = $this->admin->get_activity_scan($activity_id, $timeslot_id);
		$data['schedule'] = $this->admin->get_schedule_scan($activity_id, $timeslot_id);

		// Fetching students that belong to activity
		$data['students'] = $this->admin->get_students_realtime_time_in($activity_id, $timeslot_id);

		$this->load->view('layout/header', $data);
		$this->load->view('admin/attendance/scanqr_timein', $data);
		$this->load->view('layout/footer', $data);
	}

	public function impose_fines_timein()
	{
		// Get raw JSON input and decode it
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, true);

		$activity_id = $input['activity_id'] ?? null;
		$timeslot_id = $input['timeslot_id'] ?? null;

		log_message('debug', "Received activity_id: $activity_id, timeslot_id: $timeslot_id");

		$this->admin->imposeFinesIfAbsentIn($activity_id, $timeslot_id);

		echo json_encode(['status' => 'success']);
	}

	// SCANNING AND FACIAL RECOGNITION - PAGE (FINAL CHECK)
	public function time_out($activity_id, $timeslot_id)
	{
		$data['title'] = 'Taking Attendance - Time out';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['activity'] = $this->admin->get_activity_scan($activity_id, $timeslot_id);
		$data['schedule'] = $this->admin->get_schedule_scan($activity_id, $timeslot_id);

		// Fetching students that belong to activity
		$data['students'] = $this->admin->get_students_realtime_time_out($activity_id, $timeslot_id);

		$this->load->view('layout/header', $data);
		$this->load->view('admin/attendance/scanqr_timeout', $data);
		$this->load->view('layout/footer', $data);
	}

	public function impose_fines_timeout()
	{
		// Get raw JSON input and decode it
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, true);

		$activity_id = $input['activity_id'] ?? null;
		$timeslot_id = $input['timeslot_id'] ?? null;

		log_message('debug', "Received activity_id: $activity_id, timeslot_id: $timeslot_id");

		$this->admin->imposeFinesIfAbsentOut($activity_id, $timeslot_id);

		echo json_encode(['status' => 'success']);
	}

	// MAIN
	// public function scanUnified_timein()
	// {
	// 	date_default_timezone_set('Asia/Manila');

	// 	// Get JSON input or fallback to POST
	// 	$raw_input = json_decode(trim(file_get_contents("php://input")), true);

	// 	$student_id = $raw_input['student_id'] ?? $this->input->post('student_id');
	// 	$activity_id = $raw_input['activity_id'] ?? $this->input->post('activity_id');
	// 	$timeslot_id = $raw_input['timeslot_id'] ?? $this->input->post('timeslot_id');

	// 	if (!$student_id || !$activity_id || !$timeslot_id) {
	// 		return $this->output
	// 			->set_content_type('application/json')
	// 			->set_status_header(400)
	// 			->set_output(json_encode([
	// 				'status' => 'error',
	// 				'message' => 'Student ID and Activity ID are required.'
	// 			]));
	// 	}

	// 	// Get current datetime
	// 	$current_datetime = date('Y-m-d H:i:s');

	// 	// Check if activity exists
	// 	$activity = $this->admin->get_activity_schedule($activity_id);
	// 	if (!$activity) {
	// 		return $this->output
	// 			->set_content_type('application/json')
	// 			->set_status_header(404)
	// 			->set_output(json_encode([
	// 				'status' => 'error',
	// 				'message' => 'Activity not found.'
	// 			]));
	// 	}

	// 	// Get student details
	// 	$student = $this->admin->get_student_by_id($student_id);
	// 	$full_name = $student ? $student->first_name . ' ' . $student->last_name : 'Student';
	// 	//$profile = $student ? $student->profile_pic : 'default.jpg';

	// 	// Check if student already has time_in
	// 	$existing_attendance = $this->admin->get_attendance_record_time_in($student_id, $activity_id, $timeslot_id);
	// 	if ($existing_attendance && !empty($existing_attendance->time_in)) {
	// 		return $this->output
	// 			->set_content_type('application/json')
	// 			->set_status_header(200)
	// 			->set_output(json_encode([
	// 				'status' => 'info',
	// 				'message' => "Student ID - $student_id - $full_name has already been recorded."
	// 			]));
	// 	}

	// 	// // Get the timeslot cut-off for fines calculation
	// 	// $timeslot = $this->db->get_where('activity_time_slots', [
	// 	// 	'timeslot_id' => $timeslot_id
	// 	// ])->row();

	// 	// if ($timeslot && strtotime($timeslot->date_cut_in) < strtotime($current_datetime)) {
	// 	// 	// Apply fines logic: Check if student hasn't time-in and is past the cut-off
	// 	// 	$activity = $this->db->get_where('activity', [
	// 	// 		'activity_id' => $activity_id
	// 	// 	])->row();

	// 	// 	$fine_amount = $activity->fines ?? 0;

	// 	// 	// Check if the student is eligible for fines
	// 	// 	$existing_fine = $this->db->get_where('fines', [
	// 	// 		'student_id' => $student_id,
	// 	// 		'activity_id' => $activity_id,
	// 	// 		'timeslot_id' => $timeslot_id
	// 	// 	])->row();

	// 	// 	if (!$existing_fine) {
	// 	// 		// Insert fine record if no fine exists
	// 	// 		$this->db->insert('fines', [
	// 	// 			'student_id' => $student_id,
	// 	// 			'activity_id' => $activity_id,
	// 	// 			'timeslot_id' => $timeslot_id,
	// 	// 			'fines_amount' => $fine_amount,
	// 	// 			//'created_at' => date('Y-m-d H:i:s')
	// 	// 		]);
	// 	// 	} else {
	// 	// 		// Update the fine record if one already exists
	// 	// 		$this->db->where([
	// 	// 			'student_id' => $student_id,
	// 	// 			'activity_id' => $activity_id,
	// 	// 			'timeslot_id' => $timeslot_id
	// 	// 		]);
	// 	// 		$this->db->update('fines', [
	// 	// 			'fines_amount' => $existing_fine->fines_amount + $fine_amount, // Accumulate fine if necessary
	// 	// 		]);
	// 	// 	}
	// 	// }

	// 	// Update attendance
	// 	$update_data = ['time_in' => $current_datetime];
	// 	$updated = $this->admin->update_attendance_time_in($student_id, $activity_id, $timeslot_id, $update_data);

	// 	if ($updated) {
	// 		// Update or Insert fines summary after attendance update
	// 		//$this->update_fines_summary_for_student($student_id);

	// 		return $this->output
	// 			->set_content_type('application/json')
	// 			->set_status_header(200)
	// 			->set_output(json_encode([
	// 				'status' => 'success',
	// 				'message' => "$student_id - $full_name successfully recorded."
	// 			]));
	// 	} else {
	// 		return $this->output
	// 			->set_content_type('application/json')
	// 			->set_status_header(200)
	// 			->set_output(json_encode([
	// 				'status' => 'error',
	// 				'message' => 'No changes made or record not found.'
	// 			]));
	// 	}
	// }

	public function scanUnified_timein()
	{
		date_default_timezone_set('Asia/Manila');

		// Get JSON input or fallback to POST
		$raw_input = json_decode(trim(file_get_contents("php://input")), true);

		$student_id = $raw_input['student_id'] ?? $this->input->post('student_id');
		$activity_id = $raw_input['activity_id'] ?? $this->input->post('activity_id');
		$timeslot_id = $raw_input['timeslot_id'] ?? $this->input->post('timeslot_id');

		if (!$student_id || !$activity_id || !$timeslot_id) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Student ID and Activity ID are required.'
				]));
		}

		// Get current datetime
		$current_datetime = date('Y-m-d H:i:s');

		// Check if activity exists
		$activity = $this->admin->get_activity_schedule($activity_id);
		if (!$activity) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Activity not found.'
				]));
		}

		// Get student details
		$student = $this->admin->get_student_by_id($student_id);
		$full_name = $student ? $student->first_name . ' ' . $student->last_name : 'Student';
		$profile = $student ? $student->profile_pic : 'default.jpg';
		$img_url = base_url('assets/profile/' . $profile);

		// Check if student already has time_in
		$existing_attendance = $this->admin->get_attendance_record_time_in($student_id, $activity_id, $timeslot_id);
		if ($existing_attendance && !empty($existing_attendance->time_in)) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'info',
					'message' => "Student ID - $student_id - $full_name has already been recorded.",
					'profile_pic' => $img_url
				]));
		}


		// // Get the timeslot cut-off for fines calculation
		// $timeslot = $this->db->get_where('activity_time_slots', [
		// 	'timeslot_id' => $timeslot_id
		// ])->row();

		// if ($timeslot && strtotime($timeslot->date_cut_in) < strtotime($current_datetime)) {
		// 	// Apply fines logic: Check if student hasn't time-in and is past the cut-off
		// 	$activity = $this->db->get_where('activity', [
		// 		'activity_id' => $activity_id
		// 	])->row();

		// 	$fine_amount = $activity->fines ?? 0;

		// 	// Check if the student is eligible for fines
		// 	$existing_fine = $this->db->get_where('fines', [
		// 		'student_id' => $student_id,
		// 		'activity_id' => $activity_id,
		// 		'timeslot_id' => $timeslot_id
		// 	])->row();

		// 	if (!$existing_fine) {
		// 		// Insert fine record if no fine exists
		// 		$this->db->insert('fines', [
		// 			'student_id' => $student_id,
		// 			'activity_id' => $activity_id,
		// 			'timeslot_id' => $timeslot_id,
		// 			'fines_amount' => $fine_amount,
		// 			//'created_at' => date('Y-m-d H:i:s')
		// 		]);
		// 	} else {
		// 		// Update the fine record if one already exists
		// 		$this->db->where([
		// 			'student_id' => $student_id,
		// 			'activity_id' => $activity_id,
		// 			'timeslot_id' => $timeslot_id
		// 		]);
		// 		$this->db->update('fines', [
		// 			'fines_amount' => $existing_fine->fines_amount + $fine_amount, // Accumulate fine if necessary
		// 		]);
		// 	}
		// }

		// Update attendance
		$update_data = ['time_in' => $current_datetime];
		$updated = $this->admin->update_attendance_time_in($student_id, $activity_id, $timeslot_id, $update_data);

		if ($updated) {
			// Update or Insert fines summary after attendance update
			//$this->update_fines_summary_for_student($student_id);

			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'success',
					'message' => "$student_id - $full_name successfully recorded.",
					'profile_pic' => $img_url
				]));
		} else {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Student is not listed as a participant in this activity.'
				]));
		}
	}
	// private function update_fines_summary_for_student($student_id)
	// {
	// 	// Get total fines for the student and activity
	// 	$this->db->select('SUM(fines_amount) as total_fines');
	// 	$this->db->from('fines');
	// 	$this->db->where('student_id', $student_id);

	// 	$total_fines = $this->db->get()->row()->total_fines ?? 0;

	// 	// Check if the fines summary already exists
	// 	$existing_summary = $this->db->get_where('fines_summary', [
	// 		'student_id' => $student_id,
	// 	])->row();

	// 	if ($existing_summary) {
	// 		$this->db->where([
	// 			'student_id' => $student_id
	// 		]);
	// 		$this->db->where('organizer', 'Student Parliament');
	// 		$this->db->update('fines_summary', [
	// 			'total_fines'  => $total_fines,
	// 			'fines_status' => $total_fines > 0 ? 'Unpaid' : 'Paid'
	// 		]);
	// 	} else {
	// 		// Insert a new summary record
	// 		$this->db->insert('fines_summary', [
	// 			'student_id' => $student_id,
	// 			'total_fines' => $total_fines,
	// 			'fines_status' => $total_fines > 0 ? 'Unpaid' : 'Paid'
	// 		]);
	// 	}
	// }

	// // This method will be called by the cron job
	// public function auto_fines_missing_time_in()
	// {
	// 	$this->admin->auto_fines_missing_time_in(); // This should do the logic
	// 	echo "Auto fines executed at " . date('Y-m-d H:i:s');
	// }



	//EDIT FINES 

	// public function edit_fines()
	// {
	// 	$this->load->model('Admin_model');

	// 	$student_id = $this->input->post('student_id');
	// 	$reasons    = $this->input->post('reason');
	// 	$amounts    = $this->input->post('amount');
	// 	$changes    = $this->input->post('changes');
	// 	// $event_ids  = $this->input->post('event_id');
	// 	$fines_ids = $this->input->post('fines_id');
	// 	$attendance_ids = $this->input->post('attendance_id');


	// 	if (
	// 		!$student_id ||
	// 		!is_array($reasons) ||
	// 		!is_array($amounts) ||
	// 		!is_array($changes) ||
	// 		!is_array($fines_ids) ||
	// 		!is_array($attendance_ids)
	// 	) {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => 'Missing or invalid input data.'
	// 		]);
	// 		return;
	// 	}

	// 	$errors = [];

	// 	for ($i = 0; $i < count($fines_ids); $i++) {
	// 		$fine_id = $fines_ids[$i];
	// 		$attendance_id = $attendance_ids[$i];

	// 		// Fetch fine to confirm it exists
	// 		$fine = $this->Admin_model->get_fine_by_id($fine_id);

	// 		if (!$fine) {
	// 			$errors[] = "Fine ID $fine_id not found.";
	// 			continue;
	// 		}

	// 		$data = [
	// 			'fines_reason' => $reasons[$i],
	// 			'fines_amount' => $amounts[$i],
	// 			'remarks'      => $changes[$i],
	// 		];

	// 		$updated = $this->Admin_model->update_fine($fine_id, $data);

	// 		if (!$updated) {
	// 			$errors[] = "Failed to update fine ID: $fine_id";
	// 			continue;
	// 		}

	// 		// Optional: update attendance (if you want to do it here)


	// 		$slotInfo = $this->Admin_model->get_slot_info_by_attendance_id($attendance_id);

	// 		$attendanceUpdate = [];

	// 		if ($reasons[$i] === 'Absent') {
	// 			// Clear times if absent
	// 			$attendanceUpdate['time_in'] = null;
	// 			$attendanceUpdate['time_out'] = null;
	// 		} else if ($slotInfo) {
	// 			// Use date_time_in/out from timeslot for present
	// 			$attendanceUpdate['time_in'] = $slotInfo->date_time_in;
	// 			$attendanceUpdate['time_out'] = $slotInfo->date_time_out;
	// 		}

	// 		if (!empty($attendanceUpdate)) {
	// 			$this->Admin_model->update_attendance($attendance_id, $attendanceUpdate);
	// 		}
	// 	}

	// 	if (empty($errors)) {
	// 		echo json_encode(['status' => 'success']);
	// 	} else {
	// 		echo json_encode([
	// 			'status' => 'error',
	// 			'message' => implode("; ", $errors)
	// 		]);
	// 	}
	// }




	public function edit_fines()
	{
		$this->load->model('Admin_model');

		$student_id     = $this->input->post('student_id');
		$reasons        = $this->input->post('reason');
		$amounts        = $this->input->post('amount');
		$changes        = $this->input->post('changes');
		$fines_ids      = $this->input->post('fines_id');
		$attendance_ids = $this->input->post('attendance_id');

		if (
			!$student_id ||
			!is_array($reasons) ||
			!is_array($amounts) ||
			!is_array($changes) ||
			!is_array($fines_ids) ||
			!is_array($attendance_ids)
		) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Missing or invalid input data.'
			]);
			return;
		}

		$errors = [];

		for ($i = 0; $i < count($fines_ids); $i++) {
			$fine_id = $fines_ids[$i];
			$attendance_id = $attendance_ids[$i];

			// 1. Update attendance first
			$slotInfo = $this->Admin_model->get_slot_info_by_attendance_id($attendance_id);
			$attendanceUpdate = [];

			if ($reasons[$i] === 'Absent') {
				$attendanceUpdate['time_in'] = null;
				$attendanceUpdate['time_out'] = null;
			} else if ($slotInfo) {
				$attendanceUpdate['time_in'] = $slotInfo->date_time_in;
				$attendanceUpdate['time_out'] = $slotInfo->date_time_out;
			}

			if (!empty($attendanceUpdate)) {
				$this->Admin_model->update_attendance($attendance_id, $attendanceUpdate);
			}

			// 2. Fetch updated attendance
			$attendance = $this->Admin_model->get_attendance_by_id($attendance_id);
			if (!$attendance) {
				$errors[] = "Attendance ID $attendance_id not found.";
				continue;
			}

			// 3. Determine reason from updated attendance
			$time_in = $attendance->time_in;
			$time_out = $attendance->time_out;

			if (is_null($time_in) && is_null($time_out)) {
				$fine_reason = 'Absent';
			} elseif (is_null($time_in) || is_null($time_out)) {
				$fine_reason = 'Incomplete';
			} else {
				$fine_reason = 'Present';
			}

			// 4. Update fine record
			$data = [
				'fines_reason' => $fine_reason,
				'fines_amount' => $amounts[$i],
				'remarks'      => $changes[$i],
			];

			$updated = $this->Admin_model->update_fine($fine_id, $data);
			if (!$updated) {
				$errors[] = "Failed to update fine ID: $fine_id";
				continue;
			}
		}

		// ✅ Recalculate and update fines_summary.total_fines
		$this->db->select_sum('fines_amount');
		$this->db->where('student_id', $student_id);
		$query = $this->db->get('fines');
		$total = $query->row()->fines_amount ?? 0;

		$this->db->where('student_id', $student_id);
		$this->db->update('fines_summary', ['total_fines' => $total]);

		if (empty($errors)) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => implode("; ", $errors)
			]);
		}
	}

	public function scanUnified_timeout()
	{
		date_default_timezone_set('Asia/Manila');

		// Get JSON input or fallback to POST
		$raw_input = json_decode(trim(file_get_contents("php://input")), true);

		$student_id = $raw_input['student_id'] ?? $this->input->post('student_id');
		$activity_id = $raw_input['activity_id'] ?? $this->input->post('activity_id');
		$timeslot_id = $raw_input['timeslot_id'] ?? $this->input->post('timeslot_id');

		if (!$student_id || !$activity_id || !$timeslot_id) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Student ID and Activity ID are required.'
				]));
		}

		// Get current datetime
		$current_datetime = date('Y-m-d H:i:s');

		// Check if activity exists
		$activity = $this->admin->get_activity_schedule($activity_id);
		if (!$activity) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Activity not found.'
				]));
		}

		// Get student details
		$student = $this->admin->get_student_by_id($student_id);
		$full_name = $student ? $student->first_name . ' ' . $student->last_name : 'Student';
		$profile = $student ? $student->profile_pic : 'default.jpg';
		$img_url = base_url('assets/profile/' . $profile);

		// Check if student already has time_out
		$existing_attendance = $this->admin->get_attendance_record_time_out($student_id, $activity_id, $timeslot_id);
		if ($existing_attendance && !empty($existing_attendance->time_out)) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'info',
					'message' => "Student ID - $student_id - $full_name has already been recorded.",
					'profile_pic' => $img_url
				]));
		}

		// // Get the timeslot cut-off for fines calculation
		// $timeslot = $this->db->get_where('activity_time_slots', [
		// 	'timeslot_id' => $timeslot_id
		// ])->row();

		// if ($timeslot && strtotime($timeslot->date_cut_in) < strtotime($current_datetime)) {
		// 	// Apply fines logic: Check if student hasn't time-in and is past the cut-off
		// 	$activity = $this->db->get_where('activity', [
		// 		'activity_id' => $activity_id
		// 	])->row();

		// 	$fine_amount = $activity->fines ?? 0;

		// 	// Check if the student is eligible for fines
		// 	$existing_fine = $this->db->get_where('fines', [
		// 		'student_id' => $student_id,
		// 		'activity_id' => $activity_id,
		// 		'timeslot_id' => $timeslot_id
		// 	])->row();

		// 	if (!$existing_fine) {
		// 		// Insert fine record if no fine exists
		// 		$this->db->insert('fines', [
		// 			'student_id' => $student_id,
		// 			'activity_id' => $activity_id,
		// 			'timeslot_id' => $timeslot_id,
		// 			'fines_amount' => $fine_amount,
		// 			//'created_at' => date('Y-m-d H:i:s')
		// 		]);
		// 	} else {
		// 		// Update the fine record if one already exists
		// 		$this->db->where([
		// 			'student_id' => $student_id,
		// 			'activity_id' => $activity_id,
		// 			'timeslot_id' => $timeslot_id
		// 		]);
		// 		$this->db->update('fines', [
		// 			'fines_amount' => $existing_fine->fines_amount + $fine_amount, // Accumulate fine if necessary
		// 		]);
		// 	}
		// }

		// Update attendance
		$update_data = ['time_out' => $current_datetime];
		$updated = $this->admin->update_attendance_time_out($student_id, $activity_id, $timeslot_id, $update_data);

		if ($updated) {
			// Update or Insert fines summary after attendance update
			// $this->update_fines_summary_for_student($student_id);

			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'success',
					'message' => "$student_id - $full_name successfully recorded.",
					'profile_pic' => $img_url
				]));
		} else {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Student is not listed as a participant in the selected activity.'
				]));
		}
	}

	// LISTING OF THE ATTENDEES - PAGE (FINAL CHECK)
	public function list_activities_attendance()
	{
		$data['title'] = 'List of Activities';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		$data['activities'] = $this->admin->get_activities_by_sp();


		// Get active semester and academic year || used for read only in filters
		$semester_ay = $this->db->where('is_active', 1)->get('semester_ay')->row();
		if ($semester_ay) {
			$data['active_semester'] = $semester_ay->semester;
			$data['active_academic_year'] = $semester_ay->academic_year;
		} else {
			$data['active_semester'] = 'N/A';
			$data['active_academic_year'] = 'N/A';
		}

		$this->load->view('layout/header', $data);
		$this->load->view('admin/attendance/list-activities-attendance', $data);
		$this->load->view('layout/footer', $data);
	}

	// SHOWING ATTENDANCE LIST - PAGE (FINAL CHECK)
	// public function list_attendees($activity_id)
	// {
	// 	$data['title'] = 'List of Attendees';

	// 	$student_id = $this->session->userdata('student_id');

	// 	// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
	// 	$data['users'] = $this->admin->get_student($student_id);

	// 	$data['activities'] = $this->admin->get_activity_specific($activity_id);
	// 	// $data['students'] = $this->admin->get_all_students_attendance_by_activity($activity_id);
	// 	$data['students'] = $this->admin->get_filtered_attendance_by_activity($activity_id, $status, $department);
	// 	$data['timeslots'] = $this->admin->get_timeslots_by_activity($activity_id);
	// 	$data['departments'] = $this->admin->department_selection();

	// 	$data['activity_id'] = $activity_id; // Add this line 

	// 	$this->load->view('layout/header', $data);
	// 	$this->load->view('admin/attendance/listofattendees', $data);
	// 	$this->load->view('layout/footer', $data);
	// }


	public function list_attendees($activity_id)
	{
		$data['title'] = 'List of Attendees';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE
		$data['users'] = $this->admin->get_student($student_id);

		$data['activities'] = $this->admin->get_activity_specific($activity_id);

		// Get optional filters from GET parameters
		$status = $this->input->get('status');       // e.g. 'Present', 'Absent', etc.
		$department = $this->input->get('department'); // e.g. 'Information Systems'

		// Load filtered students
		$data['students'] = $this->admin->get_filtered_attendance_by_activity($activity_id, $status, $department);

		$data['timeslots'] = $this->admin->get_timeslots_by_activity($activity_id);
		$data['departments'] = $this->admin->department_selection();

		$data['activity_id'] = $activity_id;

		// Optional: retain filters in view (e.g. for dropdown pre-select)
		$data['selected_status'] = $status;
		$data['selected_department'] = $department;

		$this->load->view('layout/header', $data);
		$this->load->view('admin/attendance/listofattendees', $data);
		$this->load->view('layout/footer', $data);
	}


	// For exporting attendance with filters
	public function check_attendance_data()
	{
		$input = json_decode($this->input->raw_input_stream, true);

		$activity_id = $input['activity_id'] ?? null;
		$status = $input['status'] ?? null;
		$department = $input['department'] ?? null;

		$this->load->model('Admin_model');

		// Use your model to check if filtered attendance exists
		// $results = $this->Admin_model->get_all_students_attendance_by_activity($activity_id, $department, $status);


		$dept_code = $input['department'] ?? null; // input sends dept_code

		$results = $this->Admin_model->get_all_students_attendance_by_activity($activity_id, null, $status, $dept_code);


		$hasData = !empty($results);

		echo json_encode([
			'success' => true,
			'hasData' => $hasData
		]);
	}



	public function export_attendance_pdf($activity_id)
	{
		ob_clean();

		$this->load->model('Admin_model');

		// $department = $this->input->get('department') ?? null;
		// $status = $this->input->get('status') ?? null;

		// $students = $this->Admin_model->get_filtered_attendance_by_activity($activity_id, $status, $department);

		$department_code = $this->input->get('department') ?? null;
		$status = $this->input->get('status') ?? null;

		$students = $this->Admin_model->get_filtered_attendance_by_activity($activity_id, $status, null, $department_code);


		$timeslots = $this->Admin_model->get_timeslots_by_activity($activity_id);
		$activity = $this->Admin_model->get_activity_specific($activity_id);

		$semester = $activity['semester'] ?? 'N/A';
		$academic_year = $activity['academic_year'] ?? 'N/A';

		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		if (!$user['role'] || !$user['student_id']) {
			echo json_encode(['success' => false, 'message' => 'Missing session data.']);
			return;
		}

		$headerImage = '';
		$footerImage = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db->select('organization.header, organization.footer')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db->select('header, footer')
				->where('dept_id', $user['dept_id'])
				->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
			}
		}

		$pdf = new PDF('P', 'mm', 'Legal');
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->SetMargins(10, 10, 10);
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, 'Attendance Report - Activity: ' . ($activity ? $activity['activity_title'] : 'N/A'), 0, 1, 'C');

		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 7, "$semester - A.Y. $academic_year", 0, 1, 'C');

		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 8);

		// Setup
		$pageWidth = $pdf->GetPageWidth() - 20; // total usable width
		$outerMargin = 10;
		$internalPadding = 2; // left/right inside cell
		$cellMarginLeft = 10;

		// 1. Build header + rows
		$all_rows = [];
		$header = ['#', 'Student ID', 'Name', 'Department'];
		foreach ($timeslots as $slot) {
			$slotName = strtolower($slot->slot_name);
			if ($slotName === 'morning') $label = 'Morning';
			elseif ($slotName === 'afternoon') $label = 'Afternoon';
			elseif ($slotName === 'evening') $label = 'Evening';
			else $label = ucfirst($slot->slot_name);

			$header[] = "$label In";
			$header[] = "$label Out";
		}
		$header[] = 'Status';
		$all_rows[] = $header;

		foreach ($students as $i => $student) {
			$row = [
				$i + 1,
				$student['student_id'],
				$student['name'],
				$student['dept_code']
			];

			foreach ($timeslots as $slot) {
				$period = strtolower($slot->slot_name) === 'morning' ? 'am' : 'pm';
				$inTime = $student["in_$period"] ?? null;
				$outTime = $student["out_$period"] ?? null;

				$formattedIn = $inTime ? date('h:i A', strtotime($inTime)) : 'No Data';
				$formattedOut = $outTime ? date('h:i A', strtotime($outTime)) : 'No Data';

				$row[] = $formattedIn;
				$row[] = $formattedOut;
			}

			$row[] = $student['status'];
			$all_rows[] = $row;
		}

		// 2. Measure widths with internal padding
		$colWidths = [];
		for ($col = 0; $col < count($all_rows[0]); $col++) {
			$maxWidth = 0;
			foreach ($all_rows as $row) {
				$cellText = isset($row[$col]) ? $row[$col] : '';
				$cellWidth = $pdf->GetStringWidth($cellText);
				if ($cellWidth > $maxWidth) {
					$maxWidth = $cellWidth;
				}
			}
			$colWidths[] = $maxWidth + (2 * $internalPadding);
		}


		// 3. Always scale proportionally to fill full width
		$totalWidth = array_sum($colWidths);
		$scale = $pageWidth / $totalWidth;
		foreach ($colWidths as &$w) {
			$w *= $scale;
		}
		unset($w);


		// 4. Recalculate margin to center the table
		$adjustedWidth = array_sum($colWidths);
		$margin_left = ($pdf->GetPageWidth() - $adjustedWidth) / 2;

		// 5. Print table header
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetX($margin_left);
		foreach ($all_rows[0] as $i => $colName) {
			$pdf->Cell($colWidths[$i], 8, ' ' . $colName . ' ', 1, 0, 'C'); // Add space inside text
		}
		$pdf->Ln();

		// 6. Print table rows
		$pdf->SetFont('Arial', '', 8);
		for ($i = 1; $i < count($all_rows); $i++) {
			$pdf->SetX($margin_left);
			foreach ($all_rows[$i] as $j => $cell) {
				$pdf->Cell($colWidths[$j], 8, ' ' . $cell . ' ', 1, 0, 'L');
			}
			$pdf->Ln();
		}

		$pdf->Ln(10);


		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 6, 'Prepared by:', 0, 1, 'L');
		$pdf->Cell(80, 6, '_________________________', 0, 1, 'L');

		$exporter = $this->db->select("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) AS full_name")
			->where('student_id', $user['student_id'])
			->get('users')->row();
		$pdf->Cell(80, 6, $exporter ? $exporter->full_name : 'N/A', 0, 1, 'L');

		$adviserQuery = $this->db->select("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) AS full_name")
			->from('users')
			->where('role', 'Admin')
			->where('is_admin', 'Yes')
			->where('is_officer_dept', 'No');

		if (!empty($user['dept_id'])) {
			$adviserQuery->where('dept_id', $user['dept_id']);
		} else {
			$adviserQuery->where('dept_id IS NULL', null, false);
		}

		$adviser = $adviserQuery->get()->row();

		$pdf->Ln(8);
		$pdf->Cell(0, 6, 'Approved by:', 0, 1, 'L');
		$pdf->Cell(80, 6, '_________________________', 0, 1, 'L');
		$pdf->Cell(80, 6, $adviser ? $adviser->full_name : 'N/A', 0, 1, 'L');

		$filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $activity['activity_title']) . '_attendance_report.pdf';
		$pdf->Output('I', $filename);
	}

	// public function export_attendance_pdf($activity_id)
	// {
	// 	ob_clean();

	// 	$this->load->model('Admin_model');


	// 	// Get filters from GET parameters (sent via query string)
	// 	$department = $this->input->get('department') ?? null;
	// 	$status = $this->input->get('status') ?? null;

	// 	// Use the same model function for consistency
	// 	$students = $this->Admin_model->get_all_students_attendance_by_activity($activity_id, $department, $status);

	// 	usort($students, function ($a, $b) {
	// 		return strcmp($a['name'], $b['name']);
	// 	});


	// 	$timeslots = $this->Admin_model->get_timeslots_by_activity($activity_id);
	// 	$activity = $this->Admin_model->get_activity_specific($activity_id);

	// 	$semester = $activity['semester'] ?? 'N/A';
	// 	$academic_year = $activity['academic_year'] ?? 'N/A';

	// 	$user = [
	// 		'role'             => $this->session->userdata('role'),
	// 		'student_id'       => $this->session->userdata('student_id'),
	// 		'is_officer'       => $this->session->userdata('is_officer'),
	// 		'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
	// 		'dept_id'          => $this->session->userdata('dept_id'),
	// 	];

	// 	if (!$user['role'] || !$user['student_id']) {
	// 		echo json_encode(['success' => false, 'message' => 'Missing session data.']);
	// 		return;
	// 	}

	// 	$headerImage = '';
	// 	$footerImage = '';

	// 	// Role-based header/footer
	// 	if ($user['role'] === 'Admin') {
	// 		$settings = $this->db->get('student_parliament_settings')->row();
	// 		if ($settings) {
	// 			$headerImage = $settings->header;
	// 			$footerImage = $settings->footer;
	// 		}
	// 	} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
	// 		$org = $this->db->select('organization.header, organization.footer')
	// 			->join('organization', 'student_org.org_id = organization.org_id')
	// 			->where('student_org.student_id', $user['student_id'])
	// 			->get('student_org')->row();
	// 		if ($org) {
	// 			$headerImage = $org->header;
	// 			$footerImage = $org->footer;
	// 		}
	// 	} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
	// 		$dept = $this->db->select('header, footer')
	// 			->where('dept_id', $user['dept_id'])
	// 			->get('department')->row();
	// 		if ($dept) {
	// 			$headerImage = $dept->header;
	// 			$footerImage = $dept->footer;
	// 		}
	// 	}


	// 	// Generate PDF
	// 	$pdf = new PDF('P', 'mm', 'Legal');
	// 	$pdf->headerImage = $headerImage;
	// 	$pdf->footerImage = $footerImage;
	// 	$pdf->SetMargins(10, 10, 10);
	// 	$pdf->AddPage();
	// 	$pdf->SetFont('Arial', 'B', 14);
	// 	$pdf->Cell(0, 10, 'Attendance Report - Activity: ' . ($activity ? $activity['activity_title'] : 'N/A'), 0, 1, 'C');

	// 	// ✅ Add Semester and Academic Year
	// 	$pdf->SetFont('Arial', '', 10);
	// 	$pdf->Cell(0, 7, "$semester - A.Y. $academic_year", 0, 1, 'C');

	// 	$pdf->Ln(5);
	// 	$pdf->SetFont('Arial', '', 8);

	// 	// Build table headers
	// 	$header = ['#', 'Student ID', 'Name', 'Department'];
	// 	foreach ($timeslots as $slot) {
	// 		$period = strtolower($slot->slot_name) === 'morning' ? 'AM' : 'PM';
	// 		$header[] = "$period In";
	// 		$header[] = "$period Out";
	// 	}
	// 	$header[] = 'Status';

	// 	// Measure max widths based on header initially
	// 	$max_widths = [];
	// 	foreach ($header as $col) {
	// 		$max_widths[] = $pdf->GetStringWidth($col);
	// 	}

	// 	// Measure each student row
	// 	$counter = 1;
	// 	foreach ($students as $student) {
	// 		$row = [
	// 			$counter,
	// 			$student['student_id'],
	// 			$student['name'],
	// 			$student['dept_code']
	// 		];

	// 		foreach ($timeslots as $slot) {
	// 			$period = strtolower($slot->slot_name) === 'morning' ? 'am' : 'pm';
	// 			$row[] = $student["in_$period"] ?? 'No Data';
	// 			$row[] = $student["out_$period"] ?? 'No Data';
	// 		}

	// 		$row[] = $student['status'];

	// 		// Compare and store max string widths
	// 		foreach ($row as $i => $cell) {
	// 			$max_widths[$i] = max($max_widths[$i], $pdf->GetStringWidth($cell));
	// 		}

	// 		$counter++;
	// 	}

	// 	// Add padding to each column
	// 	$padding = 6;
	// 	foreach ($max_widths as &$w) {
	// 		$w += $padding;
	// 	}

	// 	// Calculate total width and center position
	// 	$table_width = array_sum($max_widths);
	// 	$page_width = $pdf->GetPageWidth();
	// 	$margin_left = ($page_width - $table_width) / 2;

	// 	// Output headers
	// 	$pdf->SetFont('Arial', 'B', 9);
	// 	$pdf->SetX($margin_left);
	// 	foreach ($header as $i => $col) {
	// 		$pdf->Cell($max_widths[$i], 8, $col, 1, 0, 'C');
	// 	}
	// 	$pdf->Ln();

	// 	// Output data rows
	// 	$pdf->SetFont('Arial', '', 8);
	// 	$counter = 1;
	// 	foreach ($students as $student) {
	// 		$row = [
	// 			$counter,
	// 			$student['student_id'],
	// 			$student['name'],
	// 			$student['dept_code']
	// 		];

	// 		foreach ($timeslots as $slot) {
	// 			$period = strtolower($slot->slot_name) === 'morning' ? 'am' : 'pm';
	// 			$row[] = $student["in_$period"] ?? 'No Data';
	// 			$row[] = $student["out_$period"] ?? 'No Data';
	// 		}

	// 		$row[] = $student['status'];

	// 		$pdf->SetX($margin_left);
	// 		foreach ($row as $i => $cell) {
	// 			$pdf->Cell($max_widths[$i], 8, $cell, 1, 0, 'L');
	// 		}
	// 		$pdf->Ln();
	// 		$counter++;
	// 	}

	// 	// Output the file
	// 	$filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $activity['activity_title']) . '_attendance_report.pdf';
	// 	$pdf->Output('I', $filename);
	// }






	public function view_attendance_reports($activity_id)
	{

		$data['title'] = 'Attendance Reports';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);


		$this->load->model('Admin_model');



		$data['activity_id'] = $activity_id;
		$data['by_department'] = $this->Admin_model->get_departments_with_attendees($activity_id);
		$data['total_attendees'] = $this->Admin_model->get_total_attendees($activity_id);
		$data['status_comparison'] = $this->Admin_model->get_attendance_status_counts($activity_id);



		$this->load->view('layout/header', $data);
		$this->load->view('admin/attendance/attendance-reports', $data);
		$this->load->view('layout/footer', $data);
	}

	//CONNECTING ATTENDANCE AND FINES
	public function update_fines() {}

	// LIST OF FINES - PAGE
	public function list_fines()
	{
		$data['title'] = 'List of Fines';

		$student_id = $this->session->userdata('student_id');

		// 🔽 Get filters from GET request
		$department = $this->input->get('department');
		$year_level = $this->input->get('year_level');
		$status = $this->input->get('status');

		$data['users'] = $this->admin->get_student($student_id);
		$data['departments'] = $this->admin->get_department();

		// 🔽 Pass filters to flash_fines()
		$data['fines'] = $this->admin->flash_fines($department, $year_level, $status);

		$this->load->view('layout/header', $data);
		$this->load->view('admin/fines/listoffines', $data);
		$this->load->view('layout/footer', $data);
	}


	//FINES PAYMENTS CONFIRMATION
	public function confirm_payment()
	{
		$this->load->model('Notification_model');

		$student_id = $this->input->post('student_id');
		$activity_id = $this->input->post('activity_id');
		$total_fines = $this->input->post('total_fines');
		$mode_of_payment = $this->input->post('mode_of_payment');
		$reference_number = trim($this->input->post('reference_number')); // Admin input (cleaned)

		$academic_year = $this->input->post('academic_year'); // get from POST form
		$semester = $this->input->post('semester');           // get from POST form

		$admin_student_id = $this->session->userdata('student_id');


		$organizer = 'Student Parliament';
		$record = $this->admin->get_fine_summary($student_id, $organizer, $academic_year, $semester);
		// ✅ retrieve student's fine summary //ADDED FOR AY AND SEM
		$admin_student_id = $this->session->userdata('student_id'); // Logged-in admin's student_id

		if (!$record) {
			echo json_encode(['status' => 'error', 'message' => 'No fines summary found.']);
			return;
		}

		$student_reference = trim($record->reference_number_students); // 🧼 Clean student's stored reference

		// CASE: REFERENCE NUMBER MISMATCH - REJECTED
		if (strcasecmp($reference_number, $student_reference) !== 0) {
			$this->admin->update_fines_summary($student_id, [
				'reference_number_admin' => $reference_number,
				'fines_status' => 'Pending',
				'mode_payment' => $mode_of_payment,
				'last_updated' => date('Y-m-d H:i:s')
			], $academic_year, $semester, $organizer); // ADDED: pass academic_year and semester to update method

			// ❌ Send rejection notification
			$this->Notification_model->add_notification(
				$student_id,                        // recipient_student_id
				$admin_student_id,                  // sender_student_id (admin)
				'fine_payment_rejected',            // type
				$record->summary_id,                // reference_id
				'Your fine payment could not be confirmed. Please double-check your reference number.'
			);

			echo json_encode(['status' => 'warning', 'message' => 'Reference mismatch. Payment on hold.']);
			return;
		}

		// CASE: REFERENCE MATCH - APPROVED
		$updated = $this->admin->update_fines_summary_receipt($student_id, [
			'reference_number_admin' => $reference_number,
			'fines_status' => 'Paid',
			'mode_payment' => $mode_of_payment,
			'approved_by' => $admin_student_id, // ✅ Add this line
			'last_updated' => date('Y-m-d H:i:s')
		], $academic_year, $semester, $organizer); // ADDED: pass academic_year and semester to update method

		if ($updated) {
			$summary_id = $record->summary_id;
			$this->generate_and_store_fine_receipt($summary_id); // 🎟️ Generate receipt

			// ✅ Send approval notification
			$this->Notification_model->add_notification(
				$student_id,                      // recipient_student_id
				$admin_student_id,                // sender_student_id (admin)
				'fine_payment_approved',          // type
				$summary_id,                      // reference_id
				'has approved your fine payment and marked it as paid.',
				null,
				base_url('student/summary-fines/')
			);

			echo json_encode(['status' => 'success', 'message' => 'Payment verified and receipt generated.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
		}
	}



	public function generate_and_store_fine_receipt($summary_id)
	{
		$this->load->model('Student_model');
		require_once(APPPATH . 'third_party/fpdf.php');

		// Get student_id from fines_summary
		$summary = $this->db->where('summary_id', $summary_id)->get('fines_summary')->row();
		if (!$summary) return;
		$student_id = $summary->student_id;
		$organizer = $summary->organizer;
		$academic_year = $summary->academic_year;
		$semester = $summary->semester;

		// Get fines data using the new filtered method
		$fines_data = $this->Student_model->get_fines_for_receipt($student_id, $organizer, $academic_year, $semester);
		if (empty($fines_data)) return;


		$receipt_data = $fines_data[0];

		// Fetch approver name
		$approver_name = 'N/A';
		if (!empty($summary->approved_by)) {
			$approver = $this->db->select('first_name, middle_name, last_name')
				->where('student_id', $summary->approved_by)
				->get('users')
				->row();
			if ($approver) {
				$approver_name = strtoupper(trim($approver->first_name . ' ' . $approver->middle_name . ' ' . $approver->last_name));
			}
		}

		$filename = 'fine_receipt_' . $summary_id . '.pdf';
		$folder_path = FCPATH . 'uploads/fine_receipts/';
		$filepath = $folder_path . $filename;

		if (!is_dir($folder_path)) mkdir($folder_path, 0777, true);

		$verification_code = strtoupper(substr(md5($summary_id . time()), 0, 8));

		// Header/footer/logo logic
		$user = [
			'role' => $this->session->userdata('role'),
			'student_id' => $this->session->userdata('student_id'),
			'is_officer' => $this->session->userdata('is_officer'),
			'is_officer_dept' => $this->session->userdata('is_officer_dept'),
			'dept_id' => $this->session->userdata('dept_id'),
		];

		$headerImage = $footerImage = $watermarkPath = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
				if (!empty($settings->logo)) $watermarkPath = $settings->logo;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db->select('organization.header, organization.footer, organization.logo')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
				if (!empty($org->logo)) $watermarkPath = $org->logo;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db->select('header, footer, logo')->where('dept_id', $user['dept_id'])->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
				if (!empty($dept->logo)) $watermarkPath = $dept->logo;
			}
		}

		$pdf = new PDF();
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->watermarkPath = $watermarkPath;

		$pdf->AddPage();
		$pdf->SetAutoPageBreak(true, 10);

		// Receipt Heading
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, 'FINE PAYMENT RECEIPT', 0, 1, 'C');
		$pdf->Ln(5);

		// Define tighter layout sizes
		$label_w_left  = 28;   // Left-side label width
		$value_w_left  = 37;   // Left-side value width
		$label_w_right = 28;   // Right-side label width (same as left for pantay)
		$value_w_right = 37;   // Right-side value width
		$gap_w         = 30;    // Space between left/right pairs

		// Row 1: Receipt No and Semester
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_left, 8, 'Receipt No:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_left, 8, strtoupper($summary_id), 0, 0);

		$pdf->Cell($gap_w); // spacer between two sets

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_right, 8, 'Semester:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_right, 8, $semester, 0, 1);

		// Row 2: Date and Academic Year (pantay with above)
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_left, 8, 'Date:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_left, 8, date('F j, Y'), 0, 0);

		$pdf->Cell($gap_w);

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell($label_w_right, 8, 'Academic Year:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell($value_w_right, 8, $academic_year, 0, 1);

		$pdf->Ln(5);




		// Student info
		// Student ID
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(35, 8, 'Student ID:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $receipt_data['student_id'], 0, 1);

		// Student Name
		$student = $this->db->select('first_name, last_name')
			->from('users')
			->where('student_id', $receipt_data['student_id'])
			->get()->row();
		$student_name = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown';

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(35, 8, 'Student Name:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $student_name, 0, 1);

		// Source of Fines
		$pdf->Ln(3);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(35, 8, 'Source of Fines:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $receipt_data['organizer'], 0, 1);


		// Table Header
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(150, 8, 'Description (Activity & Slot)', 1, 0, 'C');
		$pdf->Cell(40, 8, 'Amount (PHP)', 1, 1, 'C');


		$pdf->SetFont('Arial', '', 10);

		// Group by activity_id
		$activity_grouped = [];
		foreach ($fines_data as $f) {
			$aid = $f['activity_id'];
			if (!isset($activity_grouped[$aid])) $activity_grouped[$aid] = [];
			$activity_grouped[$aid][] = $f;
		}

		foreach ($activity_grouped as $activity_id => $entries) {
			// Detect slots
			$used_slots = [];
			foreach ($entries as $f) {
				$used_slots[$f['slot_name']] = true;
			}

			// Pre-fill breakdown
			$slot_fines = [];
			$slot_times = [];

			foreach ($used_slots as $slot => $_) {
				$slot_fines[$slot . '_in'] = 0.00;
				$slot_fines[$slot . '_out'] = 0.00;
			}

			foreach ($entries as $f) {
				$slot = $f['slot_name'];

				if (!empty($f['time_in'])) $slot_times[$slot . '_in'] = $f['time_in'];
				if (!empty($f['time_out'])) $slot_times[$slot . '_out'] = $f['time_out'];

				if (empty($f['time_in'])) $slot_fines[$slot . '_in'] += $f['activity_fine_amount'];
				if (empty($f['time_out'])) $slot_fines[$slot . '_out'] += $f['activity_fine_amount'];
			}

			$slot_order = ['Morning_in', 'Morning_out', 'Afternoon_in', 'Afternoon_out', 'Evening_in', 'Evening_out'];

			foreach ($slot_order as $label) {
				if (isset($slot_fines[$label])) {
					$amount = $slot_fines[$label];

					$time_label = isset($slot_times[$label]) ? ' (' . date('h:i A', strtotime($slot_times[$label])) . ')' : ' (No record)';
					$desc = $label . $time_label . ' - ' . $entries[0]['activity_title'];

					$pdf->Cell(150, 8, $desc, 1, 0); // wider description column
					$pdf->Cell(40, 8, 'Php ' . number_format($amount, 2), 1, 1, 'R');
				}
			}
		}

		// Total
		$total_amount = $receipt_data['total_fines'] ?? 0;
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(150, 8, 'Total Amount:', 1, 0, 'R');
		$pdf->Cell(40, 8, 'Php ' . number_format($total_amount, 2), 1, 1, 'C');



		// Payment & approver
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(50, 8, 'Payment Type:', 0, 0);
		$pdf->Cell(0, 8, $summary->mode_payment, 0, 1);

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Approved By:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $approver_name, 0, 1);

		// Verification
		$pdf->Ln(15);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 8, 'Verification Code:', 0, 1, 'C');
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, $verification_code, 0, 1, 'C');

		$pdf->Ln(10);
		$pdf->SetFont('Arial', 'I', 8);
		$pdf->Cell(0, 8, 'Use this code on the receipt verification page to validate.', 0, 1, 'C');

		$pdf->Output($filepath, 'F');

		// Update DB
		$this->db->where('summary_id', $summary_id)->update('fines_summary', [
			'generated_receipt' => $filename,
			'verification_code' => $verification_code,
			'last_updated' => date('Y-m-d H:i:s')
		]);
	}




	//Fines
	public function record_cash_payment()
	{
		$summary_id = $this->input->post('summary_id');
		$reference_admin = $this->input->post('reference_number_admin');
		$receipt = null;

		// Optional: handle file upload
		if (!empty($_FILES['receipt']['name'])) {
			$config['upload_path'] = './uploads/receipts/';
			$config['allowed_types'] = 'jpg|jpeg|png|pdf';
			$config['file_name'] = uniqid('receipt_');
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('receipt')) {
				$receipt = $this->upload->data('file_name');
			} else {
				$this->session->set_flashdata('error', $this->upload->display_errors());
				redirect('admin/list-fines');
				return;
			}
		}

		// Fetch the summary record using summary_id
		$summary = $this->db->where('summary_id', $summary_id)
			->get('fines_summary')
			->row();

		if (!$summary || $summary->fines_status === 'Paid') {
			$this->session->set_flashdata('error', 'No unpaid fines found or already paid.');
			redirect('admin/list-fines');
			return;
		}

		// Update payment
		$data = [
			'fines_status' => 'Paid',
			'mode_payment' => 'Cash',
			'reference_number_admin' => $reference_admin,
			'receipt' => $receipt,
			'last_updated' => date('Y-m-d H:i:s'),
			'approved_by' => $this->session->userdata('student_id') // Admin/officer ID
		];

		$this->db->where('summary_id', $summary_id);
		$this->db->update('fines_summary', $data);

		// Generate receipt PDF
		$this->generate_and_store_fine_receipt($summary_id);

		$this->session->set_flashdata('swal_success', 'Cash payment recorded and receipt generated successfully.');
		redirect('admin/list-fines');
	}



	//To display student total fines
	public function get_student_total_fines()
	{
		$input = json_decode(file_get_contents('php://input'), true);
		$student_id = $input['student_id'];

		// Get organizer info from session
		$dept_id = $this->session->userdata('dept_id');
		$org_id = $this->session->userdata('org_id');
		$is_dept = $this->session->userdata('is_officer_dept') === 'Yes';
		$organizer_name = $is_dept ? $this->session->userdata('dept_name') : $this->session->userdata('org_name');

		// Start building query
		$this->db->select('
			fines_summary.*,
			users.first_name,
			users.middle_name,
			users.last_name,
			users.profile_pic,
			users.year_level,
			department.dept_name
		');

		$this->db->from('fines_summary');
		$this->db->join('fines', 'fines_summary.student_id = fines.student_id', 'left');
		$this->db->join('activity', 'fines.activity_id = activity.activity_id', 'left');
		$this->db->join('users', 'users.student_id = fines_summary.student_id', 'left'); // ✅ Join users
		$this->db->join('department', 'department.dept_id = users.dept_id', 'left');


		// Filter by student
		$this->db->where('fines_summary.student_id', $student_id);

		// Filter by organizer
		if ($organizer_name) {
			$this->db->where('activity.organizer', $organizer_name);
		}

		// Optional: enforce dept/org scope if necessary
		if ($is_dept && $dept_id) {
			$this->db->where('users.dept_id', $dept_id); // ✅ correct column
		} elseif (!$is_dept && $org_id) {
			$this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');
			$this->db->where('student_org.org_id', $org_id);
			$this->db->where('student_org.is_officer', 'Yes');
		}

		// Prevent duplicates
		$this->db->group_by('fines_summary.summary_id');

		$summary = $this->db->get()->row();

		if (!$summary) {
			echo json_encode([
				'success' => false,
				'message' => 'Student not found in fines records for this organizer.'
			]);
			return;
		}

		if ($summary->fines_status === 'Paid') {
			echo json_encode([
				'success' => false,
				'message' => 'This student has already paid the fines.'
			]);
			return;
		}

		echo json_encode([
			'success' => true,
			'summary_id' => $summary->summary_id,
			'total_fines' => $summary->total_fines,
			'first_name' => $summary->first_name,
			'middle_name' => $summary->middle_name,
			'last_name' => $summary->last_name,
			'profile_pic' => $summary->profile_pic,
			'dept_name' => $summary->dept_name,
			'year_level' => $summary->year_level   // <-- added year_level here
		]);
	}







	public function update_status()
	{
		$input = json_decode(file_get_contents("php://input"), true);

		// Ensure required parameters exist
		if (!isset($input['student_id']) || !isset($input['activity_id']) || !isset($input['is_paid'])) {
			echo json_encode(["success" => false, "message" => "Invalid request"]);
			return;
		}

		$student_id = $input['student_id'];
		$activity_id = $input['activity_id'];
		$new_status = ($input['is_paid'] === "Yes") ? "Yes" : "No";

		// Update only the specific record with both student_id & activity_id
		if ($this->admin->updateFineStatus($student_id, $activity_id, $new_status)) {
			echo json_encode(["success" => true, "message" => "Fine status updated"]);
		} else {
			echo json_encode(["success" => false, "message" => "Update failed"]);
		}
	}



	//FOR EXPORT FINES WITH FILTER
	public function check_fines_data()
	{
		$department = $this->input->post('department') ?: null;
		$year_level = $this->input->post('year_level') ?: null;
		$status = $this->input->post('status') ?: null;

		$this->load->model('Admin_model');
		$data = $this->Admin_model->get_fines_by_filter($department, $year_level, $status);

		echo json_encode(['hasData' => !empty($data)]);
	}

	public function export_fines_pdf()
	{
		$this->load->library('fpdf');
		$this->load->model('Admin_model');

		$department = $this->input->get('department');
		$year_level = $this->input->get('year_level');

		$status = $this->input->get('status'); // <-- ADD THIS LINE


		$fines_data = $this->Admin_model->get_fines_by_filter($department, $year_level, $status);


		$semester = 'N/A';
		$academic_year = 'N/A';

		if (!empty($fines_data)) {
			$semester = $fines_data[0]->semester ?? 'N/A';
			$academic_year = $fines_data[0]->academic_year ?? 'N/A';
		}


		if (empty($fines_data)) {
			echo "No fine data available to export.";
			return;
		}

		$user = [
			'role' => $this->session->userdata('role'),
			'student_id' => $this->session->userdata('student_id'),
			'is_officer' => $this->session->userdata('is_officer'),
			'is_officer_dept' => $this->session->userdata('is_officer_dept'),
			'dept_id' => $this->session->userdata('dept_id'),
		];

		if (!$user['role'] || !$user['student_id']) {
			echo json_encode(['success' => false, 'message' => 'Missing session data.']);
			return;
		}

		$headerImage = '';
		$footerImage = '';

		if ($user['role'] === 'Admin') {
			$settings = $this->db->get('student_parliament_settings')->row();
			if ($settings) {
				$headerImage = $settings->header;
				$footerImage = $settings->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer'] === 'Yes') {
			$org = $this->db
				->select('organization.header, organization.footer')
				->join('organization', 'student_org.org_id = organization.org_id')
				->where('student_org.student_id', $user['student_id'])
				->get('student_org')->row();
			if ($org) {
				$headerImage = $org->header;
				$footerImage = $org->footer;
			}
		} elseif ($user['role'] === 'Officer' && $user['is_officer_dept'] === 'Yes') {
			$dept = $this->db
				->select('header, footer')
				->where('dept_id', $user['dept_id'])
				->get('department')->row();
			if ($dept) {
				$headerImage = $dept->header;
				$footerImage = $dept->footer;
			}
		}

		$pdf = new PDF();
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->AddPage('L', 'Letter');
		// 🔹 Make the title BIGGER (e.g., font size 14)
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, 'Student Parliament Fines Report', 0, 1, 'C');
		// 🔹 Make the semester & A.Y. line smaller (e.g., font size 9)
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(0, 6, "$semester - A.Y. $academic_year", 0, 1, 'C');
		$pdf->Ln(3); // Slightly smaller spacing

		$activities = [];
		foreach ($fines_data as $fine) {
			$title = trim($fine->activity_title);
			if (!in_array($title, $activities)) {
				$activities[] = $title;
			}
		}

		$padding = 2;
		$pdf->SetFont('Arial', '', 6);
		$student_id_width = $pdf->GetStringWidth('Student ID') + $padding;
		$name_width = $pdf->GetStringWidth('Name') + $padding;
		$dept_name_width = $pdf->GetStringWidth('Department') + $padding;
		$activity_widths = [];

		foreach ($fines_data as $fine) {
			$student_id_width = max($student_id_width, $pdf->GetStringWidth($fine->student_id) + $padding);
			$name_width = max($name_width, $pdf->GetStringWidth($fine->first_name . ' ' . $fine->last_name) + $padding);
			$dept_name_width = max($dept_name_width, $pdf->GetStringWidth($fine->dept_name) + $padding);
			$title = trim($fine->activity_title);
			if (!isset($activity_widths[$title])) {
				$activity_widths[$title] = $pdf->GetStringWidth($title) + $padding;
			}
		}

		// Original fixed widths
		$fixed_total_fines_width = 15;
		$fixed_status_width = 15;

		$unscaled_total_width = $student_id_width + $name_width + $dept_name_width + array_sum($activity_widths) + $fixed_total_fines_width + $fixed_status_width;
		$page_width = $pdf->GetPageWidth() - $pdf->getLeftMargin() - $pdf->getRightMargin();

		// Scale everything
		$scale = $page_width / $unscaled_total_width;
		$student_id_width *= $scale;
		$name_width *= $scale;
		$dept_name_width *= $scale;
		foreach ($activities as $act) {
			$activity_widths[$act] *= $scale;
		}
		$total_fines_width = $fixed_total_fines_width * $scale;
		$status_width = $fixed_status_width * $scale;
		$total_width = $page_width; // now fully scaled to match the page

		usort($fines_data, function ($a, $b) {
			return [$a->dept_name, $a->year_level, $a->student_id] <=> [$b->dept_name, $b->year_level, $b->student_id];
		});

		$current_dept = null;
		$current_year = null;
		$current_student_id = null;
		$student_fines = [];
		$prev_fine = null;

		foreach ($fines_data as $fine) {
			$title = trim($fine->activity_title);
			$full_name = $fine->first_name . ' ' . $fine->last_name;

			if ($fine->dept_name !== $current_dept) {
				$current_dept = $fine->dept_name;
				$current_year = null;
				$pdf->Ln(5);
				$pdf->SetFillColor(173, 216, 230);
				$pdf->SetFont('Arial', 'B', 7);
				$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
				$pdf->MultiCell($total_width, 8, "Department: " . $current_dept, 1, 'L', true);
				$pdf->SetFont('Arial', 'B', 6);
				$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
				$pdf->Cell($student_id_width, 8, 'Student ID', 1, 0, 'C');
				$pdf->Cell($name_width, 8, 'Name', 1, 0, 'C');
				$pdf->Cell($dept_name_width, 8, 'Department', 1, 0, 'C');
				foreach ($activities as $act) {
					$pdf->Cell($activity_widths[$act], 8, $act, 1, 0, 'C');
				}
				$pdf->Cell($total_fines_width, 8, 'Total Fines', 1, 0, 'C');
				$pdf->Cell($status_width, 8, 'Status', 1, 1, 'C');
			}

			if ($fine->year_level !== $current_year) {
				$current_year = $fine->year_level;
				$pdf->SetFont('Arial', 'B', 6);
				$pdf->SetFillColor(224, 235, 255);
				$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
				$pdf->MultiCell($total_width, 6, "Year Level: " . $current_year, 1, 'L', true);
			}

			if ($fine->student_id !== $current_student_id) {
				if ($current_student_id !== null) {
					$pdf->SetFont('Arial', '', 6);
					$pdf->SetFillColor(255, 255, 255);
					$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
					$pdf->Cell($student_id_width, 8, $prev_fine->student_id, 1, 0, 'L');
					$pdf->Cell($name_width, 8, $prev_fine->last_name . ' ' . $prev_fine->first_name, 1, 0, 'L');
					$pdf->Cell($dept_name_width, 8, $prev_fine->dept_name, 1, 0, 'L');
					foreach ($activities as $act) {
						$amount = isset($student_fines[$act]) ? 'PHP ' . number_format($student_fines[$act], 2) : 'PHP 0.00';
						$pdf->Cell($activity_widths[$act], 8, $amount, 1, 0, 'R');
					}
					$total_fines = array_sum($student_fines);
					// $status = $total_fines > 0 ? 'Unpaid' : 'Paid';
					$status = isset($prev_fine->fines_status) ? $prev_fine->fines_status : 'Unknown';
					$pdf->Cell($total_fines_width, 8, 'PHP ' . number_format($total_fines, 2), 1, 0, 'R');
					$pdf->Cell($status_width, 8, $status, 1, 1, 'C');
				}
				$current_student_id = $fine->student_id;
				$student_fines = [];
				$prev_fine = $fine;
			}

			$student_fines[$title] = isset($student_fines[$title])
				? $student_fines[$title] + $fine->fines_amount
				: $fine->fines_amount;
		}

		if ($current_student_id !== null) {
			$pdf->SetFont('Arial', '', 6);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
			$pdf->Cell($student_id_width, 8, $prev_fine->student_id, 1, 0, 'L');
			$pdf->Cell($name_width, 8, $prev_fine->first_name . ' ' . $prev_fine->last_name, 1, 0, 'L');
			$pdf->Cell($dept_name_width, 8, $prev_fine->dept_name, 1, 0, 'L');
			foreach ($activities as $act) {
				$amount = isset($student_fines[$act]) ? 'PHP ' . number_format($student_fines[$act], 2) : 'PHP 0.00';
				$pdf->Cell($activity_widths[$act], 8, $amount, 1, 0, 'R');
			}
			$total_fines = array_sum($student_fines);
			// $status = $total_fines > 0 ? 'Unpaid' : 'Paid';
			$status = isset($prev_fine->fines_status) ? $prev_fine->fines_status : 'Unknown';
			$pdf->Cell($total_fines_width, 8, 'PHP ' . number_format($total_fines, 2), 1, 0, 'R');
			$pdf->Cell($status_width, 8, $status, 1, 1, 'C');
		}


		// Add signature lines
		$pdf->Ln(10);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Cell(0, 6, 'Prepared by:', 0, 1, 'L');
		$pdf->Cell(80, 6, '_________________________', 0, 1, 'L');

		$exporter = $this->db
			->select("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) AS full_name")
			->where('student_id', $user['student_id'])
			->get('users')->row();
		$pdf->Cell(80, 6, $exporter ? $exporter->full_name : 'N/A', 0, 1, 'L');

		$adviser_query = $this->db
			->select("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) AS full_name")
			->where('role', 'Admin')
			->where('is_admin', 'Yes')
			->where('is_officer_dept', 'No');

		if ($user['dept_id']) {
			$adviser_query->where('dept_id', $user['dept_id']);
		} else {
			$adviser_query->where('dept_id IS NULL', null, false);
		}

		$adviser = $adviser_query->get('users')->row();

		$pdf->Ln(8);
		$pdf->Cell(0, 6, 'Approved by:', 0, 1, 'L');
		$pdf->Cell(80, 6, '_________________________', 0, 1, 'L');
		$pdf->Cell(80, 6, $adviser ? $adviser->full_name : 'N/A', 0, 1, 'L');

		$pdf->Output('I', 'StudentParliament_fines_report.pdf');
	}








	// OTHER PAGES

	//PROFILE SETTINGS - PAGE (FINAL CHECK)
	public function profile_settings()
	{
		$data['title'] = 'Profile Settings';

		$student_id = $this->session->userdata('student_id');

		// Get user and their organizations
		$student_details = $this->admin->get_user_profile();

		if ($student_details) {
			$data['student_details'] = $student_details;
			$data['organizations'] = $student_details->organizations ?? [];
		} else {
			$data['student_details'] = null;
			$data['organizations'] = [];
		}

		$data['users'] = $this->admin->get_student($student_id);

		$this->load->view('layout/header', $data);
		$this->load->view('admin/profile-settings', $data);
		$this->load->view('layout/footer', $data);
	}

	public function update_profile_pic()
	{
		$student_id = $this->session->userdata('student_id');

		if (!$student_id) {
			echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
			return;
		}

		// Fetch current profile picture
		$current_pic = $this->admin->get_profile_pic($student_id);

		if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
			$config['upload_path']   = './assets/profile/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['max_size']      = 10240; // 10MB
			$config['file_name']     = 'profile_' . $student_id . '_' . time(); // Unique filename

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('profile_pic')) {
				echo json_encode([
					'status' => 'error',
					'message' => strip_tags($this->upload->display_errors())
				]);
				return;
			}

			$file_data = $this->upload->data();
			$file_name = $file_data['file_name'];

			// Update database with new profile picture
			$update_data = ['profile_pic' => $file_name];
			$this->admin->update_profile_pic($student_id, $update_data);

			// Delete the old profile picture if it's not the default
			if ($current_pic && file_exists('./assets/profile/' . $current_pic) && $current_pic !== 'default.png') {
				unlink('./assets/profile/' . $current_pic); // Remove old image
			}

			echo json_encode([
				'status' => 'success',
				'message' => 'Profile picture updated successfully',
				'file_name' => $file_name
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'No file selected or file upload error'
			]);
		}
	}

	public function update_profile()
	{

		$student_id = $this->session->userdata('student_id');

		$data = [
			'first_name' => $this->input->post('first_name'),
			'middle_name' => $this->input->post('middle_name'),
			'last_name'  => $this->input->post('last_name'),
			'email'      => $this->input->post('email'),
			'year_level' => $this->input->post('year_level'),
			'sex' => $this->input->post('sex'),

		];

		$updated = $this->admin->update_student($student_id, $data);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No changes made or something went wrong.']);
		}
	}

	public function update_password()
	{
		$student_id = $this->session->userdata('student_id');

		// Decode the raw JSON body
		$data = json_decode(file_get_contents("php://input"), true);

		$old_password = $data['old_password'] ?? null;
		$new_password = $data['new_password'] ?? null;
		$confirm_password = $data['confirm_password'] ?? null;

		if (!$student_id) {
			echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
			return;
		}

		if ($new_password !== $confirm_password) {
			echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
			return;
		}

		$student = $this->admin->get_by_id($student_id);

		if (!$student || !password_verify($old_password, $student->password)) {
			echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
			return;
		}

		$hashed = password_hash($new_password, PASSWORD_DEFAULT);
		$this->admin->update_password($student_id, $hashed);

		echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
	}

	public function get_qr_code_by_student()
	{
		// Get student_id from session
		if ($this->session->has_userdata('student_id')) {
			$student_id = $this->session->userdata('student_id');

			// Retrieve QR code from the database (ensure you have the correct query in place)
			$qr_code = $this->admin->get_qr_code($student_id);

			if ($qr_code) {
				// Return the QR code as JSON (base64 string)
				echo json_encode(['status' => 'success', 'qr_code' => $qr_code]);
			} else {
				// No QR code found for the student
				echo json_encode(['status' => 'error', 'message' => 'QR Code not found for this student.']);
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No student found in session.']);
		}
	}

	// MANAGE OFFICERS AND PRIVILEGE - PAGE (FINAL CHECK)
	public function manage_officers()
	{
		$data['title'] = 'Manage Officers and Privilege';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// FETCHING ALL THE DEPARTMENT
		$data['department'] = $this->admin->get_department();

		// FETCHING ALL THE DEPARTMENT
		$data['organization'] = $this->admin->get_organization();

		$this->load->view('layout/header', $data);
		$this->load->view('admin/manage-officer', $data);
		$this->load->view('layout/footer', $data);
	}

	public function list_officers_dept($dept_id)
	{
		$data['title'] = 'List of Officer Department';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// FETCHING ALL THE DEPARTMENT
		$data['department'] = $this->admin->get_department();
		$data['dept_id'] = $dept_id;

		$data['privileges'] = $this->admin->manage_privilege_dept();


		$this->load->view('layout/header', $data);
		$this->load->view('admin/manage-department', $data);
		$this->load->view('layout/footer', $data);
	}

	public function update_privileges_dept()
	{
		if (!$this->input->is_ajax_request()) {
			show_error('No direct script access allowed');
		}

		$privileges_input = $this->input->post('privileges');

		if (empty($privileges_input)) {
			echo json_encode(['success' => false, 'error' => 'No data received']);
			return;
		}

		$sanitized_data = [];
		$change_logs = []; // Store old values before update

		foreach ($privileges_input as $privilege_id => $values) {
			// ✅ Fetch old values before the update
			$old = $this->admin->get_privilege_by_id($privilege_id);

			$sanitized = [
				'privilege_id' => $privilege_id,
				'manage_fines' => isset($values['manage_fines']) ? 'Yes' : 'No',
				'manage_evaluation' => isset($values['manage_evaluation']) ? 'Yes' : 'No',
				'manage_applications' => isset($values['manage_applications']) ? 'Yes' : 'No',
				'able_scan' => isset($values['able_scan']) ? 'Yes' : 'No',
				'able_create_activity' => isset($values['able_create_activity']) ? 'Yes' : 'No'
			];

			$sanitized_data[] = $sanitized;

			if ($old) {
				$change_logs[$privilege_id] = $old;
			}
		}

		// ✅ Now update in bulk
		$update = $this->admin->update_privileges_dept($sanitized_data);

		if ($update) {
			foreach ($sanitized_data as $row) {
				$privilege_id = $row['privilege_id'];
				$old = $change_logs[$privilege_id] ?? null;

				$changes = [];

				if ($old) {
					foreach (['manage_fines', 'manage_evaluation', 'manage_applications', 'able_scan', 'able_create_activity'] as $field) {
						if ($old->$field !== $row[$field]) {
							$pretty = ucwords(str_replace('_', ' ', $field));
							$changes[] = "$pretty changed from {$old->$field} to {$row[$field]}";
						}
					}
				}

				$message = !empty($changes)
					? 'Your privileges have been updated: ' . implode(', ', $changes)
					: 'Your privileges have been updated: No specific changes detected.';

				$officer = $this->admin->get_officer_by_privilege_id($privilege_id);

				if ($officer) {
					$this->db->insert('notifications', [
						'recipient_officer_id' => $officer->student_id,
						'sender_student_id'    => $this->session->userdata('student_id'),
						'type'                 => 'privilege_updated',
						'reference_id'         => $privilege_id,
						'message'              => $message,
						'is_read'              => 0,
						'created_at'           => date('Y-m-d H:i:s'),
						'link'                 => base_url('officer/dashboard')
					]);
				}
			}
		}

		echo json_encode(['success' => $update]);
	}

	public function delete_officer_dept()
	{
		$id = $this->input->post('id');

		if ($id) {
			$deleted = $this->admin->delete_officer_dept($id);

			if ($deleted) {
				echo json_encode(['success' => true]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Database deletion failed.']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid officer ID.']);
		}
	}

	public function list_officers_org($org_id)
	{
		$data['title'] = 'List of Officer Organization';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);

		// FETCHING ALL THE DEPARTMENT
		$data['organization'] = $this->admin->get_organization();
		$data['org_id'] = $org_id;

		$data['officers'] = $this->admin->get_officer_org();

		$data['privileges'] = $this->admin->manage_privilege_org();

		$this->load->view('layout/header', $data);
		$this->load->view('admin/manage-organization', $data);
		$this->load->view('layout/footer', $data);
	}

	public function update_privileges_org()
	{
		if (!$this->input->is_ajax_request()) {
			show_error('No direct script access allowed');
		}

		$privileges_input = $this->input->post('privileges');

		if (empty($privileges_input)) {
			echo json_encode(['success' => false, 'error' => 'No data received']);
			return;
		}

		$sanitized_data = [];

		foreach ($privileges_input as $privilege_id => $values) {
			$sanitized_data[] = [
				'privilege_id' => $privilege_id,
				'manage_fines' => isset($values['manage_fines']) ? 'Yes' : 'No',
				'manage_evaluation' => isset($values['manage_evaluation']) ? 'Yes' : 'No',
				'manage_applications' => isset($values['manage_applications']) ? 'Yes' : 'No',
				'able_scan' => isset($values['able_scan']) ? 'Yes' : 'No',
				'able_create_activity' => isset($values['able_create_activity']) ? 'Yes' : 'No'
			];
		}

		$update = $this->admin->update_privileges_org($sanitized_data);

		echo json_encode(['success' => $update]);
	}

	public function update_status_org()
	{
		$input = json_decode(file_get_contents("php://input"), true);

		// Ensure required parameters exist
		if (!isset($input['student_id']) || !isset($input['is_admin'])) {
			echo json_encode(["success" => false, "message" => "Invalid request"]);
			return;
		}

		$student_id = $input['student_id'];
		$new_status = ($input['is_admin'] === "Yes") ? "Yes" : "No";

		// Update only the specific record with both student_id & activity_id
		if ($this->admin->update_status_org($student_id, $new_status)) {
			echo json_encode(["success" => true, "message" => "Update admin updated"]);
		} else {
			echo json_encode(["success" => false, "message" => "Update failed"]);
		}
	}

	public function delete_officer_org()
	{
		$id = $this->input->post('id');

		if ($id) {
			$deleted = $this->admin->delete_officer_org($id);

			if ($deleted) {
				echo json_encode(['success' => true]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Database deletion failed.']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid officer ID.']);
		}
	}

	// GENERAL SETTINGS PAGE (FINAL CHECK)
	public function general_settings()
	{
		$data['title'] = 'General Settings';

		$student_id = $this->session->userdata('student_id');
		$role       = $this->session->userdata('role');

		// FETCH USER PROFILE
		$data['users'] = $this->admin->get_student($student_id);
		$data['exempted'] = $this->admin->get_exempted_students();

		$data['students'] = $this->admin->get_import_students();
		$data['departments'] = $this->admin->get_department();

		// DYNAMIC DROPDOWN OPTIONS
		$logo_targets = [];

		if ($role === 'Admin') {
			$logo_targets[] = ['value' => 'student_parliament', 'label' => 'Student Parliament'];
		} elseif ($role === 'Officer') {
			$is_officer = $this->session->userdata('is_officer');
			$is_officer_dept = $this->session->userdata('is_officer_dept');

			if ($is_officer === 'Yes') {
				$org = $this->admin->get_organization_by_student($student_id);
				if ($org) {
					$logo_targets[] = ['value' => 'organization', 'label' => $org->org_name];
				}
			}
			if ($is_officer_dept === 'Yes') {
				$dept = $this->admin->get_department_by_id($data['users']->dept_id);
				if ($dept) {
					$logo_targets[] = ['value' => 'department', 'label' => $dept->dept_name];
				}
			}
		}

		$data['logo_targets'] = $logo_targets;



		// Get the only row from semester_range (assuming there's just one)
		$data['semester'] = $this->db->get('semester_range')->row();

		// LOAD VIEW
		$this->load->view('layout/header', $data);
		$this->load->view('admin/general_settings', $data);
		$this->load->view('layout/footer', $data);
	}

	public function save_active_semester()
	{
		$semester = $this->input->post('semester');
		$start_year = $this->input->post('start_year');
		$end_year = $this->input->post('end_year');

		if (!$semester || !$start_year || !$end_year || ((int)$end_year - (int)$start_year !== 1)) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'Invalid input. Please provide a semester and valid academic year range.'
				]));
		}


		// Check if 'active_semester' setting already exists
		$exists = $this->db->get_where('semester_ay', ['is_active' => '1'])->row();
		$data = [
			'semester' => $semester,
			'academic_year' => $start_year . '-' . $end_year,
			'is_active' => '1'
		];

		if ($exists) {
			// Update existing active record
			$this->db->where('is_active', '1');
			$this->db->update('semester_ay', $data);
		} else {
			// Insert new active record
			$this->db->insert('semester_ay', $data);
		}

		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => 'success',
				'message' => 'Active semester saved successfully.',
				'value' => $semester . ' AY ' . $start_year . '-' . $end_year,
			]));
	}



	//save semester schedules
	public function save_sem()
	{
		$first_start = $this->input->post('first_start');
		$first_end = $this->input->post('first_end');
		$second_start = $this->input->post('second_start');
		$second_end = $this->input->post('second_end');
		$academic_year = $this->input->post('academic_year');

		$data = [
			'first_start'   => date('n', strtotime($first_start)),    // e.g., 1 = January
			'first_end'     => date('n', strtotime($first_end)),
			'second_start'  => date('n', strtotime($second_start)),
			'second_end'    => date('n', strtotime($second_end)),
			'academic_year' => $academic_year
		];

		// Check if any row exists
		$existing = $this->db->get('semester_range')->row();

		if ($existing) {
			// Only update the first row (assumed to have id = 1 or just the only row)
			$this->db->where('id', $existing->id);
			$this->db->update('semester_range', $data);
		} else {
			// Insert only if empty
			$this->db->insert('semester_range', $data);
		}

		$this->session->set_flashdata('success', 'Semester dates saved successfully.');
		redirect('admin/general-settings'); // Update this to your actual settings page
	}



	public function update_student()
	{
		if (!$this->input->is_ajax_request()) {
			show_error('No direct script access allowed');
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id', 'User ID', 'required|integer');
		$this->form_validation->set_rules('student_id', 'Student ID', 'required');
		$this->form_validation->set_rules('first_name', 'First Name', 'required');
		$this->form_validation->set_rules('middle_name', 'Middle Name', 'required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('department', 'Department', 'required');
		$this->form_validation->set_rules('year_level', 'Year Level', 'required');
		$this->form_validation->set_rules('sex', 'Sex', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		$data = [
			'student_id' => $this->input->post('student_id', true),
			'first_name' => $this->input->post('first_name', true),
			'middle_name' => $this->input->post('middle_name', true),
			'last_name' => $this->input->post('last_name', true),
			'dept_id' => $this->input->post('department', true),
			'year_level' => $this->input->post('year_level', true),
			'sex' => $this->input->post('sex', true),
			'email' => $this->input->post('email', true),
		];

		$user_id = $this->input->post('user_id', true);

		$updated = $this->admin->update_student_data($user_id, $data);

		if ($updated) {
			echo json_encode(['success' => true]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to update student info.'
			]);
		}
	}

	// // IMPORTING LIST STUDENT
	// public function import_list()
	// {
	// 	require_once FCPATH . 'vendor/autoload.php'; // Correct PhpSpreadsheet autoload path

	// 	if ($_FILES['import_file']['error'] == UPLOAD_ERR_OK) {
	// 		$fileTmpPath = $_FILES['import_file']['tmp_name'];
	// 		$fileName = $_FILES['import_file']['name'];
	// 		$extension = pathinfo($fileName, PATHINFO_EXTENSION);

	// 		try {
	// 			// Read data based on file type
	// 			if ($extension == 'csv') {
	// 				$data = $this->readCSV($fileTmpPath);
	// 			} elseif ($extension == 'xlsx') {
	// 				$data = $this->readXLSX($fileTmpPath);
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload a CSV or XLSX file.']);
	// 				return;
	// 			}

	// 			// Insert into database
	// 			if (!empty($data)) {
	// 				$this->admin->insert_student($data);
	// 				echo json_encode(['success' => true, 'message' => 'File uploaded and data imported successfully!']);
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'No valid data found in the file.']);
	// 			}
	// 		} catch (Exception $e) {
	// 			echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
	// 		}
	// 	} else {
	// 		echo json_encode(['success' => false, 'message' => 'No file uploaded or an error occurred.']);
	// 	}
	// }

	public function import_list()
	{
		require_once FCPATH . 'vendor/autoload.php';

		if ($_FILES['import_file']['error'] == UPLOAD_ERR_OK) {
			$fileTmpPath = $_FILES['import_file']['tmp_name'];
			$fileName = $_FILES['import_file']['name'];
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);

			try {
				$data = [];

				if ($extension == 'csv') {
					$data = $this->readCSV($fileTmpPath);
				} elseif ($extension == 'xlsx') {
					$data = $this->readXLSX($fileTmpPath);
				} else {
					echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload a CSV or XLSX file.']);
					return;
				}

				if (!empty($data)) {
					$this->admin->insert_student($data); // insert or update

					// ✅ Get upcoming activities
					$upcoming_activities = $this->db->select('*')
						->from('activity')
						->where('start_date >=', date('Y-m-d'))
						->where('status', 'Upcoming')
						->get()
						->result();

					$changes_made = false;

					foreach ($data as $student) {
						$department = $this->db->get_where('department', ['dept_id' => $student['dept_id']])->row();
						if (!$department) continue;

						$dept_name = $department->dept_name;

						foreach ($upcoming_activities as $activity) {
							$audience_array = array_map('trim', explode(',', $activity->audience));

							if (in_array($dept_name, $audience_array)) {
								$timeslots = $this->db->get_where('activity_time_slots', ['activity_id' => $activity->activity_id])->result_array();
								$timeslot_ids = array_column($timeslots, 'timeslot_id');

								$already_exists = $this->db->get_where('attendance', [
									'activity_id' => $activity->activity_id,
									'student_id'  => $student['student_id']
								])->num_rows();

								if ($already_exists == 0) {
									$this->admin->assign_single_student_to_activity(
										$activity->activity_id,
										$student['student_id'],
										$dept_name,
										$timeslot_ids
									);

									$changes_made = true;
								}
							}
						}
					}

					// ✅ Final response
					if ($changes_made) {
						echo json_encode(['success' => true, 'message' => 'File uploaded, students imported, and assigned to upcoming activities!']);
					} else {
						echo json_encode(['success' => true, 'message' => 'No changes made.']);
					}
				} else {
					echo json_encode(['success' => false, 'message' => 'No valid data found in the file.']);
				}
			} catch (Exception $e) {
				echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'No file uploaded or an error occurred.']);
		}
	}



	private function readCSV($filePath)
	{
		$data = [];
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			$isHeader = true;
			while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if ($isHeader) {
					$isHeader = false;
					continue;
				}

				// Skip if row is completely empty or required fields are missing
				if (count($row) >= 6 && !empty(trim($row[0])) && !empty(trim($row[1]))) {
					$studentId = trim($row[0]);
					$firstNameRaw = trim($row[1]);
					$firstName = str_replace(' ', '', $firstNameRaw); // Remove spaces
					$generatedPassword = password_hash(substr($studentId, -4) . strtolower($firstName), PASSWORD_DEFAULT);

					$data[] = [
						'student_id'   => $studentId,
						'first_name'   => $firstName,
						'middle_name'  => trim($row[2] ?? ''),
						'last_name'    => trim($row[3] ?? ''),
						'sex'          => trim($row[4] ?? ''),
						'year_level'   => trim($row[5] ?? ''),
						'email'        => trim($row[6] ?? ''),
						'password'     => $generatedPassword,
						'dept_id'      => trim($row[7] ?? ''),
					];
				}
			}
			fclose($handle);
		}
		return $data;
	}

	private function readXLSX($filePath)
	{
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
		$sheet = $spreadsheet->getActiveSheet();
		$rows = $sheet->toArray();
		$data = [];

		foreach ($rows as $index => $row) {
			if ($index === 0) continue; // Skip header row

			if (count($row) >= 6 && !empty(trim($row[0])) && !empty(trim($row[1]))) {
				$studentId = trim($row[0]);
				$firstNameRaw = trim($row[1]);
				$firstName = str_replace(' ', '', $firstNameRaw); // Remove spaces
				$generatedPassword = password_hash(substr($studentId, -4) . strtolower($firstName), PASSWORD_DEFAULT);

				$data[] = [
					'student_id'   => $studentId,
					'first_name'   => $firstName,
					'middle_name'  => trim($row[2] ?? ''),
					'last_name'    => trim($row[3] ?? ''),
					'sex'          => trim($row[4] ?? ''),
					'year_level'   => trim($row[5] ?? ''),
					'email'        => trim($row[6] ?? ''),
					'password'     => $generatedPassword,
					'dept_id'      => trim($row[7] ?? ''),
				];
			}
		}
		return $data;
	}





	// IMPORT DEPARTMENT OFFICERS
	// public function import_list_dept()
	// {
	// 	require_once FCPATH . 'vendor/autoload.php';

	// 	if ($_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
	// 		$fileTmpPath = $_FILES['import_file']['tmp_name'];
	// 		$fileName = $_FILES['import_file']['name'];
	// 		$extension = pathinfo($fileName, PATHINFO_EXTENSION);

	// 		try {
	// 			if ($extension === 'csv') {
	// 				$data = $this->readCSVDept($fileTmpPath);
	// 			} elseif ($extension === 'xlsx') {
	// 				$data = $this->readXLSXDept($fileTmpPath);
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV or XLSX allowed.']);
	// 				return;
	// 			}

	// 			if (!empty($data['users']) && !empty($data['privilege'])) {
	// 				$success = $this->admin->insert_dept_officers_batch($data['users'], $data['privilege']);
	// 				if ($success) {
	// 					echo json_encode(['success' => true, 'message' => 'Department officers imported successfully.']);
	// 				} else {
	// 					echo json_encode(['success' => false, 'message' => 'Database transaction failed.']);
	// 				}
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'No valid data found in the file.']);
	// 			}
	// 		} catch (Exception $e) {
	// 			echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
	// 		}
	// 	} else {
	// 		echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
	// 	}
	// }



	public function import_list_dept()
	{
		require_once FCPATH . 'vendor/autoload.php';

		if ($_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
			$fileTmpPath = $_FILES['import_file']['tmp_name'];
			$fileName = $_FILES['import_file']['name'];
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);

			try {
				if ($extension === 'csv') {
					$data = $this->readCSVDept($fileTmpPath);
				} elseif ($extension === 'xlsx') {
					$data = $this->readXLSXDept($fileTmpPath);
				} else {
					echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV or XLSX allowed.']);
					return;
				}

				if (!empty($data['users']) && !empty($data['privilege'])) {
					$imported_student_ids = array_column($data['users'], 'student_id');

					// Set all existing department officers to Inactive first
					$this->db->where('role', 'Officer')
						->where('is_officer_dept', 'Yes')
						->update('users', ['is_active' => 'Inactive']);

					// Loop through each user
					foreach ($data['users'] as $user) {
						$existing = $this->db->get_where('users', ['student_id' => $user['student_id']])->row();

						if ($existing) {
							// Update existing user to Active and update their info
							$this->db->where('student_id', $user['student_id'])
								->update('users', array_merge($user, ['is_active' => 'Active']));
						} else {
							// New user: insert and mark Active
							$user['is_active'] = 'Active';
							$this->db->insert('users', $user);
						}
					}

					// Insert privileges in batch
					// Loop through privilege entries
					foreach ($data['privilege'] as $priv) {
						$existing_priv = $this->db->get_where('privilege', ['student_id' => $priv['student_id']])->row();

						if ($existing_priv) {
							// Update if student_id already exists
							$this->db->where('student_id', $priv['student_id'])
								->update('privilege', $priv);
						} else {
							// Insert new privilege entry
							$this->db->insert('privilege', $priv);
						}
					}


					echo json_encode(['success' => true, 'message' => 'Department officers imported successfully.']);
				} else {
					echo json_encode(['success' => false, 'message' => 'No valid data found in the file.']);
				}
			} catch (Exception $e) {
				echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
		}
	}


	private function parseDeptFile($rows)
	{
		$users = [];

		foreach ($rows as $index => $row) {
			if ($index === 0) continue; // Skip header

			if (count($row) < 9 || empty(trim($row[0])) || empty(trim($row[7])) || !is_numeric(trim($row[7]))) {
				continue;
			}

			$rawId = trim($row[0]);
			$studentId = 'DEPT' . $rawId;
			$idParts = explode('-', $studentId);
			$last4 = isset($idParts[1]) ? substr($idParts[1], -4) : substr($studentId, -4);
			$generatedPassword = 'dept' . $last4;
			$hashedPassword = password_hash($generatedPassword, PASSWORD_DEFAULT);
			$position = strtolower(trim($row[8]));

			$users[] = [
				'student_id'       => $studentId,
				'first_name'       => trim($row[1]),
				'middle_name'      => trim($row[2]),
				'last_name'        => trim($row[3]),
				'sex'              => trim($row[4]),
				'year_level'       => trim($row[5]),
				'email'            => trim($row[6]),
				'password'         => $hashedPassword,
				'dept_id'		=> $row[7],
				'role'             => 'Officer',
				'is_officer_dept'  => 'Yes',
				'is_admin'        => ($position === 'adviser') ? 'Yes' : 'No',
			];

			$privilege[] = [
				'student_id' => $studentId
			];
		}

		return ['users' => $users, 'privilege' => $privilege];
	}

	private function readCSVDept($filePath)
	{
		$rows = [];
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$rows[] = $data;
			}
			fclose($handle);
		}
		return $this->parseDeptFile($rows);
	}

	private function readXLSXDept($filePath)
	{
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
		$sheet = $spreadsheet->getActiveSheet();
		$rows = $sheet->toArray();
		return $this->parseDeptFile($rows);
	}

	//IMPORTING ORGANIZATION OFFICERS
	// public function import_list_org()
	// {
	// 	require_once FCPATH . 'vendor/autoload.php';

	// 	if ($_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
	// 		$fileTmpPath = $_FILES['import_file']['tmp_name'];
	// 		$fileName = $_FILES['import_file']['name'];
	// 		$extension = pathinfo($fileName, PATHINFO_EXTENSION);

	// 		try {
	// 			if ($extension === 'csv') {
	// 				$data = $this->readCSVOrg($fileTmpPath);
	// 			} elseif ($extension === 'xlsx') {
	// 				$data = $this->readXLSXOrg($fileTmpPath);
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV or XLSX allowed.']);
	// 				return;
	// 			}

	// 			if (!empty($data['users']) && !empty($data['student_org']) && !empty($data['privilege'])) {
	// 				$success = $this->admin->insert_org_officers_batch($data['users'], $data['student_org'], $data['privilege']);
	// 				if ($success) {
	// 					echo json_encode(['success' => true, 'message' => 'Organization officers imported successfully.']);
	// 				} else {
	// 					echo json_encode(['success' => false, 'message' => 'Database transaction failed.']);
	// 				}
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'No valid data found in the file.']);
	// 			}
	// 		} catch (Exception $e) {
	// 			echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
	// 		}
	// 	} else {
	// 		echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
	// 	}
	// }

	public function import_list_org()
	{
		require_once FCPATH . 'vendor/autoload.php';

		if ($_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
			$fileTmpPath = $_FILES['import_file']['tmp_name'];
			$fileName = $_FILES['import_file']['name'];
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);

			try {
				if ($extension === 'csv') {
					$data = $this->readCSVOrg($fileTmpPath);
				} elseif ($extension === 'xlsx') {
					$data = $this->readXLSXOrg($fileTmpPath);
				} else {
					echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV or XLSX allowed.']);
					return;
				}

				if (!empty($data['users']) && !empty($data['student_org']) && !empty($data['privilege'])) {
					$imported_student_ids = array_column($data['users'], 'student_id');

					// Step 1: Get all current org officers (is_officer = 'Yes')
					$current_officers = $this->db->select('student_id')
						->from('student_org')
						->where('is_officer', 'Yes')
						->get()
						->result_array();

					$current_officer_ids = array_column($current_officers, 'student_id');

					// Step 2: Mark existing officers as Inactive
					if (!empty($current_officer_ids)) {
						$this->db->where_in('student_id', $current_officer_ids)
							->update('users', ['is_active' => 'Inactive']);
					}

					// Step 3: Insert or update users
					foreach ($data['users'] as $user) {
						$existing = $this->db->get_where('users', ['student_id' => $user['student_id']])->row();

						if ($existing) {
							$this->db->where('student_id', $user['student_id'])
								->update('users', array_merge($user, ['is_active' => 'Active']));
						} else {
							$user['is_active'] = 'Active';
							$this->db->insert('users', $user);
						}
					}

					// Step 4: Insert or update student_org
					foreach ($data['student_org'] as $org) {
						$existing_org = $this->db->get_where('student_org', ['student_id' => $org['student_id']])->row();

						if ($existing_org) {
							// Update if student_id already exists in student_org
							$this->db->where('student_id', $org['student_id'])
								->update('student_org', [
									'org_id' => $org['org_id'],
									'is_officer' => $org['is_officer'],
									'is_admin' => $org['is_admin']
								]);
						} else {
							$this->db->insert('student_org', $org);
						}
					}

					// Step 5: Insert privileges
					// Loop through privilege entries
					foreach ($data['privilege'] as $priv) {
						$existing_priv = $this->db->get_where('privilege', ['student_id' => $priv['student_id']])->row();

						if ($existing_priv) {
							// Update if student_id already exists
							$this->db->where('student_id', $priv['student_id'])
								->update('privilege', $priv);
						} else {
							// Insert new privilege entry
							$this->db->insert('privilege', $priv);
						}
					}


					echo json_encode(['success' => true, 'message' => 'Organization officers imported and updated successfully.']);
				} else {
					echo json_encode(['success' => false, 'message' => 'No valid data found in the file.']);
				}
			} catch (Exception $e) {
				echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
		}
	}









	private function parseOrgFile($rows)
	{
		$users = [];
		$student_org = [];

		foreach ($rows as $index => $row) {
			if ($index === 0) continue; // Skip header

			if (count($row) < 9 || empty(trim($row[0])) || empty(trim($row[7])) || !is_numeric(trim($row[7]))) {
				continue;
			}

			$rawId = trim($row[0]);
			$studentId = 'ORG' . $rawId;
			$idParts = explode('-', $studentId);
			$last4 = isset($idParts[1]) ? substr($idParts[1], -4) : substr($studentId, -4);
			$generatedPassword = 'org' . $last4;
			$hashedPassword = password_hash($generatedPassword, PASSWORD_DEFAULT);
			$orgId = trim($row[8]);
			$position = strtolower(trim($row[9]));

			$users[] = [
				'student_id'       => $studentId,
				'first_name'       => trim($row[1]),
				'middle_name'      => trim($row[2]),
				'last_name'        => trim($row[3]),
				'sex'              => trim($row[4]),
				'year_level'       => trim($row[5]),
				'email'            => trim($row[6]),
				'password'         => $hashedPassword,
				'dept_id'		=> $row[7],
				'role'             => 'Officer',
				'is_officer_dept'  => 'No',
				'is_admin'         => 'No',
			];

			$student_org[] = [
				'student_id' => $studentId,
				'org_id'     => $orgId,
				'is_admin'   => ($position === 'adviser') ? 'Yes' : 'No',
				'is_officer' => 'Yes',
			];

			$privilege[] = [
				'student_id' => $studentId
			];
		}

		return ['users' => $users, 'student_org' => $student_org, 'privilege' => $privilege];
	}

	private function readCSVOrg($filePath)
	{
		$rows = [];
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$rows[] = $data;
			}
			fclose($handle);
		}
		return $this->parseOrgFile($rows);
	}

	private function readXLSXOrg($filePath)
	{
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
		$sheet = $spreadsheet->getActiveSheet();
		$rows = $sheet->toArray();
		return $this->parseOrgFile($rows);
	}

	// IMPORTING EXEMPTED STUDENTS
	public function import_exempted_students()
	{
		require_once FCPATH . 'vendor/autoload.php';

		if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
			echo json_encode(['success' => false, 'message' => 'No file uploaded or an upload error occurred.']);
			return;
		}

		$fileTmpPath = $_FILES['import_file']['tmp_name'];
		$fileName = $_FILES['import_file']['name'];
		$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

		try {
			// Validate file type
			if ($extension === 'csv') {
				$data = $this->readCSVExempted($fileTmpPath);
			} elseif ($extension === 'xlsx') {
				$data = $this->readXLSXExempted($fileTmpPath);
			} else {
				echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV or XLSX allowed.']);
				return;
			}

			// Proceed with data insertion if there's valid content
			if (!empty($data)) {
				$success = $this->admin->insert_exempted_students_batch($data);
				if ($success) {
					echo json_encode(['success' => true, 'message' => 'Exempted students imported successfully.']);
				} else {
					echo json_encode(['success' => false, 'message' => 'Failed to import data into the database.']);
				}
			} else {
				echo json_encode(['success' => false, 'message' => 'The uploaded file is empty or contains invalid data.']);
			}
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'An error occurred while processing the file: ' . $e->getMessage()]);
		}
	}


	private function parseExemptedFile($rows)
	{
		$students = [];

		foreach ($rows as $index => $row) {
			if ($index === 0) continue; // Skip header

			if (count($row) < 5 || empty(trim($row[0]))) {
				continue;
			}

			$students[] = [
				'student_id'  => trim($row[0])
			];
		}

		return $students;
	}

	private function readCSVExempted($filePath)
	{
		$rows = [];
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$rows[] = $data;
			}
			fclose($handle);
		}
		return $this->parseExemptedFile($rows);
	}

	private function readXLSXExempted($filePath)
	{
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
		$sheet = $spreadsheet->getActiveSheet();
		$rows = $sheet->toArray();
		return $this->parseExemptedFile($rows);
	}


	// GENERATING QR
	public function generate_bulk_qr()
	{
		require_once(APPPATH . 'libraries/phpqrcode.php');

		$students = $this->admin->get_students_without_qr(); // Get all students without QR codes
		$count = 0;

		foreach ($students as $student) {
			$student_id = $student->student_id;

			// Generate QR code image as base64
			ob_start();
			\QRcode::png($student_id, null, QR_ECLEVEL_L, 3);
			$imageString = ob_get_contents();
			ob_end_clean();

			// Base64 encode the image data
			$qrBase64 = base64_encode($imageString);

			// Save to database (Ensure this method correctly saves the data)
			$this->admin->assign_qr($student_id, $qrBase64);
			$count++;
		}

		// Return a success response with the count of generated QR codes
		echo json_encode(['status' => 'success', 'count' => $count]);
	}

	// MANAGING ORGANIZATION
	public function save_organization()
	{
		$org_name = trim($this->input->post('org_name'));
		$logo_path = '';

		if (empty($org_name)) {
			echo json_encode(['success' => false, 'message' => 'Organization name is required.']);
			return;
		}

		// Handle file upload
		if (!empty($_FILES['org_logo']['name'])) {
			$config['upload_path'] = './assets/imageOrg/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = time() . '_' . $_FILES['org_logo']['name'];
			$config['max_size'] = 2048;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('org_logo')) {
				echo json_encode(['success' => false, 'message' => $this->upload->display_errors('', '')]);
				return;
			} else {
				$upload_data = $this->upload->data();
				$logo_path = $upload_data['file_name'];
			}
		}

		$data = [
			'org_name' => $org_name,
			'logo' => $logo_path,
		];

		$inserted = $this->admin->insert_organization($data);

		echo json_encode(['success' => $inserted]);
	}

	public function get_organizations()
	{
		$data = $this->admin->get_all();
		echo json_encode($data);
	}

	public function update_organization()
	{
		$id = $this->input->post('id');
		$org_name = $this->input->post('org_name');

		$logo = $_FILES['logo']['name'] ? $_FILES['logo']['name'] : null;
		$new_logo = null;

		if ($logo) {
			$config['upload_path'] = './assets/imageOrg/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = time() . '_' . $logo;
			$config['overwrite'] = true;

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('logo')) {
				$new_logo = $this->upload->data('file_name');
			} else {
				echo json_encode([
					'success' => false,
					'message' => $this->upload->display_errors()
				]);
				return;
			}
		}

		// Pass null if no new logo is uploaded (so existing logo remains)
		$result = $this->admin->update($id, $org_name, $new_logo);

		echo json_encode([
			'success' => $result,
			'message' => $result ? 'Organization updated successfully.' : 'Failed to update organization.'
		]);
	}


	public function about()
	{
		$data['title'] = 'About';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);


		$this->load->view('layout/header', $data);
		$this->load->view('admin/about', $data);
		$this->load->view('layout/footer', $data);
	}



	//UPLOAD LOGO

	public function upload_logo()
	{
		$this->load->model('Admin_model');
		$user = $this->session->userdata();

		if (!isset($_FILES['logo_file']['name']) || empty($_FILES['logo_file']['name'])) {
			echo json_encode(['success' => false, 'message' => 'No file selected.']);
			return;
		}

		$upload_path = './uploads/logos/';
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0755, true);
		}

		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'jpg|jpeg|png|gif';
		$config['file_name'] = time() . '_' . $_FILES['logo_file']['name'];

		$this->load->library('upload', $config);

		if ($this->upload->do_upload('logo_file')) {
			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];

			$this->Admin_model->upload_logo($file_name, $user);

			echo json_encode(['success' => true, 'message' => 'Logo uploaded successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => $this->upload->display_errors('', '')]);
		}
	}


	public function get_current_logo()
	{
		// Manually build the user array from session data
		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'), // if it's stored
		];

		if (!$user['role'] || !$user['student_id']) {
			echo json_encode(['success' => false, 'message' => 'Missing session data.']);
			return;
		}

		$this->load->model('Admin_model');
		$logo = $this->Admin_model->get_current_logo($user);

		if ($logo && file_exists(FCPATH . 'uploads/logos/' . $logo)) {
			echo json_encode([
				'success' => true,
				'logo' => base_url('uploads/logos/' . $logo)
			]);
		} else {
			echo json_encode(['success' => false]);
		}
	}








	public function save_header_footer()
	{
		$this->load->model('Admin_model');
		header('Content-Type: application/json'); // Set JSON response header

		// Require both files
		if (empty($_FILES['header_file']['name']) || empty($_FILES['footer_file']['name'])) {
			echo json_encode([
				'success' => false,
				'message' => 'Both header and footer images are required.'
			]);
			return;
		}

		$uploaded = [];
		$upload_path = './uploads/headerandfooter/';
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, true);
		}

		$config = [
			'upload_path' => $upload_path,
			'allowed_types' => 'jpg|jpeg|png|gif',
			'max_size' => 2048
		];
		$this->load->library('upload');

		// Upload header
		$config['file_name'] = time() . '_header_' . $_FILES['header_file']['name'];
		$this->upload->initialize($config);
		if ($this->upload->do_upload('header_file')) {
			$uploaded['header'] = $this->upload->data('file_name');
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Header upload failed: ' . strip_tags($this->upload->display_errors())
			]);
			return;
		}

		// Upload footer
		$config['file_name'] = time() . '_footer_' . $_FILES['footer_file']['name'];
		$this->upload->initialize($config);
		if ($this->upload->do_upload('footer_file')) {
			$uploaded['footer'] = $this->upload->data('file_name');
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Footer upload failed: ' . strip_tags($this->upload->display_errors())
			]);
			return;
		}

		// Save to DB
		$this->Admin_model->save_header_footer($uploaded);

		echo json_encode([
			'success' => true,
			'message' => 'Header and footer uploaded successfully.'
		]);
	}


	public function get_current_header_footer()
	{
		// Manually build the user array from session data
		$user = [
			'role'             => $this->session->userdata('role'),
			'student_id'       => $this->session->userdata('student_id'),
			'is_officer'       => $this->session->userdata('is_officer'),
			'is_officer_dept'  => $this->session->userdata('is_officer_dept'),
			'dept_id'          => $this->session->userdata('dept_id'),
		];

		if (!$user['role'] || !$user['student_id']) {
			echo json_encode(['success' => false, 'message' => 'Missing session data.']);
			return;
		}

		$this->load->model('Admin_model');
		$result = $this->Admin_model->get_current_header_footer($user);

		if (!is_array($result)) {
			echo json_encode([
				'success' => false,
				'message' => 'No header or footer found.',
			]);
			return;
		}

		$base = base_url('uploads/headerandfooter/');

		echo json_encode([
			'success' => true,
			'header'  => !empty($result['header']) ? $base . $result['header'] : null,
			'footer'  => !empty($result['footer']) ? $base . $result['footer'] : null,
		]);
	}






	//VERIFY RECEIPT START


	public function verify_receipt_page()
	{
		$data['title'] = 'Verify Receipt';

		$this->load->model('Student_model');

		$student_id = $this->session->userdata('student_id');

		// Fetch student profile picture
		$current_profile_pic = $this->Student_model->get_profile_pic($student_id);
		$data['profile_pic'] = !empty($current_profile_pic) ? $current_profile_pic : 'default.jpg';

		// Fetch user role
		// $users = $this->admin->get_roles($student_id);
		// $data['role'] = $users['role'];

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->admin->get_student($student_id);


		$this->load->view('layout/header', $data);
		$this->load->view('admin/verify-receipt');
		$this->load->view('layout/footer', $data);
	}

	// This is for actually verifying the receipt
	public function verify_receipt()
	{
		$this->load->model('Student_model');

		$verification_code = $this->input->post('verification_code');
		if (!$verification_code) {
			echo json_encode(['status' => 'error', 'message' => 'Verification code is required.']);
			return;
		}

		// Get user info
		$student_id = $this->session->userdata('student_id');
		$role = $this->session->userdata('role');
		$is_officer_dept = $this->session->userdata('is_officer_dept');

		// Step 1: Try registration receipt
		$receipt = $this->Student_model->get_registration_by_code($verification_code, $student_id, $role, $is_officer_dept);

		if ($receipt) {
			$receipt['receipt_type'] = 'Registration Payment';
		} else {
			// Step 2: Try fines receipt
			$receipt = $this->Student_model->get_fines_by_code($verification_code, $student_id, $role, $is_officer_dept);
			if ($receipt) {
				$receipt['receipt_type'] = 'Fines Payment';
			}
		}

		if ($receipt) {
			echo json_encode([
				'status' => 'success',
				'data' => [
					'student_id'   => $receipt['student_id'],
					'activity'     => $receipt['activity_title'] ?? 'Activity not found',
					'amount_paid'  => '₱' . number_format($receipt['amount_paid'], 2),
					'status'       => '✅ Approved',
					'date_issued'  => date('F j, Y', strtotime($receipt['registered_at'] ?? $receipt['last_updated'])),
					'receipt_type' => $receipt['receipt_type']
				]
			]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Invalid verification code or not authorized to verify this receipt.']);
		}
	}






	//VERIFY RECEIPT END
}

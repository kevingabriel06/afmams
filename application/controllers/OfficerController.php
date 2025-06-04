<?php

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
class OfficerController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Officer_model', 'officer');
		$this->load->model('Admin_model', 'admin');
		$this->load->model('Notification_model'); // add this

		if (!$this->session->userdata('student_id')) {
			redirect(site_url('login'));
		}
	}


	// public function officer_dashboard()
	// {

	// 	$data['title'] = 'Dashboard';
	// hello it is the model of the hsjkdhkjhsjdhsj
	public function officer_dashboard()
	{
		$data['title'] = 'Dashboard';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();


		// DATA
		// Get current semester count and comparison with previous semester
		$data['current_semester'] = $this->officer->get_current_semester_count();
		$data['previous_semester'] = $this->officer->get_previous_semester_count();

		// Get breakdown of activities per month for the current semester
		$data['monthly_breakdown'] = $this->officer->get_monthly_activity_count($data['current_semester']['start_date'], $data['current_semester']['end_date']);

		// COUNT OF STUDENT PER DEPARTMENT
		// Get the count of students per department
		$data['student_counts'] = $this->officer->get_student_count_per_department();

		// Calculate the total number of students
		$total_students = array_sum(array_column($data['student_counts'], 'student_count'));
		$data['total_students'] = $total_students;
		// Get breakdown of activities per month for the current semester
		$data['monthly_breakdown'] = $this->officer->get_monthly_activity_count($data['current_semester']['start_date'], $data['current_semester']['end_date']);

		// ACTIVITY ORGANIZED
		$data['activity_count'] = $this->officer->get_current_semester_count_organized();

		// TOTAL FINES
		// $data['fines_per_activity'] = $this->officer->get_total_fines_per_activity();

		// TOTAL FINES
		$data['fines_per_activity'] = $this->officer->get_total_fines_per_activity();

		// EXPECTED ATTENDEES TO ACTUAL
		$attendance_data_result = $this->officer->fetch_attendance_data();

		// Assigning the fetched data and the attendance rate to $data
		$data['attendance_data'] = $attendance_data_result['attendance_data'];
		$data['attendance_rate'] = $attendance_data_result['attendance_rate'];

		$data['student_count'] = $this->officer->number_of_students();
		$data['officer_count'] = $this->officer->number_of_officers();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/dashboard', $data);
		$this->load->view('layout/footer', $data);
	}


	// ACTIVITY MANAGEMENT

	// CREATING ACTIVITY - PAGE (FINAL CHECK)
	public function create_activity()
	{
		$data['title'] = 'Create Activity';

		$student_id = $this->session->userdata('student_id');

		$data['users'] = $this->officer->get_student($student_id);
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['dept'] = $this->officer->get_department(); // this is the audience

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/create-activity', $data);
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

			$dept_or_org_name = $this->session->userdata('dept_name') ?: $this->session->userdata('org_name');

			// Prepare Activity Data
			$data = [
				'activity_title'        => $this->input->post('title'),
				'start_date'            => $this->input->post('date_start'),
				'end_date'              => $this->input->post('date_end'),
				'description'           => $this->input->post('description'),
				'registration_deadline' => $this->input->post('registration_deadline'),
				'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
				'organizer'             => $dept_or_org_name,
				'fines'                 => str_replace(",", "", $this->input->post('fines')),
				'audience'              => $dept_or_org_name,
				'created_at'            => date('Y-m-d H:i:s')
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

			// Insert Activity and Get ID
			$activity_id = $this->officer->save_activity($data);
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
			$timeslot_ids = $this->officer->save_schedules($schedules);

			// Assign Students to Activity
			$this->assign_students_to_activity($activity_id, $data, $timeslot_ids);

			// Return Success Response
			echo json_encode([
				'status'   => 'success',
				'message'  => 'Activity Saved Successfully',
				'redirect' => site_url('officer/list-of-activity')
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

	private function assign_students_to_activity($activity_id, $data, $timeslot_ids = [])
	{

		// Get organizer from activity table
		$activity = $this->db->select('organizer, start_date, registration_deadline, registration_fee')->from('activity')->where('activity_id', $activity_id)->get()->row();
		$organizer = $activity ? $activity->organizer : null;


		$students = [];

		// Get current user's department and organization from session
		$dept_id = $this->session->userdata('dept_id');
		$org_id  = $this->session->userdata('org_id');

		if (!empty($dept_id)) {
			// Students from department and role = Student
			$this->db->select('u.student_id');
			$this->db->from('users u');
			$this->db->join('department d', 'u.dept_id = d.dept_id');
			$this->db->where('u.dept_id', $dept_id);
			$this->db->where('d.dept_name', $data['audience']);
			$this->db->where('u.role', 'Student');
			$students = $this->db->get()->result();
		} elseif (!empty($org_id)) {
			// Students from organization and role = Student
			$this->db->select('so.student_id');
			$this->db->from('student_org so');
			$this->db->join('organization o', 'so.org_id = o.org_id');
			$this->db->join('users u', 'so.student_id = u.student_id');
			$this->db->where('so.org_id', $org_id);
			$this->db->where('o.org_name', $data['audience']);
			$this->db->where('u.role', 'Student');
			$students = $this->db->get()->result();
		}

		// Insert each student into attendance for each timeslot
		foreach ($students as $student) {
			// Check if student is exempted
			$is_exempted = $this->db
				->where('student_id', $student->student_id)
				->get('exempted_students')
				->num_rows() > 0;

			foreach ($timeslot_ids as $timeslot_id) {
				// Step 1: Insert into attendance table
				$this->db->insert('attendance', [
					'activity_id' => $activity_id,
					'timeslot_id' => $timeslot_id,
					'student_id'  => $student->student_id
				]);

				if ($is_exempted) {
					$this->db->insert('attendance', [
						'activity_id' => $activity_id,
						'timeslot_id' => $timeslot_id,
						'student_id'  => $student->student_id,
						'attendance_status' => 'Exempted'
					]);
				}
				// Step 2: Get the auto-incremented attendance_id
				$attendance_id = $this->db->insert_id();

				// Step 3: Insert into fines table using the retrieved attendance_id
				$this->db->insert('fines', [
					'activity_id'    => $activity_id,
					'timeslot_id'    => $timeslot_id,
					'student_id'     => $student->student_id,
					'attendance_id'  => $attendance_id,
					'status'         => 'Pending', // optional defaults
					'fines_amount'   => 0           // or whatever default you want
				]);
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

	// FETCHING ACTIVITY - PAGE (FINAL CHECK)
	public function list_activity()
	{
		$data['title'] = 'List of Activities';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		// GETTING OF THE ACTIVITY FROM THE DATABASE
		$data['activities'] = $this->officer->get_activities(); // FOR STUDENT PARLIAMENT

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/list-activities', $data);
		$this->load->view('layout/footer', $data);
	}

	// FETCHING DETAILS OF THE ACTIVITY - PAGE
	public function activity_details($activity_id)
	{
		$data['title'] = 'Activity Details';

		$student_id = $this->session->userdata('student_id');

		$data['users'] = $this->officer->get_student($student_id);
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activity'] = $this->officer->get_activity($activity_id); // SPECIFIC ACTIVITY
		$data['schedules'] = $this->officer->get_schedule($activity_id); // GETTING OF SCHEDULE

		$data['activities'] = $this->officer->get_activities(); // FOR UPCOMING ACTIVITY PART
		$data['registrations'] = $this->officer->registrations($activity_id); // FOR REGISTRATION
		$data['departments'] = $this->officer->get_department();

		$data['verified_count'] = $this->officer->get_registered_count($activity_id);
		$data['attendees_count'] = $this->officer->get_attendees_count($activity_id);

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/activity-detail', $data);
		$this->load->view('layout/footer', $data);
	}

	// FUNCTIONALITY FOR THE REGISTRATION
	public function validate_registrations()
	{
		$student_id = $this->input->post('student_id');
		$activity_id = $this->input->post('activity_id');
		$reference_number = trim($this->input->post('reference_number'));
		$action = $this->input->post('action');
		$remarks = $this->input->post('remarks');

		$record = $this->officer->get_reference_data($student_id, $activity_id);

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

		if ($reference_number !== $record->reference_number) {
			$this->officer->validate_registration($student_id, $activity_id, [
				'reference_number_admin' => $reference_number,
				'registration_status' => 'Rejected',
				'remark' => "Reference number doesn't match the admin's record. Please contact support if this is a mistake."
			]);

			echo json_encode(['status' => 'warning', 'message' => 'Reference number does not match. Please check it. Contact the student.']);
			return;
		}

		$update = $this->officer->validate_registration($student_id, $activity_id, [
			'reference_number_admin' => $reference_number,
			'registration_status' => $action,
			'remark' => $remarks
		]);

		if ($update) {
			// ✅ Only generate receipt if status is Verified
			if ($action === 'Verified') {
				$registration_id = $this->officer->get_registration_id($student_id, $activity_id);
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

		$receipt_data = $this->Student_model->get_receipt_by_id($registration_id);
		if (!$receipt_data) return;

		$filename = 'receipt_' . $registration_id . '.pdf';
		$folder_path = FCPATH . 'uploads/generated_receipts/';
		$filepath = $folder_path . $filename;

		if (!is_dir($folder_path)) {
			mkdir($folder_path, 0777, true);
		}

		$verification_code = strtoupper(substr(md5($registration_id . time()), 0, 8));

		$pdf = new PDF();
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(true, 10);

		$watermarkPath = FCPATH . 'application/third_party/receiptlogo.png';
		if (file_exists($watermarkPath)) {
			$pdf->Image($watermarkPath, 35, 70, 140, 100, 'PNG');
		}

		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, 'OFFICIAL RECEIPT', 0, 1, 'C');
		$pdf->Ln(5);

		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(50, 8, 'Receipt No:', 0, 0, 'L');
		$pdf->Cell(0, 8, strtoupper($registration_id), 0, 1, 'L');
		$pdf->Cell(50, 8, 'Date:', 0, 0, 'L');
		$pdf->Cell(0, 8, date('F j, Y', strtotime($receipt_data['registered_at'])), 0, 1, 'L');
		$pdf->Ln(5);

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'From:', 0, 0, 'L');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $this->session->userdata('dept_name'), 0, 1, 'L');

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Received By:', 0, 0, 'L');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, strtoupper($receipt_data['student_id'] . ' - ' . $receipt_data['first_name'] . ' ' . $receipt_data['last_name']), 0, 1, 'L');

		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(120, 8, 'Description', 1, 0, 'C');
		$pdf->Cell(40, 8, 'Amount (PHP)', 1, 1, 'C');

		$pdf->SetFont('Arial', '', 10);
		$description = 'Payment for ' . $receipt_data['activity_title'] . ' Registration';
		$pdf->Cell(120, 8, $description, 1, 0, 'C');
		$pdf->Cell(40, 8, number_format($receipt_data['amount_paid'], 2), 1, 1, 'C');

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(120, 8, 'Total Amount:', 1, 0, 'R');
		$pdf->Cell(40, 8, 'P ' . number_format($receipt_data['amount_paid'], 2), 1, 1, 'C');

		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(50, 8, 'Payment Type:', 0, 0, 'L');
		$pdf->Cell(0, 8, $receipt_data['reference_number'] ? 'E-Payment (GCash)' : 'Cash', 0, 1, 'L');

		$pdf->Ln(15);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 8, 'Verification Code:', 0, 1, 'C');
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, $verification_code, 0, 1, 'C');

		$pdf->Ln(10);
		$pdf->SetFont('Arial', 'I', 8);
		$pdf->Cell(0, 8, 'Enter this code on the receipt verification page to check validity.', 0, 1, 'C');

		$pdf->Output($filepath, 'F');

		// ✅ Update the `generated_receipt` and verification code
		$this->db->where('registration_id', $registration_id);
		$this->db->update('registrations', [
			'generated_receipt' => $filename,
			'remark' => 'Receipt generated.',
			'verification_code' => $verification_code,
		]);
	}

	// CASH REGISTRATION
	public function save_cash_payment()
	{

		$student_id = $this->input->post('student_id', TRUE);  // Sanitize input
		$activity_id = $this->input->post('activity_id', TRUE);
		$receipt_number = $this->input->post('receipt_number', TRUE);
		$amount_paid = $this->input->post('amount_paid', TRUE);
		$remark = $this->input->post('remark', TRUE);

		// Validate required fields
		if (empty($student_id) || empty($activity_id) || empty($receipt_number) || empty($amount_paid)) {
			echo json_encode([
				'status' => 'error',
				'message' => 'All fields are required. Please fill in the missing information.'
			]);
			return;
		}

		// Prepare data for insertion
		$data = array(
			'student_id'             => $student_id,
			'activity_id'            => $activity_id,
			'payment_type'           => 'Cash',
			'reference_number'       => $receipt_number,
			'reference_number_admin' => $receipt_number,  // Assuming same as receipt number
			'amount_paid'            => $amount_paid,
			'registration_status'    => 'Verified',
			'remark'                 => $remark,
			'registered_at'          => date('Y-m-d H:i:s'),
			'updated_at'             => date('Y-m-d H:i:s')
		);

		// Insert into the database
		$inserted = $this->officer->insert_cash_payment($data);

		// Return the response as JSON
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

	// EDITING OF ACTIVITY - PAGE (FINAL CHECK)
	public function edit_activity($activity_id)
	{
		$data['title'] = 'Edit Activity';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activity'] = $this->officer->get_activity($activity_id);
		$data['schedules'] = $this->officer->get_schedule($activity_id);
		$data['dept'] = $this->officer->get_department();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/edit-activity', $data);
		$this->load->view('layout/footer', $data);
	}

	// DELETE THE TIMESLOTS
	public function delete_schedule($id)
	{

		if ($this->officer->delete_schedule($id)) {
			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}
	}

	// UPDATE THE ACTIVITY
	// public function update_activity($activity_id)
	// {
	// 	if ($this->input->post()) {
	// 		// Set Validation Rules
	// 		$this->form_validation->set_rules('date_start', 'Start Date', 'required');
	// 		$this->form_validation->set_rules('date_end', 'End Date', 'required');
	// 		$this->form_validation->set_rules('start_datetime[]', 'Start Date & Time', 'required');
	// 		$this->form_validation->set_rules('end_datetime[]', 'End Date & Time', 'required');
	// 		$this->form_validation->set_rules('session_type[]', 'Session Type', 'required');
	// 	}
	// }
	// public function update_activity($activity_id)
	// {
	// 	if ($this->input->post()) {
	// 		// Set Validation Rules
	// 		$this->form_validation->set_rules('title', 'Activity Title', 'required');
	// 		$this->form_validation->set_rules('date_start', 'Start Date', 'required');
	// 		$this->form_validation->set_rules('date_end', 'End Date', 'required');
	// 		$this->form_validation->set_rules('description', 'Description', 'required');
	// 		$this->form_validation->set_rules('registration_fee', 'Registration Fee', 'numeric');
	// 		$this->form_validation->set_rules('fines', 'Fines', 'numeric');
	// 		$this->form_validation->set_rules('session_type[]', 'Session Type', 'required');
	// 		$this->form_validation->set_rules('start_datetime[]', 'Start Date & Time', 'required');
	// 		$this->form_validation->set_rules('end_datetime[]', 'End Date & Time', 'required');

	// 		// Run Validation
	// 		if ($this->form_validation->run() == FALSE) {
	// 			echo json_encode(['status' => 'error', 'errors' => validation_errors()]);
	// 			return;
	// 		}
	// 		if ($this->form_validation->run() == FALSE) {
	// 			echo json_encode(['status' => 'error', 'errors' => validation_errors()]);
	// 			return;
	// 		}

	// 		// Check if Activity Exists
	// 		$activity = $this->officer->get_activity($activity_id);
	// 		if (!$activity) {
	// 			echo json_encode(['status' => 'error', 'errors' => 'Activity not found.']);
	// 			return;
	// 		}

	// 		// Prepare Activity Data
	// 		$data = [
	// 			'activity_title'        => $this->input->post('title'),
	// 			'start_date'            => $this->input->post('date_start'),
	// 			'end_date'              => $this->input->post('date_end'),
	// 			'description'           => $this->input->post('description'),
	// 			'registration_deadline' => $this->input->post('registration_deadline'),
	// 			'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
	// 			'organizer'             => $this->session->userdata('dept_name') ?: $this->session->userdata('org_name'),
	// 			'fines'                 => str_replace(",", "", $this->input->post('fines')),
	// 			'audience'              => $this->input->post('audience')
	// 		];
	// 		// Prepare New Activity Data
	// 		$data = [
	// 			'activity_title'        => trim($this->input->post('title')),
	// 			'start_date'            => $this->input->post('date_start'),
	// 			'end_date'              => $this->input->post('date_end'),
	// 			'description'           => trim($this->input->post('description')),
	// 			'registration_deadline' => $this->input->post('registration_deadline'),
	// 			'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
	// 			'organizer'             => $this->session->userdata('dept_name') ?: $this->session->userdata('org_name'),
	// 			'fines'                 => str_replace(",", "", $this->input->post('fines')),
	// 			'audience'              => $this->input->post('audience')
	// 		];

	// 		// Handle Cover Image Upload
	// 		if (!empty($_FILES['coverUpload']['name'])) {
	// 			$coverImage = $this->upload_file('coverUpload', './assets/coverEvent');
	// 			if (!$coverImage['status']) {
	// 				echo json_encode(['status' => 'error', 'errors' => $coverImage['error']]);
	// 				return;
	// 			}
	// 			$data['activity_image'] = $coverImage['file_name'];
	// 		}

	// 		// Handle QR Code Upload
	// 		if (!empty($_FILES['qrcode']['name'])) {
	// 			$qrCode = $this->upload_file('qrcode', './assets/qrcodeRegistration');
	// 			if (!$qrCode['status']) {
	// 				echo json_encode(['status' => 'error', 'errors' => $qrCode['error']]);
	// 				return;
	// 			}
	// 			$data['qr_code'] = $qrCode['file_name'];
	// 		}
	// 		// Handle QR Code Upload
	// 		if (!empty($_FILES['qrcode']['name'])) {
	// 			$qrCode = $this->upload_file('qrcode', './assets/qrcodeRegistration');
	// 			if (!$qrCode['status']) {
	// 				echo json_encode(['status' => 'error', 'errors' => $qrCode['error']]);
	// 				return;
	// 			}
	// 			$data['qr_code'] = $qrCode['file_name'];
	// 		}

	// 		// Compare Changes
	// 		$changes = $this->get_activity_changes($activity, $data);

	// 		// if (empty($changes)) {
	// 		//     echo json_encode(['status' => 'error', 'errors' => 'No changes detected.']);
	// 		//     return;
	// 		// }

	// 		// Update Activity
	// 		$this->officer->update_activity($activity_id, $data);
	// 		// Update Activity
	// 		$updated = $this->officer->update_activity($activity_id, $data);

	// 		// Fetch Input Data
	// 		$start_datetimes = $this->input->post('start_datetime') ?? [];
	// 		$start_cutoff = $this->input->post('start_cutoff') ?? [];
	// 		$end_datetimes = $this->input->post('end_datetime') ?? [];
	// 		$end_cutoff = $this->input->post('end_cutoff') ?? [];
	// 		$session_types = $this->input->post('session_type') ?? [];
	// 		$schedule_ids = $this->input->post('timeslot_id') ?? [];
	// 		if ($updated) {
	// 			// Log the changes (optional: create this method in your model)
	// 			$this->officer->log_activity_changes([
	// 				'activity_id' => $activity_id,
	// 				'student_id'     => $this->session->userdata('student_id'),
	// 				'changes'     => json_encode($changes),
	// 				'edit_time'  => date('Y-m-d H:i:s')
	// 			]);

	// 			// Handle Schedule Inputs
	// 			$start_datetimes = $this->input->post('start_datetime') ?? [];
	// 			$start_cutoffs   = $this->input->post('start_cutoff') ?? [];
	// 			$end_datetimes   = $this->input->post('end_datetime') ?? [];
	// 			$end_cutoffs     = $this->input->post('end_cutoff') ?? [];
	// 			$session_types   = $this->input->post('session_type') ?? [];
	// 			$schedule_ids    = $this->input->post('timeslot_id') ?? [];

	// 			// Update or Insert Schedules
	// 			foreach ($start_datetimes as $i => $start_datetime) {
	// 				// Ensure array indexes exist before accessing them
	// 				$start_cutoff = $start_cutoff[$i] ?? null;
	// 				$end_datetime = $end_datetimes[$i] ?? null;
	// 				$end_cutoff = $end_cutoff[$i] ?? null;
	// 				$session_type = $session_types[$i] ?? null;
	// 				$schedule_id = $schedule_ids[$i] ?? null;

	// 				// Prepare schedule data
	// 				$schedule_data = [
	// 					'activity_id'  => $activity_id,
	// 					'date_time_in' => date('Y-m-d H:i:s', strtotime($start_datetime)),
	// 					'date_cut_in' => date('Y-m-d H:i:s', strtotime($start_cutoff)),
	// 					'date_time_out' => date('Y-m-d H:i:s', strtotime($end_datetime)),
	// 					'date_cut_out' => date('Y-m-d H:i:s', strtotime($end_cutoff)),
	// 					'slot_name'    => $session_type
	// 				];
	// 				foreach ($start_datetimes as $i => $start_datetime) {
	// 					$schedule_data = [
	// 						'activity_id'   => $activity_id,
	// 						'date_time_in'  => date('Y-m-d H:i:s', strtotime($start_datetime)),
	// 						'date_cut_in'   => date('Y-m-d H:i:s', strtotime($start_cutoffs[$i] ?? null)),
	// 						'date_time_out' => date('Y-m-d H:i:s', strtotime($end_datetimes[$i] ?? null)),
	// 						'date_cut_out'  => date('Y-m-d H:i:s', strtotime($end_cutoffs[$i] ?? null)),
	// 						'slot_name'     => $session_types[$i] ?? ''
	// 					];

	// 					if (!empty($schedule_id)) {
	// 						// Update existing schedule
	// 						$this->officer->update_schedule($schedule_id, $schedule_data);
	// 					} else {
	// 						// Insert new schedule with created_at timestamp
	// 						$schedule_data['created_at'] = date('Y-m-d H:i:s');

	// 						$this->officer->save_schedule($schedule_data);
	// 					}
	// 				}
	// 				if (!empty($schedule_ids[$i])) {
	// 					// Update existing schedule
	// 					$this->officer->update_schedule($schedule_ids[$i], $schedule_data);
	// 				} else {
	// 					// Insert new schedule
	// 					$schedule_data['created_at'] = date('Y-m-d H:i:s');
	// 					$this->officer->save_schedule($schedule_data);
	// 				}
	// 			}

	// 			echo json_encode([
	// 				'status' => 'success',
	// 				'message' => 'Activity Updated Successfully',
	// 				'redirect' => site_url('officer/list-of-activity')
	// 			]);
	// 		} else {
	// 			echo json_encode(['status' => 'error', 'errors' => 'Update failed.']);
	// 		}
	// 	}
	// }

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
			$activity = $this->officer->get_activity($activity_id);
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
				'organizer'             => $this->session->userdata('dept_name') ?: $this->session->userdata('org_name'),
				'fines'                 => str_replace(",", "", $this->input->post('fines')),
				'audience'              => $this->input->post('audience')

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
			$updated = $this->officer->update_activity($activity_id, $data, $editor_id, $changes);


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
					$this->officer->update_schedule($schedule_id, $schedule_data);
					$timeslot_ids[] = $schedule_id;
				} else {
					// Insert new schedule with created_at timestamp
					$schedule_data['created_at'] = date('Y-m-d H:i:s');
					$new_schedule_id = $this->officer->save_schedule($schedule_data);
					$timeslot_ids[] = $new_schedule_id;
				}
			}

			// // Assign students again after update
			// $this->assign_students_to_activity_again($activity_id, $data, $timeslot_ids);

			// Return Success Response
			echo json_encode([
				'status'   => 'success',
				'message'  => 'Activity Updated Successfully',
				'redirect' => site_url('officer/activity-details/' . $activity_id)
			]);
		}
	}

	public function get_edit_logs($activity_id)
	{
		$logs = $this->officer->get_activity_logs($activity_id);
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

	// public function get_edit_logs($activity_id)
	// {
	// 	$logs = $this->officer->get_activity_logs($activity_id);
	// 	echo json_encode($logs);
	// }



	// EVALUATION LIST - PAGE - FINAL CHECK
	public function list_activity_evaluation()
	{
		$data['title'] = 'List of Evaluation';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		// GET THE EVALUATION FOR STUDENT PARLIAMENT
		$data['evaluation'] = $this->officer->evaluation_form();

		// Pass data to the view
		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/eval_list-activity-evaluation', $data);
		$this->load->view('layout/footer', $data);
	}

	// CREATE EVALUATION - PAGE - FINAL CHECK
	public function create_evaluationform()
	{
		$data['title'] = 'Create Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		// Fetch activities based on user role
		$data['activities'] = $this->officer->activity_organized();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/eval_create-evaluation-form', $data);
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
			'title'       => $this->input->post('formtitle'),
			'form_description' => $this->input->post('formdescription'),
			'activity_id' => $this->input->post('activity'),
			'start_date_evaluation'  => $this->input->post('startdate') ?: date('Y-m-d H:i:s'),
			'end_date_evaluation'    => $this->input->post('enddate') ?: date('Y-m-d H:i:s', strtotime('+1 week')),
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

		$formId = $this->officer->saveForm($formData); // Save form data and get form ID

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
			$this->officer->saveFormFields($fieldData); // Save form fields
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
				'redirect_url' => site_url('officer/list-activity-evaluation')
			]);
		}
	}

	// EDITING EVALUATION - PAGE - FINAL CHECK
	public function edit_evaluationform($form_id)
	{
		$data['title'] = 'Edit Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		// Fetch activities based on user role
		$data['activities'] = $this->officer->activity_organized();
		$data['forms'] = $this->officer->get_evaluation_by_id($form_id);

		$form_data = $this->officer->get_evaluation_by_id($form_id);
		$data['form_data'] = $form_data;

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/eval_edit-evaluation-form', $data);
		$this->load->view('layout/footer', $data);
	}

	// // UPDATE EVALUATION
	// public function update_eval($form_id)
	// {
	// 	// Validate form ID
	// 	if (!$form_id) {
	// 		echo json_encode(['success' => false, 'message' => 'Invalid form ID.']);
	// 		return;
	// 	}

	// 	// Validate input fields
	// 	$this->form_validation->set_rules('formtitle', 'Form Title', 'required');
	// 	$this->form_validation->set_rules('formdescription', 'Form Description', 'required');

	// 	if ($this->form_validation->run() === FALSE) {
	// 		echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
	// 		return;
	// 	}

	// 	// Prepare form data
	// 	$formData = [
	// 		'activity_id' => $this->input->post('activity'),
	// 		'title' => $this->input->post('formtitle'),
	// 		'form_description' => $this->input->post('formdescription'),
	// 		'start_date_evaluation' => $this->input->post('startdate'),
	// 		'end_date_evaluation' => $this->input->post('enddate'),
	// 	];

	// 	// Handle Cover Image Upload (if new file is uploaded)
	// 	if (!empty($_FILES['coverUpload']['name'])) {
	// 		$coverImage = $this->upload_file('coverUpload', './assets/theme_evaluation');
	// 		if (!$coverImage['status']) {
	// 			echo json_encode(['success' => false, 'message' => $coverImage['error']]);
	// 			return;
	// 		}
	// 		$formData['cover_theme'] = $coverImage['file_name'];
	// 	}

	// 	// Retrieve form fields (ensure correct format)
	// 	$fields = json_decode($this->input->post('fields'), true);
	// 	if (!is_array($fields)) {
	// 		echo json_encode(['success' => false, 'message' => 'Invalid form fields data.']);
	// 		return;
	// 	}

	// 	// Start database transaction
	// 	$this->db->trans_start();

	// 	// Update main form data
	// 	$this->db->where('form_id', $form_id)->update('forms', $formData);

	// 	// Fetch existing field IDs from database
	// 	$existingFields = $this->db->select('form_fields_id')->where('form_id', $form_id)->get('formfields')->result_array();
	// 	$existingFieldIds = array_column($existingFields, 'form_fields_id');

	// 	$updatedFieldIds = []; // Track updated/new fields

	// 	foreach ($fields as $index => $field) {
	// 		$fieldId = $field['form_fields_id'] ?? null;

	// 		$fieldData = [
	// 			'form_id'     => $form_id,
	// 			'label'       => $field['label'],
	// 			'type'        => $field['type'],
	// 			'placeholder' => $field['placeholder'] ?? null,
	// 			'required'    => !empty($field['required']) ? 1 : 0,
	// 			'order'       => $index + 1,
	// 		];

	// 		if ($fieldId && in_array($fieldId, $existingFieldIds)) {
	// 			// Update existing field
	// 			$this->db->where('form_fields_id', $fieldId)->update('formfields', $fieldData);
	// 			$updatedFieldIds[] = $fieldId;
	// 		} else {
	// 			// Insert new field
	// 			$this->db->insert('formfields', $fieldData);
	// 			$updatedFieldIds[] = $this->db->insert_id();
	// 		}
	// 	}

	// 	// Identify and delete removed fields
	// 	$fieldsToDelete = array_diff($existingFieldIds, $updatedFieldIds);
	// 	if (!empty($fieldsToDelete)) {
	// 		$this->db->where_in('form_fields_id', $fieldsToDelete)->delete('formfields');
	// 	}

	// 	// Commit transaction
	// 	$this->db->trans_complete();

	// 	// Check transaction status
	// 	if ($this->db->trans_status() === FALSE) {
	// 		echo json_encode(['success' => false, 'message' => 'Failed to update the form.']);
	// 	} else {
	// 		echo json_encode([
	// 			'success' => true,
	// 			'message' => 'Form updated successfully.',
	// 			'redirect' => site_url('officer/list-activity-evaluation'),
	// 		]);
	// 	}
	// }

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
				'redirect' => site_url('officer/list-activity-evaluation'),
			]);
		}
	}

	// VIEWING OF EVALUATION FORM - PAGE - FINAL CHECK
	public function view_evaluationform($form_id)
	{
		$data['title'] = 'View Evaluation Form';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		// Fetch activities based on user role
		$data['activities'] = $this->officer->activity_organized();
		$data['forms'] = $this->officer->get_evaluation_by_id($form_id);

		$form_data = $this->officer->get_evaluation_by_id($form_id);
		$data['form_data'] = $form_data;

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/eval_view-evaluation-form', $data);
		$this->load->view('layout/footer', $data);
	}

	// EVALUATION RESPONSES - PAGE - FINAL CHECK
	public function list_evaluation_responses($form_id)
	{
		$data['title'] = 'Evaluation Responses';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['departments'] = $this->officer->get_department();
		$data['forms'] = $this->officer->forms($form_id);

		$responses = $this->officer->get_student_evaluation_responses($form_id);

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

		$data['form_id'] = $form_id; // Add this

		// Load views
		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/eval_list-evaluation-responses', $data);
		$this->load->view('layout/footer', $data);
	}


	public function export_evaluation_responses_pdf($form_id)
	{
		ob_start();

		$this->load->library('pdf');
		$this->load->model('admin');

		$form = $this->admin->forms($form_id);
		$responses = $this->admin->get_student_evaluation_responses($form_id);

		// Reorganize responses by question
		$grouped_by_question = [];
		foreach ($responses as $r) {
			$question = $r->question;
			if (!isset($grouped_by_question[$question])) {
				$grouped_by_question[$question] = [];
			}
			$grouped_by_question[$question][] = [
				'student_id' => $r->student_id,
				'department' => $r->dept_name,
				'response'   => $r->answer
			];
		}

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
		$pdf->AddPage();

		$pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
		$pdf->AddFont('DejaVuSans', 'B', 'DejaVuSans-Bold.php');

		$pdf->SetFont('DejaVuSans', '', 12);
		$pdf->Cell(0, 10, 'Evaluation Responses: ' . $form->title, 0, 1, 'C');
		$pdf->Ln(5);

		foreach ($grouped_by_question as $question => $entries) {
			// Remove duplicates
			$uniqueEntries = [];
			$seen = [];

			foreach ($entries as $entry) {
				$uniqueKey = $entry['student_id'] . '|' . $entry['department'] . '|' . $entry['response'];
				if (!isset($seen[$uniqueKey])) {
					$seen[$uniqueKey] = true;
					$uniqueEntries[] = $entry;
				}
			}

			// Print question header
			$pdf->SetFont('DejaVuSans', 'B', 12);
			$pdf->MultiCell(0, 10, "Question: " . $question, 0, 'L');
			$pdf->Ln(2);

			// Define headers and calculate widths
			$headers = ['Student ID', 'Department', 'Response'];
			$maxWidths = [];
			foreach ($headers as $header) {
				$maxWidths[$header] = $pdf->GetStringWidth($header) + 4;
			}
			$pdf->SetFont('DejaVuSans', '', 12);

			foreach ($uniqueEntries as $entry) {
				$maxWidths['Student ID'] = max($maxWidths['Student ID'], $pdf->GetStringWidth($entry['student_id']) + 4);
				$maxWidths['Department'] = max($maxWidths['Department'], $pdf->GetStringWidth($entry['department']) + 4);

				$responseLines = explode("\n", $entry['response']);
				foreach ($responseLines as $line) {
					$maxWidths['Response'] = isset($maxWidths['Response'])
						? max($maxWidths['Response'], $pdf->GetStringWidth($line) + 4)
						: $pdf->GetStringWidth($line) + 4;
				}
			}

			// Adjust widths to fit page width nicely
			$pageWidth = $pdf->GetPageWidth();
			$leftMargin = $pdf->getLeftMargin();
			$rightMargin = $pdf->getRightMargin();
			$usableWidth = $pageWidth - $leftMargin - $rightMargin;
			$totalWidth = array_sum($maxWidths);

			if ($totalWidth > $usableWidth) {
				$scale = $usableWidth / $totalWidth;
				foreach ($maxWidths as $key => $width) {
					$maxWidths[$key] = $width * $scale;
				}
			} else if ($totalWidth < $usableWidth) {
				$extra = $usableWidth - $totalWidth;
				$colsCount = count($maxWidths);
				$extraPerCol = $extra / $colsCount;
				foreach ($maxWidths as $key => $width) {
					$maxWidths[$key] += $extraPerCol;
				}
			}

			// Print headers
			$pdf->SetFont('DejaVuSans', 'B', 12);
			$pdf->Cell($maxWidths['Student ID'], 7, 'Student ID', 1, 0, 'C');
			$pdf->Cell($maxWidths['Department'], 7, 'Department', 1, 0, 'C');
			$pdf->Cell($maxWidths['Response'], 7, 'Response', 1, 1, 'C');

			// Print data rows
			$pdf->SetFont('DejaVuSans', '', 12);
			foreach ($uniqueEntries as $entry) {
				$responseLinesCount = $pdf->NbLines($maxWidths['Response'], $entry['response']);
				$rowHeight = $responseLinesCount * 7;

				$x = $pdf->GetX();
				$y = $pdf->GetY();

				$pdf->Cell($maxWidths['Student ID'], $rowHeight, $entry['student_id'], 1, 0);
				$pdf->Cell($maxWidths['Department'], $rowHeight, $entry['department'], 1, 0);
				$pdf->MultiCell($maxWidths['Response'], 7, $entry['response'], 1);

				$pdf->SetXY($x, $y + $rowHeight);
			}

			// Calculate average rating
			$sum = 0;
			$count = 0;
			foreach ($uniqueEntries as $entry) {
				if (is_numeric($entry['response'])) {
					$sum += floatval($entry['response']);
					$count++;
				}
			}

			// Add average row if numeric responses exist
			if ($count > 0) {
				$average = $sum / $count;
				$pdf->SetFont('DejaVuSans', 'B', 12);

				$totalWidth = $maxWidths['Student ID'] + $maxWidths['Department'] + $maxWidths['Response'];
				$pdf->SetFont('DejaVuSans', 'B', 12);
				$pdf->Cell($totalWidth, 7, 'Average Rating: ' . number_format($average, 2), 1, 1, 'C');
			}



			$pdf->Ln(10);
		}

		ob_end_clean();
		$filename = 'Evaluation_Responses_' . date('Ymd_His') . '.pdf';
		$pdf->Output('D', $filename);
	}



	// EVALUATION STATISTIC - PAGE - FINAL CHECK
	public function evaluation_statistic($form_id)
	{
		$data['title'] = 'Evaluation Statistic';

		$student_id = $this->session->userdata('student_id');
		$student_id = $this->session->userdata('student_id');
		$data['privilege'] = $this->officer->get_student_privilege();

		// Fetch required data
		$total_attendees = $this->officer->count_attendees($form_id);
		$total_respondents = $this->officer->total_respondents($form_id);
		$rating_summary = $this->officer->rating_summary($form_id);
		$overall_rating = $this->officer->overall_rating($form_id);
		$answer_summary = $this->officer->answer_summary($form_id);

		// Calculate percentage of respondents
		$percentage = ($total_attendees > 0) ? round(($total_respondents / $total_attendees) * 100, 2) : 0;

		// Prepare data to pass to the view
		$data = [
			'users' => $this->officer->get_student($student_id), // Fetching user data based on student ID
			'form_id' => $form_id,
			'total_attendees' => $total_attendees,
			'total_respondents' => $total_respondents,
			'rating_summary' => $rating_summary,
			'overall_rating' => $overall_rating,
			'answer_summary' => $answer_summary,
			'respondent_percentage' => $percentage
		];

		// Load the views
		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/eval_statistic', $data);
		$this->load->view('layout/footer', $data);
	}

	//  EXCUSE APPLICATION

	// FETCHING ACTIVITY - PAGE - FINAL CHECK
	public function list_activity_excuse()
	{
		$data['title'] = 'List of Activity for Excuse Letter';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activities'] = $this->officer->get_activities();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/exc_list-activities-excuseletter', $data);
		$this->load->view('layout/footer', $data);
	}

	// LIST OF APPLICATION PER EVENT - PAGE - FINAL CHECK
	public function list_excuse_letter($activity_id)
	{
		$data['title'] = 'List of Excuse Letter';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activities'] = $this->officer->fetch_application($activity_id);
		$data['letters'] = $this->officer->fetch_letters();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/exc_list-excuse-letter', $data);
		$this->load->view('layout/footer', $data);
	}

	// EXCUSE LETTER PER STUDENT - PAGE 
	public function review_excuse_letter($excuse_id)
	{
		$data['title'] = 'Review Excuse Letter';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['excuse'] = $this->officer->review_letter($excuse_id);

		$this->load->view('layout/header', $data);
		$this->load->view('officer/activity/exc_excuse-letter', $data);
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

		$data = [
			'excuse_id' => $excuse_id,
			'status' => $approvalStatus,
			'remarks' => $remarks
		];

		$result = $this->officer->updateApprovalStatus($data);

		if ($result) {
			if ($approvalStatus === 'Approved') {
				$this->db->where('student_id', $student_id);
				$this->db->where('activity_id', $activity_id);
				$this->db->update('attendance', ['attendance_status' => 'Excused']);
			}

			// =========================== [NOTIF] Add notification ===========================
			$sender_id = $this->session->userdata('student_id') ?? 0;

			// Optional: Get activity title to include in message
			$activity = $this->db->select('activity_title')->from('activity')->where('activity_id', $activity_id)->get()->row();

			$notification_data = [
				'recipient_student_id' => $student_id,
				'recipient_admin_id'   => null,
				'sender_student_id'    => $sender_id,
				'type'                 => 'excuse_approved',
				'reference_id'         => $excuse_id,
				'message'              => 'Your excuse application for the activity "' . ($activity->activity_title ?? 'Unknown') . '" has been ' . strtolower($approvalStatus) . '.',
				'is_read'              => 0,
				'created_at'           => date('Y-m-d H:i:s'),
				'link'                 => base_url('student/excuses')  // Adjust as needed
			];

			$this->db->insert('notifications', $notification_data);
			// =========================== [/NOTIF] End notification code =====================

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

	// COMMUNITY SECTION

	// COMMUNITY - PAGE
	public function community()
	{
		$data['title'] = 'Community';
		$student_id = $this->session->userdata('student_id');

		// FETCH USER DATA
		$data['users'] = $this->officer->get_student($student_id);
		$data['authors'] = $this->officer->get_user();
		// FETCH USER DATA
		$data['users'] = $this->officer->get_student($student_id);
		$data['authors'] = $this->officer->get_user();
		$data['privilege'] = $this->officer->get_student_privilege();

		// Get offset and limit from AJAX request
		$limit = $this->input->post('limit') ?: 5;
		$offset = $this->input->post('offset') ?: 0;

		// GET LIMITED POSTS
		$data['posts'] = $this->officer->get_all_posts();
		foreach ($data['posts'] as &$post) {
			$post->like_count = $this->officer->get_like_count($post->post_id);
			$post->user_has_liked_post = $this->officer->user_has_liked($post->post_id, $student_id);
			$post->comments_count = $this->officer->get_comment_count($post->post_id);
			$post->comments = $this->officer->get_comments_by_post($post->post_id);
			$post->type = 'post';
		}

		// GET LIMITED ACTIVITIES
		$data['activities'] = $this->officer->get_shared_activities();
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
		$data['activities_upcoming'] = $this->officer->get_activities_upcoming();

		// AJAX Request: Return only the next batch
		if ($this->input->is_ajax_request()) {
			$this->load->view('officer/activity/community_feed', $data);
		} else {
			// FULL PAGE LOAD
			$this->load->view('layout/header', $data);
			$this->load->view('officer/activity/community', $data);
			$this->load->view('layout/footer', $data);
		}
	}

	// Method to fetch likes by post ID and return the list of users who liked
	public function view_likes($post_id)
	{
		// Fetch the likes data for the given post ID
		$likes = $this->officer->get_likes_by_post($post_id);

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
		if ($this->officer->user_has_liked($post_id, $student_id)) {
			// User already liked, so we will "unlike" the post
			$this->officer->unlike_post($post_id, $student_id);
			$like_img = base_url() . 'assets/img/icons/spot-illustrations/like-inactive.png';
			$like_text = 'Like';
		} else {
			// User has not liked the post yet, so we will "like" the post
			$this->officer->like_post($post_id, $student_id);
			$like_img = base_url() . 'assets/img/icons/spot-illustrations/like-active.png';
			$like_text = 'Liked';
		}

		// Get the updated like count
		$new_like_count = $this->officer->get_like_count($post_id);

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

				$result = $this->officer->add_comment($data);
				if ($result) {
					$post_id = $this->input->post('post_id');

					// Fetch updated comment count
					$comments_count = $this->officer->get_comment_count($post_id);

					// Fetch the newly added comment only
					$new_comment = $this->officer->get_latest_comment($post_id); // Ensure this function fetches only the latest

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
		$deleted = $this->officer->delete_post($post_id);

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
			$result = $this->officer->update_is_shared($activity_id);

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

		$organizer = $this->session->userdata('dept_id') ?: $this->session->userdata('org_id');

		if ($this->input->post()) {
			$this->form_validation->set_rules('content', 'Content', 'required');

			if ($this->form_validation->run() == FALSE) {
				$response = [
					'status' => 'error',
					'errors' => validation_errors()
				];
				echo json_encode($response);
				return;
			}

			// Set timezone to Asia/Manila
			$timezone = new DateTimeZone('Asia/Manila');
			$date = new DateTime('now', $timezone);
			$formatted_time = $date->format('Y-m-d H:i:s');

			$data = [
				'student_id' => $student_id,
				'content' => $this->input->post('content'),
				'privacy' => $this->input->post('privacyStatus'),
				'dept_id' => $this->session->userdata('dept_id') !== null ? $this->session->userdata('dept_id') : null,
				'org_id' => !empty($this->session->userdata('org_id')) ? $this->session->userdata('org_id') : null,
				'created_at' => $formatted_time // Add this line
			];

			if (!empty($_FILES['image']['name'])) {
				$config = [
					'upload_path'   => './assets/post/',
					'allowed_types' => 'gif|jpg|jpeg|png',
					'max_size'      => 2048,
					'encrypt_name'  => TRUE
				];

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('image')) {
					$uploadData = $this->upload->data();
					$data['media'] = $uploadData['file_name'];
				} else {
					$response = [
						'status' => 'error',
						'errors' => $this->upload->display_errors()
					];
					echo json_encode($response);
					return;
				}
			}

			$result = $this->officer->insert_data($data);

			if ($result) {
				// notifications start
				$students = $this->db->select('student_id')->where('role', 'student')->get('users')->result_array();

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
					'redirect' => site_url('officer/community')
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

	// SCANNING AND FACIAL RECOGNITION - PAGE
	public function time_in($activity_id, $timeslot_id)
	{
		$data['title'] = 'Taking Attendance - Time in';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activity'] = $this->officer->get_activity_scan($activity_id, $timeslot_id);
		$data['schedule'] = $this->officer->get_schedule_scan($activity_id, $timeslot_id);

		// Fetching students that belong to activity
		$data['students'] = $this->officer->get_students_realtime_time_in($activity_id, $timeslot_id);

		$this->load->view('layout/header', $data);
		$this->load->view('officer/attendance/scanqr_timein', $data);
		$this->load->view('layout/footer', $data);
	}

	// SCANNING AND FACIAL RECOGNITION - PAGE
	public function time_out($activity_id, $timeslot_id)
	{
		$data['title'] = 'Taking Attendance - Time out';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activity'] = $this->officer->get_activity_scan($activity_id, $timeslot_id);
		$data['schedule'] = $this->officer->get_schedule_scan($activity_id, $timeslot_id);

		// Fetching students that belong to activity
		$data['students'] = $this->officer->get_students_realtime_time_out($activity_id, $timeslot_id);

		$this->load->view('layout/header', $data);
		$this->load->view('officer/attendance/scanqr_timeout', $data);
		$this->load->view('layout/footer', $data);
	}

	// SCANNING AND FACIAL RECOGNITION FUNCTIONALITY
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

	// 	// Update attendance
	// 	$update_data = ['time_in' => $current_datetime];
	// 	$updated = $this->admin->update_attendance_time_in($student_id, $activity_id, $timeslot_id, $update_data);

	// 	if ($updated) {
	// 		return $this->output
	// 			->set_content_type('application/json')
	// 			->set_status_header(200)
	// 			->set_output(json_encode([
	// 				'status' => 'success',
	// 				'message' => "$full_name successfully recorded."
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
		$activity = $this->officer->get_activity_schedule($activity_id);
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
		$student = $this->officer->get_student_by_id($student_id);
		$full_name = $student ? $student->first_name . ' ' . $student->last_name : 'Student';
		$profile = $student ? $student->profile_pic : 'default.jpg';
		$img_url = base_url('assets/profile/' . $profile);

		// Check if student already has time_in
		$existing_attendance = $this->officer->get_attendance_record_time_in($student_id, $activity_id, $timeslot_id);
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
		// 			'updated_at' => date('Y-m-d H:i:s')
		// 		]);
		// 	}
		// }

		// Update attendance
		$update_data = ['time_in' => $current_datetime];
		$updated = $this->officer->update_attendance_time_in($student_id, $activity_id, $timeslot_id, $update_data);

		if ($updated) {
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
					'message' => 'No changes made or record not found.'
				]));
		}
	}

	public function impose_fines_timein()
	{
		// Get raw JSON input and decode it
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, true);

		$activity_id = $input['activity_id'] ?? null;
		$timeslot_id = $input['timeslot_id'] ?? null;

		log_message('debug', "Received activity_id: $activity_id, timeslot_id: $timeslot_id");

		$this->officer->imposeFinesIfAbsentIn($activity_id, $timeslot_id);

		echo json_encode(['status' => 'success']);
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
		$activity = $this->officer->get_activity_schedule($activity_id);
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
		$student = $this->officer->get_student_by_id($student_id);
		$full_name = $student ? $student->first_name . ' ' . $student->last_name : 'Student';
		$profile = $student ? $student->profile_pic : 'default.jpg';
		$img_url = base_url('assets/profile/' . $profile);

		// Check if student already has time_in
		$existing_attendance = $this->officer->get_attendance_record_time_out($student_id, $activity_id, $timeslot_id);
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

		// Update attendance
		$update_data = ['time_out' => $current_datetime];
		$updated = $this->officer->update_attendance_time_out($student_id, $activity_id, $timeslot_id, $update_data);

		if ($updated) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'success',
					'message' => "$full_name successfully recorded.",
					'profile_pic' => $img_url
				]));
		} else {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => 'error',
					'message' => 'No changes made or record not found.'
				]));
		}
	}

	public function impose_fines_timeout()
	{
		// Get raw JSON input and decode it
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, true);

		$activity_id = $input['activity_id'] ?? null;
		$timeslot_id = $input['timeslot_id'] ?? null;

		log_message('debug', "Received activity_id: $activity_id, timeslot_id: $timeslot_id");

		$this->officer->imposeFinesIfAbsentOut($activity_id, $timeslot_id);

		echo json_encode(['status' => 'success']);
	}

	// LISTING OF THE ATTENDEES - PAGE - FINAL LIST
	public function list_activities_attendance()
	{
		$data['title'] = 'List of Activities';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activities'] = $this->officer->get_activities_by_sp();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/attendance/list-activities-attendance', $data);
		$this->load->view('layout/footer', $data);
	}

	// SHOWING ATTENDANCE LIST - PAGE - FINAL LIST
	public function list_attendees($activity_id)
	{
		$data['title'] = 'List of Attendees';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['activities'] = $this->officer->get_activity_specific($activity_id);
		$data['students'] = $this->officer->get_all_students_attendance_by_activity($activity_id);
		$data['timeslots'] = $this->officer->get_timeslots_by_activity($activity_id);
		$data['departments'] = $this->officer->department_selection();


		$data['activity_id'] = $activity_id; // Add this line

		$this->load->view('layout/header', $data);
		$this->load->view('officer/attendance/listofattendees', $data);
		$this->load->view('layout/footer', $data);
	}

	public function export_attendance_pdf($activity_id)
	{
		// Clean output buffer
		ob_clean();

		// Load model
		$this->load->model('Admin_model');

		$status = $this->input->get('status');
		$department = $this->input->get('department');

		// Fetch filtered data
		$students = $this->Admin_model->get_filtered_attendance_by_activity($activity_id, $status, $department);

		// Fetch data
		// $students = $this->Admin_model->get_all_students_attendance_by_activity($activity_id);
		$timeslots = $this->Admin_model->get_timeslots_by_activity($activity_id);
		$activity = $this->Admin_model->get_activity_specific($activity_id);



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


		// Setup PDF
		$pdf = new PDF('P', 'mm', 'Legal');
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->SetMargins(10, 10, 10); // standard margins
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, 'Attendance Report - Activity: ' . ($activity ? $activity['activity_title'] : 'N/A'), 0, 1, 'C');
		$pdf->Ln(5);

		// Base font for measurements
		$pdf->SetFont('Arial', '', 8);

		// Headers for the table
		$header = ['Student ID', 'Name', 'Department'];
		foreach ($timeslots as $slot) {
			$period = strtolower($slot->slot_name);
			$label = $period === 'morning' ? 'AM' : ($period === 'afternoon' ? 'PM' : strtoupper($period));
			$header[] = "$label In";
			$header[] = "$label Out";
		}
		$header[] = 'Status';

		// Measure max content width
		$max_widths = [];
		foreach ($header as $col) {
			$max_widths[] = $pdf->GetStringWidth($col);
		}

		// Measure content for each row dynamically
		foreach ($students as $student) {
			$dataRow = [
				$student['student_id'],
				$student['name'],
				$student['dept_name']
			];

			foreach ($timeslots as $slot) {
				$period = strtolower($slot->slot_name);
				$dataRow[] = $student['in_' . $period] ?? 'No Data';
				$dataRow[] = $student['out_' . $period] ?? 'No Data';
			}

			$dataRow[] = $student['status'];

			// Update column widths dynamically based on content
			foreach ($dataRow as $i => $cell) {
				$max_widths[$i] = max($max_widths[$i], $pdf->GetStringWidth($cell));
			}
		}

		// Add padding to widths
		$padding = 6;
		foreach ($max_widths as &$width) {
			$width += $padding;
		}

		// Scale total width to fit the Letter page width (usable width = 196mm)
		$total_content_width = array_sum($max_widths);
		$usable_page_width = 196;
		$scaling_factor = $usable_page_width / $total_content_width;

		// Scale column widths
		foreach ($max_widths as &$width) {
			$width = round($width * $scaling_factor, 2);
		}

		// Output table header
		$pdf->SetFont('Arial', 'B', 9);
		foreach ($header as $i => $colName) {
			$pdf->Cell($max_widths[$i], 8, $colName, 1, 0, 'C');
		}
		$pdf->Ln();

		// Output table rows
		$pdf->SetFont('Arial', '', 8);
		foreach ($students as $student) {
			$dataRow = [
				$student['student_id'],
				$student['name'],
				$student['dept_name']
			];

			foreach ($timeslots as $slot) {
				$period = strtolower($slot->slot_name);
				$dataRow[] = $student['in_' . $period] ?? 'No Data';
				$dataRow[] = $student['out_' . $period] ?? 'No Data';
			}

			$dataRow[] = $student['status'];

			// Output each row
			foreach ($dataRow as $i => $cell) {
				$pdf->Cell($max_widths[$i], 8, $cell, 1, 0, 'L');
			}
			$pdf->Ln();
		}

		// Output PDF
		$filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $activity['activity_title']) . '_attendance_report.pdf';
		$pdf->Output('I', $filename);
	}


	//CONNECTING ATTENDANCE AND FINES
	public function update_fines() {}

	// LIST OF FINES - PAGE
	public function list_fines()
	{
		$data['title'] = 'List of Fines';

		$student_id = $this->session->userdata('student_id');

		$data['users'] = $this->officer->get_student($student_id);
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['departments'] = $this->officer->get_department();

		$data['fines'] = $this->officer->flash_fines();

		$this->load->view('layout/header', $data);
		$this->load->view('officer/fines/listoffines', $data);
		$this->load->view('layout/footer', $data);
	}


	// FINES PAYMENTS CONFIRMATION
	public function confirm_payment()
	{
		$student_id = $this->input->post('student_id');
		$total_fines = $this->input->post('total_fines');
		$mode_of_payment = $this->input->post('mode_of_payment');
		$reference_number = trim($this->input->post('reference_number'));


		$academic_year = $this->input->post('academic_year'); // get from POST form
		$semester = $this->input->post('semester');           // get from POST form

		$officer_student_id = $this->session->userdata('student_id');

		// Load organizer info
		$this->db->select('users.role, users.is_officer_dept, users.dept_id, department.dept_name');
		$this->db->from('users');
		$this->db->join('department', 'department.dept_id = users.dept_id', 'left');
		$this->db->where('users.student_id', $officer_student_id);
		$user_info = $this->db->get()->row_array();

		$organizer = null;

		if ($user_info) {
			if ($user_info['role'] === 'Officer' && $user_info['is_officer_dept'] === 'Yes') {
				$organizer = $user_info['dept_name'];
			} else {
				$this->db->select('organization.name');
				$this->db->from('student_org');
				$this->db->join('organization', 'organization.org_id = student_org.org_id');
				$this->db->where('student_org.student_id', $officer_student_id);
				$this->db->where('student_org.is_officer', 'Yes');
				$org_info = $this->db->get()->row_array();

				if ($org_info) {
					$organizer = $org_info['name'];
				}
			}
		}

		if (!$organizer) {
			echo json_encode(['status' => 'error', 'message' => 'Organizer info not found for this officer.']);
			return;
		}

		// Get fine summary with organizer
		$record = $this->officer->get_fine_summary($student_id, $organizer, $academic_year, $semester);

		if (!$record) {
			echo json_encode(['status' => 'error', 'message' => 'No fines summary found.']);
			return;
		}

		// Load Notification model if not already
		if (!isset($this->Notification_model)) {
			$this->load->model('Notification_model');
		}

		$admin_student_id = $this->session->userdata('student_id'); // sender

		// CASE: Reference number mismatch
		if ($reference_number !== $record->reference_number_students) {
			$this->officer->update_fines_summary_with_organizer($student_id, $organizer, $academic_year, $semester, [
				'reference_number_admin' => $reference_number,
				'fines_status' => 'Pending',
				'mode_payment' => $mode_of_payment,
				'last_updated' => date('Y-m-d H:i:s')
			]);

			$updated = $this->Notification_model->add_notification(
				$student_id,
				$admin_student_id,
				'fine_payment_rejected',
				$record->summary_id,
				'Your fine payment could not be confirmed. Please double-check your reference number.'
			);

			echo json_encode(['status' => 'warning', 'message' => 'Reference mismatch. Payment on hold.']);
			return;
		}

		// CASE: REFERENCE MATCH - APPROVED
		$updated = $this->officer->update_fines_summary_receipt($student_id, [
			'reference_number_admin' => $reference_number,
			'fines_status' => 'Paid',
			'mode_payment' => $mode_of_payment,
			'last_updated' => date('Y-m-d H:i:s')
		], $academic_year, $semester); // ADDED: pass academic_year and semester to update method

		if ($updated) {
			$summary_id = $record->summary_id;
			$this->generate_and_store_fine_receipt($summary_id);

			$this->Notification_model->add_notification(
				$student_id,
				$admin_student_id,
				'fine_payment_approved',
				$summary_id,
				'has approved your fine payment and marked as paid.',
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
				->get('users')->row();
			if ($approver) {
				$approver_name = strtoupper(trim($approver->first_name . ' ' . $approver->middle_name . ' ' . $approver->last_name));
			}
		}

		// Determine organizer

		if (!empty($summary->approved_by)) {
			$officer_student_id = $summary->approved_by;

			$this->db->select('users.role, users.is_officer_dept, users.dept_id, department.dept_name');
			$this->db->from('users');
			$this->db->join('department', 'department.dept_id = users.dept_id', 'left');
			$this->db->where('users.student_id', $officer_student_id);
			$user_info = $this->db->get()->row_array();

			if ($user_info) {
				if ($user_info['role'] === 'Officer' && $user_info['is_officer_dept'] === 'Yes') {
					$organizer = $user_info['dept_name'];
				} else {
					$this->db->select('organization.name');
					$this->db->from('student_org');
					$this->db->join('organization', 'organization.org_id = student_org.org_id');
					$this->db->where('student_org.student_id', $officer_student_id);
					$this->db->where('student_org.is_officer', 'Yes');
					$org_info = $this->db->get()->row_array();

					if ($org_info) {
						$organizer = $org_info['name'];
					}
				}
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

		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, 'FINE PAYMENT RECEIPT', 0, 1, 'C');
		$pdf->Ln(5);

		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(50, 8, 'Receipt No:', 0, 0);
		$pdf->Cell(0, 8, strtoupper($summary_id), 0, 1);
		$pdf->Cell(50, 8, 'Date:', 0, 0);
		$pdf->Cell(0, 8, date('F j, Y'), 0, 1);
		$pdf->Ln(5);

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Student ID:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $receipt_data['student_id'], 0, 1);

		$student = $this->db->select('first_name, last_name')->from('users')->where('student_id', $receipt_data['student_id'])->get()->row();
		$student_name = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown';

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Student Name:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $student_name, 0, 1);

		$pdf->Ln(3);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Source of Fines:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $organizer, 0, 1);

		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(120, 8, 'Description (Activity & Slot)', 1, 0, 'C');
		$pdf->Cell(40, 8, 'Amount (PHP)', 1, 1, 'C');
		$pdf->SetFont('Arial', '', 10);

		$activity_grouped = [];
		foreach ($fines_data as $f) {
			$aid = $f['activity_id'];
			if (!isset($activity_grouped[$aid])) $activity_grouped[$aid] = [];
			$activity_grouped[$aid][] = $f;
		}

		foreach ($activity_grouped as $activity_id => $entries) {
			$used_slots = [];
			foreach ($entries as $f) $used_slots[$f['slot_name']] = true;

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
					if ($amount <= 0) continue;

					$time_label = isset($slot_times[$label]) ? ' (' . date('h:i A', strtotime($slot_times[$label])) . ')' : ' (No record)';
					$desc = $label . $time_label . ' - ' . $entries[0]['activity_title'];
					$pdf->Cell(120, 8, $desc, 1, 0);
					$pdf->Cell(40, 8, 'Php ' . number_format($amount, 2), 1, 1, 'R');
				}
			}
		}

		$total_amount = $receipt_data['total_fines'] ?? 0;
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(120, 8, 'Total Amount:', 1, 0, 'R');
		$pdf->Cell(40, 8, 'Php ' . number_format($total_amount, 2), 1, 1, 'C');

		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(50, 8, 'Payment Type:', 0, 0);
		$pdf->Cell(0, 8, $summary->mode_payment, 0, 1);

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(50, 8, 'Approved By:', 0, 0);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 8, $approver_name, 0, 1);

		$pdf->Ln(15);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 8, 'Verification Code:', 0, 1, 'C');
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, $verification_code, 0, 1, 'C');
		$pdf->Ln(10);
		$pdf->SetFont('Arial', 'I', 8);
		$pdf->Cell(0, 8, 'Use this code on the receipt verification page to validate.', 0, 1, 'C');

		$pdf->Output($filepath, 'F');

		$this->db->where('summary_id', $summary_id)->update('fines_summary', [
			'generated_receipt' => $filename,
			'verification_code' => $verification_code,
			'last_updated' => date('Y-m-d H:i:s')
		]);
	}





	//CASH PAYMENT START

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
				redirect('your_redirect_page_here');
				return;
			}
		}

		// Fetch the summary record using summary_id
		$summary = $this->db->where('summary_id', $summary_id)
			->get('fines_summary')
			->row();

		if (!$summary || $summary->fines_status === 'Paid') {
			$this->session->set_flashdata('error', 'No unpaid fines found or already paid.');
			redirect('your_redirect_page_here');
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

		$organizer_name = $this->session->userdata('dept_name') ?: $this->session->userdata('org_name');

		$this->db->select('summary_id, total_fines, fines_status');
		$this->db->from('fines_summary');
		$this->db->where('student_id', $student_id);
		$this->db->where('fines_status !=', 'Paid');

		if ($organizer_name) {
			$this->db->where('organizer', $organizer_name);
		}

		$summary = $this->db->get()->row();

		if (!$summary) {
			echo json_encode([
				'success' => false,
				'message' => 'No unpaid fines for this student under your organizer.'
			]);
			return;
		}

		echo json_encode([
			'success' => true,
			'summary_id' => $summary->summary_id,
			'total_fines' => $summary->total_fines
		]);
	}






	// CASH PAYMENT END






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



	public function export_fines_pdf()
	{
		// Load FPDF
		$this->load->library('fpdf');

		// Get organizer and dept/org IDs from session
		$dept_id = $this->session->userdata('dept_id');
		$org_id = $this->session->userdata('org_id');
		$organizer = $this->session->userdata('dept_name') ?: $this->session->userdata('org_name');

		// Build the query like flash_fines()
		$this->db->select('
    users.student_id,
    users.first_name,
    users.last_name,
    users.year_level,
    department.dept_name,
    organization.org_name,
    fines_summary.*,
    fines.fines_reason,
    fines.fines_amount,
    activity.activity_title,
    activity.activity_id,
    activity.start_date
');

		$this->db->from('fines_summary');
		$this->db->join('users', 'users.student_id = fines_summary.student_id', 'left');
		$this->db->join('department', 'department.dept_id = users.dept_id', 'left');
		$this->db->join('student_org', 'student_org.student_id = users.student_id', 'left');
		$this->db->join('organization', 'organization.org_id = student_org.org_id', 'left');

		$this->db->join('fines', 'fines.student_id = fines_summary.student_id', 'left');
		$this->db->join('activity', 'activity.activity_id = fines.activity_id', 'left');

		$organizer = $this->session->userdata('dept_name') ?: $this->session->userdata('org_name');
		$dept_id = $this->session->userdata('dept_id');
		$org_id = $this->session->userdata('org_id');

		$this->db->where('activity.organizer', $organizer);

		$this->db->group_start();
		if (!empty($dept_id)) {
			$this->db->where('users.dept_id', $dept_id);
		}
		if (!empty($org_id)) {
			$this->db->or_where('student_org.org_id', $org_id);
		}
		$this->db->group_end();

		$this->db->order_by('users.student_id');
		$fines_data = $this->db->get()->result();



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

		// Initialize PDF
		// Load your PDF library (make sure your PDF class is loaded correctly)
		$pdf = new PDF();
		$pdf->headerImage = $headerImage;
		$pdf->footerImage = $footerImage;
		$pdf->AddPage('L', 'Letter');
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->Cell(0, 8, 'Student Parliament Fines Report', 0, 1, 'C');
		$pdf->Ln(5);

		// Collect unique activity titles
		$activities = [];
		foreach ($fines_data as $fine) {
			$title = trim($fine->activity_title);
			if (!in_array($title, $activities)) {
				$activities[] = $title;
			}
		}

		// Calculate dynamic column widths
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

		$other_columns_width = 15 + 15; // Total Fines + Status
		$total_width = $student_id_width + $name_width + $dept_name_width + array_sum($activity_widths) + $other_columns_width;
		$page_width = $pdf->GetPageWidth() - $pdf->getLeftMargin() - $pdf->getRightMargin();

		// Scale columns if too wide
		if ($total_width > $page_width) {
			$scale = $page_width / $total_width;
			$student_id_width *= $scale;
			$name_width *= $scale;
			$dept_name_width *= $scale;
			foreach ($activities as $act) {
				$activity_widths[$act] *= $scale;
			}
			$total_width = $page_width; // recalc after scaling
		}

		// Sort fines data
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

			// New Department
			if ($fine->dept_name !== $current_dept) {
				$current_dept = $fine->dept_name;
				$current_year = null;

				$pdf->Ln(5);
				$pdf->SetFillColor(173, 216, 230);
				$pdf->SetFont('Arial', 'B', 7);

				$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
				$pdf->MultiCell($total_width, 8, "Department: " . $current_dept, 1, 'L', true);

				// Table Header
				$pdf->SetFont('Arial', 'B', 6);
				$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());

				$pdf->Cell($student_id_width, 8, 'Student ID', 1, 0, 'C');
				$pdf->Cell($name_width, 8, 'Name', 1, 0, 'C');
				$pdf->Cell($dept_name_width, 8, 'Department', 1, 0, 'C');
				foreach ($activities as $act) {
					$pdf->Cell($activity_widths[$act], 8, $act, 1, 0, 'C');
				}
				$pdf->Cell(15, 8, 'Total Fines', 1, 0, 'C');
				$pdf->Cell(15, 8, 'Status', 1, 1, 'C');
			}

			// New Year Level
			if ($fine->year_level !== $current_year) {
				$current_year = $fine->year_level;
				$pdf->SetFont('Arial', 'B', 6);
				$pdf->SetFillColor(224, 235, 255);

				$pdf->SetX(($page_width - $total_width) / 2 + $pdf->getLeftMargin());
				$pdf->MultiCell($total_width, 6, "Year Level: " . $current_year, 1, 'L', true);
			}

			// New Student
			if ($fine->student_id !== $current_student_id) {
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
					$status = $total_fines > 0 ? 'Unpaid' : 'Paid';
					$pdf->Cell(15, 8, 'PHP ' . number_format($total_fines, 2), 1, 0, 'R');
					$pdf->Cell(15, 8, $status, 1, 1, 'C');
				}

				$current_student_id = $fine->student_id;
				$student_fines = [];
				$prev_fine = $fine;
			}

			// Accumulate fines per activity title
			$student_fines[$title] = isset($student_fines[$title])
				? $student_fines[$title] + $fine->fines_amount
				: $fine->fines_amount;
		}

		// Output last student
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
			$status = $total_fines > 0 ? 'Unpaid' : 'Paid';
			$pdf->Cell(15, 8, 'PHP ' . number_format($total_fines, 2), 1, 0, 'R');
			$pdf->Cell(15, 8, $status, 1, 1, 'C');
		}

		$pdf->Output('I', 'Fines_Report_' . date('Ymd') . '.pdf');
	}

	// OTHER PAGES

	//PROFILE SETTINGS - PAGE
	public function profile_settings()
	{
		$data['title'] = 'Profile Settings';

		$student_id = $this->session->userdata('student_id');

		// Get user and their organizations
		$student_details = $this->officer->get_user_profile();
		// Get user and their organizations
		$student_details = $this->officer->get_user_profile();
		$data['privilege'] = $this->officer->get_student_privilege();

		if ($student_details) {
			$data['student_details'] = $student_details;
			$data['organizations'] = $student_details->organizations ?? [];
		} else {
			$data['student_details'] = null;
			$data['organizations'] = [];
		}

		$data['users'] = $this->officer->get_student($student_id);

		$this->load->view('layout/header', $data);
		$this->load->view('officer/profile-settings', $data);
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
		$current_pic = $this->officer->get_profile_pic($student_id);

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
			$this->officer->update_profile_pic($student_id, $update_data);

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

		$updated = $this->officer->update_student($student_id, $data);

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

		$student = $this->officer->get_by_id($student_id);

		if (!$student || !password_verify($old_password, $student->password)) {
			echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
			return;
		}

		$hashed = password_hash($new_password, PASSWORD_DEFAULT);
		$this->officer->update_password($student_id, $hashed);

		echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
	}

	public function get_qr_code_by_student()
	{
		// Get student_id from session
		if ($this->session->has_userdata('student_id')) {
			$student_id = $this->session->userdata('student_id');

			// Retrieve QR code from the database (ensure you have the correct query in place)
			$qr_code = $this->officer->get_qr_code($student_id);

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

	// MANAGE OFFICERS AND PRIVILEGE - PAGE
	public function list_officers()
	{
		$data['title'] = 'List of Officer';

		$student_id = $this->session->userdata('student_id');

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);
		$data['privilege'] = $this->officer->get_student_privilege();

		$data['officers'] = $this->officer->get_officer();

		$data['privileges'] = $this->officer->manage_privilege();


		$this->load->view('layout/header', $data);
		$this->load->view('officer/manage-officers', $data);
		$this->load->view('layout/footer', $data);
	}

	public function update_privileges()
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

		$update = $this->officer->update_privileges($sanitized_data);

		echo json_encode(['success' => $update]);
	}

	// GENERAL SETTINGS
	public function general_settings()
	{
		$data['title'] = 'General Settings';

		$student_id = $this->session->userdata('student_id');
		$role       = $this->session->userdata('role');

		// Get full user record from users table (includes is_officer, is_officer_dept, dept_id)
		$user = $this->officer->get_student($student_id);
		$data['users'] = $user;

		$logo_targets = [];

		if ($role === 'Admin') {
			$logo_targets[] = ['value' => 'student_parliament', 'label' => 'Student Parliament'];
		} elseif (isset($user['is_officer']) && $user['is_officer'] === 'Yes') {
			$org = $this->admin->get_organization_by_student($student_id);
			if ($org && !empty($org->org_name)) {
				$logo_targets[] = ['value' => 'organization', 'label' => $org->org_name];
			}
		}

		if (isset($user['is_officer_dept']) && $user['is_officer_dept'] === 'Yes') {
			if (!empty($user['dept_id'])) {
				$dept = $this->admin->get_department_by_id($user['dept_id']);
				if ($dept && !empty($dept->dept_name)) {
					$logo_targets[] = ['value' => 'department', 'label' => $dept->dept_name];
				}
			}
		}

		$data['logo_targets'] = $logo_targets;

		// Get privilege (use student_id if needed)
		$data['privilege'] = $this->officer->get_student_privilege($student_id);

		// Load views
		$this->load->view('layout/header', $data);
		$this->load->view('officer/general_settings', $data);
		$this->load->view('layout/footer', $data);
	}



	// IMPORTING LIST
	public function import_list()
	{
		require_once FCPATH . 'vendor/autoload.php'; // Correct PhpSpreadsheet autoload path

		if ($_FILES['import_file']['error'] == UPLOAD_ERR_OK) {
			$fileTmpPath = $_FILES['import_file']['tmp_name'];
			$fileName = $_FILES['import_file']['name'];
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);

			try {
				// Read data based on file type
				if ($extension == 'csv') {
					$data = $this->readCSV($fileTmpPath);
				} elseif ($extension == 'xlsx') {
					$data = $this->readXLSX($fileTmpPath);
				} else {
					echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload a CSV or XLSX file.']);
					return;
				}

				// Insert into database
				if (!empty($data)) {
					$this->officer->insert_batch($data);
					echo json_encode(['success' => true, 'message' => 'File uploaded and data imported successfully!']);
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

				if (count($row) >= 6) {
					$studentId = $row[0];
					$firstName = $row[1];
					$generatedPassword = password_hash(substr($studentId, -4) . strtolower($firstName), PASSWORD_DEFAULT);

					$data[] = [
						'student_id'   => $studentId,
						'first_name'   => $firstName,
						'middle_name'  => $row[2],
						'last_name'    => $row[3],
						'sex'          => $row[4],
						'year_level'   => $row[5],
						'email'        => $row[6],
						'password'     => $generatedPassword,
						'dept_id'      => $row[7],
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

			if (count($row) >= 6) {
				$studentId = $row[0];
				$firstName = $row[1];
				$generatedPassword = password_hash(substr($studentId, -4) . strtolower($firstName), PASSWORD_DEFAULT);

				$data[] = [
					'student_id'   => $studentId,
					'first_name'   => $firstName,
					'middle_name'  => $row[2],
					'last_name'    => $row[3],
					'sex'          => $row[4],
					'year_level'   => $row[5],
					'email'        => $row[6],
					'password'     => $generatedPassword,
					'dept_id'      => $row[7],
				];
			}
		}
		return $data;
	}

	// GENERATING QR
	public function generate_bulk_qr()
	{
		require_once(APPPATH . 'libraries/phpqrcode.php');

		$students = $this->officer->get_students_without_qr(); // Get all students without QR codes
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
			$this->officer->assign_qr($student_id, $qrBase64);
			$count++;
		}

		// Return a success response with the count of generated QR codes
		echo json_encode(['status' => 'success', 'count' => $count]);
	}

	public function about()
	{
		$data['title'] = 'About';

		$student_id = $this->session->userdata('student_id');
		$student_id = $this->session->userdata('student_id');
		$data['privilege'] = $this->officer->get_student_privilege();

		// FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
		$data['users'] = $this->officer->get_student($student_id);


		$this->load->view('layout/header', $data);
		$this->load->view('officer/about', $data);
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
		$data['users'] = $this->officer->get_student($student_id);

		// Get privilege (use student_id if needed)
		$data['privilege'] = $this->officer->get_student_privilege($student_id);


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

		// Check registration receipts first
		$receipt = $this->Student_model->get_registration_by_code($verification_code);

		if ($receipt) {
			$receipt['receipt_type'] = 'Registration Payment';
		} else {
			// If not found, check fines
			$receipt = $this->Student_model->get_fines_by_code($verification_code);
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
			echo json_encode(['status' => 'error', 'message' => 'Invalid verification code. No receipt found.']);
		}
	}





	//VERIFY RECEIPT END











	// // Function to Compute and Update Total Fine
	// private function compute_and_update_fine($student_id, $activity_id)
	// {
	//     $this->db->query(
	//         "
	//         UPDATE fines
	//         SET total_amount = (
	//             COALESCE(am_in, 0) + COALESCE(am_out, 0) + 
	//             COALESCE(pm_in, 0) + COALESCE(pm_out, 0)
	//         )
	//         WHERE student_id = ? AND activity_id = ?",
	//         [$student_id, $activity_id]
	//     );
	// }


	// <====== FINES MONITORING =====>
	// public function list_activities_fines()
	// {
	//     $data['title'] = 'All Activities';

	//     // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
	//     $data['users'] = $this->admin->get_student($student_id);

	//     // CROSS CHECKING OF THE WHERE THE USER IS ADMIN
	//     $data['organization'] = $this->admin->admin_org();
	//     $data['department'] = $this->admin->admin_dept();

	//     $data['activities'] = $this->admin->get_activities();

	//     $this->load->view('layout/header', $data);
	//     $this->load->view('admin/fines/list-activities-fines', $data);
	//     $this->load->view('layout/footer', $data);
	// }

	// public function list_department_fines($activity_id)
	// {
	//     $data['title'] = 'List of Department';

	//     $student_id = $this->session->userdata('student_id');

	//     // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
	//     $data['users'] = $this->admin->get_student($student_id);

	//     // FETCHING ALL THE DEPARTMENT
	//     $data['department'] = $this->admin->get_department();

	//     $data['activity_id'] = $activity_id;

	//     $this->load->view('layout/header', $data);
	//     $this->load->view('admin/fines/department', $data);
	//     $this->load->view('layout/footer', $data);
	// }


	//======> FINES


}

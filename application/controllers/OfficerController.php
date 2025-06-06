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
    function Header()
    {
        // Get the full path of the logo image
        $logoPath = APPPATH . 'third_party/header.jpg'; // Ensure the correct path

        // Get page width dynamically
        $pageWidth = $this->GetPageWidth();

        // Set the image to cover the full width (assuming A4 width is 210mm)
        $this->Image($logoPath, 0, 0, $pageWidth, 40); // Adjust height as needed

        // Move the cursor below the image to avoid overlapping
        $this->SetY(45); // Adjust if necessary
    }

    function Footer()
    {
        // Get the full path of the footer image
        $footerImage = APPPATH . 'third_party/footer.jpg';

        // Get page width and height dynamically
        $pageWidth = $this->GetPageWidth();
        $pageHeight = $this->GetPageHeight();
        $footerHeight = 20; // Adjust footer height as needed

        // Position the footer at the exact bottom with no margin
        $this->Image($footerImage, 0, $pageHeight - $footerHeight, $pageWidth, $footerHeight);
    }
}
class OfficerController extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Officer_model', 'officer');

        if (!$this->session->userdata('student_id')) {
            redirect(site_url('login'));
        }
    }


    public function officer_dashboard()
    {

        $data['title'] = 'Dashboard';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);


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

        // ACTIVITY ORGANIZED
        $data['activity_count'] = $this->officer->get_current_semester_count_organized();

        // TOTAL FINES
        $data['fines'] = $this->officer->get_total_fines();


        $this->load->view('layout/header', $data);
        $this->load->view('officer/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }


    // ACTIVITY MANAGEMENT

    // CREATING ACTIVITY - PAGE
    public function create_activity()
    {
        $data['title'] = 'Create Activity';

        $student_id = $this->session->userdata('student_id');

        $data['users'] = $this->officer->get_student($student_id);

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

            // Prepare Activity Data
            $data = [
                'activity_title'        => $this->input->post('title'),
                'start_date'            => $this->input->post('date_start'),
                'end_date'              => $this->input->post('date_end'),
                'description'           => $this->input->post('description'),
                'registration_deadline' => $this->input->post('registration_deadline'),
                'registration_fee'      => str_replace(",", "", $this->input->post('registration_fee')),
                'organizer'             => $this->session->userdata('dept_name'),
                'fines'                 => str_replace(",", "", $this->input->post('fines')),
                'audience'             => implode(',', $this->input->post('audience')),
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

    // ASSIGN STUDENT IF THEIR ARE THE AUDIENCE
    private function assign_students_to_activity($activity_id, $data, $timeslot_ids = [])
    {
        // Select student IDs by joining users and departments based on department name
        $this->db->select('u.student_id');
        $this->db->from('users u');
        $this->db->join('department d', 'u.dept_id = d.dept_id');

        // Filter by department name if provided and not "all"
        if (!empty($data['audience']) && strtolower($data['audience']) !== 'all') {
            $this->db->where('d.dept_name', $data['audience']); // assuming 'dept_name' is the correct column
        }

        $students = $this->db->get()->result();

        // Insert each student into attendance for each timeslot
        foreach ($students as $student) {
            foreach ($timeslot_ids as $timeslot_id) {
                $this->db->insert('attendance', [
                    'activity_id'  => $activity_id,
                    'timeslot_id'  => $timeslot_id,
                    'student_id'   => $student->student_id
                ]);
            }

            // Insert into fines once per student per activity
            foreach ($timeslot_ids as $timeslot_id) {
                $this->db->insert('fines', [
                    'activity_id'  => $activity_id,
                    'timeslot_id'  => $timeslot_id,
                    'student_id'   => $student->student_id
                ]);
            }
        }

        foreach ($students as $student) {
            $this->db->insert('fines_summary', [
                'student_id'   => $student->student_id
            ]);
        }
    }

    // FETCHING ACTIVITY (STUDENT PARLIAMENT) - PAGE
    public function list_activity()
    {
        $data['title'] = 'List of Activities';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

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



    // SHARING ACTIVITY FROM THE ACTIVITY DETAILS
    public function share_activity()
    {
        // Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['activity_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $activity_id = $data['activity_id'];

        // Example: Mark activity as shared in the database
        $this->db->set('is_shared', 'Yes')->where('activity_id', $activity_id)->update('activity');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
        }
    }


    //UNSHARE ACTIVITY

    public function unshare_activity()
    {
        $activity_id = $this->input->post('activity_id');

        if ($activity_id) {
            $this->db->where('activity_id', $activity_id);
            $this->db->update('activity', ['is_shared' => 'No']);

            echo json_encode(['status' => 'success']);
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
        $data['users'] = $this->officer->get_student($student_id);

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
                'organizer'             => $this->session->userdata('dept_name'),
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

            // Update Activity
            $this->officer->update_activity($activity_id, $data);

            // Fetch Input Data
            $start_datetimes = $this->input->post('start_datetime') ?? [];
            $start_cutoff = $this->input->post('start_cutoff') ?? [];
            $end_datetimes = $this->input->post('end_datetime') ?? [];
            $end_cutoff = $this->input->post('end_cutoff') ?? [];
            $session_types = $this->input->post('session_type') ?? [];
            $schedule_ids = $this->input->post('timeslot_id') ?? [];

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
                } else {
                    // Insert new schedule with created_at timestamp
                    $schedule_data['created_at'] = date('Y-m-d H:i:s');

                    $this->officer->save_schedule($schedule_data);
                }
            }

            // Return Success Response
            echo json_encode([
                'status'   => 'success',
                'message'  => 'Activity Updated Successfully',
                'redirect' => site_url('officer/list-of-activity')
            ]);
        }
    }

    // END EDITING OF ACTIVITY

    // EVALUATION LIST - PAGE
    public function list_activity_evaluation()
    {
        $data['title'] = 'List of Evaluation';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        // GET THE EVALUATION FOR STUDENT PARLIAMENT
        $data['evaluation'] = $this->officer->evaluation_form();

        // Pass data to the view
        $this->load->view('layout/header', $data);
        $this->load->view('officer/activity/eval_list-activity-evaluation', $data);
        $this->load->view('layout/footer', $data);
    }

    // CREATE EVALUATION - PAGE
    public function create_evaluationform()
    {
        $data['title'] = 'Create Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

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

        $this->db->trans_complete(); // Complete transaction

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Failed to create the form.']);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Form created successfully.',
                'redirect_url' => site_url('officer/list-activity-evaluation')
            ]);
        }
    }

    // EDITING EVALUATION - PAGE
    public function edit_evaluationform($form_id)
    {
        $data['title'] = 'Edit Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        // Fetch activities based on user role
        $data['activities'] = $this->officer->activity_organized();
        $data['forms'] = $this->officer->get_evaluation_by_id($form_id);

        $form_data = $this->officer->get_evaluation_by_id($form_id);
        $data['form_data'] = $form_data;

        $this->load->view('layout/header', $data);
        $this->load->view('officer/activity/eval_edit-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    // UPDATE EVALUATION
    public function update_eval($form_id)
    {
        // Validate form ID
        if (!$form_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid form ID.']);
            return;
        }

        // Validate input fields
        $this->form_validation->set_rules('formtitle', 'Form Title', 'required');
        $this->form_validation->set_rules('formdescription', 'Form Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }

        // Prepare form data
        $formData = [
            'activity_id' => $this->input->post('activity'),
            'title' => $this->input->post('formtitle'),
            'form_description' => $this->input->post('formdescription'),
            'start_date_evaluation' => $this->input->post('startdate'),
            'end_date_evaluation' => $this->input->post('enddate'),
        ];

        // Handle Cover Image Upload (if new file is uploaded)
        if (!empty($_FILES['coverUpload']['name'])) {
            $coverImage = $this->upload_file('coverUpload', './assets/theme_evaluation');
            if (!$coverImage['status']) {
                echo json_encode(['success' => false, 'message' => $coverImage['error']]);
                return;
            }
            $formData['cover_theme'] = $coverImage['file_name'];
        }

        // Retrieve form fields (ensure correct format)
        $fields = json_decode($this->input->post('fields'), true);
        if (!is_array($fields)) {
            echo json_encode(['success' => false, 'message' => 'Invalid form fields data.']);
            return;
        }

        // Start database transaction
        $this->db->trans_start();

        // Update main form data
        $this->db->where('form_id', $form_id)->update('forms', $formData);

        // Fetch existing field IDs from database
        $existingFields = $this->db->select('form_fields_id')->where('form_id', $form_id)->get('formfields')->result_array();
        $existingFieldIds = array_column($existingFields, 'form_fields_id');

        $updatedFieldIds = []; // Track updated/new fields

        foreach ($fields as $index => $field) {
            $fieldId = $field['form_fields_id'] ?? null;

            $fieldData = [
                'form_id'     => $form_id,
                'label'       => $field['label'],
                'type'        => $field['type'],
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

        // Identify and delete removed fields
        $fieldsToDelete = array_diff($existingFieldIds, $updatedFieldIds);
        if (!empty($fieldsToDelete)) {
            $this->db->where_in('form_fields_id', $fieldsToDelete)->delete('formfields');
        }

        // Commit transaction
        $this->db->trans_complete();

        // Check transaction status
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Failed to update the form.']);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Form updated successfully.',
                'redirect' => site_url('officer/list-activity-evaluation'),
            ]);
        }
    }

    // VIEWING OF EVALUATION FORM - PAGE
    public function view_evaluationform($form_id)
    {
        $data['title'] = 'View Evaluation Form';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        // Fetch activities based on user role
        $data['activities'] = $this->officer->activity_organized();
        $data['forms'] = $this->officer->get_evaluation_by_id($form_id);

        $form_data = $this->officer->get_evaluation_by_id($form_id);
        $data['form_data'] = $form_data;

        $this->load->view('layout/header', $data);
        $this->load->view('officer/activity/eval_view-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    // EVALUATION RESPONSES - PAGE
    public function list_evaluation_responses($form_id)
    {
        $data['title'] = 'Evaluation Responses';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARY
        $data['users'] = $this->officer->get_student($student_id);

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

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('officer/activity/eval_list-evaluation-responses', $data);
        $this->load->view('layout/footer', $data);
    }

    // EVALUATION STATISTIC - PAGE
    public function evaluation_statistic($form_id)
    {
        $data['title'] = 'Evaluation Statistic';

        $student_id = $this->session->userdata('student_id');

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

    // FETCHING ACTIVITY - PAGE
    public function list_activity_excuse()
    {
        $data['title'] = 'List of Activity for Excuse Letter';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        $data['activities'] = $this->officer->get_activities();

        $this->load->view('layout/header', $data);
        $this->load->view('officer/activity/exc_list-activities-excuseletter', $data);
        $this->load->view('layout/footer', $data);
    }

    // LIST OF APPLICATION PER EVENT - PAGE
    public function list_excuse_letter($activity_id)
    {
        $data['title'] = 'List of Excuse Letter';


        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

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
        $excuse_id = $this->input->post('excuse_id');  // Assuming the application ID is passed
        $activity_id = $this->input->post('activity_id');
        $student_id = $this->input->post('student_id');

        // Prepare the data for updating the excuse
        $data = [
            'excuse_id' => $excuse_id,
            'status' => $approvalStatus,
            'remarks' => $remarks
        ];

        // Call the model to update the excuse status
        $result = $this->officer->updateApprovalStatus($data);

        if ($result) {
            // If approved, also update the attendance status to 'excuse'
            if ($approvalStatus === 'Approved') {
                $this->db->where('student_id', $student_id);
                $this->db->where('activity_id', $activity_id);
                $this->db->update('attendance', ['attendance_status' => 'excuse']);
            }

            echo json_encode(['success' => true, 'message' => 'Approval status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update approval status.']);
        }
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

        // Get offset and limit from AJAX request
        $limit = $this->input->post('limit') ?: 5;
        $offset = $this->input->post('offset') ?: 0;

        // GET LIMITED POSTS
        $data['posts'] = $this->officer->get_all_posts($limit, $offset);
        foreach ($data['posts'] as &$post) {
            $post->like_count = $this->officer->get_like_count($post->post_id);
            $post->user_has_liked_post = $this->officer->user_has_liked($post->post_id, $student_id);
            $post->comments_count = $this->officer->get_comment_count($post->post_id);
            $post->comments = $this->officer->get_comments_by_post($post->post_id);
            $post->type = 'post';
        }

        // GET LIMITED ACTIVITIES
        $data['activities'] = $this->officer->get_shared_activities($limit, $offset);
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

        $dept = $this->officer->admin_dept();
        $org = $this->officer->admin_org();

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
                'dept_id' => !empty($dept->dept_id) ? $dept->dept_id : NULL,
                'org_id' => !empty($org->org_id) ? $org->org_id : NULL,
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
                $response = [
                    'status' => 'success',
                    'message' => 'You shared a post.',
                    'redirect' => site_url('officer/community')
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'errors' => 'Failed to post. Please try again.'
                ];
            }

            echo json_encode($response);
            return;
        }

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
    public function time_in($activity_id)
    {
        $data['title'] = 'Taking Attendance - Time in';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        $data['activity'] = $this->officer->get_activity($activity_id);
        $data['schedule'] = $this->officer->get_schedule($activity_id);

        // Fetching students that belong to activity
        $data['students'] = $this->officer->get_students_realtime_time_in($activity_id);

        $this->load->view('layout/header', $data);
        $this->load->view('officer/attendance/scanqr_timein', $data);
        $this->load->view('layout/footer', $data);
    }

    // SCANNING AND FACIAL RECOGNITION - PAGE
    public function time_out($activity_id)
    {
        $data['title'] = 'Taking Attendance - Time out';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        $data['activity'] = $this->officer->get_activity($activity_id);
        $data['schedule'] = $this->officer->get_schedule($activity_id);

        // Fetching students that belong to activity
        $data['students'] = $this->officer->get_students_realtime_time_out($activity_id);

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

        // Check if student already has time_in
        $existing_attendance = $this->officer->get_attendance_record_time_in($student_id, $activity_id, $timeslot_id);
        if ($existing_attendance && !empty($existing_attendance->time_in)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'info',
                    'message' => "Student ID - $student_id - $full_name has already been recorded."
                ]));
        }

        // Get the timeslot cut-off for fines calculation
        $timeslot = $this->db->get_where('activity_time_slots', [
            'timeslot_id' => $timeslot_id
        ])->row();

        if ($timeslot && strtotime($timeslot->date_cut_in) < strtotime($current_datetime)) {
            // Apply fines logic: Check if student hasn't time-in and is past the cut-off
            $activity = $this->db->get_where('activity', [
                'activity_id' => $activity_id
            ])->row();

            $fine_amount = $activity->fines ?? 0;

            // Check if the student is eligible for fines
            $existing_fine = $this->db->get_where('fines', [
                'student_id' => $student_id,
                'activity_id' => $activity_id,
                'timeslot_id' => $timeslot_id
            ])->row();

            if (!$existing_fine) {
                // Insert fine record if no fine exists
                $this->db->insert('fines', [
                    'student_id' => $student_id,
                    'activity_id' => $activity_id,
                    'timeslot_id' => $timeslot_id,
                    'fines_amount' => $fine_amount,
                    //'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Update the fine record if one already exists
                $this->db->where([
                    'student_id' => $student_id,
                    'activity_id' => $activity_id,
                    'timeslot_id' => $timeslot_id
                ]);
                $this->db->update('fines', [
                    'fines_amount' => $existing_fine->fines_amount + $fine_amount, // Accumulate fine if necessary
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Update attendance
        $update_data = ['time_in' => $current_datetime];
        $updated = $this->officer->update_attendance_time_in($student_id, $activity_id, $timeslot_id, $update_data);

        if ($updated) {
            // Update or Insert fines summary after attendance update
            $this->update_fines_summary_for_student($student_id);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => "$student_id - $full_name successfully recorded."
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

    private function update_fines_summary_for_student($student_id)
    {
        // Get total fines for the student and activity
        $this->db->select('SUM(fines_amount) as total_fines');
        $this->db->from('fines');
        $this->db->where('student_id', $student_id);
        $total_fines = $this->db->get()->row()->total_fines ?? 0;

        // Check if the fines summary already exists
        $existing_summary = $this->db->get_where('fines_summary', [
            'student_id' => $student_id
        ])->row();

        if ($existing_summary) {
            // Update the existing summary
            $this->db->where([
                'student_id' => $student_id
            ]);
            $this->db->update('fines_summary', [
                'total_fines' => $total_fines,
                'fines_status' => $total_fines > 0 ? 'Unpaid' : 'Paid'
            ]);
        } else {
            // Insert a new summary record
            $this->db->insert('fines_summary', [
                'student_id' => $student_id,
                'total_fines' => $total_fines,
                'fines_status' => $total_fines > 0 ? 'Unpaid' : 'Paid'
            ]);
        }
    }

    // This method will be called by the cron job
    public function auto_fines_missing_time_in()
    {
        $this->officer->auto_fines_missing_time_in(); // This should do the logic
        echo "Auto fines executed at " . date('Y-m-d H:i:s');
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

        // Check if student already has time_in
        $existing_attendance = $this->officer->get_attendance_record_time_out($student_id, $activity_id, $timeslot_id);
        if ($existing_attendance && !empty($existing_attendance->time_out)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'info',
                    'message' => "Student ID - $student_id - $full_name has already been recorded."
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
                    'message' => "$full_name successfully recorded."
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

    // LISTING OF THE ATTENDEES - PAGE
    public function list_activities_attendance()
    {
        $data['title'] = 'List of Activities';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        $data['activities'] = $this->officer->get_activities_by_sp();

        $this->load->view('layout/header', $data);
        $this->load->view('officer/attendance/list-activities-attendance', $data);
        $this->load->view('layout/footer', $data);
    }

    // SHOWING ATTENDANCE LIST - PAGE
    public function list_attendees($activity_id)
    {
        $data['title'] = 'List of Attendees';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        $data['activities'] = $this->officer->get_activity_specific($activity_id);
        $data['students'] = $this->officer->get_all_students_attendance_by_activity($activity_id);
        $data['timeslots'] = $this->officer->get_timeslots_by_activity($activity_id);
        $data['departments'] = $this->officer->department_selection();

        $this->load->view('layout/header', $data);
        $this->load->view('officer/attendance/listofattendees', $data);
        $this->load->view('layout/footer', $data);
    }



    public function export_attendance_pdf($activity_id)
    {
        // Clean output buffer
        ob_clean();


        // Fetch data
        $students = $this->officer->get_all_students_attendance_by_activity($activity_id);
        $timeslots = $this->officer->get_timeslots_by_activity($activity_id);
        $activity = $this->officer->get_activity_specific($activity_id);

        // Setup PDF
        $pdf = new PDF('L', 'mm', 'A4'); // Use your custom PDF class!
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Attendance Report - Activity: ' . ($activity ? $activity['activity_title'] : 'N/A'), 0, 1, 'C');
        $pdf->Ln(5);

        // Prepare columns
        $header = ['Student ID', 'Name', 'Department'];
        foreach ($timeslots as $slot) {
            $period = strtolower($slot->slot_name);
            $label = $period === 'morning' ? 'AM' : ($period === 'afternoon' ? 'PM' : strtoupper($period));
            $header[] = "$label In";
            $header[] = "$label Out";
        }
        $header[] = 'Status';

        // Font for table
        $pdf->SetFont('Arial', '', 10);

        // Calculate maximum width for each column
        $colWidths = [];
        foreach ($header as $col) {
            $colWidths[] = $pdf->GetStringWidth($col) + 6;
        }

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

            foreach ($dataRow as $index => $cell) {
                $cellWidth = $pdf->GetStringWidth($cell) + 6;
                if ($cellWidth > $colWidths[$index]) {
                    $colWidths[$index] = $cellWidth;
                }
            }
        }

        // Normalize total width if too wide
        $pageWidth = 297 - 20; // A4 landscape - margins
        $totalWidth = array_sum($colWidths);

        if ($totalWidth > $pageWidth) {
            $scale = $pageWidth / $totalWidth;
            foreach ($colWidths as $i => $w) {
                $colWidths[$i] = $w * $scale;
            }
            $totalWidth = array_sum($colWidths);
        }

        // Center the table by setting X position
        $startX = ($pageWidth - $totalWidth) / 2 + 10; // +10 for left margin
        $pdf->SetX($startX);

        // Output table header
        $pdf->SetFont('Arial', 'B', 10);
        foreach ($header as $i => $colName) {
            $pdf->Cell($colWidths[$i], 10, $colName, 1, 0, 'C');
        }
        $pdf->Ln();

        // Table rows
        $pdf->SetFont('Arial', '', 9);
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

            // Reset X before each row to keep table centered
            $pdf->SetX($startX);

            foreach ($dataRow as $i => $cell) {
                $pdf->Cell($colWidths[$i], 10, $cell, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        // Output PDF
        $pdf->Output('I', 'attendance_report.pdf');
    }


    //CONNECTING ATTENDANCE AND FINES
    public function update_fines() {}

    // LIST OF FINES - PAGE
    public function list_fines()
    {
        $data['title'] = 'List of Fines';

        $student_id = $this->session->userdata('student_id');

        $data['users'] = $this->officer->get_student($student_id);

        $data['fines'] = $this->officer->flash_fines();

        $this->load->view('layout/header', $data);
        $this->load->view('officer/fines/listoffines', $data);
        $this->load->view('layout/footer', $data);
    }


    //FINES PAYMENTS CONFIRMATION
    public function confirm_payment()
    {
        $student_id = $this->input->post('student_id');
        $total_fines = $this->input->post('total_fines');
        $mode_of_payment = $this->input->post('mode_of_payment');
        $reference_number = trim($this->input->post('reference_number'));

        $record = $this->officer->get_fine_summary($student_id);

        if (!$record) {
            echo json_encode(['status' => 'error', 'message' => 'No fines summary found.']);
            return;
        }

        if ($reference_number !== $record->reference_number_students) {
            $this->officer->update_fines_summary($student_id, [
                'reference_number_admin' => $reference_number,
                'fines_status' => 'Pending',
                'mode_payment' => $mode_of_payment,
                'last_updated' => date('Y-m-d H:i:s')
            ]);

            echo json_encode(['status' => 'warning', 'message' => 'Reference mismatch. Payment on hold.']);
            return;
        }

        $updated = $this->officer->update_fines_summary_receipt($student_id, [
            'reference_number_admin' => $reference_number,
            'fines_status' => 'Paid',
            'mode_payment' => $mode_of_payment,
            'last_updated' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            $summary_id = $record->summary_id;
            $this->generate_and_store_fine_receipt($summary_id);

            echo json_encode(['status' => 'success', 'message' => 'Payment verified and receipt generated.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
        }
    }

    public function generate_and_store_fine_receipt($summary_id)
    {
        $this->load->model('Student_model');
        require_once(APPPATH . 'third_party/fpdf.php');

        // Get all fines for the summary
        $fines_data = $this->Student_model->get_fine_summary_data($summary_id);
        if (empty($fines_data)) return;

        $receipt_data = $fines_data[0]; // general info comes from first row

        $filename = 'fine_receipt_' . $summary_id . '.pdf';
        $folder_path = FCPATH . 'uploads/fine_receipts/';
        $filepath = $folder_path . $filename;

        if (!is_dir($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $verification_code = strtoupper(substr(md5($summary_id . time()), 0, 8));

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);

        $logoPath = FCPATH . 'application/third_party/receiptlogo.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 35, 70, 140, 100, 'PNG');
        }

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'FINE PAYMENT RECEIPT', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 8, 'Receipt No:', 0, 0, 'L');
        $pdf->Cell(0, 8, strtoupper($summary_id), 0, 1, 'L');
        $pdf->Cell(50, 8, 'Date:', 0, 0, 'L');
        $pdf->Cell(0, 8, date('F j, Y'), 0, 1, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, 'Student ID:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, $receipt_data['student_id'], 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, 'Student Name:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, $receipt_data['first_name'] . ' ' . $receipt_data['last_name'], 0, 1, 'L');

        // Organizer as source
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, 'Source of Fines:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, $receipt_data['organizer'], 0, 1, 'L');

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, 8, 'Description (Activity Title)', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Amount (PHP)', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $total = 0;
        foreach ($fines_data as $fine) {
            $desc = $fine['fines_reason'] . ' (' . $fine['activity_title'] . ')';
            $amount = number_format($fine['fines_amount'], 2);
            $pdf->Cell(120, 8, $desc, 1, 0, 'L');
            $pdf->Cell(40, 8, $amount, 1, 1, 'R');
            $total += $fine['fines_amount'];
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, 8, 'Total Amount:', 1, 0, 'R');
        $pdf->Cell(40, 8, 'P ' . number_format($total, 2), 1, 1, 'C');

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 8, 'Payment Type:', 0, 0, 'L');
        $pdf->Cell(0, 8, $receipt_data['mode_payment'], 0, 1, 'L');

        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Verification Code:', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $verification_code, 0, 1, 'C');

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 8, 'Use this code on the receipt verification page to validate.', 0, 1, 'C');

        $pdf->Output($filepath, 'F');

        // ✅ Update the `fines_summary` table
        $this->db->where('summary_id', $summary_id);
        $this->db->update('fines_summary', [
            'generated_receipt' => $filename,
            'last_updated' => date('Y-m-d H:i:s')
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
        if ($this->officer->updateFineStatus($student_id, $activity_id, $new_status)) {
            echo json_encode(["success" => true, "message" => "Fine status updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }

    // OTHER PAGES

    //PROFILE SETTINGS - PAGE
    public function profile_settings()
    {
        $data['title'] = 'Profile Settings';

        $student_id = $this->session->userdata('student_id');

        // Get user and their organizations
        $student_details = $this->officer->get_user_profile();

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
    public function manage_officers()
    {
        $data['title'] = 'Manage Officers and Privilege';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->officer->get_department();

        // FETCHING ALL THE DEPARTMENT
        $data['organization'] = $this->officer->get_organization();

        $this->load->view('layout/header', $data);
        $this->load->view('officer/manage-officer', $data);
        $this->load->view('layout/footer', $data);
    }

    public function list_officers_dept($dept_id)
    {
        $data['title'] = 'List of Officer Department';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['department'] = $this->officer->get_department();
        $data['dept_id'] = $dept_id;

        $data['officers'] = $this->officer->get_officer_dept();

        $data['privileges'] = $this->officer->manage_privilege();


        $this->load->view('layout/header', $data);
        $this->load->view('officer/manage-department', $data);
        $this->load->view('layout/footer', $data);
    }

    public function update_privileges()
    {
        if ($this->input->is_ajax_request()) {
            $privileges_input = $this->input->post('privileges');

            if (!empty($privileges_input)) {
                $sanitized_data = [];

                foreach ($privileges_input as $privilege_id => $values) {
                    $entry = ['privilege_id' => $privilege_id];

                    if (isset($values['manage_fines'])) {
                        $entry['manage_fines'] = 'Yes';
                    }

                    if (isset($values['manage_evaluation'])) {
                        $entry['manage_evaluation'] = 'Yes';
                    }

                    if (isset($values['manage_applications'])) {
                        $entry['manage_applications'] = 'Yes';
                    }

                    if (isset($values['able_scan'])) {
                        $entry['able_scan'] = 'Yes';
                    }

                    if (isset($values['able_create_activity'])) {
                        $entry['able_create_activity'] = 'Yes';
                    }

                    $sanitized_data[] = $entry;
                }

                $update_status = $this->officer->update_privileges($sanitized_data);

                echo json_encode(['success' => $update_status]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No data received']);
            }
        } else {
            show_error('No direct script access allowed');
        }
    }

    public function update_status_dept()
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
        if ($this->officer->update_status_dept($student_id, $new_status)) {
            echo json_encode(["success" => true, "message" => "Update admin updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }

    public function list_officers_org($org_id)
    {
        $data['title'] = 'List of Officer Organization';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

        // FETCHING ALL THE DEPARTMENT
        $data['organization'] = $this->officer->get_organization();
        $data['org_id'] = $org_id;

        $data['officers'] = $this->officer->get_officer_org();


        $this->load->view('layout/header', $data);
        $this->load->view('officer/manage-organization', $data);
        $this->load->view('layout/footer', $data);
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
        if ($this->officer->update_status_org($student_id, $new_status)) {
            echo json_encode(["success" => true, "message" => "Update admin updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }

    // GENERAL SETTINGS
    public function general_settings()
    {
        $data['title'] = 'General Settings';

        $student_id = $this->session->userdata('student_id');

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);

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

        // FETCHING DATA BASED ON THE ROLES AND PROFILE PICTURE - NECESSARRY
        $data['users'] = $this->officer->get_student($student_id);


        $this->load->view('layout/header', $data);
        $this->load->view('officer/about', $data);
        $this->load->view('layout/footer', $data);
    }












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

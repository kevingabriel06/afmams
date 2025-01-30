<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Activity_model', 'activity');
        $this->load->model('Community_model', 'community');

        // if(!$this->session->userdata('user_id'))
        // {
        //     redirect(site_url('login'));
        // }
    }

    
    //create activity form
    public function create_activity() {
        $data['title'] = 'Create Activity';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/create-activity', $data);
        $this->load->view('layout/footer', $data);
    }


    // list of activity
    public function list_activity() {
        $data['title'] = 'List of Activities';
        
        // getting data of activity
        $data['activities'] = $this->activity->get_activities();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities', $data);
        $this->load->view('layout/footer', $data);
    }

    
    public function activity_details($activity_id) {
        $data['title'] = 'Activity Details';

        $data['activity'] = $this->activity->get_activity($activity_id);
        $data['activities'] = $this->activity->get_activities();
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/activity-detail', $data);
        $this->load->view('layout/footer', $data);
    }

    // list of events para sa excuse letter
    public function list_event_excuse() {
        $data['title'] = 'List of Activity for Excuse Letter';
        
        $data['activities'] = $this->activity->get_activities();

    
    
        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-activities-excuseletter', $data);
        $this->load->view('layout/footer', $data);

    }

  

    // list of excuse letter per event
    public function list_excuse_letter() {
        $data['title'] = 'List of Excuse Letter';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/list-excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

    // excuse letter per student
    public function review_excuse_letter() {
        $data['title'] = 'Review Excuse Letter';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/excuse-letter', $data);
        $this->load->view('layout/footer', $data);
    }

   
    public function create_evaluationform() {
        $data['title'] = 'Create Evaluation Form';
        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/create-evaluation-form', $data);
        $this->load->view('layout/footer', $data);
    }

    public function community() {
        $data['title'] = 'Community';

        $data['authors'] = $this->community->get_user();
        $data['users'] = $this->community->get_users();

        $data['posts'] = $this->community->get_post();

        // Get the organization name from the model
        $student_id = '21-03529';
        $org_id = '2';
        $dept_id = '1';
        
        $organization = $this->community->getOrganizationName($student_id, $org_id);
        $department = $this->community->getDepartmentName($student_id, $dept_id);

        $data['org_name'] = $organization->org_name;
        $data['dept_name'] = $department->dept_name;

        // $data['roomtypes'] = $this->roomtypes->get_roomtypes();

        // $data['users'] = $this->User_model->get_users();

        // // Assuming you have a method to get the user's role
        // $id = $this->session->userdata('user_id'); // Example: get role from session

        // // Pass the role to the view
        // $data['id'] = $id;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activity/community', $data);
        $this->load->view('layout/footer', $data);
    }

    public function displayOrganizationName() {
        // Assume $user, $student_org, and $organization are available
        $student_id = $this->input->post('student_id'); // Example: Retrieve from POST
        $org_id = $this->input->post('org_id'); // Example: Retrieve from POST

        // Get the organization name from the model
        $organization = $this->community->getOrganizationName($student_id, $org_id);

        if ($organization) {
            echo $organization->org_name;
        } else {
            echo "No organization found or user is not an officer.";
        }
    }

    public function save() {
        // Check if the form is submitted
        if ($this->input->post()) {
            // Set validation rules
            $this->form_validation->set_rules('title', 'Activity', 'required');
            $this->form_validation->set_rules('date_start', 'Start Date', 'required');
            $this->form_validation->set_rules('date_end', 'End Date', 'required');
        
            // Check if validation passes
            if ($this->form_validation->run() == FALSE) {
                // If validation fails, return validation errors
                $response = [
                    'status' => 'error',
                    'errors' => validation_errors()
                ];
            } else {
                // If validation passes, proceed to save the data
                $data = array(
                    'activity_title' => $this->input->post('title'),
                    'start_date' => $this->input->post('date_start'),
                    'end_date' => $this->input->post('date_end'),
                    'registration_deadline' => $this->input->post('registration_deadline'),
                    'registration_fee' => $this->input->post('registration_fee'),
                    'am_in' => $this->input->post('am_in'),
                    'am_out' => $this->input->post('am_out'),
                    'pm_in' => $this->input->post('pm_in'),
                    'pm_out' => $this->input->post('pm_out'),
                    'description' => $this->input->post('description'),
                    'privacy' => $this->input->post('privacy')
                );
                
                if (!empty($_FILES['image']['name'])) {
                    $config = [
                        'upload_path' => './assets/coverEvent',
                        'allowed_types' => 'gif|jpg|jpeg|png',
                        'max_size' => 2048, // Increased max_size for better handling
                    ];
                    $this->load->library('upload', $config);
    
                    if ($this->upload->do_upload('image')) {
                        $uploadData = $this->upload->data();
                        $data['activity_image'] = $uploadData['file_name'];
                    } else {
                        $response = [
                            'status' => 'error',
                            'errors' => $this->upload->display_errors()
                        ];
                        echo json_encode($response);
                        return;
                    }
                }

                $result = $this->activity->save_activity($data);
            
                if ($result) {
                    // If data is saved successfully, return success message
                    $response = array(
                        'status' => 'success',
                        'message' => 'Activity Saved Successfully',
                        'redirect' => site_url('create-activity')
                    );
                } else {
                    // If data is not saved, return error message
                    $response = array(
                        'status' => 'error',
                        'errors' => 'Failed to Save Activity'
                    );
                }
        
                
                // $id = $this->input->post('id');

                // if ($id) {
                //     // Update existing category
                //     if ($this->roomtypes->update_category($id, $data)) {
                //         $response = [
                //             'status' => 'success',
                //             'message' => 'Room Type Updated Successfully',
                //             'redirect' => site_url('roomtypes')
                //         ];
                //     } else {
                //         $response = [
                //             'status' => 'error',
                //             'message' => 'Failed to Update Room Type'
                //         ];
                //     }
                // } else {
                //     // Save data to database
                //    


                // }
                
            }
        } 
        
        // Return JSON response
        echo json_encode($response);
    }

    
    // public function delete($id) {
    //     // Fetch the room type data
    //     $roomtype = $this->roomtypes->get($id);
        
    //     $filename = $roomtype->image;
        
    //     $imagePath = './assets/uploadImg/' . $filename;

    //     // Attempt to delete the room type from the database
    //     if ($this->roomtypes->delete_roomtype($id) && unlink($imagePath)) {
    //         $response = array(
    //             'status' => 'success',
    //             'message' => 'Deleted Sucessfully',
    //             'redirect' => site_url('roomtypes')
    //         );
    //     }else {
    //         $response = array(
    //             'status' => 'error',
    //             'errors' => 'Failed to Delete Room Type',
    //         );
    //     }
        
    //     echo json_encode($response);
    // }
}
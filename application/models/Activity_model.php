<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    //Getting the activity
    public function get_activities() {
        $activity = $this->db->get_where('activity')->result();
        return $activity;
    }

     // Fetch a specific activity by ID
    public function get_activity($activity_id) {
        return $this->db->get_where('activity', ['activity_id' => $activity_id])->row_array();
    }

    public function save_activity($data) {
        $data = [
            'activity_title'  => $this->input->post('title'),
            'start_date' => $this->input->post('date_start'),
            'end_date' => $this->input->post('date_end'),
            'registration_deadline' => $this->input->post('registration_deadline'),
            'registration_fee' => $this->input->post('registration_fee'),
            'am_in' => $this->input->post('am_in'),
            'am_out' => $this->input->post('am_out'),
            'pm_in' => $this->input->post('pm_in'),
            'pm_out' => $this->input->post('pm_out'),
            'description' => $this->input->post('description'),
            'activity_image' => $this->upload->data('file_name'),
            'privacy' => $this->input->post('privacy')
        ];
 
        $result = $this->db->insert('activity', $data);
        return $result;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    // CHECKING OF ROLES FOR DISPLAYING DIFFERENT PARTS OF SYSTEM
    public function get_roles($student_id){
        $student_id = $this->session->userdata('student_id');

        return $this->db->get_where('users', ['student_id' => $student_id])->row_array();
    }
}
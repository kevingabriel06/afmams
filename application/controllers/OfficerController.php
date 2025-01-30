<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OfficerController extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        // Load the url helper to use base_url()
        $this->load->helper('url');
    }

	public function officer_dashboard()
	{
		$this->load->view('officer/officerdashboard');
	}



	
}

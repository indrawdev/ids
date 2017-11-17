<?php

class InfoJurnal extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		if (trim($this->session->userdata('gDatabase')) <> '')
		{
			$this->load->view('vinfojurnal');
		}
		else
		{
			redirect('','refresh');
		}
	}
}
?>
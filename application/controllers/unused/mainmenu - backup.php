<?php

class MainMenu extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		//change db
		$this->load->model('mMainModul','',TRUE);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//end of change db
		
		$this->load->model('mMainMenu','',TRUE);
		$data['data_menu'] = $this->mMainMenu->load_menu();
		$this->load->view('vMainMenu',$data);
	}
}

?>
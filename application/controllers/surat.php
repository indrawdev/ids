<?php

class Surat extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
	}
	
	function index()
	{
		if (trim($this->session->userdata('gDatabase')) <> '')
		{
			$this->load->view('vsurat');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function product()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdcust = trim($this->input->post('fs_kd_cust'));
		$kdcount = trim($this->input->post('fs_count'));

		$cari = trim($this->input->post('fs_search'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_surat_all($kdcust,$kdcount,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->product_surat($kdcust,$kdcount,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
}
?>
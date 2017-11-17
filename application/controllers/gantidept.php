<?php

class GantiDept extends CI_Controller
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
			$this->load->view('vgantidept');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function ceksave()
	{
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'lanjut'
		);
		echo json_encode($hasil);
	}
	
	function save()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$deptdef = trim($this->input->post('fs_nm_dept'));
		$usernm = trim($this->session->userdata('gUser'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->dept_def($kddept,$kdcount);
		$ssql = $ssql->row();
		$deptdef = $ssql->fs_nm_code;
		$kota = ucwords(strtolower($ssql->fs_kota));
		
		$lDept = strlen(trim($kddept));
		$lDept = $lDept - 2;
		$xDept = substr(trim($kddept), $lDept, 2);
		
		$lCount = strlen(trim($kdcount));
		$lCount = $lCount - 2;
		$xCount = substr(trim($kdcount), $lCount, 2);
		
		$usertipe = '0';
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->user_tipe($usernm);
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$usertipe = $ssql->fb_sparepart;
		}
		
		//set sesi
		$new = array(
			'gDept'			=> trim($kddept),
			'gCount'		=> trim($kdcount),
			'gDeptName'		=> trim($deptdef),
			'gGudang'		=> trim($xDept).trim($xCount),
			'gWilayah'		=> trim($xDept),
			
			'gKota'			=> trim($kota),
			'gSparePart'	=> trim($usertipe)
		);
		$this->session->set_userdata($new);
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Change Login Department Success'
		);
		echo json_encode($hasil);
	}
}
?>
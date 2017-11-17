<?php

class Variable extends CI_Controller
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
			$this->load->view('vvariable');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function kodekey()
	{
		$nstart = trim($this->input->post('start'));
		$kdkey = trim($this->input->post('fs_kd_key'));
		$nmkey = trim($this->input->post('fs_nm_key'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekey_all($kdkey,$nmkey,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodekey($kdkey,$nmkey,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodevar()
	{
		$nstart = trim($this->input->post('start'));
		$kdkey = trim($this->input->post('fs_kd_key'));
		$kdvar = trim($this->input->post('fs_kd_var'));
		$nmvar = trim($this->input->post('fs_nm_var'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodevar_all($kdkey,$kdvar,$nmvar,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodevar($kdkey,$kdvar,$nmvar,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$kdkey = trim($this->input->post('fs_kd_key'));
		$kdvar = trim($this->input->post('fs_kd_var'));
		
		if (trim($kdkey) == '' or trim($kdvar) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mVariable','',true);
			$ssql = $this->mVariable->cek_kode($kdkey,$kdvar);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Reference number already exists, do you want to update it?'
				);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'lanjut'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$kdkey = trim($this->input->post('fs_kd_key'));
		$kdvar = trim($this->input->post('fs_kd_var'));
		$nmvar = trim($this->input->post('fs_nm_var'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$subsidi = trim($this->input->post('fn_subsidi'));
		
		$xupdate = false;
		$this->load->model('mVariable','',true);
		$ssql = $this->mVariable->cek_kode($kdkey,$kdvar);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		
		$data = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_key'			=> trim($kdkey),
			'fs_kd_vareable'	=> trim($kdvar),
			'fs_nm_vareable'	=> trim($nmvar)
		);
		
		if ($xupdate == false)
		{
			$this->db->insert('tm_vareable', $data);
		}
		else
		{
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_key = '".trim($kdkey)."'
						AND	fs_kd_vareable = '".trim($kdvar)."'";
			
			$this->db->where($where);
			$this->db->update('tm_vareable', $data);
		}
		
		if (trim($kdkey) == '17')
		{
			//hapus detail
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND fs_key = '".trim($kdkey)."'
						AND	fs_kd_vareable = '".trim($kdvar)."'";
			
			$this->db->where($where);
			$this->db->delete('tm_acnopmtyp');
			//eof hapus detail
			
			$data = array(
				'fs_kd_comp' => trim($this->session->userdata('gComp')),
				'fs_key' => trim($kdkey),
				'fs_kd_vareable' => trim($kdvar),
				'fs_kd_acno' => trim($kdacno),
				'fn_subsidi' => trim($subsidi)
			);
			
			$this->db->insert('tm_acnopmtyp', $data);
		}
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Variable Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Variable Update Success'
			);
			echo json_encode($hasil);
		}
	}
}
?>
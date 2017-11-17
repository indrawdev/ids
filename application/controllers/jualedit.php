<?php

class JualEdit extends CI_Controller
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
			$this->load->view('vjualedit');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$cust = trim($this->input->post('fs_nm_cussup'));
		$cari = trim($this->input->post('fs_cari'));
		$trx = 'JL';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust,'0','1',$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->jual_unit($trx,$refno,$docno,$cust,'0','1',$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refno2()
	{
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		$refno = trim($this->input->post('fs_refno'));
		$nmcust = trim($this->input->post('fs_nm_cust'));
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cb_trs_all($kdtrx,$kdstrx,$nmcust,$refno,'0',$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cb_trs($kdtrx,$kdstrx,$nmcust,$refno,'0',$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = 'JL';
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference Number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mJual','',true);
			$ssql = $this->mJual->cek_refno($kdtrx,$refno);
			
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
					'sukses'	=> false,
					'hasil'		=> 'Saving Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdedit = trim($this->input->post('fb_edit'));
		
		if (trim($kdedit) == 'true')
		{
			$kdedit = 1;
		}
		else
		{
			$kdedit = 0;
		}
		
		$data = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_refno'		=> trim($refno),
			'fb_edit'		=> trim($kdedit)
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->update('tx_posheader', $data);
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Saving Sales Update Success'
		);
		echo json_encode($hasil);
	}
	
	function ceksave2()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5000';
		$kdstrx = '0100';
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference Number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mCBReceive','',true);
			$ssql = $this->mCBReceive->cek_kode($kdtrx,$kdstrx,$refno);
			
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
					'sukses'	=> false,
					'hasil'		=> 'Saving Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save2()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdedit = trim($this->input->post('fb_edit'));
		
		if (trim($kdedit) == 'true')
		{
			$kdedit = 0;
		}
		else
		{
			$kdedit = 1;
		}
		
		$data = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_refno'		=> trim($refno),
			'fb_close'		=> trim($kdedit)
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->update('tx_actheader', $data);
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Saving Cash Receipt Update Success'
		);
		echo json_encode($hasil);
	}
}
?>
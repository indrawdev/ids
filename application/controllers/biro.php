<?php

class Biro extends CI_Controller
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
			$this->load->view('vbiro');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function kodeagen()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdagen = trim($this->input->post('fs_kd_agen'));
		$nmagen = trim($this->input->post('fs_nm_agen'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->agen2_all($kdagen,$nmagen,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->agen2($kdagen,$nmagen,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$kdagen = trim($this->input->post('fs_kd_agen'));
		
		if (trim($kdagen) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mBiro','',true);
			$ssql = $this->mBiro->cek_kode($kdagen);
			
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
		$kdagen = trim($this->input->post('fs_kd_agen'));
		$nmagen = trim($this->input->post('fs_nm_agen'));
		$kdaktif = trim($this->input->post('fb_aktif'));
		$tlp = trim($this->input->post('fs_tlp'));
		$alamat = trim($this->input->post('fs_alamat'));
		
		if (trim($kdaktif) == 'true')
		{
			$kdaktif = 1;
		}
		else
		{
			$kdaktif = 0;
		}
		
		$xupdate = false;
		$this->load->model('mBiro','',true);
		$ssql = $this->mBiro->cek_kode($kdagen);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_birojs'	=> trim($kdagen),
			'fs_nm_birojs'	=> trim($nmagen),
			'fs_alamat'		=> trim($alamat),
			'fs_tlp'		=> trim($tlp),
			'fb_active'		=> trim($kdaktif)
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tm_masterbirojs', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_kd_birojs = '".trim($kdagen)."'";
			
			$this->db->where($where);
			$this->db->update('tm_masterbirojs', $data);
		}
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Agen Success',
				'kdagen'	=> $kdagen
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Agen Update Success',
				'kdagen'	=> $kdagen
			);
			echo json_encode($hasil);
		}
	}
}
?>
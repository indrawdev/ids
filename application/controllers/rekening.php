<?php

class Rekening extends CI_Controller
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
			$this->load->view('vrekening');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function kodeacno()
	{
		$nstart = trim($this->input->post('start'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$nmacno = trim($this->input->post('fs_nm_acno'));
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeacno_all($kdacno,$nmacno,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodeacno($kdacno,$nmacno,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kelacno()
	{
		$nstart = trim($this->input->post('start'));
		$kdgrup = trim($this->input->post('fs_kd_group'));
		$nmgrup = trim($this->input->post('fs_nm_group'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->acno_grup_all($kdgrup,$nmgrup,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->acno_grup($kdgrup,$nmgrup,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function griddetil()
	{
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mRekening','',true);
		$ssql = $this->mRekening->griddetil_all();
		$total = $ssql->num_rows();
		
		$ssql = $this->mRekening->griddetil($nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function griddetil2()
	{
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mRekening','',true);
		$ssql = $this->mRekening->griddetil2_all();
		$total = $ssql->num_rows();
		
		$ssql = $this->mRekening->griddetil2($nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$kdacno = trim($this->input->post('fs_kd_acno'));
		
		if (trim($kdacno) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mRekening','',true);
			$ssql = $this->mRekening->cek_kode($kdacno);
			
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
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$nmacno = trim($this->input->post('fs_nm_acno'));
		$kdaktif = trim($this->input->post('fb_aktif'));
		
		if (trim($kdaktif) == 'true')
		{
			$kdaktif = 1;
		}
		else
		{
			$kdaktif = 0;
		}
		
		$xupdate = false;
		$this->load->model('mRekening','',true);
		$ssql = $this->mRekening->cek_kode($kdacno);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_acno'	=> trim($kdacno),
			'fs_nm_acno'	=> trim($nmacno),
			'fb_active'		=> trim($kdaktif),
			
			'fb_bs'			=> '1',
			'fb_is'			=> '1',
			'fn_balance'	=> '0',
			'fb_dbcr'		=> '1',
			'fb_hidden'		=> '0'
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
			
			$this->db->insert('tm_acno', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_kd_acno = '".trim($kdacno)."'";
			
			$this->db->where($where);
			$this->db->update('tm_acno', $data);
		}
		
		//hapus tm_cbacno
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_acno = '".trim($kdacno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_cbacno');
		//eof hapus tm_cbacno
		
		$data = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_acno'	=> trim($kdacno),
			'fb_flag'		=> '0',
			'fb_delete'		=> '0',
			'fb_active'		=> '1',
			'fs_dbcr'		=> 'D',
			
			'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
			'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
			'fs_upddt'		=> trim($this->session->userdata('gUser')),
			'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
		);
		$this->db->insert('tm_cbacno', $data);
		
		$data = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_acno'	=> trim($kdacno),
			'fb_flag'		=> '1',
			'fb_delete'		=> '0',
			'fb_active'		=> '1',
			'fs_dbcr'		=> 'D',
			
			'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
			'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
			'fs_upddt'		=> trim($this->session->userdata('gUser')),
			'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
		);
		$this->db->insert('tm_cbacno', $data);
		
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Account Success',
				'kdacno'	=> $kdacno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Account Update Success',
				'kdacno'	=> $kdacno
			);
			echo json_encode($hasil);
		}
	}
	
	function ceksave2()
	{
		$kdkelacno = trim($this->input->post('fs_kd_group'));
		
		if (trim($kdkelacno) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mRekening','',true);
			$ssql = $this->mRekening->cek_kodegrup($kdkelacno);
			
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
	
	function save2()
	{
		$kdkelacno = trim($this->input->post('fs_kd_group'));
		$nmkelacno = trim($this->input->post('fs_nm_group'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		
		$xupdate = false;
		$this->load->model('mRekening','',true);
		$ssql = $this->mRekening->cek_kodegrup($kdkelacno);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_group'	=> trim($kdkelacno),
			'fs_nm_group'	=> trim($nmkelacno),
			'fs_kd_acno'	=> trim($kdacno),
			'fb_active'		=> '1',
			
			'fb_bs'			=> '1',
			'fb_is'			=> '1',
			'fn_balance'	=> '0',
			'fb_dbcr'		=> '1'
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
				'fs_uppdt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tm_grpacno', $data);
		}
		else
		{
			$dt2 = array(
				'fs_uppdt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_kd_group = '".trim($kdkelacno)."'";
			
			$this->db->where($where);
			$this->db->update('tm_grpacno', $data);
		}
		
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Group Account Success',
				'kdkelacno'	=> $kdkelacno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Group Account Update Success',
				'kdkelacno'	=> $kdkelacno
			);
			echo json_encode($hasil);
		}
	}
}
?>
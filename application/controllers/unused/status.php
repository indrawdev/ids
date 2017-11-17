<?php

class Status extends CI_Controller
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
		$this->load->view('vstatus');
	}
	
	function addfield()
	{
		//tabel header
		$nmtabel = 'tx_unitstatus';
		$nmkolom = 'fs_kd_comp';
		$pjgkolom = '10';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_refno';
		$pjgkolom = '25';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fd_refno';
		$pjgkolom = '10';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_docno';
		$pjgkolom = '30';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fd_docno';
		$pjgkolom = '10';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_usrcrt';
		$pjgkolom = '25';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fd_usrcrt';
		$pjgkolom = '8';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_upddt';
		$pjgkolom = '25';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fd_upddt';
		$pjgkolom = '8';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		//Eof tabel header
		
		
		//tabel detil
		$nmtabel = 'tx_unitstatus_detil';
		$nmkolom = 'fs_kd_comp';
		$pjgkolom = '10';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_refno';
		$pjgkolom = '25';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_rangka';
		$pjgkolom = '50';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_mesin';
		$pjgkolom = '50';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fn_status';
		$pjgkolom = '1';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fd_tgl_pelihara';
		$pjgkolom = '10';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_ket';
		$pjgkolom = '200';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		//Eof tabel detil
		
		//tabel tm_icregister
		$nmtabel = 'tm_icregister';
		$nmkolom = 'fn_status';
		$pjgkolom = '1';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fd_tgl_pelihara';
		$pjgkolom = '10';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmkolom = 'fs_ket';
		$pjgkolom = '200';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		//Eof tabel tm_icregister
		
		$hasil = array('sukses' => true);
		echo json_encode($hasil);
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->status_pelihara_all($refno);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->status_pelihara($refno,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function grid_detail()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mStatus','',true);
		$ssql = $this->mStatus->grid($refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_detail2()
	{
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mStatus','',true);
		$ssql = $this->mStatus->grid2_all($cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mStatus->grid2($cari,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array('sukses' => true, 'hasil' => 'lanjut');
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mStatus','',true);
			$ssql = $this->mStatus->cek_refno($refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array('sukses' => true, 'hasil' => 'Reference number already exists, do you want to update it?');
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, Reference number unknown!!');
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$refno = trim($this->input->post('fs_refno'));
		$refnodt = trim($this->input->post('fd_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$docnodt = trim($this->input->post('fd_docno'));
		$kdtrx = 'STA';
		
		$xupdate = false;
		$this->load->model('mStatus','',true);
		$ssql = $this->mStatus->cek_refno($refno);
		
		if ($ssql->num_rows() > 0)
		{
			//refno ada
			$ssql = $ssql->row();
			$refno = $ssql->fs_refno;
			$xupdate = true;
			//eof refno ada
		}
		else
		{
			//generate refno
			$ldept = strlen(trim($this->session->userdata('gDept')));
			$ldept = $ldept - 2;
			$lcount = strlen(trim($this->session->userdata('gCount')));
			$lcount = $lcount - 2;
			
			$xdept = substr(trim($this->session->userdata('gDept')), $ldept, 2).substr(trim($this->session->userdata('gCount')), $lcount, 2);
			$xdate = substr(trim($refnodt), 2, 6);
			
			$xprefix = trim($this->session->userdata('gSparePart')).trim($kdtrx).'/'.trim($xdept).'/'.trim($xdate).'/';
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_nostatus($xprefix);
			//eof generate refno
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				
				$lrefno = strlen(trim($ssql->fs_refno));
				$lrefno = $lrefno - 5;
				
				$refno = $xprefix.sprintf("%05d",(substr(trim($ssql->fs_refno), $lrefno, 5) + 1));
			}
			else
			{
				$refno = $xprefix.'00001';
			}
		}
		
		
		$dt = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_refno' => trim($refno),
			'fd_refno' => trim($refnodt),
			'fs_docno' => trim($docno),
			'fd_docno' => trim($docnodt),
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt' => trim($this->session->userdata('gUser')),
				'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tx_unitstatus', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_unitstatus', $data);
		}
		
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_unitstatus_detil');
		//eof hapus detail
		
		//simpan detail
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$rangka = explode('|', trim($this->input->post('fs_rangka')));
		$mesin = explode('|', trim($this->input->post('fs_mesin')));
		$status = explode('|', trim($this->input->post('fn_status')));
		$tglpelihara = explode('|', trim($this->input->post('fd_tgl_pelihara')));
		$ket = explode('|', trim($this->input->post('fs_ket')));
		
		$jml = count($rangka) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				$data = array(
					'fs_kd_comp' => trim($this->session->userdata('gComp')),
					'fs_refno' => trim($refno),
					'fs_rangka' => trim($rangka[$i]),
					'fs_mesin' => trim($mesin[$i]),
					'fn_status' => trim($status[$i]),
					
					'fd_tgl_pelihara' => trim($tglpelihara[$i]),
					'fs_ket' => trim($ket[$i])
				);
				$this->db->insert('tx_unitstatus_detil', $data);
			}
		}
		//eof simpan detail
		
		$this->load->model('mStatus','',true);
		$ssql = $this->mStatus->update_status($rangka,$mesin,$refno);
		
		if ($xupdate == false)
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Maintenance Status Success', 'refno' => $refno);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Maintenance Status Update Success', 'refno' => $refno);
			echo json_encode($hasil);
		}
	}
}
?>
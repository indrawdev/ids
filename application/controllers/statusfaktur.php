<?php

class StatusFaktur extends CI_Controller
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
			$this->load->view('vstatusfaktur');
		}
		else
		{
			redirect('','refresh');
		}
	}

	function cust_list()
	{
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mStatusFaktur','',true);
		$ssql = $this->mStatusFaktur->custlist_all($cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mStatusFaktur->custlist($cari,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}

	function griddetil()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_nm_cust'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$kdtrx = '5800';
		$kdstrx = '0100';
		
		$this->load->model('mStatusFaktur','',true);
		$ssql = $this->mStatusFaktur->faktur_request_all($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin);
		$total = $ssql->num_rows();
		
		$ssql = $this->mStatusFaktur->faktur_request($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$nstart);
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}

	function ceksave()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$status = trim($this->input->post('fs_status'));

		if ($rangka == '' or $mesin == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Chassis and Machine Status unknown!!'
			);
			echo json_encode($hasil);
			return;
		}
		else if ($status == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Status unknown!!'
			);
			echo json_encode($hasil);
			return;
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

	function save()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$status = trim($this->input->post('fs_status'));

		$xupdate = true;
		$data = array(
					'fs_flag'	=> trim($status)
				);
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_chasis = '".trim($rangka)."'
					AND	fs_engine = '".trim($mesin)."'";

		$this->db->where($where);
		$this->db->update('tx_TrxRequestFakturD', $data);

		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Status Faktur Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Status Faktur Update Success'
			);
			echo json_encode($hasil);
		}
		
	}

	function cekremove()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		if ($rangka == '' or $mesin == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Chassis or Machine unknown!!'
			);
			echo json_encode($hasil);
			return;
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Remove STNK & BPKB Info?'
			);
			echo json_encode($hasil);
		}
	}

	/*
	function remove()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$data = array(
			'fs_stnk'	=> '',
			'fd_stnk'	=> '',
			'fs_bpkb'	=> '',
			'fd_bpkb'	=> '',
			'fs_nopol'	=> ''
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_rangka = '".trim($rangka)."'
					AND	fs_machine = '".trim($mesin)."'";

		$this->db->where($where);
		$this->db->update('tm_icregister', $data);
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove STNK & BPKB Info Success'
		);
		echo json_encode($hasil);
	}
	*/

}
<?php

class Crud extends CI_Controller
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
			$this->load->view('vcrud');
		}
		else
		{
			redirect('','refresh');
		}
	}


	function kodecrud() 
	{
		$nstart = trim($this->input->post('start'));
		$kdcrud = trim($this->input->post('fs_kd_crud'));
		
		$this->load->model('mCrud','',true);
		$ssql = $this->mCrud->crud_all();
		$total = $ssql->num_rows();
		//$ssql = $this->mCrud->product_master($kdproduk,$nmproduk,$nstart);
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}

	function griddetil()
	{
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mCrud','',true);
		$ssql = $this->mCrud->griddetil_all();
		$total = $ssql->num_rows();
		
		//$ssql = $this->mProduk->griddetil($nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}

	function ambil_kota() 
	{
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));

		$this->load->model('mCrud','',true);
		$sSQL = $this->mCrud->city_all();
		$xTotal = $sSQL->num_rows();	

		$sSQL = $this->mCrud->city($nStart);

		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fn_city_id' => trim($xRow->fn_city_id),
					'fs_city_name'	=> trim($xRow->fs_city_name)
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}


	function ceksave()
	{
		$kdcrud = trim($this->input->post('fs_kd_crud'));
		if (trim($kdcrud) == '') {
			$hasil = array(
					'sukses' => false,
					'hasil' => 'Saving Failed'
				);
			echo json_encode($hasil);
		}
		else {
			$this->load->model('mCrud','',true);
			$ssql = $this->mCrud->cek_kode($kdcrud);
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
						'sukses' => true,
						'hasil' => 'CRUD kode, already exists, do you want to update it?'
					);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
						'sukses' => true,
						'hasil' => 'lanjut'
					);
				echo json_encode($hasil);
			}
		}
	}


	function save() 
	{
		$kdcrud = trim($this->input->post('fs_kd_crud'));
		$fname 	= trim($this->input->post('fs_fname'));
		$lname 	= trim($this->input->post('fs_lname'));
		$jekel 	= trim($this->input->post('fb_jekel'));
		$city 	= trim($this->input->post('fn_city_id'));
		$address = trim($this->input->post('fs_address'));
		$kdaktif = trim($this->input->post('fb_active'));
		$kddb = trim($this->input->post('fb_db'));

		if (trim($kdaktif) == 'true')
		{
			$kdaktif = 1;
		}
		else 
		{
			$kdaktif = 0;
		}

		if (trim($kddb) == 'true')
		{
			$kddb = 1;
		}
		else
		{
			$kddb = 0;
		}

		$xupdate = false;

		$this->load->model('mCrud','',true);
		
		$ssql = $this->mCrud->cek_kode($kdcrud);

		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}

		$dt1 = array(
					'fs_fname' 	=> $fname,
					'fs_lname' 	=> $lname,
					'fb_jekel' 	=> $jekel,
					'fn_city_id' => $city,
					'fs_address' => $address,
					'fb_active' => $kdaktif
				);
		if ($xupdate == false)
		{
			$dt2 = array(
						'fs_kd_crud' => $kdcrud,
						'fs_usrcrt' => trim($this->session->userdata('gUser')),
						'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
						'fs_upddt' => trim($this->session->userdata('gUser')),
						'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
					);
			$data = array_merge($dt1, $dt2);
			$this->db->insert('tm_crud', $data);
		}
		else 
		{
			$dt2 = array(
						'fs_upddt' => trim($this->session->userdata('gUser')),
						'fd_upddt' => trim(date('Y-m-d H:i:s'))
					);
			$data = array_merge($dt1, $dt2);
			$where = "fs_kd_crud = '".trim($kdcrud)."'";
			$this->db->where($where);
			$this->db->update('tm_crud', $data);
		}

		if ($xupdate == false)
		{
			$hasil = array(
						'sukses' => true,
						'hasil' => 'Saving CRUD Success'
					);
			echo json_encode($hasil);
		}
		else 
		{
			$hasil = array(
						'sukses' => true,
						'hasil' => 'Saving CRUD Update Success'
					);
			echo json_encode($hasil);
		}

	}
}
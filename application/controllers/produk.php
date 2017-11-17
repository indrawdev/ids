<?php

class Produk extends CI_Controller
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
			$this->load->view('vproduk');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function norangka()
	{
		$kdproduk = trim($this->input->post('fs_kd_product'));
		$kdrangka = '';
		$kdmesin = '';
		$cc = '0';
		
		$this->load->model('mProduk','',true);
		$ssql = $this->mProduk->norangka($kdproduk);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdrangka = $ssql->fs_rangka;
			$kdmesin = $ssql->fs_mesin;
			$cc = $ssql->fn_cc;
		}
		
		$hasil = array(
			'sukses'	=> true,
			'kdrangka'	=> trim($kdrangka),
			'kdmesin'	=> trim($kdmesin),
			'cc'		=> trim($cc)
		);
		echo json_encode($hasil);
	}
	
	function kodeproduk()
	{
		$nstart = trim($this->input->post('start'));
		$kdproduk = trim($this->input->post('fs_kd_product'));
		$nmproduk = trim($this->input->post('fs_nm_product'));
		$cari = trim($this->input->post('fs_cari'));
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_master_all($kdproduk,$nmproduk,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->product_master($kdproduk,$nmproduk,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function griddetil()
	{
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mProduk','',true);
		$ssql = $this->mProduk->griddetil_all();
		$total = $ssql->num_rows();
		
		$ssql = $this->mProduk->griddetil($nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function gridbeli()
	{
		$this->load->model('mProduk','',true);
		$ssql = $this->mProduk->gridbeli();
		
		echo json_encode($ssql->result());
	}
	
	function gridjual()
	{
		$this->load->model('mProduk','',true);
		$ssql = $this->mProduk->gridjual();
		
		echo json_encode($ssql->result());
	}
	
	function ceksave()
	{
		$kdproduk = trim($this->input->post('fs_kd_product'));
		
		if (trim($kdproduk) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Product Code unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mProduk','',true);
			$ssql = $this->mProduk->cek_kode($kdproduk);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Product Code already exists, do you want to update it?'
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
		$kdproduk = trim($this->input->post('fs_kd_product'));
		$nmproduk = trim($this->input->post('fs_nm_product'));
		$kdaktif = trim($this->input->post('fb_active'));
		$kdrangka = trim($this->input->post('fs_rangka'));
		$kdmesin = trim($this->input->post('fs_mesin'));
		$cc = trim($this->input->post('fn_cc'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$kdacnox = trim($this->input->post('fs_kd_acnox'));
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
		$this->load->model('mProduk','',true);
		$ssql = $this->mProduk->cek_kode($kdproduk);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		
		$dt = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_product'		=> trim($kdproduk),
			'fs_nm_product'		=> trim($nmproduk),
			'fs_kd_group'		=> 'INV',
			'fs_kd_acno'		=> trim($kdacno),
			
			'fs_kd_acnox'		=> trim($kdacnox),
			'fb_active'			=> trim($kdaktif),
			'fs_kd_type'		=> '0',
			'fs_kd_cogs'		=> '1',
			'fb_purchase'		=> '1',
			
			'fb_sale'			=> '1',
			'fb_transfer'		=> '1',
			'fb_warning'		=> '1',
			'fn_limitQuota'		=> '0',
			'fs_kd_unit1'		=> 'UNIT',
			
			'fb_ProdDept'		=> '0',
			'fb_attribute'		=> '0',
			'fn_balance'		=> '0',
			'fn_valueBal'		=> '0',
			'fs_kd_acnor'		=> trim($kdacno),
			
			'fs_kd_acnorx'		=> trim($kdacnox),
			'fs_kd_component'	=> '',
			'fn_component'		=> '0',
			'fs_unitpurchase'	=> 'UNIT',
			'fs_unitsale'		=> 'UNIT',
			
			'fs_kd_moving'		=> '',
			'fs_kd_jnsprod'		=> '2'
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
			
			$this->db->insert('tm_product', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_kd_product = '".trim($kdproduk)."'";
			
			$this->db->where($where);
			$this->db->update('tm_product', $data);
		}
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->unit2_all($kdproduk);
		$jml = $ssql->num_rows();
		
		if ($jml < 1)
		{
			$data = array(
				'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
				'fs_kd_product'	=> trim($kdproduk),
				'fs_kd_unit'	=> 'UNIT',
				'fs_kd_type'	=> '0',
				'fb_active'		=> '1',
				
				'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'		=> trim($this->session->userdata('gUser')),
				'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
			);
			
			$this->db->insert('tx_unitproduct', $data);
		}
		
		//hapus rangka
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_product = '".trim($kdproduk)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_motortp');
		//eof hapus rangka
		
		$data = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_product'	=> trim($kdproduk),
			'fs_type'		=> '0',
			
			'fs_chasis'		=> trim($kdrangka),
			'fs_engine'		=> trim($kdmesin),
			'fs_cilinder'	=> trim($cc),
			'fb_active'		=> '1',
			
			'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
			'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
			'fs_upddt'		=> trim($this->session->userdata('gUser')),
			'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
		);
		$this->db->insert('tm_motortp', $data);
		
		
		if ($kddb == '1')
		{
			$kdcomp = explode('|', trim($this->input->post('fs_kd_comp')));
			$nmcomp = explode('|', trim($this->input->post('fs_nm_comp')));
			$nmdb = explode('|', trim($this->input->post('fs_nm_db')));
			
			$jml = count($kdcomp) - 1;
			if ($jml != 0)
			{
				for ($i=1; $i<=$jml; $i++)
				{
					$this->load->model('mProduk','',true);
					$ssql = $this->mProduk->simpanprodtodb($nmdb[$i],$kdcomp[$i],$kdproduk);
				}
			}
		}
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Product Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Product Update Success'
			);
			echo json_encode($hasil);
		}
	}
	
	function ceksave2()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		
		if (trim($kddept) == '' or trim($kdcount) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Departement unknown!!'
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
	
	function save2()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_unitprcpj');
		//eof hapus detail
		
		
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$harga = explode('|', trim($this->input->post('fn_harga')));
		$diskon = explode('|', trim($this->input->post('fn_diskon')));
		$deposit = explode('|', trim($this->input->post('fn_deposit')));
		$kdproduk = '';
		
		$jml = count($kdprod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				if (trim($kdproduk) == '' or trim($kdproduk) <> trim($kdprod[$i]))
				{
					$data = array(
						'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
						'fs_kd_dept'	=> trim($kddept),
						'fs_count'		=> trim($kdcount),
						'fs_kd_product'	=> trim($kdprod[$i]),
						'fs_kd_type'	=> '0',
						'fd_setting'	=> trim(date('Ymd')),
						
						'fn_unitprcc'	=> trim($harga[$i]),
						'fd_datec'		=> trim(date('Ymd')),
						'fd_sdatec'		=> '20150101',
						'fd_edatec'		=> '30000101',
						'fb_aktivec'	=> '1',
						'fn_discc'		=> trim($diskon[$i]),
						'fn_depositc'	=> trim($deposit[$i]),
						
						'fn_unitprcn'	=> '0',
						'fd_daten'		=> '30000101',
						'fd_sdaten'		=> '30000101',
						'fd_edaten'		=> '30000101',
						'fb_aktiven'	=> '0',
						'fn_discn'		=> '0',
						'fn_depositn'	=> '0',
						
						'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
						'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
						'fs_upddt'		=> trim($this->session->userdata('gUser')),
						'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
					);
					$this->db->insert('tm_unitprcpj', $data);
				}
				$kdproduk = trim($kdprod[$i]);
			}
		}
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Saving Default Price Purchase Update Success'
		);
		echo json_encode($hasil);
	}
	
	function ceksave3()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		
		if (trim($kddept) == '' or trim($kdcount) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Departement unknown!!'
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
	
	function save3()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_unitprcoe');
		//eof hapus detail
		
		
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$harga = explode('|', trim($this->input->post('fn_harga')));
		$diskon = explode('|', trim($this->input->post('fn_diskon')));
		$bbn = explode('|', trim($this->input->post('fn_bbn')));
		$dp = explode('|', trim($this->input->post('fn_dp')));
		$kdproduk = '';
		
		$jml = count($kdprod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				if (trim($kdproduk) == '' or trim($kdproduk) <> trim($kdprod[$i]))
				{
					$data = array(
						'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
						'fs_kd_dept'	=> trim($kddept),
						'fs_count'		=> trim($kdcount),
						'fs_kd_product'	=> trim($kdprod[$i]),
						'fs_kd_type'	=> '0',
						'fd_setting'	=> trim(date('Ymd')),
						
						'fn_unitprcc'	=> trim($harga[$i]),
						'fd_datec'		=> trim(date('Ymd')),
						'fd_sdatec'		=> '20150101',
						'fd_edatec'		=> '30000101',
						'fb_aktivec'	=> '1',
						
						'fn_unitprcn'	=> '0',
						'fd_daten'		=> '30000101',
						'fd_sdaten'		=> '30000101',
						'fd_edaten'		=> '30000101',
						'fb_aktiven'	=> '0',
						
						'fn_grsmargin'	=> trim($diskon[$i]),
						'fn_bbn'		=> trim($bbn[$i]),
						'fn_jualksi'	=> trim($dp[$i]),
						
						'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
						'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
						'fs_upddt'		=> trim($this->session->userdata('gUser')),
						'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
					);
					$this->db->insert('tm_unitprcoe', $data);
				}
				$kdproduk = trim($kdprod[$i]);
			}
		}
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Saving Default Price Sales Update Success'
		);
		echo json_encode($hasil);
	}
}
?>
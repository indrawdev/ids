<?php

class Beli extends CI_Controller
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
			$this->load->view('vbeli');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function supp_kode()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdsupp = trim($this->input->post('fs_code'));
		$nmsupp = trim($this->input->post('fs_nm_code'));

		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->supp_beli_all($kdsupp,$nmsupp,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->supp_beli($kdsupp,$nmsupp,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$invno = trim($this->input->post('fs_invno'));

		$cari = trim($this->input->post('fs_cari'));

		$trx = 'BL';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->beli_unit_all($trx,$refno,$docno,$invno,'0',$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->beli_unit($trx,$refno,$docno,$invno,'0',$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function product()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$nmprod = trim($this->input->post('fs_nm_product'));
		$tgl = trim($this->input->post('fd_refno'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_all($kdprod,$nmprod,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->product_beli($kdprod,$nmprod,$tgl,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function unit()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$kdunit = trim($this->input->post('fs_kd_unit'));
		$nmunit = trim($this->input->post('fs_nm_unit'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->unit_all($kdprod);
		
		echo json_encode($ssql->result());
	}
	
	function color()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdwarna = trim($this->input->post('fs_kd_vareable'));
		$nmwarna = trim($this->input->post('fs_nm_vareable'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->warna_all($kdwarna,$nmwarna,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->warna($kdwarna,$nmwarna,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function metode()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdmetode = trim($this->input->post('fs_kd_metode'));
		$nmmetode = trim($this->input->post('fs_nm_metode'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->beli_mtd_all($kdmetode,$nmmetode);
		
		echo json_encode($ssql->result());
	}
	
	function tax()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdtax = trim($this->input->post('fs_kd_tax'));
		$nmtax = trim($this->input->post('fs_nm_tax'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->tax_all($kdtax,$nmtax,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->tax($kdtax,$nmtax,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function otax()
	{
		$hasil = array(
			'sukses'	=> true,
			'tax'		=> $this->session->userdata('gOTax')
		);
		echo json_encode($hasil);
	}
	
	function wh()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdwh = trim($this->input->post('fs_code'));
		$nmwh = trim($this->input->post('fs_nm_code'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->wh_all('','','',$kdwh,$nmwh,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->wh('','','',$kdwh,$nmwh,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cek_reg()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cekreg($rangka,'');
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$nmprod = $ssql->fs_nm_product;
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Chasis already exists in database in the product "'.trim($nmprod).'", add chassis cancel!!'
			);
			echo json_encode($hasil);
			return;
		}
		else
		{
			// $hasil = array(
				// 'sukses'	=> true
			// );
			// echo json_encode($hasil);
		}
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cekreg('',$mesin);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$nmprod = $ssql->fs_nm_product;
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Machine already exists in database in the product "'.trim($nmprod).'", add machine cancel!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true
			);
			echo json_encode($hasil);
		}
	}
	
	function grid_prod()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->grid_prod($refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_reg()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->grid_reg($refno);
		
		echo json_encode($ssql->result());
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = 'BL';
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$ppn = trim($this->input->post('fn_ppn'));
		
		if (trim($ppn) <= 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Canceled, Total PPN less than or equal to zero (0)!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		$prod = explode('|', trim($this->input->post('fs_kd_product')));
		$qty = explode('|', trim($this->input->post('fn_qty')));
		
		$jml = count($prod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				if (trim($qty[$i]) == '0')
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Saving Failed, </br> Chassis is empty for product "'.trim($prod[$i]).'"!! </br> Please fill it first'
					);
					echo json_encode($hasil);
					return;
				}
			}
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, </br> Product is empty, please fill in advance!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cek_mutasi($rangka,$mesin);
		
		if ($ssql->num_rows() > 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already transfer out!!</br>Please transfer in first!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cek_jual($rangka,$mesin);
		
		if ($ssql->num_rows() > 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already sold!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'lanjut'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mBeli','',true);
			$ssql = $this->mBeli->cek_refno($kdtrx,$refno);
			
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
		$refnodt = trim($this->input->post('fd_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$docnodt = trim($this->input->post('fd_docno'));
		$invno = trim($this->input->post('fs_invno'));
		
		$invnodt = trim($this->input->post('fd_invno'));
		$kdsupp = trim($this->input->post('fs_kd_sup'));
		$count = trim($this->input->post('fs_count'));
		$kdterm = trim($this->input->post('fs_kd_term'));
		$kdbelimtd = trim($this->input->post('fs_kd_belimtd'));
		
		$nmbelimtd = trim($this->input->post('fs_nm_belimtd'));
		$sumtotalharga = trim($this->input->post('fn_sumtotal_harga'));
		$sumtotaldisc = trim($this->input->post('fn_sumtotal_disc'));
		$sumdisc = trim($this->input->post('fn_sumdisc'));
		$total = trim($this->input->post('fn_total'));
		
		$ppn = trim($this->input->post('fn_ppn'));
		$kdtax = trim($this->input->post('fs_kd_tax'));
		$nmtax = trim($this->input->post('fs_nm_tax'));
		$taxpersen = trim($this->input->post('fn_taxpersen'));
		$taxtotal = trim($this->input->post('fn_taxtotal'));
		
		$gtotal = trim($this->input->post('fn_gtotal'));
		$kdtrx = 'BL';
		
		//get periode
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->getperiode($refnodt);
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$periode = $ssql->periode;
		}
		else
		{
			$periode = '';
		}
		//eof get periode
		
		$xupdate = false;
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cek_refno($kdtrx,$refno);
		
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
			$ssql = $this->mMainModul->get_fakturbeli($xprefix);
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
		
		//hapus tx_actheaderdt
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof hapus tx_actheaderdt
		
		$dt = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
			'fs_count'			=> trim($this->session->userdata('gCount')),
			'fs_kd_trx'			=> trim($kdtrx),
			'fs_refno'			=> trim($refno),
			
			'fd_refno'			=> trim($refnodt),
			'fd_periode'		=> trim($periode),
			'fs_kd_cussup'		=> trim($kdsupp),
			'fs_countcussup'	=> trim($count),
			'fs_docno'			=> trim($docno),
			
			'fd_docno'			=> trim($docnodt),
			'fs_kd_term'		=> trim($kdterm),
			'fn_grsamt'			=> trim($sumtotalharga),
			'fn_dscamt'			=> trim($sumtotaldisc),
			'fn_sdscamt'		=> trim($sumdisc),
			
			'fn_netbframt'		=> trim($total),
			'fn_taxamt'			=> trim($ppn),
			'fs_kd_otax'		=> trim($kdtax),
			'fs_nm_otax'		=> trim($nmtax),
			'fn_otaxpct'		=> trim($taxpersen),
			
			'fn_otaxamt'		=> trim($taxtotal),
			'fn_netaftramt'		=> trim($gtotal),
			'fn_addonamt'		=> '0',
			'fn_deducamt'		=> '0',
			'fn_trxamt'			=> trim($gtotal),
			
			'fn_rmnamt'			=> trim($gtotal),
			'fn_bbn'			=> '0',
			'fn_subsidi'		=> '0',
			'fb_spearpart'		=> trim($this->session->userdata('gSparePart')),
			'fn_installment'	=> '0',
			
			'fs_kd_payment'		=> '',
			'fs_nm_payment'		=> '',
			'fs_acno'			=> '',
			'fs_kd_wh'			=> '',
			'fs_nm_wh'			=> '',
			
			'fs_kd_salesman'	=> '',
			'fs_nm_salesman'	=> '',
			'fs_kd_salesmtd'	=> trim($kdbelimtd),
			'fs_nm_salesmtd'	=> trim($nmbelimtd),
			
			'fs_kd_dp'			=> '',
			'fs_nm_dp'			=> '',
			'fn_dpamt'			=> '0',
			'fs_invno'			=> trim($invno),
			'fd_invno'			=> trim($invnodt)
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
			
			$this->db->insert('tx_posheader', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_posheader', $data);
		}
		
		//hapus stok
		$this->load->model('mJual','',true);
		$ssql = $this->mJual->unit_stokupdate($refno,'DEL');
		//eof hapus stok
		
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus detail
		
		//simpan detail
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$nmprod = explode('|', trim($this->input->post('fs_nm_product')));
		$qty = explode('|', trim($this->input->post('fn_qty')));
		$kdunit = explode('|', trim($this->input->post('fs_kd_unit')));
		$harga = explode('|', trim($this->input->post('fn_harga')));
		
		$disc = explode('|', trim($this->input->post('fn_disc')));
		$kdlux = explode('|', trim($this->input->post('fs_kd_lux')));
		$nmlux = explode('|', trim($this->input->post('fs_nm_lux')));
		
		$luxpersen = explode('|', trim($this->input->post('fn_luxpersen')));
		$lux = explode('|', trim($this->input->post('fn_lux')));
		$seqno = explode('|', trim($this->input->post('fs_seqno')));
		
		$jml = count($kdprod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				$jmlharga = (float)(round($qty[$i] * $harga[$i], 6));
				$jmldiskon = (float)(round($qty[$i] * $disc[$i], 6));
				$diskon = (float)(round($qty[$i] * $harga[$i] / $sumtotalharga * $sumdisc, 6));
				
				$data = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
					'fs_count'		=> trim($this->session->userdata('gCount')),
					'fs_kd_trx'		=> trim($kdtrx),
					'fs_refno'		=> trim($refno),
					
					'fd_refno'		=> trim($refnodt),
					'fd_periode'	=> trim($periode),
					'fs_seqno'		=> trim($seqno[$i]),
					'fs_kd_product'	=> trim($kdprod[$i]),
					'fs_nm_product'	=> trim($nmprod[$i]),
					
					'fn_qtytr'		=> trim($qty[$i]),
					'fn_qtytr1'		=> trim($qty[$i]),
					'fs_unitbill'	=> trim($kdunit[$i]),
					'fn_unitprc'	=> trim($harga[$i]),
					'fn_dscprc'		=> trim($disc[$i]),
					
					'fn_lineamt'	=> $jmlharga,
					'fn_ldscamt'	=> $jmldiskon,
					'fn_sdscamt'	=> $diskon,
					'fn_addonamt'	=> '0',
					'fn_deducamt'	=> '0',
					
					'fn_ftrxamt'	=> (float)(round($jmlharga - $jmldiskon - $diskon, 6)),
					'fs_kd_wh'		=> '',
					'fs_countwh'	=> '',
					'fs_kd_bin'		=> '',
					'fs_luxcd'		=> $kdlux[$i],
					
					'fs_luxnm'		=> $nmlux[$i],
					'fn_luxpct'		=> $luxpersen[$i],
					'fn_luxamt'		=> $lux[$i],
					'fs_kd_ppnbm'	=> '',
					'fs_nm_ppnbm'	=> '',
					
					'fn_ppnbm'		=> '0',
					'fn_ppnbmpct'	=> '0',
					
					'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'		=> trim($this->session->userdata('gUser')),
					'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tx_posdetail', $data);
			}
		}
		//eof simpan detail
		
		
		//hapus reg
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		//eof hapus reg
		
		//simpan reg
		$kdprodreg = explode('|', trim($this->input->post('fs_kd_prodreg')));
		$rangka = explode('|', trim($this->input->post('fs_rangka')));
		$mesin = explode('|', trim($this->input->post('fs_mesin')));
		$cc = explode('|', trim($this->input->post('fs_cc')));
		$thn = explode('|', trim($this->input->post('fs_thn')));
		
		$kdwarna = explode('|', trim($this->input->post('fs_kd_color')));
		$kdwh = explode('|', trim($this->input->post('fs_kd_wh')));
		$nmwh = explode('|', trim($this->input->post('fs_nm_wh')));
		$hpp = explode('|', trim($this->input->post('fn_hpp')));
		
		$seqno = explode('|', trim($this->input->post('fs_seqnoreg')));
		$nmseqno = '';
		$seqnoreg = 0;
		
		$jml = count($kdprodreg) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				//generate reg product
				$xprefix = trim($this->session->userdata('gDept')).trim($kdprodreg[$i]).trim($refnodt);
				$this->load->model('mMainModul','',true);
				$ssql = $this->mMainModul->get_regproduct($xprefix);
				//eof generate reg product
				
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					
					$register = $xprefix.sprintf("%06d",trim($ssql->fs_seqno) + 1);
				}
				else
				{
					$register = $xprefix.'000001';
				}
				
				if (trim($nmseqno) == '' or trim($nmseqno) <> trim($seqno[$i]))
				{
					$seqnoreg = 1;
				}
				else
				{
					++$seqnoreg;
				}
				
				$data = array(
					'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
					'fs_count'			=> trim($this->session->userdata('gCount')),
					'fs_kd_trx'			=> trim($kdtrx),
					'fs_kd_strx'		=> '0100',
					
					'fs_refno'			=> trim($refno),
					'fd_periode'		=> trim($periode),
					'fs_seqnoRegister'	=> trim(sprintf("%06d",$seqnoreg)),
					'fs_seqno'			=> trim($seqno[$i]),
					'fs_register'		=> trim($register),
					
					'fs_kd_product'		=> trim($kdprodreg[$i]),
					'fs_kd_type'		=> '0',
					'fn_hpp'			=> trim($hpp[$i]),
					'fb_delete'			=> '0',
					'fn_silinder'		=> trim($cc[$i]),
					
					'fs_rangka'			=> trim($rangka[$i]),
					'fs_machine'		=> trim($mesin[$i]),
					'fs_kd_warna'		=> trim($kdwarna[$i]),
					'fd_thnpembuatan'	=> trim($thn[$i]),
					'fd_thnperakitan'	=> trim($thn[$i]),
					
					'fn_qty'			=> '1',
					'fs_kd_wh'			=> trim($kdwh[$i]),
					'fs_nm_wh'			=> trim($nmwh[$i]),
					
					'fs_usrcrt'			=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'			=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'			=> trim($this->session->userdata('gUser')),
					'fd_upddt'			=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tm_icregister', $data);
				
				$this->load->model('mBeli','',true);
				$ssql = $this->mBeli->reg_last($refno,$rangka[$i],$mesin[$i]);
				
				$nmseqno = trim($seqno[$i]);
			}
		}
		//eof simpan reg
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->pos_beli($refno,$kdtrx,$kdbelimtd);
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Purchase Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Purchase Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
	}
	
	function cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Remove Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mBeli','',true);
			$ssql = $this->mBeli->cek_refno('BL',$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$this->load->model('mBeli','',true);
				$ssql = $this->mBeli->cek_mutasi($rangka,$mesin);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Remove Failed, There are product which spesific chasis and macines already transfer out!!</br>Please transfer in first!!'
					);
					echo json_encode($hasil);
					return;
				}
				
				$this->load->model('mBeli','',true);
				$ssql = $this->mBeli->cek_jual($rangka,$mesin);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Remove Canceled, There are product which spesific chasis and macines already sold!!'
					);
					echo json_encode($hasil);
				}
				else
				{
					$hasil = array(
						'sukses'	=> true,
						'hasil'		=> 'Remove record?'
					);
					echo json_encode($hasil);
				}
			}
			else
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Remove Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function remove()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		//hapus beli
		
		//hapus tx_actheaderdt
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof hapus tx_actheaderdt
		
		//hapus tx_actheader
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheader');
		//eof hapus tx_actheader
		
		//hapus tx_actdetail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		//eof hapus tx_actdetail
		
		//hapus tx_posheader
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posheader');
		//eof hapus tx_posheader
		
		//hapus tx_posdetail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus tx_posdetail
		
		//hapus tm_icregister
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		//eof hapus tm_icregister
		
		//eof hapus beli
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove purchase reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function print_beli()
	{ 
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tbeliunit';
		$nmfile = 'beliunit-'.$jamskg;
		
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.config_item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.config_item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Pembelian');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		// $oExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . '&RPage &P of &N');
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		//query dg nomor refno
		
		$sKdComp = trim($this->session->userdata('gComp'));
		$sKdDept = trim($this->session->userdata('gDept'));
		$sKdCount = trim($this->session->userdata('gCount'));
		$KdTrans = "BL";
		$sRefno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->beli_cetak($sKdComp,$sKdDept,$sKdCount,$KdTrans,$sRefno);
				
		$data = array();
		$data = $ssql;
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$NamaDepartemen = $ssql->NamaDepartemen;
			$AlamatCabang = $ssql->AlamatCabang;
			$Refno = $ssql->Refno;
			$TanggalRefno = $ssql->Tanggal;
			$DocNo = $ssql->DocNo;
			$UserName = trim($this->session->userdata('gUser'));
			
			$GrossAmount = $ssql->fn_grsamt;
			$DiscountAmount = $ssql->fn_dscamt;
			$SubDiscountAmount = $ssql->fn_sdscamt;
			$NetAmount = $ssql->fn_netbframt;
			$Tax = $ssql->fn_taxamt;
			$OtherTax = $ssql->fn_otaxamt;
			
			$NetAfterTax = $ssql->fn_netaftramt;
			$AddOnAmount = $ssql->fn_addonamt;
			$DeductionAmount = $ssql->fn_deducamt;
			$GrandTotal = $ssql->fn_grdamt;
			$Bilang = $this->mMainModul->terbilang($GrandTotal,4);
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$sel->setCellValue('A'.$i,'PEMBELIAN');
			$sel->mergeCells('A'.$i.':F'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$p = $i;
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'DEPARTEMENT');
			$sel->setCellValue('B'.$i,': '.trim($NamaDepartemen));
			$sel->setCellValue('E'.$i,'DATE');
			$sel->setCellValue('F'.$i,': '.trim($TanggalRefno));
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'REFNO');
			$sel->setCellValue('B'.$i,': '.trim($Refno));
			$sel->setCellValue('E'.$i,'DOCUMENT NO.');
			$sel->setCellValue('F'.$i,': '.trim($DocNo));
			$i = $i + 2;
			$k = $k + 2;
			$sel->setCellValue('A'.$i,'CODE');
			$sel->setCellValue('B'.$i,'PRODUCT NAME');
			$sel->setCellValue('C'.$i,'UNIT');
			$sel->setCellValue('D'.$i,'QTY');
			$sel->setCellValue('E'.$i,'UNIT PRICE');
			$sel->setCellValue('F'.$i,'TOTAL');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i++;
			
			
			$NamaUnit = '';
			foreach ($data->result() as $row) 
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':F'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p + 7;
					$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p + 5;
					$j = $q + 1;
					$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
					$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
					$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$p = $i;
					
					$k = 1;
					$sel->setCellValue('A'.$i,'PEMBELIAN');
					$sel->mergeCells('A'.$i.':F'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$p = $i;
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i,'DEPARTEMENT');
					$sel->setCellValue('B'.$i,': '.trim($NamaDepartemen));
					$sel->setCellValue('E'.$i,'DATE');
					$sel->setCellValue('F'.$i,': '.trim($TanggalRefno));
					$i++;
					$k++;
					$sel->setCellValue('A'.$i,'REFNO');
					$sel->setCellValue('B'.$i,': '.trim($Refno));
					$sel->setCellValue('E'.$i,'NOTE');
					$sel->setCellValue('F'.$i,': ');
					$i = $i + 2;
					$k = $k + 2;
					$sel->setCellValue('A'.$i,'CODE');
					$sel->setCellValue('B'.$i,'PRODUCT NAME');
					$sel->setCellValue('C'.$i,'UNIT');
					$sel->setCellValue('D'.$i,'QTY');
					$sel->setCellValue('E'.$i,'UNIT PRICE');
					$sel->setCellValue('F'.$i,'TOTAL');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, trim($row->fs_kd_product));
					$sel->setCellValue('B'.$i, trim($row->fs_nm_product));
					$sel->setCellValue('C'.$i, trim($row->fs_nm_unit));
					$sel->setCellValue('D'.$i, trim($row->fn_qty));
					$sel->setCellValue('E'.$i, number_format(trim($row->fn_unit_price),2,'.',','));
					$sel->setCellValue('F'.$i, number_format(trim($row->fn_total),2,'.',','));
					
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$i++;
					$k++;
				}
				
				if (trim($NamaUnit) == '' or trim($NamaUnit) <> trim($row->fs_kd_product))
				{
					$sel->setCellValue('A'.$i, trim($row->fs_kd_product));
					$sel->setCellValue('B'.$i, trim($row->fs_nm_product));
					$sel->setCellValue('C'.$i, trim($row->fs_nm_unit));
					$sel->setCellValue('D'.$i, trim($row->fn_qty));
					$sel->setCellValue('E'.$i, number_format(trim($row->fn_unit_price),2,'.',','));
					$sel->setCellValue('F'.$i, number_format(trim($row->fn_total),2,'.',','));
					
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$q = $i;
					$i++;
					$k++;
					$l = 1;
				}
				$sel->setCellValue('A'.$i, trim($l));
				$sel->setCellValue('B'.$i, trim($row->fs_rangka));
				$sel->setCellValue('C'.$i, trim($row->fs_mesin));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$NamaUnit = trim($row->fs_kd_product);
				$i++;
				$k++;
				$l++;
			}
			$j = $i - 1;
			$sel->getStyle('A'.$j.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$q = $p + 5;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$q = $p + 5;
			$j = $q + 1;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'Gross Amount:');
			$sel->setCellValue('B'.$i, number_format($GrossAmount,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Discount Amount:');
			$sel->setCellValue('B'.$i, number_format($DiscountAmount,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Sub Dsc Amount:');
			$sel->setCellValue('B'.$i, number_format($SubDiscountAmount,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Net Amount:');
			$sel->setCellValue('B'.$i, number_format($NetAmount,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Tax:'  );
			$sel->setCellValue('B'.$i, number_format($Tax,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Other Tax:');
			$sel->setCellValue('B'.$i, number_format($OtherTax,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Net After Tax:');
			$sel->setCellValue('B'.$i, number_format($NetAfterTax,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'AddOn Amount:');
			$sel->setCellValue('B'.$i, number_format($AddOnAmount,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Deduction Amount:');
			$sel->setCellValue('B'.$i, number_format($DeductionAmount,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			// $sel->setCellValue('A'.$i,'Down Payment:');
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $i++;
			// $k++;
			$sel->setCellValue('A'.$i,'Grand Total:');
			$sel->setCellValue('B'.$i, number_format($GrandTotal,2,'.',',') );
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$i=$i+3;
			$k=$k+3;
			
			$sel->setCellValue('A'.$i,'Terbilang : # '. $Bilang .' #');
			// $sel->getStyle('A'.$i,$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i,$i)->getFont()->setItalic(true);
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$i=$i+5;
			$k=$k+5;
			
			$sel->setCellValue('A'.$i,'Diterima Oleh : ');
			$sel->setCellValue('C'.$i,'Diserahkan Oleh : ');
			$sel->mergeCells('C'.$i.':E'.$i);
			$sel->setCellValue('F'.$i,'Disetujui Oleh : ');
			
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i = $i + 20;
			$k = $k + 20;
			
			$sel->setCellValue('A'.$i,'(.......................)');
			$sel->setCellValue('C'.$i,'(.......................)');
			$sel->mergeCells('C'.$i.':E'.$i);
			$sel->setCellValue('F'.$i,'(.......................)');
			
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i = $i + 5;
			$k = $k + 5;
			
			$sel->setCellValue('A'.$i,'Faktur ini bukan merupakan kwitansi');
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i,$i)->getFont()->setItalic(true);
			
			
			$oWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
			$oWriter->save($xPath.$nmfile.'.xls');
			
			//hapus file lama
			$this->load->model('mMainModul','',true);
			$jamskg = $this->mMainModul->microtime_float();
			$exp = 1800;
			
			$current_dir = @opendir($xPath);
			while ($filename = @readdir($current_dir))
			{
				if ($filename != '.' and $filename != '..' and $filename != 'captcha')
				{
					$filename2 = str_replace('.xls', '', $filename);
					$filename2 = str_replace('.pdf', '', $filename2);
					
					$xlen = strlen($filename2);
					$xmulai = stripos($filename2,'-') + 1;
					$filename2 = substr($filename2,$xmulai,$xlen);
					
					if (($filename2 + $exp) < $jamskg)
					{
						@unlink($xPath.$filename);
					}
				}
			}
			@closedir($current_dir);
			//eof hapus file lama
			
			$this->load->model('mMainModul','',true);
			$this->mMainModul->pdf($nmfile);
			
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> '"'.$nmfile.'.pdf" has been created!!',
				'nmfile'	=> $xPath.$nmfile.'.pdf'
			);
			
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($hasil);
		}	
	}
}
?>